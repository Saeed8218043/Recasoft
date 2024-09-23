<?php

namespace App\Http\Controllers;
use App\AlertHistory;
use App\Company;
use App\Device;
use App\DeviceSetting;
use App\DeviceTemperature;
use App\Equipment;
use App\Events\HelloPusherEvent;
use App\Events\TouchEvent;
use App\Manager;
use App\Notification;
use App\NotificationDevice;
use App\NotificationEmail;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Log;
use Mail;
use Illuminate\Support\Facades\Config;

class DataController extends Controller
{
public function testReminder(Request $request){
	$device = Device::where('device_id', "bive56n95ss000eukj80")->first();
	$deviceId = $device->id;
	$this->updateTriggers($deviceId, $device, $device->temperature);

}
	public function updateTriggers($deviceId = '', Device $device, $temp = '')
	{
		if (!$device) {
			$device = Device::where('device_id', $deviceId)->select('id')->first();
		}
		if ($device) {
			$device_id = isset($device->id) ? $device->id : 0;
			$res = DB::select('SELECT ND.last_deviate_time,ND.id,ND.notification_id,ND.reminder_sent,ND.device_id,N.alert_type,N.temp_range,N.upper_celcius,N.reminder_days,N.company_id,N.lower_celcius,ND.created_at,ND.already_sent FROM `notification_devices` ND
			left join notifications N on N.id = ND.notification_id
			where N.isActive=1  and ND.device_id=?', [$device_id]); //and N.alert_type="Temperature"
			Log::info('triggers');
			if (count($res) > 0) {
				foreach ($res as $ro) {
					$temeprature_last_updated = isset($device->temeprature_last_updated) ? $device->temeprature_last_updated : Carbon::now();
					$device_notification_id = isset($ro->id) ? $ro->id : 0;
					$sensor_name = !empty($device->name) ? $device->name : $device->device_id;
					$notification_id = isset($ro->notification_id) ? $ro->notification_id : 0;
					$alert_type = isset($ro->alert_type) ? $ro->alert_type : '';
					$last_deviate_time = isset($ro->last_deviate_time) ? $ro->last_deviate_time : '';
					$reminder_days = isset($ro->reminder_days) ? $ro->reminder_days : '';
					$already_sent = isset($ro->already_sent) ? $ro->already_sent : '';
					$reminder_sent = isset($ro->reminder_sent) ? $ro->reminder_sent : '';
					Log::info('ND id :' . $device_notification_id);
					Log::info('Days ago :' . Carbon::parse($last_deviate_time)->diffInDays(Carbon::now()));
					if ($alert_type != 'Temperature') {
						if (!($last_deviate_time == NULL || $last_deviate_time == '')) {
							NotificationDevice::whereId($device_notification_id)->update(['last_deviate_time' => null, 'already_sent' => 0,'updated_at' => Carbon::now()]);
						}
					}

					$temp_range = isset($ro->temp_range) ? $ro->temp_range : '';
					$upper_celcius = isset($ro->upper_celcius) ? $ro->upper_celcius : '';
					$lower_celcius = isset($ro->lower_celcius) ? $ro->lower_celcius : '';
					Log::info('Upper celsius' . $upper_celcius);
					Log::info('Lower celsius' . $lower_celcius);
					$newTime = '';
					$out = 0;
					if ($temp_range == 'below' && $temp < $lower_celcius) {
						// Log::info('below temp in');
						// Log::info($temp);
						// Log::info($lower_celcius);
						$out = 1;
					} elseif ($temp_range == 'above' && $temp > $upper_celcius) {
						// Log::info('above temp in');
						// Log::info($temp);
						// Log::info($upper_celcius);
						$out = 1;
					}
					if ($out == 1) {
						$updated_date = $temeprature_last_updated > $ro->created_at ;

						if($updated_date == false){
							continue;
						}
						else{
							if ($last_deviate_time == '' || $last_deviate_time == NULL ) {
							NotificationDevice::whereId($device_notification_id)->update(['last_deviate_time' => $temeprature_last_updated]);
						}
					}
						Log::info('In statement');
						Log::info('reminder days' . $reminder_days);

						if (isset($reminder_days) && $reminder_days!=null && Carbon::parse($last_deviate_time)->diffInDays(Carbon::now()) == $reminder_days && $already_sent == 1 && $reminder_sent == 0) {

							Log::info('reminder called');
							$notifications = NotificationEmail::where('notification_id', $notification_id)->get();
							Log::info('Notif table :' . $notifications);
							$equipment =Device::where('sensor_id',$device->device_id)->first();
							$name = !empty($equipment->name) ? $equipment->name : '';
							if ($equipment ==null || $equipment=='') {
								$name = !empty($device->name)?$device->name:$device->device_id;
							}else{
								$name =$equipment->name;
							}
							foreach ($notifications as  $notification) {
								$device = Device::where('id',$ro->device_id)->first();
								$subject = "Reminder ($notification->subject)";
								$company = Company::where('company_id',$device->company_id)->first();
								$subject = str_replace('$name',$name,$subject);
								$subject = str_replace('$description',$device->description,$subject);
								$subject = str_replace('$deviceID',$ro->device_id,$subject);
								$subject = str_replace('$celsius',$device->temperature.'°C',$subject);
								$subject = str_replace('$device_name',$device->name,$subject);
							  $subject = str_replace('$project_name',$company->name,$subject);
							  $dev = Device::where('id',$ro->device_id)->first();
							  $url =route("sensor-details",['company_id'=>$device->company_id,'device_id'=>$dev->device_id]);
							$subject = str_replace('$url',$url,$subject);
							  $body = $notification->content;
							  $company = Company::where('company_id',$device->company_id)->first();
							  $body = str_replace('$name',$name,$body);
							  $body = str_replace('$connected_sensor', $sensor_name, $body);
							  $body = str_replace('$description',$device->description,$body);
							  $body = str_replace('$deviceID',$dev->device_id,$body);
							  $body = str_replace('$celsius',$device->temperature.'°C',$body);
							  $body = str_replace('$device_name',$device->name,$body);
							$body = str_replace('$project_name',$company->name,$body);
							// $url =route("sensor-details",['company_id'=>$device->company_id,'device_id'=>$dev->device_id]);
							$url = route("equipment-details", ['company_id' => $equipment->company_id, 'device_id' => $equipment->device_id]);

							$body = str_replace('$url',$url,$body);
							$data_ar = [
								'upper_celcius' => $upper_celcius,
								'lower_celcius' => $lower_celcius,
								'date' => $last_deviate_time,
								'device_id' => $device->device_id,
								'temp'=>$device->temperature,
								'body'=>$body
							];
								$emailString[] = isset($notification->email) ? $notification->email : '';
								$notification_type = isset($notification->notification_type) ? $notification->notification_type : '';
								if ($notification_type == 0) {
									Log::info('before email');
									$this->reminderEmail($emailString, $data_ar, $subject);
								}
								// elseif($notification_type==1){
								// 	Log::info('before SMS');
								// 	$message ='This is an reminder that the issue below is still not resolved';
								// 	$this->reminderSMS($emailString,$message);  
								//   }
							}
							NotificationDevice::whereId($device_notification_id)->update(['reminder_sent' => 1]);
						}
						
						 
						
					} 
					elseif ($out == 0) {
						// Log::info('restate sensor');
						// Log::info($device_notification_id);
						// Log::info($temp);
						// Log::info($lower_celcius);
						NotificationDevice::whereId($device_notification_id)->update(['last_deviate_time' => null, 'already_sent' => 0]);
					}
				}
			}
		}
	}


	function time_diff_string($from, $to, $full = false) {
		$from = new \DateTime($from);
		$to = new \DateTime($to);
		$diff = $to->diff($from);
  
		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;
  
		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}
  
		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	  }

	public function reminderEmail($emailString, $data_ar = [], $subject)
	{
		Log::info($data_ar);
		Config::set('mail.from', config('mail.alert_email','alerts@recasoft.no'));
		Config::set('mail.username', config('mail.alert_email','alerts@recasoft.no'));
		foreach ($emailString as $es) {
			$emails = explode(',', $es);
			foreach ($emails as $to_email) {
				$to_email = trim($to_email);
				try {
					Log::info('Try Email');
					Mail::send('emails.reminder_email', $data_ar, function ($message) use ($to_email, $subject) {
						$message->to($to_email)
							->subject($subject);
						$message->from('alerts@recasoft.no', 'Recasoft Technologies');
						$message->replyTo('alerts@recasoft.no', 'Recasoft Technologies');
					});
				} catch (\Exception $e) {
					Log::info('Catch Email error');
					Log::info($e);
					// echo 'error';
				}
			}
		}
	}

	//   public function reminderSMS($phoneString,$message){
	// 	if($phoneString!=''){
	// 		foreach($phoneString as $number){
	// 			Log::info('in phone function');
	// 			$phones = explode(',',$number);
	// 			foreach($phones as $phone){
	// 			  $this->sendSms($phone,$message);    
	// 			}
	// 		}
	// 	  }
	//   }

	public function postData(Request $request)
	{

		$eventType = isset($request['event']['eventType']) ? $request['event']['eventType'] : '';
		$deviceType = isset($request['metadata']['deviceType']) ? $request['metadata']['deviceType'] : '';
		// Log::info($request->all());
		// Log::info($request['metadata']['deviceType']);

		Log::info($request->all());


		/*if(!in_array($eventType, ['temperature','labelsChanged'])){
			return [];
		}*/

		$projectId = isset($request['metadata']['projectId']) ? $request['metadata']['projectId'] : '';
		$deviceId = isset($request['metadata']['deviceId']) ? $request['metadata']['deviceId'] : '';
		// Log::info('deviceId');
		// Log::info($deviceId);
		if ($eventType == 'touch') {
			event(new TouchEvent(array('event_type' => 'touch', 'projectId' => $projectId, 'deviceId' => $deviceId)));
		}
		$eventId = isset($request['event']['eventId']) ? $request['event']['eventId'] : '';
		$targetName = isset($request['event']['targetName']) ? $request['event']['targetName'] : '';
		$deviceName = isset($request['labels']['name']) ? $request['labels']['name'] : '';
		$deviceDescription = isset($request['labels']['description']) ? $request['labels']['description'] : '';
		// Log::info('targetName');
		// Log::info($targetName);

		$description = isset($request['labels']['description']) ? $request['labels']['description'] : '';
		$name = isset($request['labels']['name']) ? $request['labels']['name'] : '';

		if ($eventType == 'labelsChanged' && $deviceId != '') {
			$dataAR = isset($request['event']['data']) ? $request['event']['data'] : [];
			$updateArr = [];
			foreach ($dataAR as $key => $ARR) {
				foreach ($ARR as $key => $VAL) {
					if (in_array($key, ['name', 'description'])) {
						$updateArr[$key] = $VAL;
					}
				}
			}
			if (count($updateArr) > 0) {
				// Log::info('updateArr');
				// Log::info($updateArr);
				Device::where('device_id', $deviceId)->update($updateArr);
			}
		}

		$transmissionMode = $cloudConnector = $temperature = $temperature2 = '';
		$time_stamp = $time_stamp2 = '';
		if ($eventType == 'temperature') {

			$temperature = isset($request['event']['data']['temperature']['value']) ? $request['event']['data']['temperature']['value'] : '';
			$time_stamp = $updateTime = isset($request['event']['data']['temperature']['updateTime']) ? $request['event']['data']['temperature']['updateTime'] : '';

		} elseif ($eventType == 'networkStatus') {

			$temperature2 = isset($request['event']['data']['networkStatus']['signalStrength']) ? $request['event']['data']['networkStatus']['signalStrength'] : '';
			$time_stamp = $updateTime = isset($request['event']['data']['networkStatus']['updateTime']) ? $request['event']['data']['networkStatus']['updateTime'] : '';
			$cloudConnector = isset($request['event']['data']['networkStatus']['cloudConnectors'][0]['id']) ? $request['event']['data']['networkStatus']['cloudConnectors'][0]['id'] : '';
			$transmissionMode = isset($request['event']['data']['networkStatus']['transmissionMode']) ? $request['event']['data']['networkStatus']['transmissionMode'] : '';

		} elseif ($eventType == 'cellularStatus') {

			$temperature2 = isset($request['event']['data']['cellularStatus']['signalStrength']) ? $request['event']['data']['cellularStatus']['signalStrength'] : '';
			$time_stamp = $updateTime = isset($request['event']['data']['cellularStatus']['updateTime']) ? $request['event']['data']['cellularStatus']['updateTime'] : '';
			$cloudConnector = isset($request['event']['data']['cellularStatus']['cloudConnectors'][0]['id']) ? $request['event']['data']['cellularStatus']['cloudConnectors'][0]['id'] : '';
			$transmissionMode = isset($request['event']['data']['cellularStatus']['transmissionMode']) ? $request['event']['data']['cellularStatus']['transmissionMode'] : '';

		}
		elseif ($eventType == 'connectionStatus') {
			$offline = $updateTime = isset($request['event']['data']['connectionStatus']['connection']) ? $request['event']['data']['connectionStatus']['connection'] : '';
		}
		elseif ($eventType == 'alertFired') {
			$offline2 = $updateTime = isset($request['event']['data']['alertFired']['alertType']) ? $request['event']['data']['alertFired']['alertType'] : '';
		}
		elseif ($eventType == 'batteryStatus') {

			$temperature2 = isset($request['event']['data']['batteryStatus']['percentage']) ? $request['event']['data']['batteryStatus']['percentage'] : '';
			$time_stamp = $updateTime = isset($request['event']['data']['batteryStatus']['updateTime']) ? $request['event']['data']['batteryStatus']['updateTime'] : '';
			$cloudConnector = isset($request['event']['data']['batteryStatus']['cloudConnectors'][0]['id']) ? $request['event']['data']['batteryStatus']['cloudConnectors'][0]['id'] : '';
			$transmissionMode = isset($request['event']['data']['batteryStatus']['transmissionMode']) ? $request['event']['data']['batteryStatus']['transmissionMode'] : '';
			$device = Device::where('device_id', $deviceId)->first();
			if ($device) {
				$device->battery_level = $temperature2;
				$device->save();
			}
			return ['status' => true];
		}
				
				

		$date = date('Y-m-d H:i:s', strtotime($time_stamp));

		if (in_array($eventType, ['proximity', 'temperature', 'networkStatus', 'cellularStatus'])) {
			event(new HelloPusherEvent(array('deviceId' => $deviceId)));
			// Log::info('Pusher');
			// Log::info($projectId);
		}

		// if($eventType=='temperature'){
		$device = Device::where('device_id', $deviceId)->first();
		if (!isset($device->id)) {
			// Log::info('Device Not Found.');
			// return ['status'=>false];
			// return false;
			if (in_array($eventType, ['temperature']) || in_array($deviceType, ['ccon'])) {
				$device = new Device();
				$device->name = $name;
				$device->description = $description;
				$device->company_id = $projectId;
				$device->device_id = $deviceId;
				if((isset($offline) && $offline == "OFFLINE") || (isset($offline2) && $offline2 == "cloudConnectorOffline")){
					$device->is_active = 0;
				}else{
					$device->is_active = 1;
				}
				if ($deviceType == 'ccon') {
					$device->event_type = 'ccon';
				} else {
					$device->event_type = 'temperature';
				}
			}
		}

		if(isset($device->is_active) && $device->is_active == 0){
			$this->sendResolvedNotification($device);
			}

		if ($device && $temperature != '') {
			$device->temperature = $temperature;
			$this->updateTriggers($deviceId, $device, $temperature);
		}
		if ($device && $transmissionMode != '') {
			$device->transmissionMode = $transmissionMode;
		}
		if ($device && $time_stamp != '') {
			$device->temeprature_last_updated = $date;
		}
		$coming_from_id = isset($device->coming_from_id) ? $device->coming_from_id : '';
		if (isset($device->company_id) && $device->company_id != $projectId && $coming_from_id == '') {
			$device->company_id = $projectId;
		}
		if ($eventType == 'temperature' && $deviceName != '') {
			$device->name = $deviceName;
		}
		if ($eventType == 'temperature' && $deviceDescription != '') {
			$device->description = $deviceDescription;
		}
		if ($device) {

			$device->device_status = 1;
			if((isset($offline) && $offline == "OFFLINE") || (isset($offline2) && $offline2 == "cloudConnectorOffline")){
				$device->is_active = 0;
			}else{
				$device->is_active = 1;
				
			}

			$device->save();
		}

		// Log::info('temperature');
		// Log::info($temperature);
		// Log::info('time_stamp');
		// Log::info($time_stamp);

		if ($temperature != '' && $time_stamp != '' && $time_stamp != '0000-00-00 00:00:00') {
			$samples = isset($request['event']['data']['temperature']['samples']) ? $request['event']['data']['temperature']['samples'] : '';
	
			if($samples!=''){
				foreach ($samples as $key => $sample) {
					Log::info('sample log');
					$date = date('Y-m-d H:i:s', strtotime($samples[$key]['sampleTime']));
					$ins = [
						'event_id' => $eventId,
						'device_id' => $deviceId,
						'temperature' => $samples[$key]['value'],
						'type' => $eventType,
						'created_at' => $date
					];

					if ($temperature2 != '') {
						$ins['signal_strength'] = $temperature2;
					}
					DeviceTemperature::insertOrIgnore($ins);
				}
				
			}
			else{
				$ins = [
					'event_id' => $eventId,
					'device_id' => $deviceId,
					'temperature' => $temperature,
					'type' => $eventType,
					'created_at' => $date,
				];
				if ($temperature2 != '') {
					$ins['signal_strength'] = $temperature2;
				}
				DeviceTemperature::insertOrIgnore($ins);
			}
			
			
			
			
			Log::info('1#1');
		}

		if ($temperature2 != '' && $time_stamp != '' && $time_stamp != '0000-00-00 00:00:00') {
			Log::info('2#2');
			$ins = [
				'event_id' => $eventId,
				'device_id' => $deviceId,
				'temperature' => 0.00,
				'type' => $eventType,
				'created_at' => $date,
			];
			

			if ($cloudConnector != '') {
				$ins['cloudConnector'] = $cloudConnector;
				$dv = Device::where('device_id', $cloudConnector)->select('temperature')->first();
				$updateAr = ['is_active' => 1, 'temeprature_last_updated' => $date,'signal_strength'=>$temperature2];
				if (isset($dv->temperature) && strtolower($dv->temperature) == 'offline') {
					$updateAr['temperature'] = 'CELLULAR';
				}
				Device::where('device_id', $cloudConnector)->update($updateAr);
			}
			
			// Log::info($ins);

			if ($temperature2 != '') {
				if ($device) {
					$connectors = DB::select("SELECT DISTINCT cloudConnector FROM device_temperature WHERE device_id = ?", [$device->device_id]);
				
					$maxSignalStrength = [];
					foreach ($connectors as $connector) {
						if (!empty($connector->cloudConnector)) {
							$cloudConnector = $connector->cloudConnector;
							// Log::info('cloud connectors :'.$cloudConnector);
							$con = Device::select('signal_strength')->where('device_id', $cloudConnector)->first();
							if ($con) {
								$currentSignalStrength = $con->signal_strength;
								$maxSignalStrength[] = $currentSignalStrength;
							}
						}
					}
					$maxValue ='';
					// Log::info('cloud signal strength: ');
					// Log::info($maxSignalStrength);
					if (!empty($maxSignalStrength)) {
						$maxValue = max($maxSignalStrength);
						// Log::info('Maximum signal strength: '.$maxValue);
					} else {
						Log::info('No signal strengths found.');
						$maxValue = $temperature2;
					}
				   $ins['signal_strength'] = $maxValue;
					$device->signal_strength = $maxValue;
					$device->save();
				}
				
			}
			DeviceTemperature::insertOrIgnore($ins);
		}

		// }
		// Log::info($request);
		// Log::info($deviceType);
		// Log::info('Device Temperature Data');
		return ['status' => true];
	}

	public function sendResolvedNotification($device){
		// dd($device);
		if ($device) {
			$device_id = isset($device->id) ? $device->id : 0;
			$device_name = isset($device->name) ? $device->name : '';
			$res = DB::select('SELECT
			ND.last_deviate_time,
			ND.id,
			ND.notification_id,
			ND.reminder_sent,
			ND.device_id,
			N.alert_type,
			N.isResolved ,
			N.temp_range,
			N.upper_celcius,
			N.reminder_days,
			N.company_id,
			N.lower_celcius,
			ND.created_at,
				ND.resolve_sent,
			ND.already_sent
		FROM
			`notification_devices` ND
		LEFT JOIN
			notifications N
		ON
			N.id = ND.notification_id
		WHERE
		  N.isResolved = 1 AND N.alert_type ="Device Monitoring (Beta)" AND  ND.resolve_sent = 0 AND  ND.already_sent = 1 AND N.isActive = 1 AND ND.device_id =? ', [$device_id]); //and N.alert_type="Temperature"
			Log::info('sendResolvedNotification');
			Log::info($res);
		if(count($res)>0){
			$time = Carbon::now();
			$equipment = Device::where('sensor_id',$device->device_id)->first();
			$eq_name = isset($equipment->name)?$equipment->name:'';
			if(isset($device_name) && $device_name!=''){
				$device_idOrName = $device_name ;
			}else{
				$device_idOrName = $device->device_id;
			}
			$company = Company::where('company_id',$device->company_id)->first();
			if(isset($company) && $company->name!=''){
				$company_idOrName = $company->name;
			}else{
				$company_idOrName = $device->company_id;
			}
			$subject ="$company->name / Device is online";
			if (count($res) > 0) {
				foreach ($res as $ro) {
					$device_notification_id = isset($ro->id) ? $ro->id : 0;
					$notification_id = isset($ro->notification_id) ? $ro->notification_id : 0;
					$notification_data = Notification::where('id', $notification_id)->first();
					$notifications = NotificationEmail::where('notification_id', $notification_id)->get();
							foreach ($notifications as  $notification) {
								if($device->event_type =="ccon"){
									$data_ar = [
										// 'Connected Sensor' => $device_idOrName,
										'body'=>"<strong>Project: </strong> $company_idOrName <br>
												<strong>Connector: </strong> $device_idOrName  was offline, but now online<br>
												<strong>Online At: </strong> $time <br>"
									];
									$body = "<strong>Project: </strong> $company_idOrName <br>
											<strong>Connector: </strong> $device_idOrName was offline, but now online <br>
											<strong>Online At: </strong> $time";
								}else{

									$data_ar = [
										// 'Connected Sensor' => $device_idOrName,
										'body'=>"<strong>Project: </strong> $company_idOrName <br>
												<strong>Equipment: </strong> $eq_name was offline, but now online<br>
												<strong>Connected Sensor: </strong> $device_idOrName <br>
												<strong>Online At: </strong> $time <br>"
									];
									$body = "<strong>Project: </strong> $company_idOrName <br>
											<strong>Equipment: </strong> $eq_name was offline, but now online <br>
											<strong>Connected Sensor: </strong> $device_idOrName
											<strong>Online At: </strong> $time";
								}


									$emailString[] = isset($notification->email) ? $notification->email : '';
									$notification_type = isset($notification->notification_type) ? $notification->notification_type : '';
									if ($notification_type == 0) {
										$this->resolvedEmail($emailString, $data_ar, $subject,$device_notification_id);
										AlertHistory::create([
											'name' => $notification_data->name,
											'email' => $notification->email,
											'company_id' => $notification_data->company_id,
											'device_id' => $device->device_id,
											'body' => $body
										  ]);
									}
							}

					}
				}

			}
		}
	}

	public function resolvedEmail($emailString, $data_ar = [], $subject,$device_notification_id)
	{
		Config::set('mail.from', config('mail.alert_email','alerts@recasoft.no'));
		Config::set('mail.username', config('mail.alert_email','alerts@recasoft.no'));
		foreach ($emailString as $es) {
				$emails = explode(',', $es);
				$ND = NotificationDevice::where('id',$device_notification_id)->first();
				Log::info($ND);
			if($ND->resolve_sent == 0){
				NotificationDevice::whereId($device_notification_id)->update(['resolve_sent' => 1]);
			foreach ($emails as $email) {
				$to_email = trim($email);
				Log::info($to_email);
				try {
					Log::info('Try Email');
					Mail::send('emails.resolve_email', $data_ar, function ($message) use ($to_email, $subject) {
						$message->to($to_email)
							->subject($subject);
						$message->from('alerts@recasoft.no', 'Recasoft Technologies');
						$message->replyTo('alerts@recasoft.no', 'Recasoft Technologies');
					});
				} catch (\Exception $e) {
					Log::info('Catch Email error');
					Log::info($e);
					// echo 'error';
				}
			}
		}
		}
	}


	public function get_equipments(Request $request)
	{
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: *');
		header("Access-Control-Allow-Headers: *");
		// Log::info('getManagerDashboard');
		// ->selectRaw('count("CD"."id") as company_id')                    
		// ->groupBy('CD.B')  
		$currentCompany = isset($request->currentCompany) ? $request->currentCompany : '';
		$access_token = isset($request->access_token) ? $request->access_token : '';
		$from = isset($request->from) ? $request->from : '';
		$to = isset($request->to) ? $request->to : '';
		if ($access_token == '') {

			return [];
		}
		$payload =  tokenDecode($access_token);

		$companies = [];
		if (isset($payload->companies) && is_array($payload->companies)) {
			foreach ($payload->companies as $comp) {
				if (isset($comp->role, $comp->company->isActive, $comp->company->id) && $comp->company->isActive == true && $comp->role == 'OWNER') {
					$companies[] = $comp->company->id;
				} elseif (isset($comp->role, $comp->isActive, $comp->company->id) && $comp->isActive == true && $comp->role == 'OWNER') {
					$companies[] = $comp->company->id;
				}
			}
		}

		if ($currentCompany != '') {
			$companies = [$currentCompany];
		}
		if (count($companies) == 0) {
			return [];
		}
		if ($from == '') {
			$from = date('Y-m', time()) . '-01';
		}
		if ($to == '') {
			$to = date('Y-m-d', time());
		}
		$companies_string = implode('_', $companies);
		$cacheKey = 'all_' . $companies_string . '_' . $from . '_' . $to;
		if ($value = \Cache::get($cacheKey)) {
			return response()->json($value);
		}
		/*$termTracker = \Cache::get('TermTracker_'.$link_id, function () use($link_id) {
                $termTracker = \App\Termtracker::where('from_id',$link_id)->orderBy('created_at', 'desc')->first();
                \Cache::put('TermTracker_'.$link_id, $termTracker, 300);
                return $termTracker;
            });*/
		DB::beginTransaction();
		$totalActivities =  DB::connection('pgsql')->table('helse-api-master$prod.Activity as A')
			->join('helse-api-master$prod._ActivityToEventDefinition as AD', 'AD.A', '=', 'A.id')
			->join('helse-api-master$prod._CompanyToEventDefinition as CD', 'CD.B', '=', 'AD.B')
			->join('helse-api-master$prod.EventDefinition as D', 'D.id', '=', 'CD.B')
			->whereIn('CD.A', $companies)
			->where('A.time', '>', $from . ' 00:00:00')
			->where('A.time', '<=', $to . ' 23:59:59')
			->whereNull('D.deletedAt')
			->count();

		$completedActivities =  DB::connection('pgsql')->table('helse-api-master$prod.Activity as A')
			->join('helse-api-master$prod._ActivityToEventDefinition as AD', 'AD.A', '=', 'A.id')
			->join('helse-api-master$prod._CompanyToEventDefinition as CD', 'CD.B', '=', 'AD.B')
			->join('helse-api-master$prod.EventDefinition as D', 'D.id', '=', 'CD.B')
			->whereIn('CD.A', $companies)
			->where('A.time', '>', $from . ' 00:00:00')
			->where('A.time', '<=', $to . ' 23:59:59')
			->whereNull('D.deletedAt')
			->whereNotNull('A.completionTime')
			->count();

		$deviations =  DB::connection('pgsql')->table('helse-api-master$prod._CompanyToDeviation as CD')
			->join('helse-api-master$prod.Deviation as D', 'D.id', '=', 'CD.B')
			->whereIn('CD.A', $companies)
			->where('D.status', '=', 'PENDING')
			->where('D.createdAt', '>', $from . ' 00:00:00')
			->where('D.createdAt', '<=', $to . ' 23:59:59')
			->count();

		$procedures =  DB::connection('pgsql')->table('helse-api-master$prod._Procedure_company as CD')
			->join('helse-api-master$prod.Procedure as D', 'D.id', '=', 'CD.B')
			->whereIn('CD.A', $companies)
			->where('D.createdAt', '>', $from . ' 00:00:00')
			->where('D.createdAt', '<=', $to . ' 23:59:59')
			->whereNotNull('D.endTemperature')
			->count();

		$deliveries =  DB::connection('pgsql')->table('helse-api-master$prod._Delivery_company as CD')
			->join('helse-api-master$prod.Delivery as D', 'D.id', '=', 'CD.B')
			->whereIn('CD.A', $companies)
			->where('D.createdAt', '>', $from . ' 00:00:00')
			->where('D.createdAt', '<=', $to . ' 23:59:59')
			// ->whereNotNull('D.endTemperature')  
			->count();


		$waste =  DB::connection('pgsql')->table('helse-api-master$prod._Waste_company as CD')
			->join('helse-api-master$prod.Waste as D', 'D.id', '=', 'CD.B')
			->whereIn('CD.A', $companies)
			->where('D.createdAt', '>', $from . ' 00:00:00')
			->where('D.createdAt', '<=', $to . ' 23:59:59')
			// ->whereNotNull('D.endTemperature')  
			->count();

		$controlList =  DB::connection('pgsql')->table('helse-api-master$prod._Checklist_company as CD')
			->join('helse-api-master$prod.Checklist as D', 'D.id', '=', 'CD.A')
			->whereIn('CD.B', $companies)
			->where('D.completedAt', '>', $from . ' 00:00:00')
			->where('D.completedAt', '<=', $to . ' 23:59:59')
			// ->whereNotNull('D.endTemperature')  
			->count();

		$trainingDocs =  DB::connection('pgsql')->table('helse-api-master$prod._CompanyToTrainingDocLog as CD')
			->join('helse-api-master$prod.TrainingDocLog as D', 'D.id', '=', 'CD.B')
			->whereIn('CD.A', $companies)
			->where('D.confirmedAt', '>', $from . ' 00:00:00')
			->where('D.confirmedAt', '<=', $to . ' 23:59:59')
			// ->whereNotNull('D.endTemperature')  
			->count();
		DB::commit();

		$resAr = [
			'totalActivities' => $totalActivities,
			'completedActivities' => $completedActivities,
			'deviations' => $deviations,
			'procedures' => $procedures,
			'deliveries' => $deliveries,
			'waste' => $waste,
			'controlList' => $controlList,
			'trainingDocs' => $trainingDocs,
		];
		\Cache::put($cacheKey, $resAr, 600);



		return response()->json($resAr);
	}

	public function moduleLogs(Request $request)
	{
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);

		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: *');
		header("Access-Control-Allow-Headers: *");

		$validModules = ['activities', 'procedures', 'deviations', 'foodWastage', 'deliveries', 'trainingDocuments', 'controlLists'];
		// ->selectRaw('count("CD"."id") as company_id')                    
		// ->groupBy('CD.B')  
		$currentCompany = isset($request->currentCompany) ? $request->currentCompany : '';
		$module = isset($request->module) ? $request->module : '';
		$access_token = isset($request->access_token) ? $request->access_token : '';
		$from = isset($request->from) ? $request->from : '';
		$to = isset($request->to) ? $request->to : '';

		if (!in_array($module, $validModules)) {
			return [];
		}
		if ($access_token == '') {

			return [];
		}
		$payload =  tokenDecode($access_token);


		$companies = [];
		if (isset($payload->companies) && is_array($payload->companies)) {
			foreach ($payload->companies as $comp) {
				if (isset($comp->role, $comp->company->isActive, $comp->company->id) && $comp->company->isActive == true && $comp->role == 'OWNER') {
					$companies[] = $comp->company->id;
				} elseif (isset($comp->role, $comp->isActive, $comp->company->id) && $comp->isActive == true && $comp->role == 'OWNER') {
					$companies[] = $comp->company->id;
				}
			}
		}

		if ($currentCompany != '') {
			$companies = [$currentCompany];
		}
		if (count($companies) == 0) {
			return [];
		}
		if ($from == '') {
			$from = date('Y-m', time()) . '-01';
		}
		if ($to == '') {
			$to = date('Y-m-d', time());
		}
		$companies_string = implode('_', $companies);
		$cacheKey = 'module_' . '_' . $module . '_' . $companies_string . '_' . $from . '_' . $to;
		if ($value = \Cache::get($cacheKey)) {
			return response()->json($value);
		}
		if ($module == 'activities') {
			$totalActivities =  DB::connection('pgsql')->table('helse-api-master$prod.Activity as A')
				->join('helse-api-master$prod._ActivityToEventDefinition as AD', 'AD.A', '=', 'A.id')
				->join('helse-api-master$prod._CompanyToEventDefinition as CD', 'CD.B', '=', 'AD.B')
				->join('helse-api-master$prod.EventDefinition as D', 'D.id', '=', 'CD.B')
				// ->join('helse-api-master$prod._AreaToEventDefinition as AED', 'AED.B', '=', 'D.id')
				->join('helse-api-master$prod.Company as COM', 'COM.id', '=', 'CD.A')
				->whereIn('CD.A', $companies)
				->where('A.time', '>', $from . ' 00:00:00')
				->where('A.time', '<=', $to . ' 23:59:59')
				->whereNull('D.deletedAt')
				// ->where('D.isActive','t') 
				->selectRaw('count("COM"."id") as total,"COM"."name","COM"."id"')
				->groupBy('COM.id')
				->orderBy('COM.name')
				->get();

			$completedActivities =  DB::connection('pgsql')->table('helse-api-master$prod.Activity as A')
				->join('helse-api-master$prod._ActivityToEventDefinition as AD', 'AD.A', '=', 'A.id')
				->join('helse-api-master$prod._CompanyToEventDefinition as CD', 'CD.B', '=', 'AD.B')
				->join('helse-api-master$prod.EventDefinition as D', 'D.id', '=', 'CD.B')
				->join('helse-api-master$prod.Company as COM', 'COM.id', '=', 'CD.A')
				->whereIn('CD.A', $companies)
				->where('A.time', '>', $from . ' 00:00:00')
				->where('A.time', '<=', $to . ' 23:59:59')
				->whereNull('D.deletedAt')
				->whereNotNull('A.completionTime')
				->selectRaw('count("COM"."id") as total,"COM"."name","COM"."id"')
				->groupBy('COM.id')
				->orderBy('COM.name')
				->get();
			$compData = [];
			foreach ($completedActivities as $completedActivity) {
				if (isset($completedActivity->id, $completedActivity->total)) {
					$compData[$completedActivity->id] = $completedActivity->total;
				}
			}
			$data = [];
			foreach ($totalActivities as $totalActivity) {
				$ID = isset($totalActivity->id) ? $totalActivity->id : '';
				$name = isset($totalActivity->name) ? $totalActivity->name : '';
				$total = isset($totalActivity->total) ? $totalActivity->total : 0;
				$completed = isset($compData[$ID]) ? $compData[$ID] : 0;
				if ($ID != '') {
					$data[] = [
						'id' => $ID,
						'act' => 1,
						'name' => $name,
						'total' => $total,
						'completed' => $completed,
					];
				}
			}
			$resAr = [
				'data' => $data,
			];
			\Cache::put($cacheKey, $resAr, 600);
			return response()->json($resAr);
		} elseif ($module == 'deviations') {
			$data =  DB::connection('pgsql')->table('helse-api-master$prod._CompanyToDeviation as CD')
				->join('helse-api-master$prod.Deviation as D', 'D.id', '=', 'CD.B')
				->join('helse-api-master$prod.Company as COM', 'COM.id', '=', 'CD.A')
				->whereIn('CD.A', $companies)
				->where('D.status', '=', 'PENDING')
				->where('D.createdAt', '>', $from . ' 00:00:00')
				->where('D.createdAt', '<=', $to . ' 23:59:59')
				->selectRaw('count("COM"."id") as total,"COM"."name","COM"."id"')
				->groupBy('COM.id')
				->orderBy('COM.name')
				->get();
		} elseif ($module == 'procedures') {
			$data =  DB::connection('pgsql')->table('helse-api-master$prod._Procedure_company as CD')
				->join('helse-api-master$prod.Procedure as D', 'D.id', '=', 'CD.B')
				->join('helse-api-master$prod.Company as COM', 'COM.id', '=', 'CD.A')
				->whereIn('CD.A', $companies)
				->where('D.createdAt', '>', $from . ' 00:00:00')
				->where('D.createdAt', '<=', $to . ' 23:59:59')
				->whereNotNull('D.endTemperature')
				->selectRaw('count("COM"."id") as total,"COM"."name","COM"."id"')
				->groupBy('COM.id')
				->orderBy('COM.name')
				->get();
		} elseif ($module == 'deliveries') {
			$data =  DB::connection('pgsql')->table('helse-api-master$prod._Delivery_company as CD')
				->join('helse-api-master$prod.Delivery as D', 'D.id', '=', 'CD.B')
				->join('helse-api-master$prod.Company as COM', 'COM.id', '=', 'CD.A')
				->whereIn('CD.A', $companies)
				->where('D.createdAt', '>', $from . ' 00:00:00')
				->where('D.createdAt', '<=', $to . ' 23:59:59')
				->selectRaw('count("COM"."id") as total,"COM"."name","COM"."id"')
				->groupBy('COM.id')
				->orderBy('COM.name')
				// ->whereNotNull('D.endTemperature')  
				->get();
		} elseif ($module == 'foodWastage') {
			$data =  DB::connection('pgsql')->table('helse-api-master$prod._Waste_company as CD')
				->join('helse-api-master$prod.Waste as D', 'D.id', '=', 'CD.B')
				->join('helse-api-master$prod.Company as COM', 'COM.id', '=', 'CD.A')
				->whereIn('CD.A', $companies)
				->where('D.createdAt', '>', $from . ' 00:00:00')
				->where('D.createdAt', '<=', $to . ' 23:59:59')
				->selectRaw('count("COM"."id") as total,"COM"."name","COM"."id"')
				->groupBy('COM.id')
				->orderBy('COM.name')
				// ->whereNotNull('D.endTemperature')  
				->get();
		} elseif ($module == 'controlLists') {
			$data =  DB::connection('pgsql')->table('helse-api-master$prod._Checklist_company as CD')
				->join('helse-api-master$prod.Checklist as D', 'D.id', '=', 'CD.A')
				->join('helse-api-master$prod.Company as COM', 'COM.id', '=', 'CD.B')
				->whereIn('CD.B', $companies)
				->where('D.completedAt', '>', $from . ' 00:00:00')
				->where('D.completedAt', '<=', $to . ' 23:59:59')
				->selectRaw('count("COM"."id") as total,"COM"."name","COM"."id"')
				->groupBy('COM.id')
				->orderBy('COM.name')
				// ->whereNotNull('D.endTemperature')  
				->get();
		} elseif ($module == 'trainingDocuments') {
			$data =  DB::connection('pgsql')->table('helse-api-master$prod._CompanyToTrainingDocLog as CD')
				->join('helse-api-master$prod.TrainingDocLog as D', 'D.id', '=', 'CD.B')
				->join('helse-api-master$prod.Company as COM', 'COM.id', '=', 'CD.A')
				->whereIn('CD.A', $companies)
				->where('D.confirmedAt', '>', $from . ' 00:00:00')
				->where('D.confirmedAt', '<=', $to . ' 23:59:59')
				->selectRaw('count("COM"."id") as total,"COM"."name","COM"."id"')
				->groupBy('COM.id')
				->orderBy('COM.name')
				// ->whereNotNull('D.endTemperature')  
				->get();
		}
		$resAr = [
			'data' => $data,
		];
		\Cache::put($cacheKey, $resAr, 300);
		return response()->json($resAr);



		return response()->json([
			'totalActivities' => $totalActivities,
			'completedActivities' => $completedActivities,
			'deviations' => $deviations,
			'procedures' => $procedures,
			'deliveries' => $deliveries,
			'waste' => $waste,
			'controlList' => $controlList,
			'trainingDocs' => $trainingDocs,
		]);
	}
	public function transferDevice($token, $to_project_id, $from_project_id, $device_id)
	{
		$curl = curl_init();
		$arr = [
			'devices' => [
				'projects/' . $from_project_id . '/devices/' . $device_id
			]
		];
		$url = "https://api.disruptive-technologies.com/v2/projects/" . $to_project_id . "/devices:transfer";
		// $url = "https://api.disruptive-technologies.com/v2/projects/". $from_project_id . "/devices/". $device_id;
		echo $url . '<br>';
		// print_r($arr);
		echo $encoded_post  = json_encode($arr);
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $encoded_post,
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer " . $token,
				"Content-Type: application/json"
			),
		));

		$response = curl_exec($curl);
		if (curl_exec($curl) === false) {
			echo 'Curl error: ' . curl_error($curl);
			curl_close($curl);
		} else {
			$result = json_decode($response, true);
			return $result;
			curl_close($curl);
		}

		die();
	}
	public function getTok(Request $Request)
	{
		$restaurant = Company::whereId(2)->first();

		/*$secret_key    = $restaurant->service_account_id;	
		$service_account_email = $restaurant->service_account_email;
		echo $service_account_email.'<br>';
		       $service_account_id     = $restaurant->key_id;
		$token         		   = $this->get_jwt_token($service_account_id,$service_account_email,$secret_key);*/

		$service_account_id = 'cabk40aa385g00amb1k0';
		$service_account_email = 'buhqt2r24te000b250gg@blegsq6ec0m00097e3sg.serviceaccount.d21s.com';
		$secret_key = '701638510b26437d9fc47d7b787aed9a';
		$token = $this->get_jwt_token($service_account_id, $service_account_email, $secret_key);

		echo $token         		   = $token['access_token'];
		$source_project_id = 'cabj8g7jhoqahbm4u2ug';
		$to_project_id = 'buh71lgoonrl27r1frqg';
		$device_id = 'bjmfv0m7kro000cp4bd0';
		$res = $this->transferDevice($token, $to_project_id, $source_project_id, $device_id);
		// $res              = $this->get_projects_list($token);
		echo '<pre>';
		print_r($res);
		echo '</pre>';
		die();
	}
	public function get_data(Request $Requ)
	{
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 0);
		date_default_timezone_set('Europe/Oslo');
		$data = [];
		// $restaurants = DB::table('companies')->select('*')->where('id',4)->get();
		$restaurants = DB::table('companies')->select('*');
		if (isset($Requ->single) && $Requ->single == 1) {
			$restaurants->where('id', 39);
		}
		$restaurants = $restaurants->orderBy('id', 'DESC')->get();

		$end_time    = date('Y-m-d\TH:i:s.000') . 'Z';
		$start_time  = date('Y-m-d\TH:i:s.000', strtotime("-1 months", strtotime("NOW"))) . 'Z';

		foreach ($restaurants as $key => $restaurant) {
			$service_account_id    = $restaurant->service_account_id;
			$service_account_email = $restaurant->service_account_email;
			echo $service_account_email . '<br>';
			$secret_key            = $restaurant->secret_key;
			$token         		   = $this->get_jwt_token($service_account_id, $service_account_email, $secret_key);
			$token         		   = $token['access_token'];
			$projects              = $this->get_projects_list($token);
			// Log::info($projects);
			$project_name          = isset($projects['projects'][0]['name']) ? $projects['projects'][0]['name'] : '';
			echo 'ProjectName=' . $project_name . '<br>';
			// Log::info('projectName');
			// Log::info($project_name);

			if (isset($project_name) && $project_name != '') {
				$arr               = explode('/', $project_name);
				$project_id        = isset($arr[1]) ? $arr[1] : '';
				if ($project_id != '') {
					$devices    = $this->get_devices_list($token, $project_id);
					if ($project_name == 'projects/c329u2p5683qbsdjpslg') {
						// Log::info('devices');
						// Log::info($devices);
					}

					$devices    = isset($devices['devices']) ? $devices['devices'] : [];
					$company    = Company::where('id', $restaurant->id)->first();

					if (!empty($company)) {
						$company->project_id =  $project_id;
						$company->save();
					}
					echo 'Devices=' . count($devices) . '<br>';
					// Log::info('Devices=' . count($devices));
					$time_stamp = Carbon::now();
					if (isset($devices) && count($devices) > 0) {
						foreach ($devices as $key => $device) {
							//Log::info($device);
							$device_name     = isset($device['labels']['name']) ? $device['labels']['name'] : '';
							$signal_strength = isset($device['reported']['networkStatus']['signalStrength']) ? $device['reported']['networkStatus']['signalStrength'] : '';
							$temperature     = isset($device['reported']['temperature']['value']) ? $device['reported']['temperature']['value'] : 0.00;
							$battery_level   = isset($device['reported']['batteryStatus']['percentage']) ? $device['reported']['batteryStatus']['percentage'] : 0;
							$description     = isset($device['labels']['description']) ? $device['labels']['description'] : '';
							$device_id_name  = isset($device['name']) ? $device['name'] : '';

							$arr1            = explode('/', $device_id_name);
							$device_id       = isset($arr1[3]) ? $arr1[3] : '';

							// $device_status   = isset($device['reported']['connectionStatus']['connection'])?$device['reported']['connectionStatus']['connection']:'';	
							$device_status = isset($device['reported']['networkStatus']['rssi']) ? $device['reported']['networkStatus']['rssi'] : 0;
							if ($device_status == 0) {
								$device_status       = 0;
								// $device_updated_time = isset($device['reported']['connectionStatus']['updateTime'])?$device['reported']['connectionStatus']['updateTime']:'';
								$device_updated_time =  isset($device['reported']['networkStatus']['updateTime']) ? $device['reported']['networkStatus']['updateTime'] : '';
								$device_updated_time = date('Y-m-d H:i:s', strtotime($device_updated_time));
							} else {
								$device_status       = 1;
								$device_updated_time =  isset($device['reported']['networkStatus']['updateTime']) ? $device['reported']['networkStatus']['updateTime'] : '';
								$device_updated_time = date('Y-m-d H:i:s', strtotime($device_updated_time));
							}
							echo $device_id . '<br>';
							$get_device_available_status = Device::where('device_id', $device_id)->first();
							$device_temperature          = DeviceTemperature::select('event_id')->where('device_id', $device_id)->OrderBy('api_updated_time', 'DESC')->limit(1)->get();
							$last_saved_event_id         = isset($device_temperature[0]['event_id']) ? $device_temperature[0]['event_id'] : '';

							if (empty($get_device_available_status)) {
								$insert_data_devices[] = ['device_id' => $device_id, 'company_id' => $restaurant->id, 'name' => $device_name, 'description' => $description, 'temperature' => $temperature, 'signal_strength' => (int)$signal_strength, 'battery_level' => $battery_level, 'device_status' => $device_status, 'device_updated_time' => $device_updated_time, 'created_at' => $time_stamp];
							} else {
								$get_device_available_status->name     	  = $device_name;
								$get_device_available_status->description     	  = $description;
								$get_device_available_status->temperature     	  = $temperature;
								$get_device_available_status->signal_strength     = (int)$signal_strength;
								$get_device_available_status->device_updated_time = $device_updated_time;
								$get_device_available_status->updated_at      	  = $time_stamp;
								$get_device_available_status->device_status      	  = $device_status;
								$get_device_available_status->save();
							}

							if (isset($device['labels']) && count($device['labels'])) {
								$devSettings = DeviceSetting::where('device_id', $device_id)->get();
								$settingsAr = [];
								foreach ($devSettings as $devSetting) {
									$settingsAr[$devSetting->setting_key] = $devSetting->setting_value;
								}
								foreach ($device['labels'] as $setting_key => $setting_value) {
									$settingVal = isset($settingsAr[$setting_key]) ? $settingsAr[$setting_key] : '';
									if (!isset($settingsAr[$setting_key])) {
										$insert_data_devices_setting[] = ['device_id' => $device_id, 'setting_key' => $setting_key, 'setting_value' => $setting_value, 'created_at' => $time_stamp];
									} elseif ($setting_value != $settingVal || $settingVal == '') {
										DB::table('devices_setting')->where(['device_id' => $device_id, 'setting_key' => $setting_key])->update(['setting_value' => $setting_value]);
									}
								}
							}

							if ($device_id_name != '') {
								if ($device_id != '') {
									$nextPageToken   = '';
									$device_history  = $this->get_history($token, $project_id, $device_id, $start_time, $end_time, $nextPageToken);
									$nextPageToken   = isset($device_history['nextPageToken']) ? $device_history['nextPageToken'] : '';
									$events          = isset($device_history['events']) ? $device_history['events'] : [];
									$MaxTempTriggerSettings = DeviceSetting::where('device_id', $device_id)->where('setting_key', 'MaxTempTrigger')->first();


									$MinTempTriggerSettings = DeviceSetting::where('device_id', $device_id)->where('setting_key', 'MinTempTrigger')->first();


									$tempMin = (isset($MinTempTriggerSettings->setting_value)  && $MinTempTriggerSettings->setting_value != '') ? $MinTempTriggerSettings->setting_value : '';
									$tempMax = (isset($MaxTempTriggerSettings->setting_value)  && $MaxTempTriggerSettings->setting_value != '') ? $MaxTempTriggerSettings->setting_value : '';

									$notification_sent = isset($get_device_available_status->notification_sent) ? $get_device_available_status->notification_sent : 0;
									echo $tempMin . '==>' . $tempMax . '<br>';

									$this->history_recursive($token, $project_id, $device_id, $start_time, $end_time, $events, $nextPageToken, $last_saved_event_id, $notification_sent, $tempMin, $tempMax);


									//$this->history_recursive($token, $project_id, $device_id,$start_time,$end_time,$events,$nextPageToken,$last_saved_event_id);																										
								}
							}
						}
						if (isset($insert_data_devices) && count($insert_data_devices) > 0) {
							DB::table('devices')->insert($insert_data_devices);
						}
						if (isset($insert_data_devices_setting) && count($insert_data_devices_setting) > 0) {
							DB::table('devices_setting')->insertOrIgnore($insert_data_devices_setting);
							$insert_data_devices_setting = [];
						}

						$insert_data_devices         = [];
					}
				}
			}
		}
		$this->get_equipments();
		return 'success';
	}










	public function get_jwt_token($service_account_id, $service_account_email, $secret_key)
	{

		$header = json_encode([
			"alg" => "HS256",
			"kid" => $service_account_id,
		]);

		// Create token payload as a JSON string
		$payload = json_encode([
			"iat" => strtotime("now"),
			"exp" => strtotime("+1 hours"),
			"aud" => "https://identity.disruptive-technologies.com/oauth2/token",
			"iss" => $service_account_email,
		]);

		// Encode Header to Base64Url String
		$base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

		// Encode Payload to Base64Url String
		$base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

		// Create Signature Hash
		$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret_key, true);

		// Encode Signature to Base64Url String
		$base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

		// Create JWT
		$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
		// return $jwt;
		return $this->get_access_token($jwt);
	}

	public function get_access_token($jwt)
	{

		$curl = curl_init();

		$arr = [
			'assertion' => $jwt,
			'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
		];
		// print_r($arr);
		$encoded_post  = http_build_query($arr);
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://identity.disruptive-technologies.com/oauth2/token",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $encoded_post,
			CURLOPT_HTTPHEADER => array(
				"Content-Type: application/x-www-form-urlencoded"
			),
		));

		$response = curl_exec($curl);
		if (curl_exec($curl) === false) {
			echo 'Curl error: ' . curl_error($curl);
			curl_close($curl);
		} else {
			$result = json_decode($response, true);
			return $result;
			curl_close($curl);
		}
	}

	public function get_organizations_list()
	{

		$curl 		   = curl_init();
		$token         = $this->get_jwt_token();
		$token         = $token['access_token'];

		curl_setopt_array($curl, array(
			CURLOPT_URL            => "https://api.disruptive-technologies.com/v2/organizations?page_size=1&page_token",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => "GET",
			CURLOPT_HTTPHEADER     => array(
				"Authorization: Bearer " . $token,
				"Accept: application/json"
			),
		));

		$response = curl_exec($curl);

		if (curl_errno($curl)) {
			echo 'Error:' . curl_error($curl);
			curl_close($curl);
		} else {
			$result = json_decode($response, true);
			return $result;
			curl_close($curl);
		}
	}

	public function get_projects_list($token)
	{

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.disruptive-technologies.com/v2/projects",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer " . $token,
				"Accept: application/json"
			),
		));

		$response = curl_exec($curl);


		if (curl_errno($curl)) {
			echo 'Error:' . curl_error($curl);
			curl_close($curl);
		} else {

			$result = json_decode($response, true);
			return $result;
			curl_close($curl);
		}
	}

	public function get_devices_list($token, $project_id)
	{
	}


	public function existing_equipments()
	{
		$equipments = Equipment::select('equipment_id')->get()->pluck('equipment_id')->toArray();
		return $equipments;
	}

	public function add_deviation($title = 'MaxTempTrigger Deviated', $userId = '', $companyId = 'ck126dceb1mpo0723y6em2bk0', $TemP = '')
	{
		date_default_timezone_set('UTC');
		/*$userId='ckcg68j6r4lci0795soiec9xs';
		$companyId='ck126dceb1mpo0723y6em2bk0';*/
		$uniqueId = Str::random(25);
		if ($TemP != '') {
			$title = $title . ' Temp: ' . $TemP;
		}
		$deviation = [
			'id' => $uniqueId,
			'reason' => $title,
			'status' => 'PENDING',
			'comment' => '',
			'actions' => '',
			'picture' => '',
			'pictureName' => '',
			'createdAt' => date('Y-m-d H:i:s', time()),
			'updatedAt' => date('Y-m-d H:i:s', time())
		];
		echo $id = DB::connection('pgsql')->table('helse-api-master$prod.Deviation')->insertGetId($deviation);
		Log::info('added deviation id=' . $id);
		if ($id != '') {
			if ($userId != '') {
				$uniqueId = Str::random(25);
				$deviationCreated = [
					'id' => $uniqueId,
					'A' => $id,
					'B' => $userId,
				];
				DB::connection('pgsql')->table('helse-api-master$prod._Deviation_created_by')->insert($deviationCreated);
				$uniqueId = Str::random(25);
				$deviationCreated = [
					'id' => $uniqueId,
					'A' => $id,
					'B' => $userId,
				];
				DB::connection('pgsql')->table('helse-api-master$prod._Deviation_reported_by')->insert($deviationCreated);
			}

			$uniqueId = Str::random(25);
			$deviationCreated = [
				'id' => $uniqueId,
				'B' => $id,
				'A' => $companyId,

			];
			DB::connection('pgsql')->table('helse-api-master$prod._CompanyToDeviation')->insert($deviationCreated);
		}
		date_default_timezone_set('Europe/Oslo');
	}
	public function get_managers()
	{

		// $CompanyUser         =  DB::connection('pgsql')->table('helse-api-master$prod.CompanyUser as cu')->where('cu.role','MANAGER')->orWhere('cu.role','OWNER')->get();
		// $CompanyUserToUser   =  DB::connection('pgsql')->table('helse-api-master$prod._CompanyUserToUser')->get();
		// $User                =  DB::connection('pgsql')->table('helse-api-master$prod.User')->get();
		// $Company             =  DB::connection('pgsql')->table('helse-api-master$prod.Company')->get();
		// $_CompanyUserToCompany             =  DB::connection('pgsql')->table('helse-api-master$prod._CompanyUserToCompany')->get();
		// A=>Company_id
		// B=>company_user
		// $data['CompanyUser'] = $CompanyUser;
		// $data['CompanyUserToUser'] = $CompanyUserToUser;
		// $data['User'] = $User;
		// $data['Company'] = $Company;
		// $data['_CompanyUserToCompany'] = $_CompanyUserToCompany;
		// return $data;

		$managers =  DB::connection('pgsql')->table('helse-api-master$prod.CompanyUser as cu')
			->join('helse-api-master$prod._CompanyUserToUser as cutu', 'cutu.A', '=', 'cu.id')
			->join('helse-api-master$prod.User as u', 'cutu.B', '=', 'u.id')
			->join('helse-api-master$prod._CompanyUserToCompany as cutc', 'cutc.B', '=', 'cu.id')
			->join('helse-api-master$prod.Company as c', 'c.id', '=', 'cutc.A')
			->select(
				'c.id as company_id',
				'c.name',
				'u.id as user_id',
				'cu.email',
				'cu.isActive',
				'cu.role',
				'cu.role',
				'cu.token',
				'u.firstName',
				'u.lastName',
				'u.phone'
			)
			->where(function ($query) {
				$query->where('cu.role', 'MANAGER')
					->orWhere('cu.role', 'OWNER');
			})
			->where('cu.email', '!=', '')
			// ->groupBy('user_id','cu.email','cu.isActive','cu.role','cu.token')                    
			->get();

		if (isset($managers) && count($managers) > 0) {
			foreach ($managers as $key => $manager) {
				$get_manager_available_status = Manager::where('manager_id', $manager->user_id)->first();
				$company_id = $manager->company_id;
				$company    = Company::where('company_id', $company_id)->first();
				if (empty($get_manager_available_status)) {
					echo 'manager_not found: ' . $manager->user_id;
					echo '<br>';
					echo '<br>';
					if (!empty($company)) {
						echo 'company not found: ' . $company_id;
						echo '<br>';
						echo '<br>';
						$insert_data_managers[] = ['manager_id' => $manager->user_id, 'first_name' => $manager->firstName, 'last_name' => $manager->lastName, 'email' => $manager->email, 'role' => $manager->role, 'companies_ids' => $company_id, 'status_api' => $manager->isActive];
						DB::table('managers')->insert($insert_data_managers);
						$insert_data_managers = [];
					} else {
						echo 'company found ID: ' . $company_id;
						echo '<br>';
						echo '<br>';
						$insert_data_managers[] = ['manager_id' => $manager->user_id, 'first_name' => $manager->firstName, 'last_name' => $manager->lastName, 'email' => $manager->email, 'role' => $manager->role, 'status_api' => $manager->isActive];
						DB::table('managers')->insert($insert_data_managers);
						$insert_data_managers = [];
					}
				} else {
					echo 'Manager  found: ' . $manager->user_id;
					echo '<br>';
					echo '<br>';
					if (!empty($company)) {
						echo 'Company not found else: ' . $company_id;
						echo '<br>';
						echo '<br>';
						$companies_ids    = $get_manager_available_status->companies_ids;
						$companies_ids_array = [];
						if ($companies_ids != '') {
							$companies_ids_array  = explode(',', $companies_ids);
						}
						if (!in_array($company_id, $companies_ids_array)) {
							array_push($companies_ids_array, $company_id);
						}

						$companies_ids_string = implode(',', $companies_ids_array);
						$get_manager_available_status->status_api  	 = $manager->isActive;
						$get_manager_available_status->email         = $manager->email;
						$get_manager_available_status->companies_ids = $companies_ids_string;
						$get_manager_available_status->save();
					}
				}
			}
			// if(isset($insert_data_managers) && count($insert_data_managers)>0){                
			//     DB::table('managers')->insert($insert_data_managers); 
			// }
		}
		echo 'success';
	}
	public function sendEmailOffline($to = '', $resturant_name = '', $device_name = '', $temp = '')
	{
		if ($to == '') {
			return false;
		}
		/*if($to!='abid.mashkraft@gmail.com'){
		 	return false;
		 }*/
		$date = Carbon::now()->format('Y-m-d');
		$time = Carbon::now()->format('H:i');
		$data = [
			'resturant_name' => $resturant_name,
			'device_name' => $device_name,
			'date' => $date,
			'time' => $time,
			'temperature' => $temp,
		];
		try {

			Mail::send('mail.offline', $data, function ($message) use ($data, $to) {
				$message->to($to);
				$message->from('noreply@appngo.com', 'AppnGO');
				$message->replyTo('noreply@appngo.com', 'AppnGO');
				$message->subject('AppnGO Device Offline');
			});
		} catch (Exception $e) {
			echo 'error';
		}
	}
	public function sendEmail($to = '', $resturant_name = '', $device_name = '', $temp = '')
	{
		if ($to == '') {
			return false;
		}
		$date = Carbon::now()->format('Y-m-d');
		$time = Carbon::now()->format('H:i');
		$data = [
			'resturant_name' => $resturant_name,
			'device_name' => $device_name,
			'date' => $date,
			'time' => $time,
			'temperature' => $temp,
		];
		try {
			Mail::send('mail.deviation', $data, function ($message) use ($data, $to) {
				$message->to($to);
				$message->from('noreply@appngo.com', 'AppnGO');
				$message->replyTo('noreply@appngo.com', 'AppnGO');
				$message->subject('AppnGO Device Deviation');
			});
		} catch (Exception $e) {
			echo 'error';
		}
	}
	public function findOffline()
	{
		$devices = Device::with('company')->get();
		echo count($devices) . '=deviceCount<br>';

		$date    = Carbon::now();
		echo $date . '=currentTime<br>';
		$date->modify('-40 minutes');
		echo $formatted_date = $date->format('Y-m-d H:i:s');

		if (isset($devices) && count($devices) > 0) {
			foreach ($devices as $key => $device) {


				$device_updated_time = isset($device->device_updated_time) ? $device->device_updated_time : '';

				$user_ids    = json_decode($device->device_settings, true);
				$manager_ids    = isset($user_ids['user_ids']) ? $user_ids['user_ids'] : [];
				$delay    = isset($user_ids['delay']) ? $user_ids['delay'] : 0;
				echo $delay . '=delay<br>';
				$email_push    = isset($device->email_push) ? $device->email_push : 0;
				$is_notify_push    = isset($device->notify_push) ? $device->notify_push : 0;
				$is_push = 1;
				if ($delay > 0) {
					if ($delay < 15) {
						$delay = 15;
					}

					if ($formatted_date > $device_updated_time) {
						$companyName = isset($device->company->name) ? $device->company->name : '';
						/*if($companyName!='Jamie Oliver Aker Brygge'){
							continue;
						}*/
						echo $companyName . '=companyName<br>';
						echo $device->name . '=deviceName<br>';
						echo $device->device_updated_time . '=device_updated_time<br>';
						echo $formatted_date . '=>>>' . $device_updated_time . '<br>';
						echo 'IN<br>';

						// continue;


						$notification_sent = isset($device->notification_sent) ? $device->notification_sent : 0;
						echo $is_push . '=isPush=isPush<br>';
						if ($is_push == 1 && $notification_sent == 0) {
							$availed = [];
							$push_notifications_log =  DB::table('push_notifications_log')->select('*')->where('device_id', $device->device_id)->orderBy('id', 'desc')->first();


							$delayTimeAr  = $this->convertTime($delay);
							$delayTimeUnit = isset($delayTimeAr[1]) ? $delayTimeAr[1] : '';
							$delayTime = isset($delayTimeAr[0]) ? $delayTimeAr[0] : '';




							$title = $device->name . ': MaxTempTrigger Deviated';
							$title = $device->name;
							$body  = 'Device is offline.'; //.$delayTime.' '.$delayTimeUnit.'!';

							if (isset($manager_ids) && count($manager_ids) > 0 && $is_notify_push == 1) {
								$Company = Company::where('id', $device->company_id)->first();
								$CompName = isset($Company->name) ? $Company->name : '';
								$Company_Id = isset($Company->company_id) ? $Company->company_id : '';
								$title = $CompName . ': ' . $title;
								foreach ($manager_ids as $key3 => $id) {

									echo '<br>';
									echo $id . '<br>';
									if (!isset($push_notifications_log->created_at)) {
										// $manager_id = 'ckbfk8vio5wp40723xuyacsg7';    										
										$check = $this->send_push($title, $body, $id, $availed, $Company_Id);


										if (isset($check) && is_array($check)) {
											// $check = json_decode($result,true);
											if (isset($check['success']) &&  $check['success'] == 1) {
												Device::where('device_id', $device->device_id)->update(['notification_sent' => 1, 'device_status' => 0]);
												$push_log = [];
												$push_log = ['device_id' => $device->device_id, 'created_at' => $date];
												DB::table('push_notifications_log')->insert($push_log);
											}
										}

										echo 'push send if condition';
										echo '<br>';
										echo 'push_result:' . json_encode($check) . '   Manager_id: ' . $id;
										echo '<br>';
									} else {
										$check = $this->send_push($title, $body, $id, $availed, $Company_Id);
										if (isset($check) && is_array($check)) {
											// $check  = json_decode($result,true);
											if (isset($check['success']) && $check['success'] == 1) {
												Device::where('device_id', $device->device_id)->update(['notification_sent' => 1, 'device_status' => 0]);
												$push_log = [];
												$push_log = ['device_id' => $device->device_id, 'created_at' => $date];
												DB::table('push_notifications_log')->insert($push_log);

												echo 'push send else condition';
												echo '<br>';
												echo 'push_result:' . json_encode($check) . '   Manager_id: ' . $id;;
												echo '<br>';
											}
										}
										/*$to_time    = strtotime($date);
										$from_time  = strtotime($push_notifications_log->created_at);
										$difference = round(abs($to_time - $from_time) / 60,2);
										if($difference >= $delay){

													    											    									
										}
										else{
											$is_deviation=0;
											echo 'already sent push: '.$difference.' minutes';
											echo '<br>';
										}	*/
									}
								}
							}

							if ($email_push == 1) {
								echo 'email push code<br>';
								Log::info('Email Push');
								Log::info('Company_ID =' . $device->company_id);
								$Company = Company::where('id', $device->company_id)->first();
								echo '<pre>';
								print_r($Company->toArray());
								echo '</pre>';
								if (isset($Company->company_id)) {

									$device_name = isset($device->name) ? $device->name : '';
									$resturant_name = isset($Company->name) ? $Company->name : '';
									Log::info('resturant_name=' . $resturant_name);
									Log::info('device_name=' . $device_name);
									$device_setting = DeviceSetting::where('device_id', $device->device_id)->where('setting_key', 'Email')->first();
									$emailList = isset($device_setting->setting_value) ? $device_setting->setting_value : '';
									Log::info('emailList=' . $emailList);
									if ($emailList != '') {
										Device::where('device_id', $device->device_id)->update(['notification_sent' => 1, 'device_status' => 0]);
										//Device::where('device_id',$device->device_id)->update(['notification_sent'=>1]);

										$emailAr = explode(' ', $emailList);
										foreach ($emailAr as $EmailAddress) {
											Log::info('Email Push=' . $EmailAddress);
											$TEMP = isset($device->temperature) ? $device->temperature : '';
											$this->sendEmailOffline($EmailAddress, $resturant_name, $device_name, $TEMP);
										}
									}

									// $this->add_deviation($title,$deviation_user,$Company->company_id);	
								}
							}
						}
					}
				}
				if ($formatted_date > $device_updated_time) {
					$companyName = isset($device->company->name) ? $device->company->name : '';
					/*if($companyName!='Jamie Oliver Aker Brygge'){
							continue;
						}*/

					Device::where('device_id', $device->device_id)->update(['device_status' => 0]);
				} else {
					if ($device->device_status == 0) {
						echo $companyName . '=companyNam11<br>';
						echo $device->name . '=deviceNam11<br>';
						echo $device->device_status . '=device_status<br>';
						Device::where('device_id', $device->device_id)->update(['device_status' => 1, 'notification_sent' => 0]);
					}
				}
				$is_push = 0;
				/*echo 'key: '.$key;
    			echo '<br>';*/
			}
		}
	}
	public function deviation_set00()
	{
		// $this->sendEmail();
		return $devices = Device::where('device_status', 1)
			->where('device_id', 'bjel4m7bluqg00dluft0')
			->where(function ($query) {
				$query->where('notify_push', 1)
					->orWhere('email_push', 1)
					->orWhere('add_as_deviation', 1);
			})
			->where('device_settings', '!=', '')->get();
	}
	public function deviation_set0()
	{
		// $this->sendEmail();
		$devices = Device::where('device_status', 1)
			->where('device_id', 'bjel4m7bluqg00dluft0')
			// ->where('notify_push',1)
			->where(function ($query) {
				$query->where('notify_push', 1)
					->orWhere('email_push', 1)
					->orWhere('add_as_deviation', 1);
			})
			->where('device_settings', '!=', '')->get();
		$date    = Carbon::now();
		$date->modify('-40 minutes');
		$formatted_date = $date->format('Y-m-d H:i:s');


		if (isset($devices) && count($devices) > 0) {
			foreach ($devices as $key => $device) {
				echo $device->name . '=deviceName<br>';
				$is_push = 0;
				echo 'key: ' . $key;
				echo '<br>';
				$user_ids    = json_decode($device->device_settings, true);
				$manager_ids    = isset($user_ids['user_ids']) ? $user_ids['user_ids'] : [];
				$delay    = isset($user_ids['delay']) ? $user_ids['delay'] : 0;
				$is_deviation    = isset($user_ids['deviation']) ? $user_ids['deviation'] : 0;
				$deviation_user    = isset($user_ids['deviation_user']) ? $user_ids['deviation_user'] : '';
				$add_as_deviation = $is_deviation    = isset($device->add_as_deviation) ? $device->add_as_deviation : 0;
				$email_push    = isset($device->email_push) ? $device->email_push : 0;
				$is_notify_push    = isset($device->notify_push) ? $device->notify_push : 0;

				if (count($manager_ids) == 0) {
					continue;
				}

				// return $manager_ids;
				$device_setting = DeviceSetting::where('device_id', $device->device_id)->where('setting_key', 'MaxTempTrigger')->first();
				$equipment = Equipment::where('equipment_id', $device->equipment_id)->first();
				/*if(!isset($equipment->id)){
    				continue;
    			}*/
				$MinTempTriggerSettings = DeviceSetting::where('device_id', $device->device_id)->where('setting_key', 'MinTempTrigger')->first();
				/*$tempMin = isset($equipment->tempMin)?$equipment->tempMin:0;
    			$tempMax = isset($equipment->tempMax)?$equipment->tempMax:0;*/
				if (!isset($MinTempTriggerSettings->setting_value) && !isset($device_setting->setting_value)) {
					continue;
				}
				$tempMin = (isset($MinTempTriggerSettings->setting_value) && $MinTempTriggerSettings->setting_value != '') ? $MinTempTriggerSettings->setting_value : '';
				$tempMax = (isset($device_setting->setting_value) && $device_setting->setting_value != '') ? $device_setting->setting_value : '';
				echo $tempMin . '=tempMin<br>';
				echo $tempMax . '=tempMax<br>';

				if (!empty($device_setting)) {
					$MaxTempTrigger = $device_setting->setting_value;
					if ($MaxTempTrigger != '') {
						echo $MaxTempTrigger . '=MaxTempTrigger<br>';
						// return $device_history = DeviceTemperature::select('temperature')->where('device_id', $device->device_id)->where('created_at','>',$formatted_date)->get()->pluck('temperature')->toArray();
						$device_history = DeviceTemperature::where('device_id', $device->device_id)->where('created_at', '>=', $formatted_date)->get()->sortBy('api_updated_time');
						// $value = -14.00;
						// $point = 0.10;
						// foreach ($device_history as $key1 => $value) {

						// 	$value = -14 + $point;
						// 	$point = $point + 0.1;
						// 	$device_history[$key1]['temperature'] =  (float)$value;
						// }

						// $count  = count($device_history)/2;
						// $count  = round($count,0);
						// $device_history[$count]['temperature'] = -16;

						// return $device_history;
						$first_update_time = '';
						$last_update_time = '';
						$is_start = 0;
						$is_push  = 0;
						if (isset($device_history) && count($device_history) > 0) {
							$device_history22 = $device_history->toArray();
							echo '<pre>';
							print_r($device_history22);
							echo '</pre>';

							foreach ($device_history as $key2 => $value) {
								if ($tempMin != '' && $tempMax != '' && ($value->temperature > $tempMax || $value->temperature < $tempMin)) {

									if ($is_start == 0) {
										echo 'is started<br>';
										echo $value->temperature . '=curTemp<br>';
										$is_start = 1;
										$first_update_time = strtotime($value->api_updated_time);
									}
									// echo $value->api_updated_time.'<br>';				
								} elseif ($tempMax != '' && $value->temperature > $tempMax) {

									if ($is_start == 0) {
										echo 'is started<br>';
										echo $value->temperature . '=curTemp<br>';
										$is_start = 1;
										$first_update_time = strtotime($value->api_updated_time);
									}
									// echo $value->api_updated_time.'<br>';				
								} else {
									// echo $value->api_updated_time.'<br>';
									if ($is_start == 1) {
										$last_update_time = strtotime($value->api_updated_time);
										$diff = $last_update_time - $first_update_time;
										$mins = $diff / 60;
										echo '<br>';
										// echo $mins.'=minutes<br>';
										$is_push = 0;
										if ($mins > $delay) {
											$is_push = 1;
											break;
										}

										$is_start = 0;
										$first_update_time = '';
										$last_update_time = '';
									}
									/*$is_push = 0;
    								$t1    = strtotime( $value->api_updated_time);
									$t2    = strtotime( $device_history[count($device_history)-1]['api_updated_time']);
									$diff  = $t2 - $t1;
									$hours = $diff / ( 60 * 60 );
									$hours = round($hours,2);
									echo $hours;
									echo '<br>';
									echo 'api_updated_time: '.$value->api_updated_time;
									echo '<br>';
									echo 'device_history last_item time: '.$device_history[count($device_history)-1]['api_updated_time'];
									echo '<br>';
									if($hours < 2){
										echo 'break, temperature got normal';
										echo '<br>';
										echo 'is_push:'.$is_push;
										echo '<br>';										
										break;
									}*/
								}
							}
							echo $is_start . '=isPush=isPush<br>';
							if ($is_start == 1) {

								$value = $device_history[count($device_history) - 1];


								if (isset($value->api_updated_time)) {
									$last_update_time = strtotime($value->api_updated_time);
									$diff = $last_update_time - $first_update_time;
									$mins = $diff / 60;
									echo '<br>';
									echo $mins . '=minutes<br>';
									$is_push = 0;
									if ($mins > $delay) {
										$is_push = 1;
									}
								}
							}
							$notification_sent = isset($device->notification_sent) ? $device->notification_sent : 0;
							echo $is_push . '=isPush=isPush<br>';
							if ($is_push == 1 && $notification_sent == 0) {
								$availed = [];
								$push_notifications_log =  DB::table('push_notifications_log')->select('*')->where('device_id', $device->device_id)->orderBy('id', 'desc')->first();


								$delayTimeAr  = $this->convertTime($delay);
								$delayTimeUnit = isset($delayTimeAr[1]) ? $delayTimeAr[1] : '';
								$delayTime = isset($delayTimeAr[0]) ? $delayTimeAr[0] : '';




								$title = $device->name . ': MaxTempTrigger Deviated';
								$title = $device->name;
								$body  = 'Temperature deviated for more than ' . $delayTime . ' ' . $delayTimeUnit . '!';

								if (isset($manager_ids) && count($manager_ids) > 0 && $is_notify_push == 1) {
									$Company = Company::where('id', $device->company_id)->first();
									$CompName = isset($Company->name) ? $Company->name : '';
									$Company_Id = isset($Company->company_id) ? $Company->company_id : '';
									$title = $CompName . ': ' . $title;
									foreach ($manager_ids as $key3 => $id) {

										echo '<br>';
										echo $id . '<br>';
										if (!isset($push_notifications_log->created_at)) {
											// $manager_id = 'ckbfk8vio5wp40723xuyacsg7';    										
											$check = $this->send_push($title, $body, $id, $availed, $Company_Id);


											if (is_array($check)) {
												// $check = json_decode($result,true);
												if (isset($check['success']) &&  $check['success'] == 1) {
													Device::where('device_id', $device->device_id)->update(['notification_sent' => 1]);
													$push_log = [];
													$push_log = ['device_id' => $device->device_id, 'created_at' => $date];
													DB::table('push_notifications_log')->insert($push_log);
												}
											}

											echo 'push send if condition';
											echo '<br>';
											echo 'push_result:' . json_encode($check) . '   Manager_id: ' . $id;
											echo '<br>';
										} else {
											$to_time    = strtotime($date);
											$from_time  = strtotime($push_notifications_log->created_at);
											$difference = round(abs($to_time - $from_time) / 60, 2);
											if ($difference >= $delay) {

												$check = $this->send_push($title, $body, $id, $availed, $Company_Id);
												if (is_array($check)) {
													// $check  = json_decode($result,true);
													if (isset($check['success']) && $check['success'] == 1) {
														Device::where('device_id', $device->device_id)->update(['notification_sent' => 1]);
														$push_log = [];
														$push_log = ['device_id' => $device->device_id, 'created_at' => $date];
														DB::table('push_notifications_log')->insert($push_log);

														echo 'push send else condition';
														echo '<br>';
														echo 'push_result:' . json_encode($check) . '   Manager_id: ' . $id;;
														echo '<br>';
													}
												}
											} else {
												$is_deviation = 0;
												echo 'already sent push: ' . $difference . ' minutes';
												echo '<br>';
											}
										}
									}
								}
								if ($add_as_deviation == 1) {
									echo 'add deviation code<br>';
									Log::info('Add Deviation');
									Log::info('Company_ID =' . $device->company_id);
									$Company = Company::where('id', $device->company_id)->first();
									if (isset($Company->company_id)) {
										$TemP = isset($device->temperature) ? $device->temperature : '';
										$this->add_deviation($title, $deviation_user, $Company->company_id, $TemP);
										Device::where('device_id', $device->device_id)->update(['notification_sent' => 1]);
									}
								}
								if ($email_push == 1) {
									echo 'email push code<br>';
									Log::info('Email Push');
									Log::info('Company_ID =' . $device->company_id);
									$Company = Company::where('id', $device->company_id)->first();
									echo '<pre>';
									print_r($Company->toArray());
									echo '</pre>';
									if (isset($Company->company_id)) {
										$device_name = isset($device->name) ? $device->name : '';
										$resturant_name = isset($Company->name) ? $Company->name : '';
										Log::info('resturant_name=' . $resturant_name);
										Log::info('device_name=' . $device_name);
										$device_setting = DeviceSetting::where('device_id', $device->device_id)->where('setting_key', 'Email')->first();
										$emailList = isset($device_setting->setting_value) ? $device_setting->setting_value : '';
										Log::info('emailList=' . $emailList);
										if ($emailList != '') {
											Device::where('device_id', $device->device_id)->update(['notification_sent' => 1]);

											$emailAr = explode(' ', $emailList);
											foreach ($emailAr as $EmailAddress) {
												Log::info('Email Push=' . $EmailAddress);
												$TEMP = isset($device->temperature) ? $device->temperature : '';
												$this->sendEmail($EmailAddress, $resturant_name, $device_name, $TEMP);
											}
										}

										// $this->add_deviation($title,$deviation_user,$Company->company_id);	
									}
								}
							}
						}
					}
				}
			}
			echo 'success';
		}
		$this->findOffline();
		// $this->clear_push_notification_table();
	}
	public function deviation_set()
	{
		return [];
		// $this->sendEmail();
		$devices = Device::where('device_status', 1)
			// ->where('notify_push',1)
			->where(function ($query) {
				$query->where('notify_push', 1)
					->orWhere('email_push', 1)
					->orWhere('add_as_deviation', 1);
			})
			->where('device_settings', '!=', '')->get();
		$date    = Carbon::now();
		$date->modify('-40 minutes');
		$formatted_date = $date->format('Y-m-d H:i:s');


		if (isset($devices) && count($devices) > 0) {
			foreach ($devices as $key => $device) {
				echo $device->name . '=deviceName<br>';
				$is_push = 0;
				echo 'key: ' . $key;
				echo '<br>';
				$user_ids    = json_decode($device->device_settings, true);
				$manager_ids    = isset($user_ids['user_ids']) ? $user_ids['user_ids'] : [];
				$delay    = isset($user_ids['delay']) ? $user_ids['delay'] : 0;
				if ($delay == 0) {
					$delay = 120;
				}
				$is_deviation    = isset($user_ids['deviation']) ? $user_ids['deviation'] : 0;
				$deviation_user    = isset($user_ids['deviation_user']) ? $user_ids['deviation_user'] : '';
				$add_as_deviation = $is_deviation    = isset($device->add_as_deviation) ? $device->add_as_deviation : 0;
				$email_push    = isset($device->email_push) ? $device->email_push : 0;
				$is_notify_push    = isset($device->notify_push) ? $device->notify_push : 0;

				if (count($manager_ids) == 0) {
					continue;
				}

				// return $manager_ids;
				$device_setting = DeviceSetting::where('device_id', $device->device_id)->where('setting_key', 'MaxTempTrigger')->first();
				$equipment = Equipment::where('equipment_id', $device->equipment_id)->first();
				/*if(!isset($equipment->id)){
    				continue;
    			}*/
				$MinTempTriggerSettings = DeviceSetting::where('device_id', $device->device_id)->where('setting_key', 'MinTempTrigger')->first();
				/*$tempMin = isset($equipment->tempMin)?$equipment->tempMin:0;
    			$tempMax = isset($equipment->tempMax)?$equipment->tempMax:0;*/
				if (!isset($MinTempTriggerSettings->setting_value) && !isset($device_setting->setting_value)) {
					continue;
				}
				$tempMin = (isset($MinTempTriggerSettings->setting_value) && $MinTempTriggerSettings->setting_value != '') ? $MinTempTriggerSettings->setting_value : '';
				$tempMax = (isset($device_setting->setting_value) && $device_setting->setting_value != '') ? $device_setting->setting_value : '';
				echo $tempMin . '=tempMin<br>';
				echo $tempMax . '=tempMax<br>';

				if (!empty($device_setting)) {
					$MaxTempTrigger = $device_setting->setting_value;
					if ($MaxTempTrigger != '') {
						echo $MaxTempTrigger . '=MaxTempTrigger<br>';
						// return $device_history = DeviceTemperature::select('temperature')->where('device_id', $device->device_id)->where('created_at','>',$formatted_date)->get()->pluck('temperature')->toArray();
						$device_history = DeviceTemperature::where('device_id', $device->device_id)->where('created_at', '>=', $formatted_date)->get()->sortBy('api_updated_time');
						// $value = -14.00;
						// $point = 0.10;
						// foreach ($device_history as $key1 => $value) {

						// 	$value = -14 + $point;
						// 	$point = $point + 0.1;
						// 	$device_history[$key1]['temperature'] =  (float)$value;
						// }

						// $count  = count($device_history)/2;
						// $count  = round($count,0);
						// $device_history[$count]['temperature'] = -16;

						// return $device_history;
						$first_update_time = '';
						$last_update_time = '';
						$is_start = 0;
						$is_push  = 0;
						if (isset($device_history) && count($device_history) > 0) {
							$device_history22 = $device_history->toArray();
							echo '<pre>';
							print_r($device_history22);
							echo '</pre>';

							foreach ($device_history as $key2 => $value) {
								if ($tempMin != '' && $tempMax != '' && ($value->temperature > $tempMax || $value->temperature < $tempMin)) {

									if ($is_start == 0) {
										echo 'is started<br>';
										echo $value->temperature . '=curTemp<br>';
										$is_start = 1;
										$first_update_time = strtotime($value->api_updated_time);
									}
									// echo $value->api_updated_time.'<br>';				
								} elseif ($tempMax != '' && $value->temperature > $tempMax) {

									if ($is_start == 0) {
										echo 'is started<br>';
										echo $value->temperature . '=curTemp<br>';
										$is_start = 1;
										$first_update_time = strtotime($value->api_updated_time);
									}
									// echo $value->api_updated_time.'<br>';				
								} else {
									// echo $value->api_updated_time.'<br>';
									if ($is_start == 1) {
										$last_update_time = strtotime($value->api_updated_time);
										$diff = $last_update_time - $first_update_time;
										$MinTempTriggerSettings = $diff / 60;
										echo '<br>';
										// echo $mins.'=minutes<br>';
										$is_push = 0;
										if ($mins > $delay) {
											$is_push = 1;
											break;
										}

										$is_start = 0;
										$first_update_time = '';
										$last_update_time = '';
									}
									/*$is_push = 0;
    								$t1    = strtotime( $value->api_updated_time);
									$t2    = strtotime( $device_history[count($device_history)-1]['api_updated_time']);
									$diff  = $t2 - $t1;
									$hours = $diff / ( 60 * 60 );
									$hours = round($hours,2);
									echo $hours;
									echo '<br>';
									echo 'api_updated_time: '.$value->api_updated_time;
									echo '<br>';
									echo 'device_history last_item time: '.$device_history[count($device_history)-1]['api_updated_time'];
									echo '<br>';
									if($hours < 2){
										echo 'break, temperature got normal';
										echo '<br>';
										echo 'is_push:'.$is_push;
										echo '<br>';										
										break;
									}*/
								}
							}
							echo $is_start . '=isPush=isPush<br>';
							if ($is_start == 1) {

								$value = $device_history[count($device_history) - 1];


								if (isset($value->api_updated_time)) {
									$last_update_time = strtotime($value->api_updated_time);
									$diff = $last_update_time - $first_update_time;
									$mins = $diff / 60;
									echo '<br>';
									// echo $mins.'=minutes<br>';
									$is_push = 0;
									if ($mins > $delay) {
										$is_push = 1;
									}
								}
							}
							$notification_sent = isset($device->notification_sent) ? $device->notification_sent : 0;
							echo $is_push . '=isPush=isPush<br>';
							if ($is_push == 1 && $notification_sent == 0) {
								$availed = [];
								$push_notifications_log =  DB::table('push_notifications_log')->select('*')->where('device_id', $device->device_id)->orderBy('id', 'desc')->first();


								$delayTimeAr  = $this->convertTime($delay);
								$delayTimeUnit = isset($delayTimeAr[1]) ? $delayTimeAr[1] : '';
								$delayTime = isset($delayTimeAr[0]) ? $delayTimeAr[0] : '';




								$title = $device->name . ': MaxTempTrigger Deviated';
								$title = $device->name;
								$body  = 'Temperature deviated for more than ' . $delayTime . ' ' . $delayTimeUnit . '!';

								if (isset($manager_ids) && count($manager_ids) > 0 && $is_notify_push == 1) {
									$Company = Company::where('id', $device->company_id)->first();
									$CompName = isset($Company->name) ? $Company->name : '';
									$Company_Id = isset($Company->company_id) ? $Company->company_id : '';
									$title = $CompName . ': ' . $title;
									foreach ($manager_ids as $key3 => $id) {

										echo '<br>';
										echo $id . '<br>';
										if (!isset($push_notifications_log->created_at)) {
											// $manager_id = 'ckbfk8vio5wp40723xuyacsg7';    										
											$check = $this->send_push($title, $body, $id, $availed, $Company_Id);


											if (is_array($check)) {
												// $check = json_decode($result,true);
												if (isset($check['success']) &&  $check['success'] == 1) {
													Device::where('device_id', $device->device_id)->update(['notification_sent' => 1]);
													$push_log = [];
													$push_log = ['device_id' => $device->device_id, 'created_at' => $date];
													DB::table('push_notifications_log')->insert($push_log);
												}
											}

											echo 'push send if condition';
											echo '<br>';
											echo 'push_result:' . json_encode($check) . '   Manager_id: ' . $id;
											echo '<br>';
										} else {
											$to_time    = strtotime($date);
											$from_time  = strtotime($push_notifications_log->created_at);
											$difference = round(abs($to_time - $from_time) / 60, 2);
											if ($difference >= $delay) {

												$check = $this->send_push($title, $body, $id, $availed, $Company_Id);
												if (is_array($check)) {
													// $check  = json_decode($result,true);
													if (isset($check['success']) && $check['success'] == 1) {
														Device::where('device_id', $device->device_id)->update(['notification_sent' => 1]);
														$push_log = [];
														$push_log = ['device_id' => $device->device_id, 'created_at' => $date];
														DB::table('push_notifications_log')->insert($push_log);

														echo 'push send else condition';
														echo '<br>';
														echo 'push_result:' . json_encode($check) . '   Manager_id: ' . $id;;
														echo '<br>';
													}
												}
											} else {
												$is_deviation = 0;
												echo 'already sent push: ' . $difference . ' minutes';
												echo '<br>';
											}
										}
									}
								}
								if ($add_as_deviation == 1) {
									echo 'add deviation code<br>';
									Log::info('Add Deviation');
									Log::info('Company_ID =' . $device->company_id);
									$Company = Company::where('id', $device->company_id)->first();
									if (isset($Company->company_id)) {
										$TemP = isset($device->temperature) ? $device->temperature : '';
										$this->add_deviation($title, $deviation_user, $Company->company_id, $TemP);
										Device::where('device_id', $device->device_id)->update(['notification_sent' => 1]);
									}
								}
								if ($email_push == 1) {
									echo 'email push code<br>';
									Log::info('Email Push');
									Log::info('Company_ID =' . $device->company_id);
									$Company = Company::where('id', $device->company_id)->first();
									echo '<pre>';
									print_r($Company->toArray());
									echo '</pre>';
									if (isset($Company->company_id)) {
										$device_name = isset($device->name) ? $device->name : '';
										$resturant_name = isset($Company->name) ? $Company->name : '';
										Log::info('resturant_name=' . $resturant_name);
										Log::info('device_name=' . $device_name);
										$device_setting = DeviceSetting::where('device_id', $device->device_id)->where('setting_key', 'Email')->first();
										$emailList = isset($device_setting->setting_value) ? $device_setting->setting_value : '';
										Log::info('emailList=' . $emailList);
										if ($emailList != '') {
											Device::where('device_id', $device->device_id)->update(['notification_sent' => 1]);

											$emailAr = explode(' ', $emailList);
											foreach ($emailAr as $EmailAddress) {
												Log::info('Email Push=' . $EmailAddress);
												$TEMP = isset($device->temperature) ? $device->temperature : '';
												$this->sendEmail($EmailAddress, $resturant_name, $device_name, $TEMP);
											}
										}

										// $this->add_deviation($title,$deviation_user,$Company->company_id);	
									}
								}
							}
						}
					}
				}
			}
			echo 'success';
		}
		$this->findOffline();
		// $this->clear_push_notification_table();
	}
	public function convertTime($minutes = 0)
	{
		$seconds = $minutes * 60;
		$dtF = new \DateTime('@0');
		$dtT = new \DateTime("@$seconds");
		$diff = $dtT->diff($dtF);

		$time = $unit = '';

		if ($diff->h > 0) {
			if ($diff->h > 1) {
				$unit = 'hours';
			} else {
				$unit = 'hour';
			}
			$time = $diff->h;
		} elseif ($diff->i > 0) {
			if ($diff->i > 1) {
				$unit = 'minutes';
			} else {
				$unit = 'minute';
			}
			$time = $diff->i;
		} elseif ($diff->s > 0) {
			if ($diff->s > 1) {
				$unit = 'seconds';
			} else {
				$unit = 'second';
			}
			$time = $diff->s;
		}
		return [
			$time, $unit
		];
	}
	public function testNotification()
	{
		$ar = [];
		$this->send_push('test', 'test body', 'ckbfk8vio5wp40723xuyacsg7', $ar, 'cjpe2kjbm002x084884c75v8h');
	}
	public function send_push($title = '', $body = '', $manager_id, &$availed = [], $compId = '')
	{

		defined('API_ACCESS_KEY')  or define('API_ACCESS_KEY', 'AAAAHWPJpkQ:APA91bHp_8LoMZV1ingdFWY2KnyGebYqU_wmUmsMyWV0gdiV55vT1aH1z0WlR9nN4F0oN2ycAXgPnPmhfofjAt_lCzL6UP-dSGRNvMDYOtL3cwzQDKsrviAJqMPQ3GvAYZcPUrLJSYxh');

		$idx = '';

		// $id = 54;
		$results = Manager::where('manager_id', $manager_id)->get();

		$success = [];
		if (count($results) && $results != '') {
			foreach ($results as $row) {
				$first_name        = isset($row->first_name) ? $row->first_name : '';
				$last_name         = isset($row->last_name) ? $row->last_name : '';
				$user_name         = $first_name . ' ' . $last_name;
				$fcm_token         = isset($row->fcm_token) ? $row->fcm_token : '';

				$notification_body = '';
				if ($fcm_token != '') {
					if (in_array($fcm_token, $availed)) {
						continue;
					}
					$availed[] = $fcm_token;

					$noticefonticon = '';

					$fields = array(
						'to' => $fcm_token,
						'priority' => 'high',
						"data"    => array(
							"name" => "Dear " . $user_name,
							"title" => $title,
							"message" => $body,
							"is_background" => false,
							"timestamp" => date('Y-m-d G:i:s'),
							'screen' => 'devices',
							'company_id' => $compId,
						),
						"notification" =>  [
							"name" => "Dear " . $user_name,
							"title" => $title,
							"body" => $body,
							"click_action" => "FCM_PLUGIN_ACTIVITY", //Must be present for AndroidType a
						],

					);

					$headers = array(
						'Authorization: key=' . API_ACCESS_KEY,
						'Content-Type: application/json'
					);
					#Send Reponse To FireBase Server 
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
					$result = curl_exec($ch);
					curl_close($ch);
					$success = isset($result) ? json_decode($result, true) : [];
				}
			}
		}
		return isset($success) ? $success : [];
	}

	public function clear_push_notification_table()
	{

		$logs = DB::table('push_notifications_log')->select('*')->get();
		$date = Carbon::now();
		$ids = [];
		foreach ($logs as $key => $log) {
			$to_time    = strtotime($date);
			$from_time  = strtotime($log->created_at);
			$difference = round(abs($to_time - $from_time) / 60, 2);

			if ($difference >= 59) {
				array_push($ids, $log->id);
			}
		}

		DB::table('push_notifications_log')->whereIN('id', $ids)->delete();
	}

		public function testFunction(){
			$content=Notification::find('63');
			$subject='test';
			$to_email='abc@mail.com';
			
			 Mail::send('emails.notification-email', ['test'=>$content], function($message) use ($to_email, $subject) {
          $message->to('siddiqueakbar560@gmail.com')
          ->subject('test email notification');
          $message->from('notification@recasoft.com','Recasoft Technologies');
          $message->replyTo('notification@recasoft.com','Recasoft Technologies');
        });
			 dd('sdfkdfkd');

			$curl = curl_init();

		
// 		$params = array('identifier' => 'c148rsd17uj000bpp7vg');
// $query = http_build_query($params);
// $url = "https://api.disruptive-technologies.com/v2/claimInfo?identifier=1";

// curl_setopt($ch, CURLOPT_URL, "www.example.com/index.php?$query");

		$service_account_id = 'cabk40aa385g00amb1k0';
		$service_account_email = 'buhqt2r24te000b250gg@blegsq6ec0m00097e3sg.serviceaccount.d21s.com';
		$secret_key = '701638510b26437d9fc47d7b787aed9a';
		$token = $this->get_jwt_token($service_account_id, $service_account_email, $secret_key);
		// dd($token);
		// $identifier='c148rsd17uj000bpp7v';
		// print_r($arr);
		// echo $encoded_post  = json_encode($arr);
		// 
	
		curl_setopt_array($curl, array(
          CURLOPT_URL            => "https://api.disruptive-technologies.com/v2:claim-info?identifier=abc-71-yiz",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING       => "",
          CURLOPT_MAXREDIRS      => 10,
          CURLOPT_TIMEOUT        => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST  => "GET",
          CURLOPT_HTTPHEADER     => array(
             "Authorization: Bearer ".$token['access_token'],
             "Accept: application/json"
          ),
        ));
		$claimedDevices=0;
		$response = curl_exec($curl);
		$data=json_decode($response,true);
		dd($data);
		foreach($data['kit']['devices'] as $device){
			
			if($device['isClaimed']==true)
	       	  $claimedDevices++;
		}
		dd($claimedDevices);
		if(isset(json_decode($response, true)['device'])){
			dd('hiiiii');
		}else{
			dd('byeee');
		}

		dd(json_decode($response, true));
	}


	public function claimSensor(Request $request){

    	$curl = curl_init();


		$service_account_id = 'cabk40aa385g00amb1k0';
		$service_account_email = 'buhqt2r24te000b250gg@blegsq6ec0m00097e3sg.serviceaccount.d21s.com';
		$secret_key = '701638510b26437d9fc47d7b787aed9a';
		$token = $this->get_jwt_token($service_account_id, $service_account_email, $secret_key);

	
		curl_setopt_array($curl, array(
          CURLOPT_URL            => "https://api.disruptive-technologies.com/v2:claim-info?identifier=".$request->device_id,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING       => "",
          CURLOPT_MAXREDIRS      => 10,
          CURLOPT_TIMEOUT        => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST  => "GET",
          CURLOPT_HTTPHEADER     => array(
             "Authorization: Bearer ".$token['access_token'],
             "Accept: application/json"
          ),
        ));

		$response = curl_exec($curl);
		$claimedDevices=0;
		if(!isset(json_decode($response,true)['error'])){
		  // if(isset(json_decode($response, true)['device'])){
			$data=json_decode($response, true);
			if($data['type']=='KIT'){
	       	  if(isset($data['kit']['devices'])){
			       	  foreach($data['kit']['devices'] as $device){
					
					if($device['isClaimed']==true)
			       	  $claimedDevices++;
				}
	       	  }
			}
			 $html  = view('sensors.single_claim_device',compact('data','claimedDevices'));
	        $html  = $html->render();
	       if($data['type']=='DEVICE'){
	       	return response()->json(['response'=>$data,'claim_html'=>$html,'error'=>false,'isClaimed'=>json_decode($response, true)['device']['isClaimed']]);
	       }else{
	       	 
	    		return response()->json(['response'=>$data,'claim_html'=>$html,'error'=>false,'isClaimed'=>false,'claimedDevices'=>$claimedDevices]);
	    	}
			// }
		}else{
			return response()->json(['error'=>true]);
		} 	
    }



    public function claimSensorPost(Request $request){
    	
   //  	$obj = (object) [
			//     'deviceIds' => ["c148rsd17uj000bpp7vg","bive56n95ss000eukj80"],
			
				
			// ];

    		
    		$obj = (object) [
			    'deviceIds' => $request->ids,
			    'kitIds'=>$request->kitIds
				
			];
			

			// dd(json_encode($obj));


    	$curl = curl_init();


		$service_account_id = 'cabk40aa385g00amb1k0';
		$service_account_email = 'buhqt2r24te000b250gg@blegsq6ec0m00097e3sg.serviceaccount.d21s.com';
		$secret_key = '701638510b26437d9fc47d7b787aed9a';
		$token = $this->get_jwt_token($service_account_id, $service_account_email, $secret_key);
		$company = Company::where('company_id',$request->company_id)->first();
		// dd($company);
		if($company->parent_id==0 && $company!=null){
			$project_id=$request->company_id;
			$to_company = null;
		}else{
			$parent_company = Company::where('id',$company->parent_id)->first();
			$project_id=$parent_company->company_id;
			$to_company = isset($request->company_id)?$request->company_id:'';
		}
		$encoded_post  = json_encode($obj);

		curl_setopt_array($curl, array(
			CURLOPT_URL =>  "https://api.disruptive-technologies.com/v2/projects/".$project_id."/devices:claim?dryRun=false",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $encoded_post,
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer ".$token['access_token'],
				"Content-Type: application/x-www-form-urlencoded"
			),
		));
		$response = curl_exec($curl);
		$data=json_decode($response, true);
		//dd($data);
		$devices =[];
		sleep(10); // Delay execution by 10 seconds
		if(isset($data['claimedDevices']) && count($data['claimedDevices'])>0){
			foreach($data['claimedDevices'] as $device){
				$devices[] =$device['deviceId'];
			}
			
			foreach($devices as $device_id){
				$this->GetSensorData($to_company,$project_id,$device_id);
				//$this->GetSensorData($to_company,$project_id,'c8kaveasffvg00bpmo1g');
		}
		return redirect()->back()->with('title', 'Device claimed')->with('success', 'Devices claimed successfully');
		}else{
			return redirect()->back()->with('title', 'Device not claimed')->with('error', 'Devices might be already claimed');
		}
    }

	public function GetSensorData($to_company,$from_project_id, $device_id)
	{
		$curl = curl_init();
		//dd($token);
		$service_account_id = 'cabk40aa385g00amb1k0';
		$service_account_email = 'buhqt2r24te000b250gg@blegsq6ec0m00097e3sg.serviceaccount.d21s.com';
		$secret_key = '701638510b26437d9fc47d7b787aed9a';
		$token = $this->get_jwt_token($service_account_id, $service_account_email, $secret_key);

		$arr = [
			'devices' => [
				'projects/' . $from_project_id . '/devices/' . $device_id
			]
		];
		$url = "https://api.disruptive-technologies.com/v2/projects/". $from_project_id . "/devices/". $device_id;
		// $url ="https://api.disruptive-technologies.com/v2:claim-info?identifier=".$device_id;
		echo $url . '<br>';
		// print_r($arr);
		echo $encoded_post  = json_encode($arr);
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			//CURLOPT_POSTFIELDS => $encoded_post,
			CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer " . $token['access_token'],
				"Content-Type: application/json"
			),
		));

		$response = curl_exec($curl);
		$result = json_decode($response, true);
		//dd($result);
		 if (isset($result['code']) && $result['code']==404) {
			return redirect()->back()->with('title', 'Not found')->with('error', 'Devices not found in this project with APIs something went wrong.');
		} else {
			$exist_device =Device::find($device_id);
			// dd($result);
			if($exist_device==null || $exist_device==''){
				if(isset($result['type'])){
				if ($result['type']=='temperature') {
				// dd($result['type']);
				$time_stamp = $result['reported']['networkStatus']['updateTime'];
				$date = date('Y-m-d H:i:s', strtotime($time_stamp));
				$name = isset($result['labels']['name'])?$result['labels']['name']:'';
				$description = isset($result['labels']['description'])?$result['labels']['description']:'';
				$parent_compnay =Company::where('company_id',$from_project_id)->first();
				$device = new Device();
				$device->name = $name;
				$device->description = $description;
				$device->company_id = isset($to_company)?$to_company:$from_project_id;
				$device->coming_from_id = isset($parent_compnay->id)?$parent_compnay->id:0;
				$device->device_id = $device_id;
				$device->temeprature_last_updated = $date;
				$device->device_status = 1;
				$device->is_active = 1;
				$device->signal_strength = $result['reported']['networkStatus']['signalStrength'];
				$device->temperature = $result['reported']['temperature']['value'];
				$device->transmissionMode = $result['reported']['networkStatus']['transmissionMode'];
				$device->battery_level = $result['reported']['batteryStatus']['percentage'];
				$device->event_type = 'temperature';
				$device->save();
				$device_idOrName ='';
				if($name ==''){
					$device_idOrName = $device_id;
				}else{
					$device_idOrName = $name;
				}

				$user =auth()->user()->name;
				$action ="Claimed";
				$company = Company::where('company_id',$device->company_id)->first();
				$message = "$user claimed Sensor ($device_idOrName) in $company->name";
				SystemLogs($message,$device->company_id,$action);

			}
			elseif($result['type']=='ccon'){
				$time_stamp = isset($result['reported']['cellularStatus']['updateTime'])?$result['reported']['cellularStatus']['updateTime']:'';
				$name = isset($result['labels']['name'])?$result['labels']['name']:'';
				$description = isset($result['labels']['description'])?$result['labels']['description']:'';
				$offline = $result['reported']['connectionStatus']['connection'];
				$date = date('Y-m-d H:i:s', strtotime($time_stamp));
				$parent_compnay =Company::where('company_id',$from_project_id)->first();
				$device = new Device();
				$device->name = $name;
				$device->description = $description;
				$device->company_id = isset($to_company)?$to_company:$from_project_id;
				$device->coming_from_id = isset($parent_compnay->id)?$parent_compnay->id:0;
				$device->device_id = $device_id;
				$device->temeprature_last_updated = $date;
				$device->device_status = 1;
				$device->signal_strength = isset($result['reported']['cellularStatus']['signalStrength'])?$result['reported']['cellularStatus']['signalStrength']:'';
				$device->event_type = 'ccon';

				if($offline == "OFFLINE"){
					$device->is_active = 0;
				}else{
					$device->is_active = 1;
				}
				$device->save();

				$device_idOrName ='';
				if($name ==''){
					$device_idOrName = $device_id;
				}else{
					$device_idOrName = $name;
				}

				$user =auth()->user()->name;
				$action ="Claimed";
				$company = Company::where('company_id',$device->company_id)->first();
				$message = "$user claimed Connector ($device_idOrName) in $company->name";
				SystemLogs($message,$device->company_id,$action);

			}

		}
		//return redirect()->back()->with('title', 'Devices claimed')->with('success', 'Devices has been successfully claimed.');
	}

			return $result;
			curl_close($curl);
	}

		die();
	}
	
}
