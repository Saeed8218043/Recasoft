<?php


namespace App\Http\Controllers;
// use Config;
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
use Illuminate\Support\Facades\URL;
use App\NotificationEmail;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Mail\Message;
use Log;
use Mail;
use Illuminate\Support\Facades\Config;


class CronController extends Controller
{
  public function test123()
  {

    // event(new TouchEvent(array('event_type'=>'touch','projectId'=>'cc7k8a2cqcr1l9vcitg0','deviceId'=>'bqfacp4c0001392n0sig')));
    // die();
    $phone = '+923069082597';
    $message = 'AOA
new sensor deviation
pleae check';

    $this->sendSms($phone, $message);
  }
  public function sendNotification(Request $request)
  {
  // dd($request->all());
    $res =  DB::select(
      DB::raw("SELECT
      ND.already_sent,
      N.id AS notification_id,
      NOW() AS ct_time, ND.last_deviate_time, DATE_ADD(
          ND.last_deviate_time,
          INTERVAL N.delay_time HOUR
      ) AS notification_time,
      ND.id,ND.device_id,N.alert_type,N.temp_range,N.upper_celcius,N.lower_celcius
  FROM
      `notification_devices` ND
  LEFT JOIN
      notifications N
  ON
      N.id = ND.notification_id
  WHERE :now >= DATE_ADD(ND.last_deviate_time,INTERVAL N.delay_time HOUR) AND
      N.isActive = 1 AND ND.last_deviate_time IS NOT NULL
          AND ND.already_sent = 0"),
      ['now' => Carbon::now()->toDateTimeString()]
    ); 
    // dd($res);
    foreach ($res as $row) {

      if ($row->last_deviate_time != null || $row->last_deviate_time != '') {

        $notifications = NotificationEmail::where('notification_id', $row->notification_id)->get();
        $notification_data = Notification::where('id', $row->notification_id)->first();
        $devID = isset($row->device_id) ? $row->device_id : 0;
        $device = Device::where('id', $devID)->first();
        $company = Company::where('company_id', $device->company_id)->first();
        $project_name = isset($company->name) ? $company->name : '';
        $deviceID = isset($device->device_id)?$device->device_id:'';
        $equipment =Device::where('sensor_id',$deviceID)->first();
        $equipment_id = isset($equipment)?$equipment->device_id:$device->device_id;
          $name = !empty($equipment->name) ? $equipment->name : '';
          if ($equipment ==null || $equipment=='') {
            $name = !empty($device->name)?$device->name:$device->device_id;
          }else{
            $name =$equipment->name;
          }
        if ($device->is_active == 1 && $row->alert_type == 'Temperature') {
          
          foreach ($notifications as $notification) {

            $this->sendTrigger($notification, $devID);

            DB::update('update notification_devices set already_sent = 1 where id = ?', [$row->id]);
            DB::update('update notification_devices set reminder_sent = 0 where id = ?', [$row->id]);
            if (isset($notifications) && isset($notification_data)) {

              $body = $notification->content;
              $body = str_replace('$name', $name, $body);
              $body = str_replace('$description', $equipment->description, $body);
              $body = str_replace('$deviceID', $equipment_id, $body);
              $body = str_replace('$celsius', $device->temperature . '°C', $body);
              $body = str_replace('$connected_sensor', $name, $body);
              $body = str_replace('$project_name', $project_name, $body);
              $url = route("equipment-details", ['company_id' => $equipment->company_id, 'device_id' => $equipment->device_id]);
              $body = str_replace('$url', $url, $body);

              AlertHistory::create([
                'name' => $notification_data->name,
                'email' => $notification->email,
                'company_id' => $notification_data->company_id,
                'device_id' => $equipment_id,
                'body' => $body
              ]);
            }
          }
        } elseif($device->is_active == 0 && $row->alert_type == 'Device Monitoring (Beta)'){

          $notifications = NotificationEmail::where('notification_id', $row->notification_id)->get();
          $notification_data = Notification::where('id', $row->notification_id)->first();
          $devID = isset($row->device_id) ? $row->device_id : 0;
          $device = Device::where('id', $devID)->first();
          $company = Company::where('company_id', $device->company_id)->first();
          $project_name = isset($company->name) ? $company->name : '';
          $equipment =Device::where('sensor_id',$device->device_id)->first();
          $name = !empty($equipment->name) ? $equipment->name : '';
          $equipment_id = isset($equipment->device_id)?$equipment->device_id:$device->device_id;
            foreach ($notifications as $notification) {
  
              $this->sendTrigger($notification, $devID);
  
              DB::update('update notification_devices set already_sent = 1 where id = ?', [$row->id]);
              DB::update('update notification_devices set reminder_sent = 0 where id = ?', [$row->id]);
              if (isset($notifications) && isset($notification_data)) {
  
                $body = $notification->content;
                $body = str_replace('$name', $name, $body);
                if($device->event_type=="ccon"){
                  $body = str_replace('$description', $device->description, $body);
                  $body = str_replace('$deviceID', $deviceID, $body);
                  $body = str_replace('$connected_sensor', 'none', $body);
                }else{
                  $body = str_replace('$description', $equipment->description, $body);
                  $body = str_replace('$deviceID', $equipment_id, $body);
                  $body = str_replace('$connected_sensor', $name, $body);
                }
                $body = str_replace('$celsius', $device->temperature . '°C', $body);
                $body = str_replace('$project_name', $project_name, $body);
                $url = route("equipment-details", ['company_id' => $equipment->company_id, 'device_id' => $equipment->device_id]);
                $body = str_replace('$url', $url, $body);
  
                AlertHistory::create([
                  'name' => $notification_data->name,
                  'email' => $notification->email,
                  'company_id' => $notification_data->company_id,
                  'device_id' => isset($equipment)?$equipment->device_id:$device->device_id,
                  'body' => $body
                ]);
              }
            }
        }
      }
     
    }

    $zero_delay_temp = DB::select("SELECT
			ND.already_sent,
			N.id AS notification_id,
			NOW() AS ct_time, ND.last_deviate_time,
			ND.id,
			ND.device_id,
			N.alert_type,
			N.temp_range,
			N.delay_time,
			N.upper_celcius,
			N.lower_celcius
		FROM
			`notification_devices` ND
		LEFT JOIN
			notifications N
		ON
			N.id = ND.notification_id
		WHERE
			N.isActive = 1 AND N.delay_time=0 AND ND.already_sent = 0", []);

    foreach ($zero_delay_temp as $row) {
      $devID = isset($row->device_id) ? $row->device_id : 0;
      $device = Device::where('id', $devID)->first();

      if ($row->last_deviate_time != null || $row->last_deviate_time != '') {

        $notifications = NotificationEmail::where('notification_id', $row->notification_id)->get();
        $notification_data = Notification::where('id', $row->notification_id)->first();
        $company = Company::where('company_id', $device->company_id)->first();
        $project_name = isset($company->name) ? $company->name : '';
        $deviceID = isset($device->device_id)?$device->device_id:'';
        $equipment =Device::where('sensor_id',$deviceID)->first();
        $name = !empty($equipment->name) ? $equipment->name : '';
        $equipment_id = isset($equipment->device_id)?$equipment->device_id:$device->device_id;
        if ($equipment ==null || $equipment=='') {
          $name = !empty($device->name)?$device->name:$device->device_id;
        }else{
          $name =$equipment->name;
        }

        if ($device->is_active == 1 && $row->alert_type == 'Temperature') {
          foreach ($notifications as $notification) {

            $this->sendTrigger($notification, $devID);

            DB::update('update notification_devices set already_sent = 1 where id = ?', [$row->id]);
            DB::update('update notification_devices set reminder_sent = 0 where id = ?', [$row->id]);
            if (isset($notifications) && isset($notification_data)) {
              $body = $notification->content;
              $body = str_replace('$name', $name, $body);
              $body = str_replace('$description', $equipment->description, $body);
              $body = str_replace('$deviceID', $equipment_id, $body);
              $body = str_replace('$celsius', $device->temperature . '°C', $body);
              $body = str_replace('$connected_sensor', $name, $body);
              $body = str_replace('$project_name', $project_name, $body);

              $url = route("sensor-details", ['company_id' => $device->company_id, 'device_id' => $devID]);
              $body = str_replace('$url', $url, $body);

              AlertHistory::create([
                'name' => $notification_data->name,
                'email' => $notification->email,
                'company_id' => $notification_data->company_id,
                'device_id' => $equipment_id,
                'body' => $body
              ]);
            }
          }
        }
      }
      if ($row->last_deviate_time == null || $row->last_deviate_time == '') {
        if ($device->is_active == 0 && $row->alert_type == 'Device Monitoring (Beta)') {

          $notifications = NotificationEmail::where('notification_id', $row->notification_id)->get();
          $notification_data = Notification::where('id', $row->notification_id)->first();
          $company = Company::where('company_id', $device->company_id)->first();
          $project_name = isset($company->name) ? $company->name : '';
          $deviceID = isset($device->device_id)?$device->device_id:'';
          $equipment =Device::where('sensor_id',$deviceID)->first();
          $name = !empty($equipment->name) ? $equipment->name : '';
          $equipment_id = isset($equipment)?$equipment->device_id:$device->device_id;
          if (($equipment ==null || $equipment=='') && $device->event_type!="ccon") {
            $name = !empty($device->name)?$device->name:$device->device_id;
            $equipment_name = !empty($equipment->name) ? $equipment->name : '';
          }else{
            $equipment_name =' none';
            $name =$device->name;
          }
          foreach ($notifications as $notification) {
            $this->sendTrigger($notification, $devID);

            DB::update('update notification_devices set already_sent = 1 where id = ?', [$row->id]);
            DB::update('update notification_devices set reminder_sent = 0 where id = ?', [$row->id]);
            if (isset($notifications) && isset($notification_data)) {
              $body = $notification->content;
              $body = str_replace('$name', $name, $body);
              if($device->event_type=="ccon"){
                $body = str_replace('$description', $device->description, $body);
                $body = str_replace('$deviceID', $deviceID, $body);
                $body = str_replace('$connected_sensor', 'none', $body);
              }else{
                $body = str_replace('$description', $equipment->description, $body);
                $body = str_replace('$deviceID', $equipment_id, $body);
                $body = str_replace('$connected_sensor', $equipment_name, $body);
              }
              $body = str_replace('$celsius', $device->temperature . '°C', $body);
              $body = str_replace('$project_name', $project_name, $body);

              $url = route("sensor-details", ['company_id' => $device->company_id, 'device_id' => $devID]);
              $body = str_replace('$url', $url, $body);

              AlertHistory::create([
                'name' => $notification_data->name,
                'email' => $notification->email,
                'company_id' => $notification_data->company_id,
                'device_id' => $equipment_id,
                'body' => $body
              ]);
            }
          }
        }
      }
    }


    $offline_alert = DB::select(
      DB::raw("SELECT
        ND.already_sent,
        N.id AS notification_id,
        NOW() AS ct_time, ND.last_deviate_time, DATE_ADD(
            ND.last_deviate_time,
            INTERVAL N.delay_time HOUR
        ) AS notification_time,
        ND.id,
        ND.device_id,
        N.alert_type,
        N.temp_range,
        N.upper_celcius,
        N.lower_celcius
      FROM
        `notification_devices` ND
      LEFT JOIN
        notifications N
      ON
        N.id = ND.notification_id
      WHERE :now >= DATE_ADD(ND.created_at,INTERVAL N.delay_time HOUR) AND
        N.isActive = 1 AND ND.last_deviate_time IS NULL 
        AND N.delay_time=0   AND ND.already_sent = 0 AND N.alert_type ='Device Monitoring (Beta)'"),
      ['now' => Carbon::now()->toDateTimeString()]
    );
    foreach ($offline_alert as $row) {
      $devID = isset($row->device_id) ? $row->device_id : 0;
      $device = Device::where('id', $devID)->first();
      
      if ($row->last_deviate_time == null || $row->last_deviate_time == '') {
        if ($device->is_active == 0 && $row->alert_type == 'Device Monitoring (Beta)') {
          
          $notifications = NotificationEmail::where('notification_id', $row->notification_id)->get();
          $notification_data = Notification::where('id', $row->notification_id)->first();
          $company = Company::where('company_id', $device->company_id)->first();
          $project_name = isset($company->name) ? $company->name : '';
          $deviceID = isset($device->device_id)?$device->device_id:'';
          $equipment =Device::where('sensor_id',$deviceID)->first();
          $name = !empty($equipment->name) ? $equipment->name : '';
          $equipment_id = isset($equipment)?$equipment->device_id:$device->device_id;
          if ($equipment ==null || $equipment=='') {
            $name = !empty($device->name)?$device->name:$device->device_id;
          }else{
            $name =$equipment->name;
          }

          foreach ($notifications as $notification) {
            $this->sendTrigger($notification, $devID);

            DB::update('update notification_devices set already_sent = 1 where id = ?', [$row->id]);
            DB::update('update notification_devices set reminder_sent = 0 where id = ?', [$row->id]);
            if (isset($notifications) && isset($notification_data)) {
              $body = $notification->content;
              $body = str_replace('$name', $name, $body);
              if($device->event_type=="ccon"){
                $body = str_replace('$description', $device->description, $body);
                $body = str_replace('$deviceID', $deviceID, $body);
                $body = str_replace('$connected_sensor', 'none', $body);
              }else{
                $body = str_replace('$description', $equipment->description, $body);
                $body = str_replace('$deviceID', $equipment_id, $body);
                $body = str_replace('$connected_sensor', $equipment_name, $body);
              }
              $body = str_replace('$celsius', $device->temperature . '°C', $body);
              $body = str_replace('$project_name', $project_name, $body);

              $url = route("sensor-details", ['company_id' => $device->company_id, 'device_id' => $devID]);
              $body = str_replace('$url', $url, $body);

              AlertHistory::create([
                'name' => $notification_data->name,
                'email' => $notification->email,
                'company_id' => $notification_data->company_id,
                'device_id' => $equipment_id,
                'body' => $body
              ]);
            }
          }
        }
      }
    }
      $maintenance = DB::select("SELECT id,name,isActive,alert_type,company_id,maintenance_repeat,m_date, DATE_ADD(m_date,INTERVAL maintenance_repeat DAY) AS notification_time
      FROM notifications n
      WHERE m_date IS NOT NULL AND n.alert_type='Maintenance'");
      foreach ($maintenance as $row) {
        
        if ($row->isActive ==1) {
          
          $ND = NotificationDevice::where('notification_id',$row->id)->get();
          $notifications = NotificationEmail::where('notification_id', $row->id)->get();
          foreach($ND as $row2){
          $notification_data = Notification::where('id', $row->id)->first();
          $devID = isset($row2->device_id) ? $row2->device_id : 0;
          $device = Device::where('id', $devID)->first();
          $company = Company::where('company_id', $device->company_id)->first();
          $project_name = isset($company->name) ? $company->name : '';
          $deviceID = isset($device->device_id)?$device->device_id:'';
          $equipment =Device::where('sensor_id',$deviceID)->first();
          $name = !empty($equipment->name) ? $equipment->name : '';
          $equipment_id = isset($equipment)?$equipment->device_id:$device->device_id;
          if ($equipment ==null || $equipment=='') {
            $name = !empty($device->name)?$device->name:$device->device_id;
          }else{
            $name =$equipment->name;
          }
          if ($row->alert_type == 'Maintenance') {
            foreach ($notifications as $notification) {
              // dd(Carbon::now() > Carbon::parse($row->m_date));
              $mDate = Carbon::parse($row->m_date);
              $time = Carbon::now();

              if ($time->greaterThan($mDate)) {
                  // Send the notification
  
              if(Carbon::now() > Carbon::parse($row->m_date)){
                
                if($row->maintenance_repeat ==0 && $row2->maintenance_sent==1){
                  continue;
                }

                $this->sendTrigger($notification, $devID);

                if($row->maintenance_repeat !=0 ){
                  DB::update("update notifications set m_date = '$row->notification_time' where id = ?", [$row->id]);
                }
                //
                if($row->maintenance_repeat ==0 && $row2->maintenance_sent==0){
                  DB::update("update notification_devices set maintenance_sent = 1 where notification_id = ?", [$row->id]);
                }
                //
                DB::update("update notification_devices set last_deviate_time = '$time' where notification_id = ?", [$row->id]);
                if (isset($notifications) && isset($notification_data)) {
    
                  $body = $notification->content;
                  $body = str_replace('$name', $name, $body);
                  $body = str_replace('$description', isset($equipment->description)?$equipment->description:$device->device_id, $body);
                  $body = str_replace('$deviceID', $equipment_id, $body);
                  $body = str_replace('$celsius', $device->temperature . '°C', $body);
                  $body = str_replace('$connected_sensor', $name, $body);
                  $body = str_replace('$project_name', $project_name, $body);
                  $url = route("equipment-details", ['company_id' => $device->company_id, 'device_id' => $equipment_id]);
                  $body = str_replace('$url', $url, $body);
    
                  AlertHistory::create([
                    'name' => $notification_data->name,
                    'email' => $notification->email,
                    'company_id' => $notification_data->company_id,
                    'device_id' => $equipment_id,
                    'body' => $body
                  ]);
                }
              }
            }
          }
          }
        }
        }
      }


  }

  public function triggerSMS($phoneString, $message, $subject)
  {
    if ($phoneString != '') {
      $phones = explode(',', $phoneString);
      foreach ($phones as $phone) {
        $this->sendSms($phone, $message);
      }
    }
  }
  public function sendTrigger(NotificationEmail $notification, $device_id = 0)
  {
    Log::info('sendTrigger');
    Log::info($device_id);
    $subject = isset($notification->subject) ? $notification->subject : 'Notification';
    $notification_type = isset($notification->notification_type) ? $notification->notification_type : 0;
    $emailString = isset($notification->email) ? $notification->email : '';
    $content = isset($notification->content) ? $notification->content : '';
    $device = Device::where('id', $device_id)->with('company')->first();
    if ($device) {
      Log::info($device->toArray());
    }
    $company = Company::where('company_id', $device->company_id)->first();
    $project_name = isset($company->name) ? $company->name : '';
    $deviceID = isset($device->device_id) ? $device->device_id : '';
    $equipment =Device::where('sensor_id',$deviceID)->first();
    $description = isset($equipment->description) ? $equipment->description : $device->description;
    $celsius = isset($device->temperature) ? $device->temperature : '';
    $equipment_id = isset($equipment)?$equipment->device_id:$device->device_id;
    $sensor = !empty($device->name)?$device->name:$device->device_id;
    $name = isset($equipment->name)?$equipment->name:$device->name;
    if (isset($subject) && $subject != null) {
      $subject = str_replace('$name', $name, $subject);

      if($device->event_type=="ccon"){
                $subject = str_replace('$description', $device->description, $subject);
                $subject = str_replace('$deviceID', $deviceID, $subject);
                $subject = str_replace('$connected_sensor', 'none', $subject);
                $subject = str_replace('$celsius', $device->signal_strength . '%', $subject);
              }
              else if($device->event_type=="Temperature" && $equipment==null){
                $name =$equipment->name;
                $subject = str_replace('$description', $description, $subject);
                $subject = str_replace('$deviceID', $equipment_id, $subject);
                $subject = str_replace('$connected_sensor', 'none', $subject);
              }
              else if($device->event_type =="equipment"){
                $name =$device->name;
                $subject = str_replace('$description', $description, $subject);
                $subject = str_replace('$connected_sensor', $sensor, $subject);
                $subject = str_replace('$deviceID', $equipment_id, $subject);
                $subject = str_replace('$celsius', $celsius . '°C', $subject);
              }
      $subject = str_replace('$project_name', $project_name, $subject);
      if ($equipment ==null || $equipment=='') {
      $url = URL::secure(route("sensor-details", ['company_id' => $device->company_id, 'device_id' => $equipment_id]));
      }else{
    $url = URL::secure(route("equipment-details", ['company_id' => $device->company_id, 'device_id' => $equipment_id]));
      }
      $subject = str_replace('$url', $url, $subject);
    }
    $content = str_replace('$name', $name, $content);
    // if($device->event_type=="ccon"){
    //   $content = str_replace('$description', $device->description, $content);
    //   $content = str_replace('$deviceID', $deviceID, $content);
    //   $content = str_replace('$connected_sensor', 'none', $content);
    //   $subject = str_replace('$celsius', $device->signal_strength . '%', $subject);
    // }else{
    //   $content = str_replace('$description', $description, $content);
    //   $content = str_replace('$deviceID', $equipment_id, $content);
    //   $content = str_replace('$connected_sensor', $sensor, $content);
    //   $content = str_replace('$celsius', $celsius . '°C', $content);
    // }

    if($device->event_type=="ccon"){
      $content = str_replace('$description', $device->description, $content);
      $content = str_replace('$deviceID', $deviceID, $content);
      $content = str_replace('$connected_sensor', 'none', $content);
      $content = str_replace('$celsius', $device->signal_strength . '%', $content);
    }
    else if($device->event_type=="Temperature" && ($equipment==null || $equipment=='')){
      $content = str_replace('$description', $description, $content);
      $content = str_replace('$deviceID', $equipment_id, $content);
      $content = str_replace('$connected_sensor', 'none', $content);
    }
    else{
      $content = str_replace('$description', $description, $content);
      $content = str_replace('$deviceID', $equipment_id, $content);
      $content = str_replace('$connected_sensor', $sensor, $content);
      $content = str_replace('$celsius', $celsius . '°C', $content);
    }
    $content = str_replace('$project_name', $project_name, $content);
    if ($equipment ==null || $equipment=='') {
      $url = URL::secure(route("sensor-details", ['company_id' => $device->company_id, 'device_id' => $equipment_id]));
    }else{
      $url = URL::secure(route("equipment-details", ['company_id' => $device->company_id, 'device_id' => $equipment_id]));
    }
    $content = str_replace('$url', $url, $content);

    if ($notification_type == 1) {
      Log::info('before SMS');
      \Artisan::call('optimize:clear');
      $this->triggerSMS($emailString, $content, $subject);
    } else {
      Log::info('before Email');
      $data_ar = [
        'content' => nl2br($content)
      ];
      $this->triggerEmail($emailString, $data_ar, $subject);
    }
  }
  public function triggerEmail($emailString = '', $data_ar = [], $subject)
  {
    $emails = explode(',', $emailString);

    Config::set('mail.from', config('mail.alert_email', 'alerts@recasoft.no'));
    Config::set('mail.username', config('mail.alert_email', 'alerts@recasoft.no'));
    foreach ($emails as $to_email) {
      $to_email = trim($to_email);
      // $to_email = "saeed.mashkraft@gmail.com";
      try {
        Log::info('Try Email');
        Mail::send('emails.notification-email', $data_ar, function ($message) use ($to_email, $subject) {
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
  public function disableSensor()
  {
    $time = Carbon::now()->subMinutes(60);
    $ids = Device::where('is_active', 1)->where('temeprature_last_updated', '<', $time)->pluck('id')->toArray();
    if (count($ids) > 0) {
      $ids_string = implode(',', $ids);
      $res = DB::select('SELECT ND.already_sent,N.id as notification_id,NOW() as ct_time,ND.last_deviate_time,DATE_ADD(ND.last_deviate_time,INTERVAL N.delay_time HOUR) as notification_time,ND.id,ND.device_id,N.alert_type,N.temp_range,N.upper_celcius,N.lower_celcius FROM `notification_devices` ND
left join notifications N on N.id = ND.notification_id
where N.isActive=1 and N.alert_type="Device Monitoring (Beta)" and ND.last_deviate_time is null and ND.already_sent=0 and ND.device_id in (?)', [$ids_string]);
      foreach ($res as $row) {
        $device_notification_id = isset($row->id) ? $row->id : 0;
        NotificationDevice::whereId($device_notification_id)->update(['last_deviate_time' => Carbon::now()]);
      }

      Device::where('is_active', 1)->where('temeprature_last_updated', '<', $time)->update(['is_active' => 0]);
    }
  }

  public function singleSensor(Request $request, $device_id = 'c17i4mt17uj000bpsba0')
  {
    $service_account_id = 'c90q4hr8m12000e1jmhg';
    $service_account_email = 'buhptsj24te000b250bg@buh71lgoonrl27r1frqg.serviceaccount.d21s.com';
    $secret_key = '3357d008cad9410383d50b5e1658914b';
    $service_account_id = 'cb1aqhicu95g00er1cog';
    $service_account_email = 'buhptsj24te000b250bg@buh71lgoonrl27r1frqg.serviceaccount.d21s.com';
    $secret_key = '4b695c1990f74c39a011396962c186f8';
    $token = $this->get_jwt_token($service_account_id, $service_account_email, $secret_key);
    $token = $token['access_token'];

    return $res = $this->get_single_sensor($device_id, $token);
    /*echo '<pre>';
      print_r($res);
      echo '</pre>';
      die();*/
  }
  public function checkCompaniesState(Request $request)
  {
    $companies = Company::where('parent_id', 0)->get();
    foreach ($companies as $company) {
      $company_id = isset($company->company_id) ? $company->company_id : '';
      $comp_id = isset($company->id) ? $company->id : '';
      $existDevices = Device::where('device_status', 1)->where(function ($q) use ($company_id, $comp_id) {
        $q->where('company_id', $company_id);
        $q->orWhere('coming_from_id', $comp_id);
      })->pluck('device_id')->toArray();
      echo '<pre>';
      print_r($existDevices);
      echo '</pre>';

      $token = $this->get_jwt_token($company->key_id, $company->service_account_email, $company->service_account_id);

      $token = isset($token['access_token']) ? $token['access_token'] : '';
      $result = $this->get_sensors_clouds($company_id, $token);
      $devices = isset($result['devices']) ? $result['devices'] : [];
      if (isset($devices) && is_array($devices) && count($devices) > 0) {
        foreach ($devices as $row) {
          $deviceId = '';
          if (isset($row['name']) && $row['name'] != '') {
            echo $row['name'] . '===loopName<br>';
            $exp = explode('/', $row['name']);
            $deviceId = end($exp);
            if (in_array($deviceId, $existDevices)) {
              $deviceKey = array_search($deviceId, $existDevices);
              if (isset($existDevices[$deviceKey])) {
                unset($existDevices[$deviceKey]);
              }
            }
          }
        }
        if (count($existDevices) > 0) {
          echo '<pre>';
          print_r($existDevices);
          echo '</pre>';
          foreach ($existDevices as $dev) {
            $res = $this->singleSensor(request(), $dev);
            if (isset($res['name']) && $res['name'] != '') {
              $projectLink = $res['name'];
              echo $projectLink . '<br>';
              $namesAr = explode('/', $projectLink);
              $projectID = isset($namesAr[1]) ? $namesAr[1] : '';
              if ($projectID != '') {
                $compp = Company::where('company_id', $projectID)->select('id', 'company_id')->first();
                echo $dev . '==updated to new project<br>';
                if ($compp) {

                  DB::select('update devices set company_id = ? where device_id = ? limit 1', [$projectID, $dev]);
                  // Device::where('device_id',$dev)->update(['company_id'=>$projectID]);
                  if (in_array($dev, $existDevices)) {
                    $deviceKey = array_search($dev, $existDevices);
                    if (isset($existDevices[$deviceKey])) {
                      unset($existDevices[$deviceKey]);
                    }
                  }
                }
              }
            }
          }
        }
        if (count($existDevices) > 0) {
          echo '<pre>';
          print_r($existDevices);
          echo '</pre>';

          $devices_string = implode('","', $existDevices);
          DB::select('update devices set not_available = not_available+1, is_active=0 where device_id in ("' . $devices_string . '")', []);
          DB::select('update devices set device_status=0 where device_id in ("' . $devices_string . '") and not_available>1', []);
        }
      }
    }
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
    $encoded_post = http_build_query($arr);
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


  public function get_event_history($project_id, $device_id, $token, $nextPageToken)
  {
    /*if($nextPageToken==''){
          return [];
        }*/
    $timestamp1 = strtotime(date('Y-m-d H:i:s', strtotime('-1 month')));
    $timestamp2 = strtotime(date('Y-m-d H:i:s'));
    $startTime = gmdate("Y-m-d\TH:i:s\Z", $timestamp1);
    $endTime = gmdate("Y-m-d\TH:i:s\Z", $timestamp2);
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL            => "https://api.disruptive-technologies.com/v2/projects/" . $project_id . "/devices/" . $device_id . "/events?startTime=$startTime&endTime=$endTime&pageSize=1000&pageToken=$nextPageToken",
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

  public function get_sensors_clouds($project_id, $token)
  {
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL            => "https://api.disruptive-technologies.com/v2/projects/" . $project_id . "/devices",
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

  public function get_single_sensor($sensor_id, $token)
  {
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL            => "https://api.disruptive-technologies.com/v2/projects/-/devices/" . $sensor_id,
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
}
