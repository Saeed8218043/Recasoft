<?php

namespace App\Http\Controllers;
use Dompdf\Dompdf;
use App\Company;
use App\Document;
use App\AdminCompany;
use App\Device;
use Illuminate\Support\Facades\URL;
use App\DeviceDocument;
use App\AppNotification;
use App\DeviceTemperature;
use App\Http\Controllers\Controller;
use App\Mail\RegisterUser;
use App\Note;
use App\Notification as notifications;
use App\NotificationDevice;
use App\User;
use Cache;
use Carbon\Carbon;
use DB;
use DataTables;
use Faker\Factory as Faker;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Image;
use Mail;

class CompaniesController extends Controller
{
  public function __construct()
    {
         // $this->middleware('CheckAdmin');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   
public function index()
{
  if (ob_get_level() == 0) {
      ob_start();
  }
  
    	// $this->data['users']=Company::all();
    return view('companies.index', $this->data);
}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // $this->data['genders']=Gender::all();
        $userType = isset($request->userType)?$request->userType:0;
        $this->data['userType'] = old('role_id',$userType);
        $this->data['code'] = Str::random(7);
        return view('companies.create', $this->data);
    }

    private function resizeImage($imageName,$width=200,$height=null){
        $imagePath=public_path('images/coupons/thumbs/');
        if(!is_dir($imagePath)){
            mkdir($imagePath, 755);
        }
        $img = Image::make(public_path('images/coupons/'.$imageName));

        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });


        $img->save($imagePath.$imageName);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      // return response()->json($request->all());
      $json = array();
      $company = Company::where(array('email'=>$request->company_email,'company_id'=>$request->company_id))->first();
      if(isset($company->id)){
        $json['status']='false';
        $json['message']='Company is already exist.';
        $json['class']='alert-danger';
      }else{
        $token = $this->get_jwt_token($request->key_id,$request->service_account_email,$request->service_account_id);
        $token = isset($token['access_token'])?$token['access_token']:'';
        if($token==''){
            $json['status']='false';
            $json['message']='Invalid response';
            $json['class']='alert-danger';
        }elseif($token!=''){
            $add_company = new Company();
            $add_company->name = isset($request->company_name)?$request->company_name:'';
            $add_company->organization_name = isset($request->organization_name)?$request->organization_name:'';
            $add_company->organization_no = isset($request->organization_no)?$request->organization_no:'';
            $add_company->email = isset($request->company_email)?$request->company_email:'';
            $add_company->phone = isset($request->company_phone)?$request->company_phone:'';
            $add_company->description = isset($request->description)?$request->description:'';
            $add_company->company_id = isset($request->company_id)?$request->company_id:'';
            $add_company->service_account_email = isset($request->service_account_email)?$request->service_account_email:'';
            $add_company->service_account_id = isset($request->service_account_id)?$request->service_account_id:'';
            $add_company->key_id = isset($request->key_id)?$request->key_id:'';
            $add_company->is_active = 1;
            $add_company->save();

            // $url = route('companies.historyData',['company_id'=>$add_company->company_id]);
            $url = url('get_history_data/'.$add_company->company_id);
            $this->send_to_server($url);

            $json['status']='true';
            $json['message']='Company has been created successfully. It may take few minutes to show sensors.';
            $json['class']='alert-success';
          }
      }
      return response()->json($json,200);
    }

    public function store2(Request $request)
    {
      
      $id = \Auth::user()->id;
      // Log::info('My CompaniesMessage',['users' => $id]);
      $company = Company::where(array('company_id'=>$request->company_id))->first();
      if(isset($company->id)){
        return redirect()->back()->with('warning','Company is already exits!');
            
      }else
      {
        $add_company = new Company();
        $add_company->name = isset($request->company_name)?$request->company_name:'';
        $add_company->organization_name = isset($request->organization_name)?$request->organization_name:'';
        $add_company->organization_no = isset($request->organization_no)?$request->organization_no:'';
        $add_company->email = isset($request->company_email)?$request->company_email:'';
        $add_company->phone = isset($request->company_phone)?$request->company_phone:'';
        $add_company->description = isset($request->description)?$request->description:'';
        $add_company->company_id = isset($request->company_id)?$request->company_id:'';
        $add_company->is_active = 1;
        $add_company->parent_id = $request->par_id;
        $add_company->user_id = $id;
        $add_company->save();

        // $json['status']='true';
        // $json['message']='Company has been created successfully. It may take few minutes to show sensors.';
        // $json['class']='alert-success';
      // }
      $user =auth()->user()->name;
      $action ="Create";
      $company = Company::where('company_id',$request->company_id)->first();
      $par_company = Company::where('id',$company->parent_id)->first();
      $message = "$user created a new child company ($company->name) in $par_company->name";
      SystemLogs($message,$request->company_id,$action);

        return redirect()->back()->with('message','Company has been created successfully!');
      }

    }
    //  public function storeCompanyWithApi(Request $request)
    // {
    //   $id = \Auth::user()->id;
    //   // Log::info('My CompaniesMessage',['users' => $id]);
    //   $company = Company::where(array('company_id'=>$request->company_id))->first();
    //   if(isset($company->id)){
    //     return redirect()->back()->with('warning','Company is already exits!');
            
    //   }else
    //   {

    //     $add_company = new Company();
    //     $add_company->name = isset($request->company_name)?$request->company_name:'';
    //     $add_company->email = isset($request->company_email)?$request->company_email:'';
    //     $add_company->phone = isset($request->company_phone)?$request->company_phone:'';

    //     $add_company->company_id = isset($request->company_id)?$request->company_id:'';
    //     $add_company->is_active = 1;
    //     $add_company->parent_id = 0;
    //     $add_company->user_id = 0;
    //     $add_company->save();

    //     // $json['status']='true';
    //     // $json['message']='Company has been created successfully. It may take few minutes to show sensors.';
    //     // $json['class']='alert-success';
    //   // }
      
    //     return redirect()->back()->with('message','Company has been created successfully!');
    //   }

    // }



    public function test_send_to_server(Request $request){
      $id = isset($request->id)?$request->id:'c329u2p5683qbsdjpslg';
      // echo $url = route('companies.historyData',['company_id'=>'c329u2p5683qbsdjpslg']);
      echo $url = url('get_history_data/'.$id);
            $this->send_to_server($url);
    }
    public function send_to_server($url=''){
      
      if($url!=''){
        
            // log_message('error',"curl ".$url." > /dev/null &");            
          shell_exec("curl -k ".$url." > /dev/null &"); 
      }
        
    }
    public function get_history_data($company_id=''){
      ini_set('memory_limit', '-1');
      ini_set('max_execution_time', 0);
      
      if($company_id==''){
        return false;
      }
      
      $company = Company::where('company_id',$company_id)->first();
      if($company){
        // $request->company_id = $company_id;
        $token = $this->get_jwt_token($company->key_id,$company->service_account_email,$company->service_account_id);
        $token = isset($token['access_token'])?$token['access_token']:'';
        $result = $this->get_sensors_clouds($company_id,$token);
        $devices = isset($result['devices'])?$result['devices']:[];
        if(isset($devices) && is_array($devices) && count($devices)>0){
          foreach($devices as $row){
            $deviceId='';
            if(isset($row['name']) && $row['name']!=''){
              $exp = explode('/', $row['name']);
              $deviceId = end($exp);
            }
            $type = isset($row['type'])?$row['type']:'';
            $name = isset($row['labels']['name'])?$row['labels']['name']:'';
            $description = isset($row['labels']['description'])?$row['labels']['description']:'';
            $labels = isset($row['labels'])?$row['labels']:'';
            $labels_json = json_encode($labels);
            $cloudConnector = $macAddress=$ipAddress=null;

            if($type=='ccon'){
              $signalStrength = isset($row['reported']['cellularStatus']['signalStrength'])?$row['reported']['cellularStatus']['signalStrength']:0;
              $temperature = isset($row['reported']['connectionStatus']['connection'])?$row['reported']['connectionStatus']['connection']:'';
              $updateTime = isset($row['reported']['connectionStatus']['updateTime'])?$row['reported']['connectionStatus']['updateTime']:'';
              $cloudConnector = isset($row['reported']['connectionStatus']['cloudConnectors'][0]['id'])?$row['reported']['connectionStatus']['cloudConnectors'][0]['id']:'';
              $macAddress = isset($row['reported']['ethernetStatus']['macAddress'])?$row['reported']['ethernetStatus']['macAddress']:'';
              $ipAddress = isset($row['reported']['ethernetStatus']['ipAddress'])?$row['reported']['ethernetStatus']['ipAddress']:'';
            }elseif($type=='temperature' || $type=='proximity'){
              $signalStrength = isset($row['reported']['networkStatus']['signalStrength'])?$row['reported']['networkStatus']['signalStrength']:0;
              $temperature = isset($row['reported']['temperature']['value'])?$row['reported']['temperature']['value']:0;
              $updateTime = isset($row['reported']['temperature']['updateTime'])?$row['reported']['temperature']['updateTime']:null;
            }

            $batteryStatus = isset($row['reported']['batteryStatus']['percentage'])?$row['reported']['batteryStatus']['percentage']:0;
            $batteryUpdateTime = isset($row['reported']['batteryStatus']['updateTime'])?$row['reported']['batteryStatus']['updateTime']:date('Y-m-d H:i:s',time());
            if($updateTime!=null && $updateTime!=''){
              $updateTime = date('Y-m-d H:i:s',strtotime($updateTime));
            }
            Log::info('batteryUpdateTime');
            Log::info($batteryUpdateTime);
            Log::info('updateTime');
            Log::info($updateTime);
            $device = Device::where('device_id',$deviceId)->first();
            if(!isset($device->id)){
              $device = new Device();
              $device->name = $name;
              $device->description = $description;
              $device->company_id = $company_id;
              $device->device_id = $deviceId;
              $device->temperature = $temperature;
              $device->signal_strength = $signalStrength;
              $device->battery_level = $batteryStatus;
              $device->battery_updated_datetime = $batteryUpdateTime;
              $device->temeprature_last_updated = $updateTime;
              $device->event_type = $type;
              $device->macAddress = $macAddress;
              $device->ipAddress = $ipAddress;
              $device->labels_json = $labels_json;
              $device->device_status=1;
              $device->save();

              $resultPage = $this->getNextPageToken($company_id,$deviceId,$token);
              foreach($resultPage as $page){
                $history = $this->get_event_history($company_id,$deviceId,$token,$page);
                $events = isset($history['events'])?$history['events']:[];

                          // $history = $this->get_event_history($company_id,$deviceId,$token);
                          // $events = isset($history['events'])?$history['events']:[];
                if(isset($events) && is_array($events) && count($events)>0){
                  foreach($events as $event){
                    $cloudConnector=null;
                    $eventId = isset($event['eventId'])?$event['eventId']:'';
                    $eventType = isset($event['eventType'])?$event['eventType']:'';
                    $time_stamp = isset($event['timestamp'])?$event['timestamp']:'';
                    $dataObject = isset($event['data'])?$event['data']:[];
                   $cloudConnector =  isset($dataObject['networkStatus']['cloudConnectors'][0]['id'])?$dataObject['networkStatus']['cloudConnectors'][0]['id']:'';
                    $date = date('Y-m-d H:i:s', strtotime($time_stamp));
                    if($eventType=='temperature' || $eventType=='proximity'){
                      $value = isset($event['data']['temperature']['value'])?$event['data']['temperature']['value']:'';
                      if($value!='' && $time_stamp!='0000-00-00 00:00:00'){
                        $ins=[
                          'event_id'=>$eventId,
                          'device_id'=>$deviceId,
                          'temperature'=>$value,
                          'signal_strength'=>0,
                          'type'=>$eventType,
                          'cloudConnector'=>$cloudConnector,
                          'created_at'=>$date,
                        ];
                        DeviceTemperature::insertOrIgnore($ins);
                      }
                    }elseif($eventType=='networkStatus'){
                      $value = isset($event['data']['networkStatus']['signalStrength'])?$event['data']['networkStatus']['signalStrength']:0;
                      if($value>0 && $time_stamp!='0000-00-00 00:00:00'){
                        $ins=[
                          'event_id'=>$eventId,
                          'device_id'=>$deviceId,
                          'temperature'=>0,
                          'signal_strength'=>$value,
                          'type'=>$eventType,
                          'cloudConnector'=>$cloudConnector,
                          'created_at'=>$date,
                        ];
                        DeviceTemperature::insertOrIgnore($ins);
                      }
                    }elseif($eventType=='cellularStatus'){
                      $value = isset($event['data']['cellularStatus']['signalStrength'])?$event['data']['cellularStatus']['signalStrength']:0;
                      if($value>0 && $time_stamp!='0000-00-00 00:00:00'){
                        $ins=[
                          'event_id'=>$eventId,
                          'device_id'=>$deviceId,
                          'temperature'=>0,
                          'signal_strength'=>$value,
                          'cloudConnector'=>$cloudConnector,
                          'type'=>$eventType,
                          'created_at'=>$date,
                        ];
                        DeviceTemperature::insertOrIgnore($ins);
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
    public function transferDevice($token,$to_project_id,$from_project_id,$device_id){

    $curl = curl_init();
    
    $arr=[
      'devices'=>[
        'projects/'.$from_project_id.'/devices/'.$device_id
      ]
    ];
    $url = "https://api.disruptive-technologies.com/v2/projects/".$to_project_id."/devices:transfer";
    // echo $url.'<br>';
    // print_r($arr);
    $encoded_post  =json_encode($arr);
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS =>$encoded_post,
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer ".$token,
      "Content-Type: application/json"
      ),
    ));
    
    $response = curl_exec($curl);
    if(curl_exec($curl) === false){
        echo 'Curl error: ' . curl_error($curl);
        curl_close($curl);
    }
    else{         
      $result = json_decode($response,true);
      return $result;
      curl_close($curl);
    }
  
    die();
  
        
  }
    public function moveSensor(Request $request){
      // dd($request->all());
      $device_id = isset($request->sID)?$request->sID:'';
      $to_company = isset($request->transfer_sensor)?$request->transfer_sensor:'';
        $json=[
          'success'=>false,
          'transfer_sensor'=>$to_company,
          'sID'=>$device_id,
        ];
      if($device_id!='' && $to_company!=''){
        $device =Device::where('device_id',$device_id)->first();
        $from_company = isset($device->company_id)?$device->company_id:'';
        if($from_company!=''){
          $service_account_id = 'cabk40aa385g00amb1k0';
          $service_account_email = 'buhqt2r24te000b250gg@blegsq6ec0m00097e3sg.serviceaccount.d21s.com';
          $secret_key = '701638510b26437d9fc47d7b787aed9a';
          $token = $this->get_jwt_token($service_account_id,$service_account_email,$secret_key);
          if(isset($token['access_token'])){
            $token = $token['access_token'];
            $res = $this->transferDevice($token,$to_company,$from_company,$device_id);
            if(isset($res['transferredDevices'])){
              $device->company_id = $to_company; 
              //implemented new condition
              $toCompany=Company::where('company_id',$to_company)->first();
                  if(isset($toCompany) && $toCompany->parent_id ==0){
                    $device->coming_from_id = 0;
                  }
                  else{
                    $device->coming_from_id = $toCompany->parent_id;
                  }

                   $device_row = Device::where('device_id',$device_id)->first();
                   $dev_id = isset($device_row->id)?$device_row->id:'';
                   
                  //  dd($dev_id);
                  
                  $notification_devices=NotificationDevice::where('device_id',$dev_id)->get();
                  // dd($notification_devices);
                  $notifications=notifications::where('company_id',$device_row->company_id)->get();
                  if(count($notifications)>0){
                    // dd($notifications);
                    foreach ($notifications as $key => $notification) {
      
                      $notification_device=NotificationDevice::where('device_id',$dev_id)->where('notification_id',$notification->id)->first();
                      // dd($notification_device);
                      if(isset($notification_device) && $notification_device!=null){
                        $notification_device->delete();
                      }
                      $notification_remaining_devices=NotificationDevice::where('notification_id',$notification->id)->get();
                      if(count($notification_remaining_devices)==0){
                        $notification->delete();
                      }
      
                    }
                  }
                  $dev_name = !empty($device->name)?$device->name:$device_id;
                  $user =auth()->user()->name;
                  $action ="Moved";
                  $company = Company::where('id',$request->comp_ID)->first();
                  $to_company2 = Company::where('company_id',$to_company)->first();
                  $message = "$user moved device ($dev_name) from $company->name to $to_company2->name.";
                  SystemLogs($message,$device->company_id,$action);
                  $device->save();

                  // Remove the device from the notification_devices table
                $notification = notifications::where('company_id', $from_company)->first();
                if (isset($notification)) {
                    NotificationDevice::where('notification_id', $notification->id)->where('device_id', $device->id)->delete();
                }
                }
            $json['success']=true;
            $json['url']=route('sensors',['company_id'=>$from_company]);
            return response()->json($json);
          }
           $json['from_company_out']=1;
        return response()->json($json);      
        }
        $json['to_company_out']=1;
        $json['device']=$device;
        return response()->json($json);  
      }
      $json['out']=1;
      return response()->json($json);
    }

    public function moveSensor2(Request $request){
      // dd($request->all());
      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);
      //Log::info('My CompaniesMessage',['users' => [$request->sID, $request->transfer_sensor, $request->comp_ID]]);
      $device_id = isset($request->sID)?$request->sID:'';
      
      $to_company = isset($request->transfer_sensor)?$request->transfer_sensor:'';
        $json=[
          'success'=>false,
          'transfer_sensor'=>$to_company,
          'sID'=>$device_id,
        ];
        $device =Device::select('company_id', 'coming_from_id','id','name')->where('device_id',$device_id)->first();
      if($device_id!='' && $to_company!=''){
        // Log::info('My MessageSensor',['users' => [$device, gettype($device)]]);
        $from_company = isset($device->company_id)?$device->company_id:'';
        if($from_company!=''){
          //$service_account_id = 'cabk40aa385g00amb1k0';
          //$service_account_email = 'buhqt2r24te000b250gg@blegsq6ec0m00097e3sg.serviceaccount.d21s.com';
          //$secret_key = '701638510b26437d9fc47d7b787aed9a';
          //$token = $this->get_jwt_token($service_account_id,$service_account_email,$secret_key);
          //if(isset($token['access_token'])){
            //$token = $token['access_token'];
            //$res = $this->transferDevice($token,$to_company,$from_company,$device_id);
            //if(isset($res['transferredDevices'])){
          $device->company_id = $to_company;
          //implemented new condition
          $toCompany=Company::where('company_id',$to_company)->first();
                  if(isset($toCompany) && $toCompany->parent_id ==0){
                    $device->coming_from_id = 0;
                  }
                  else{
                    $device->coming_from_id = $toCompany->parent_id;
                  }

          $device->save();

        }
        // Remove the device from the notification_devices table
        $notification = notifications::where('company_id', $from_company)->first();
        if (isset($notification)) {
            NotificationDevice::where('notification_id', $notification->id)->where('device_id', $device->id)->delete();
        }
        $json['success']=true;
        $json['sensor_coming_from_id'] =  $request->comp_ID;
        return response()->json($json);
               
      }
          $dev_name = !empty($device->name)?$device->name:$device_id;
          $user =auth()->user()->name;
          $action ="Moved";
          $company = Company::where('company_id',$device->company_id)->first();
          $to_company2 = Company::where('company_id',$to_company)->first();
          $message = "$user moved device ($dev_name) from $company->name to $to_company2->name.";
          SystemLogs($message,$request->company_id,$action);

      return response()->json($json);
    }

    public function moveSensors(Request $request){
      // dd($request->all());
    	$user_id=Auth::user()->id;
    	if($user_id>1){
    		if($request->has('device_ids')){
    			
    			foreach($request->device_ids as $device_id){

    				 $res=$this->companyAdminMoveSensor($device_id,$request->transfer_sensor,$request->comp_ID);

    			}
    		}

	         
    	}else{
    		if($request->has('device_ids')){

          foreach($request->device_ids as $device_id){
            
            $device_row = Device::where('device_id',$device_id)->first();

            $this->adminMoveSensor($device_id,$request->transfer_sensor,$request->comp_ID);

            $dev_id = isset($device_row->id)?$device_row->id:'';

            $notification_devices=NotificationDevice::where('device_id',$dev_id)->get();
            // dd($notification_devices);
            $notifications=notifications::where('company_id',$device_row->company_id)->get();
            if(count($notifications)>0){
              // dd($notifications);
              foreach ($notifications as $key => $notification) {

                $notification_device=NotificationDevice::where('device_id',$dev_id)->where('notification_id',$notification->id)->first();
                // dd($notification_device);
                if(isset($notification_device) && $notification_device!=null){
                  $notification_device->delete();
                }
                $notification_remaining_devices=NotificationDevice::where('notification_id',$notification->id)->get();
                if(count($notification_remaining_devices)==0){
                  $notification->delete();
                }

              }
            }
               
            
    			}
    		}
    		
    	}
    	return response()->json(['success'=>true]);


    }



    public function adminMoveSensor($device_id,$transfer_sensor,$comp_ID)
    {
    	$device_id = isset($device_id)?$device_id:'';
		      $to_company = isset($transfer_sensor)?$transfer_sensor:'';
		        $json=[
		          'success'=>false,
		          'transfer_sensor'=>$to_company,
		          'sID'=>$device_id,
		        ];
		      if($device_id!='' && $to_company!=''){
		        $device =Device::where('device_id',$device_id)->first();
		        $from_company = isset($device->company_id)?$device->company_id:'';
		        if($from_company!=''){
		          $service_account_id = 'cabk40aa385g00amb1k0';
		          $service_account_email = 'buhqt2r24te000b250gg@blegsq6ec0m00097e3sg.serviceaccount.d21s.com';
		          $secret_key = '701638510b26437d9fc47d7b787aed9a';
		          $token = $this->get_jwt_token($service_account_id,$service_account_email,$secret_key);
		          if(isset($token['access_token'])){
		            $token = $token['access_token'];
		            $res = $this->transferDevice($token,$to_company,$from_company,$device_id);
		            if(isset($res['transferredDevices'])){
		              $device->company_id = $to_company;

                  $toCompany=Company::where('company_id',$to_company)->first();
                  if(isset($toCompany) && $toCompany->parent_id ==0){
                    $device->coming_from_id = 0;
                  }
                  else{
                    $device->coming_from_id = $toCompany->parent_id;
                  }
                  
                  

              $dev_name = !empty($device->name)?$device->name:$device_id;
              $user =auth()->user()->name;
              $action ="Moved";
              $company = Company::where('id',$comp_ID)->first();
              $to_company2 = Company::where('company_id',$to_company)->first();
              $message = "$user moved device ($dev_name) from $company->name to $to_company2->name.";
              SystemLogs($message,$to_company,$action);

              $equipment = Device::where('sensor_id',$device_id)->first();
              if($equipment!=null){
                $equipment->update([
                    'sensor_id'=>0
                  ]);
              }

         			 $device->save();
               
                // Remove the device from the notification_devices table
                $notifications = notifications::where('company_id', $from_company)->get();
                if (isset($notifications) && count($notifications)>0) {
                    foreach($notifications as $notification){
                      NotificationDevice::where('notification_id', $notification->id)->where('device_id', $device->id)->delete();
                      $ND = NotificationDevice::where('notification_id', $notification->id)->first();
                      if($ND == null){
                        $notification->delete();
                      }
                    }
                }

		            }
		            $json['success']=true;
		            $json['url']=route('sensors',['company_id'=>$from_company]);
		            return response()->json($json);
		          }
		           $json['from_company_out']=1;
		        return response()->json($json);      
		        }
		        $json['to_company_out']=1;
		        $json['device']=$device;
		        return response()->json($json);  
		      }
		      $json['out']=1;
    }


    public function companyAdminMoveSensor($device_id,$transfer_sensor,$comp_ID){
      $device_id = isset($device_id)?$device_id:'';
      $to_company = isset($transfer_sensor)?$transfer_sensor:'';
        $json=[
          'success'=>false,
          'transfer_sensor'=>$to_company,
          'sID'=>$device_id,
        ];
      if($device_id!='' && $to_company!=''){
        $device =Device::select('company_id','coming_from_id','id')->where('device_id',$device_id)->first();
        

        $from_company = isset($device->company_id)?$device->company_id:'';
        if($from_company!=''){

          $device->company_id = $to_company;
          
          $toCompany=Company::where('company_id',$to_company)->first();

            if(isset($toCompany) && $toCompany->parent_id ==0){
              $device->coming_from_id = 0;
            }
            else{
              $device->coming_from_id = $toCompany->parent_id;
            }
          $dev_name = !empty($device->name)?$device->name:$device_id;
          $user =auth()->user()->name;
          $action ="Moved";
          $company = Company::where('id',$comp_ID)->first();
          $to_company2 = Company::where('company_id',$to_company)->first();
          $message = "$user moved device($dev_name) from $company->name to $to_company2->name.";
          SystemLogs($message,$to_company,$action);
           $equipment = Device::where('sensor_id',$device_id)->first();
           if($equipment!=null){
             $equipment->update([
                 'sensor_id'=>0
               ]);
           }

          $device->save();

          // Remove the device from the notification_devices table
          $notifications = notifications::where('company_id', $from_company)->get();
          if (isset($notifications) && count($notifications)>0) {
              foreach($notifications as $notification){
                NotificationDevice::where('notification_id', $notification->id)->where('device_id', $device->id)->delete();
                $ND = NotificationDevice::where('notification_id', $notification->id)->first();
                if($ND == null){
                  $notification->delete();
                }
              }
          }
        }
        $json['success']=true;
        $json['sensor_coming_from_id'] =  $comp_ID;
        return response()->json($json);
               
      }
      return response()->json($json);
    }



    public function uploadSensorDoc(Request $request){
      $request->validate([
        'sensor_doc_name' => 'required',
        'sensor_doc' => 'max:5128',
        'sensor_doc' => 'mimes:doc,pdf,docx,zip,png,jpg,jpeg'
    ]);

    $device_id = isset($request->sID)?$request->sID:'';
    $name = isset($request->sensor_doc_name)?$request->sensor_doc_name:'';
    
    $device =Device::select('id','company_id')->where('device_id',$device_id)->first();
    if($request->hasFile('sensor_doc')){
      $file = $request->file('sensor_doc');
      $fileName = $file->getClientOriginalName();
      $file->move(base_path() .'/storage/app/public', $fileName);
      $fiLe = new DeviceDocument();

      $fiLe->url = $fileName;
      $fiLe->name = $name;
      if($device != ''){
        $fiLe->device_id = $device->id;
      }
      $fiLe->save();
    }
    $user =auth()->user()->name;
    $action ="Create";
    $company = Company::where('company_id',$device->company_id)->first();
    $message = "$user created a new resource ($name) in $company->name";
    SystemLogs($message,$device->company_id,$action);

    return back()->with('resourceMessage', 'file upload successfully!');
    }

    public function SearchProject(Request $request){
      $query = $request->get('query');    
      $user_id = isset($request->user_id)?$request->user_id:'';
      
      $company_id = $request->input('company_id');
      $encodedCompany = $request->input('currentCompany');
      $decodedCompany = htmlspecialchars_decode($encodedCompany);
      $currentCompany = json_decode($decodedCompany, true);
      $currentID = Company::select('id')->where(array('name'=>$currentCompany['name'],'company_id'=>$currentCompany['company_id']))->first();
      $cID = $currentCompany['id'];   
      // dd($currentCompany['name']);

      $company = Company::select('name','id','parent_id')->where('company_id',$company_id)->first();
      $selectedParent = isset($company->parent_id)?$company->parent_id:0;
      $parent_ID = Company::select('parent_id')->get();
      $id = \Auth::user()->id;
      $companies2 = Company::where('user_id', $id)->get();
      $flag = false;
      if (!empty($currentID)) {
  for ($i = 0; $i < sizeof($parent_ID); $i++){
      if ($parent_ID[$i]->parent_id == $currentID->id){
          $flag = true;
      }
  }
}

  if($user_id==1){
      $companies = Company::where('parent_id',0)->where('company_id','!=',$company_id)
      ->select('id','name','company_id')
      ->where('name', 'LIKE', '%' . $query . '%')
      ->orderBy('name','ASC')
      ->get();
  }else{
      $companies = Company::where(function($q) use ($cID,$selectedParent){
          $q->where('id',$selectedParent);
          $q->orWhere('parent_id',$cID);    
          if($selectedParent>0){
              $q->orWhere('parent_id',$selectedParent);    
          }
          
      })->where('company_id','!=',$company_id)->where('name', 'LIKE', '%' . $query . '%')->get();
  }

      // dd($companies);
      return response()->json($companies);
    }
    public function uploadSensorDoc2(Request $request){
      // dd($request->all());
    $device_id = isset($request->sID)?$request->sID:'';
    $sensor_doc = isset($request->sensor_doc)?$request->sensor_doc:'';

    $name = isset($request->sensor_file_name)?$request->sensor_file_name:'';
    $id = isset($request->sensor_file_id)?$request->sensor_file_id:'';
      $folder = Document::where('id',$id)->first();
    
    $device =Device::select('id')->where('device_id',$device_id)->first();
    
      $fiLe = new DeviceDocument();
      
      if($folder->belongsTo ==0){
        $fiLe->belongsTo =$id;
      }else{
        $fiLe->belongsTo =$folder->belongsTo;
      }

      $fiLe->url = $sensor_doc;
      $fiLe->name = $name;
      if($device != ''){
        $fiLe->device_id = $device->id;
      
      $fiLe->save();
    }
    

    return back()->with('messageUpload', 'file upload successfully!');
    }

    public function uploadSensorNotes(Request $request){
      $request->validate([
        'name' => 'required',
        'notes' => 'required',
    ]);
    $device_id = isset($request->sID)?$request->sID:'';
    $name = isset($request->name)?$request->name:'';
    $notes = isset($request->notes)?$request->notes:'';
    
    $device =Device::select('id')->where('device_id',$device_id)->first();
    if(isset($device)){
      $note = new Note();
      $note->name = $name;
      $note->notes = $notes;
      $note->device_id =$device->id;
      if($device != ''){
        $note->device_id = $device->id;
      }
      $note->save();
    }
        $user =auth()->user()->name;
        $action ="Create";
        $company = Company::where('company_id',$request->company_id)->first();
        $message = "$user created note in $company->name";
        SystemLogs($message,$request->company_id,$action);

    return back()->with('noteSaved', 'Note saved successfully!');
  }

    public function loadDeviceURL(Request $request){
      $search = isset($request->search)?$request->search:'';
      $json=[];
      if($search!=''){
        $json=['status'=>true];
        $email = \Auth::user()->email;
        $sql = DB::table('company_members_invite')->where('email',$email)->pluck('company_id')->toArray();
        
        $device = Device::where('device_id',$search)->with('company');
        if(isset($sql) && count($sql)>0){
            $device->whereIn('company_id',$sql);
            // $where .= " and c.company_id IN('".implode("','",$sql)."') ";
        }
        $device = $device->first();  
        if($device){
          $company_id = isset($device->company->company_id)?$device->company->company_id:'';
          $device_id = isset($device->device_id)?$device->device_id:'';
          $event_type = isset($device->event_type)?$device->event_type:'';
          $url = url('sensor-details/'.$company_id.'/'.$device_id);
          $json['url']=$url;
          $json['type']=$event_type;
          $html = view('templates.searchDevice', ['device'=>$device,'url'=>$url])->render();
          $json['html']=$html;
        }
      }
      return response()->json($json);
      
    }
    public function companiesList(Request $request){
        $html='';
        $where='';
        $company_id = isset($request->company_id)?$request->company_id:'';
        $search = isset($request->search)?$request->search:'';
        if($search!=''){
            $where .= " and c.name like '".$search."%' ";
        }
        $email = \Auth::user()->email;
        $id = \Auth::user()->id;
        
        $sql = DB::table('company_members')->where('user_id',$id)->pluck('comp_id')->toArray();
        /*if(count($sql)>0){
          $sql = Company::whereIn($sql)->orWhereIn('parent_id',$sql)->pluck('id')->toArray();  
        }*/
        
        $join='';
        //Log::info('My CompaniesMessage',['users' => $sql]);
        if(isset($sql) && count($sql)>0){
            // $where .= " and c.company_id IN('".implode("','",$sql)."') or c.user_id= '".$id."' ";
            $where .= " and c.id IN (select id from companies where id IN('".implode("','",$sql)."') or parent_id IN('".implode("','",$sql)."'))  ";//or c.user_id= '".$id."'
            $join=' left join devices d on (d.company_id=c.company_id and d.device_status=1 ) ';
        }elseif($email=='admin@recasoft.com'){
            $where .= "and c.parent_id=0";
            $join=' left join devices d on ( d.company_id = c.company_id AND d.device_status = 1) ';
        }else{
            $where .= " and c.company_id='".md5(time())."' ";
            $join=' left join devices d on (d.company_id=c.company_id ) ';
        }
        $counter=1;
        $query = "select c.parent_id,c.name, c.company_id, d.device_id,
                  count(IF(d.event_type='ccon',1,null)) as connTotal,
                  count(IF(d.event_type='temperature' OR d.event_type='proximity',1,null)) as sensorTotal
                  from companies c
                  $join
                  where c.is_active=1 $where
                  group by c.company_id";
        $company = DB::select($query);
        if(isset($company) && count($company)>0){
            $parent_ids = [];
            foreach($company as $row){
              $parent_ids[]=$row->parent_id;
            }
            $companiesName=[];
            if(count($parent_ids)>0 && $id>0){
              $parentCompanies = Company::whereIn('id',$parent_ids)->select('id','name')->get();
              foreach($parentCompanies as $comp){
                  $companiesName[$comp->id]=$comp->name;
              }
            }
            foreach($company as $row){
              if($id==1){
                $parentName='Recasoft Technologies';
              }else{
                if($row->parent_id==0){
                  $parentName='<label class="badge badge-primary">Inventory Account</label>';  
                }else{
                  $parentName=isset($companiesName[$row->parent_id])?$companiesName[$row->parent_id]:'Recasoft Technologies';
                }
                
              }
                $trClass='';
                if($company_id==$row->company_id){
                    $trClass = 'm--bg-skyblue';
                }
                // $device_id ='';
                // if($request->segment =='equipment-details'){
                //   $equipment = Device::where('company_id',$row->company_id)->where('event_type','equipment')->first();
                //   if($equipment!=null || $equipment!=''){
                //     $device_id = isset($equipment->device_id)?$equipment->device_id:"";
                //     $html .= '<tr style="cursor:pointer;" class="listRow '.$trClass.'" data-id="'.$row->company_id.'" data-device="'.$device_id.'">
                //               <td>'.$row->name.'<br/><small>'.$parentName.'</small></td>
                //               <td align="center">'.$row->connTotal.'</td>
                //               <td align="center">'.$row->sensorTotal.'</td>
                //             </tr>';
                //  }
                // }else{

                  $html .= '<tr style="cursor:pointer;" class="listRow '.$trClass.'" data-id="'.$row->company_id.'" data-device="'.$row->device_id.'" parent-id="'.$row->parent_id.'">
                              <td>'.$row->name.'<br/><small>'.$parentName.'</small></td>
                              <td align="center">'.$row->connTotal.'</td>
                              <td align="center">'.$row->sensorTotal.'</td>
                            </tr>';
                  
                // }
                $counter++;
            }
        }else{
            $html .= '<tr class="text-center"><td colspan="4">No record found</td></tr>';
        }

        echo $html;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $id;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $request = request();
     $shop_id=0;
      /*  if($request->session()->has('shop_id')){
            $shop_id = $request->session()->get('shop_id');
        }*/
    $userType = isset($request->userType)?$request->userType:0;
        $this->data['userType'] = old('role_id',$userType);	
     $this->data['user']=Company::where('id',(int)$id)
     // ->where('shop_id',$shop_id)
     ->firstOrFail();
     
     return view('companies.update', $this->data);
 }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    	$id = (int)$id;
        $validator = Validator::make($request->all(), [
          'total_coupons' => 'required|max:255',
          'coupon_type' => 'required|max:255',
          // 'code' => 'required|unique:coupons|max:255',
          'code' => [
            'required',
            function($attribute, $value, $fail) use ($id) {

                if($attribute == 'code'){
                    $customer = Company::where($attribute, $value)->first();
                    if($customer !== null)
                        if($id != $customer->id)
                            return $fail(ucfirst($attribute).' already exists.');
                    }

                },
            ]
        ]);
        if ($validator->fails()) {
            return redirect()->route('companies.edit',[$id])
            ->withErrors($validator)
            ->withInput();
        }
          $shop_id=0;
        if($request->session()->has('shop_id')){
            $shop_id = $request->session()->get('shop_id');
        }

        $user = Company::where('id',(int)$id)->firstOrFail();
        $user->name = isset($request->name)?$request->name:'';
        $user->code = $request->code;
        $user->shop_id = $shop_id;
        $user->uses_per_customer =  isset($request->uses_per_customer)?$request->uses_per_customer:0;
        $user->total_coupons =  isset($request->total_coupons)?$request->total_coupons:0;
        // $user->from_date =  isset($request->from_date)?$request->from_date:0;
        // $user->to_date =  isset($request->to_date)?$request->to_date:0;
        $from_date = isset($request->from_date)?date('Y-m-d',strtotime($request->from_date)):'';
        if($from_date!=''){
          $user->from_date = $from_date;
        }
        $to_date =  isset($request->to_date)?date('Y-m-d',strtotime($request->to_date)):'';
        if($to_date!=''){
          $user->to_date= $to_date;
        }
        $user->total_amount =  isset($request->total_amount)?$request->total_amount:0;
        $user->discount =  isset($request->discount)?$request->discount:0;
        $user->coupon_type = (int) isset($request->coupon_type)?$request->coupon_type:0;
        $user->is_active = (int) isset($request->is_active)?$request->is_active:0;
        $user->free_shipping = (int) isset($request->free_shipping)?$request->free_shipping:0;

        
        $user->save();
        return redirect()->route('companies.index')->with('success','Company has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteDoc($id){
     $dd = DeviceDocument::whereId($id)->first();
     $device = Device::where('id',$dd->device_id)->first();
     $d_name = $device->name??$device->device_id;
      $user =auth()->user()->name;
      $action ="Delete";
      $company = Company::where('company_id',$device->company_id)->first();
      $message = "$user deleted resource ($dd->name) of ($d_name) in $company->name";
      SystemLogs($message,$device->company_id,$action);

        DeviceDocument::whereId($id)->delete();
        return redirect()->back()->with('messageUpload','Document has been successfully deleted.');
    }
    public function deleteNote($id){
      $notes = Note::whereId($id)->first();
        $device = Device::where('id',$notes->device_id)->first();
        $d_name = $device->name??$device->device_id;
         $company = Company::where('company_id',$device->company_id)->first();
         $user =auth()->user()->name;
         $action ="Delete";
         $message = "$user delete notes ($notes->name) of ($d_name) in $company->name";
         SystemLogs($message,$device->company_id,$action);
         $notes->delete();
        return redirect()->back()->with('noteSaved','Note has been successfully deleted.');
    }
    public function viewNoteValue(Request $request){
        $data = Note::where('id',$request->id)->first();
        return response()->json(['data' => $data]);
    }
    public function editNote(Request $request){
      $notes = Note::where('id',$request->note_id)->first();
     $device = Device::where('id',$notes->device_id)->first();
     $d_name = $device->name??$device->device_id;
      $company = Company::where('company_id',$device->company_id)->first();
      $user =auth()->user()->name;
      $action ="Update";
      $message = "$user edit notes ($notes->name) of ($d_name) in $company->name";
      SystemLogs($message,$device->company_id,$action);

       $notes->update(['name'=>$request->name,'notes'=>$request->notes]);

        return redirect()->back()->with('noteSaved','Note has been successfully Updated.');
    }
    // public function ViewNote(Request $request){
    //       // Retrieve the values from your data source
    //     $id = request()->input('edit_id');
    //     $name = request()->input('name');
    //     $notes = request()->input('notes');

    //     // Load the view and pass the data
    //     $html = view('pdf.template', compact('id', 'name', 'notes'));

    //     // Generate the PDF
    //     $dompdf = new Dompdf();
    //     $dompdf->loadHtml($html);
    //     $dompdf->render();

    //     // Output the PDF to the browser
    //     $dompdf->stream('document.pdf', ['Attachment' => false]);
    // }
    public function destroy($id)
    {

        $restore =  request('restore');
        //->trashed();//->update(['is_delete'=>1]);
        if($restore==1){
            $row = Company::where('id',(int)$id)->firstOrFail();
            if($row->restore()){
                return redirect()->route('companies.index')->with('success','Company has been restored successfully.');
            }
        }else{
            $row = Company::where('id',(int)$id)->firstOrFail();
            if($row->delete()){
                return redirect()->route('companies.index')->with('success','Company has been deleted successfully.');
            }
        }
        return redirect()->route('companies.index')->with('success','Someting went wrong. Please try again.');
    }
    public function restore($id)
    {
        $row = Company::findOrFail((int)$id);//->trashed();//->update(['is_delete'=>1]);
        if($row->trashed()){
            $row->restore();
            return redirect()->route('companies.index')->with('success','Company has been restored successfully.');
        }
        return redirect()->route('companies.index')->with('success','Someting went wrong. Please try again.');
    }
    public function showData(Request $request)
    {
      $shop_id=0;
        /*if($request->session()->has('shop_id')){
            $shop_id = $request->session()->get('shop_id');
        }*/

	        // if ($request->ajax()) {
    	$search = $request->input('search.value');
    	$columns = $request->get('columns');
    	$length = $request->get('length');
    	$start = $request->get('start');
    	$tableOrder = $request->get('order');
    	$sort_order = isset($tableOrder[0]['dir'])?$tableOrder[0]['dir']:'DESC';
    	$sort_by_Number = isset($tableOrder[0]['column'])?$tableOrder[0]['column']:0;
    	$valid_sort_by=['id','name','email','phone','created_at','is_active','id'];
    	$sort_by = isset($valid_sort_by[$sort_by_Number])?$valid_sort_by[$sort_by_Number]:'id';
    	$count_total = new Company();//::where('shop_id',$shop_id);
      if($search!=''){
          $count_total->where(function($q)use($search){
            $q->where('email','like',$search.'%');
            $q->orWhere('phone','like',$search.'%');
            $q->orWhere('name','like',$search.'%');
            if(strtolower($search)=='buyer'){
              $q->orWhere('role_id',0);
            }elseif(strtolower($search)=='seller'){
              $q->orWhere('role_id',1);
            }
          });
        }
      $count_total = $count_total->count();
    	$data = Company::/*where('shop_id',$shop_id)*//*->where('role_id',0)*/skip((int)$start)->limit((int)$length)->orderBy($sort_by,$sort_order);
        if($search!=''){
          $data->where(function($q)use($search){
            $q->where('email','like',$search.'%');
            $q->orWhere('phone','like',$search.'%');
            $q->orWhere('name','like',$search.'%');
            if(strtolower($search)=='buyer'){
              $q->orWhere('role_id',0);
            }elseif(strtolower($search)=='seller'){
              $q->orWhere('role_id',1);
            }
          });
        }
        $data = $data->get();

        foreach($data as &$row){
          $row->id = $row->id;
          $userName = isset($row->name)?$row->name:'';
          $image = isset($row->image)?$row->image:'';
          $userImage='';
          if(file_exists(public_path('/images/coupons/thumbs/'.$image)) && $image!='' && $image!=null){
            $userImage = url('/images/coupons/thumbs/'.$image); 
            
          }else{
            $userImage = url('/assets/images/logo_icon_dark.png'); 
          }
          
          
          $update = route('companies.edit',$row->id);
          $btn = "<a title='Edit' href='{$update}' class='btn btn-primary btn-xs' ><i class=\"icon-pencil\"   ></i></a>". '   ';
          if(isset($row->deleted_at) && $row->deleted_at!=''){
                // $delete =  route('companies.restore',$row->id);
            $delete =  route('companies.destroy',$row->id);
            $btn .= "<a title='Restore' href='javascript:;' data-href='{$delete}' class='btn btn-success btn-xs restoreRow'><i class=\"icon-upload\"  ></i></a>";
        }else{
            $delete =  route('companies.destroy',$row->id);
            $btn .= "<a title='Delete' href='javascript:;' data-href='{$delete}' class='btn btn-danger btn-xs deleteRow'><i class=\"icon-trash\"  ></i></a>";    
        }
        $row->phone = $row->phone;;
        if($row->is_active==1){
          $row->is_active = '<label class="label label-success">Active</label>';  
        }else{
          $row->is_active = '<label class="label label-danger">Inactive</label>';  
        }
        if($row->role_id==1){
          $row->role_id = '<label class="label label-info">Seller</label>';  
        }else{
          $row->role_id = '<label class="label label-primary">Buyer</label>';  
        }
        if($row->coupon_type==1){
          $row->coupon_type = '<label class="label label-warning">Fixed Amount</label>';  
        }else{
          $row->coupon_type = '<label class="label label-warning">Percentage</label>';  
        }
        $row->action='<div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" m-dropdown-toggle="click" aria-expanded="true">
                                                <a href="#" class="m-portlet__nav-link m-dropdown__toggle btn m-btn m-btn--link">
                                                    <i class="la la-ellipsis-v"></i>
                                                </a>
                                                <div class="m-dropdown__wrapper">
                                                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                                                    <div class="m-dropdown__inner">
                                                        <div class="m-dropdown__body">
                                                            <div class="m-dropdown__content">
                                                                <ul class="m-nav">
                                                                    <li class="m-nav__item mb-3">
                                                                        <a href="'.$update.'" class="m-nav__link">
                                                                            <i class="m-nav__link-icon">
                                                                                <span class="iconify" data-icon="fluent:rename-20-regular"></span>
                                                                            </i>
                                                                            <span class="m-nav__link-text">Edit</span>
                                                                        </a>
                                                                    </li>
                                                                    
                                                                    <!--<li class="m-nav__item mt-3">
                                                                        <a href="#" class="btn btn-outline-danger m-btn m-btn--pill m-btn--wide btn-sm"><span class="iconify" data-icon="carbon:delete"></span> Delete</a>
                                                                    </li> -->
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>';
    }

    $json=[
	// 'draw'=>1,
       'recordsTotal'=>$count_total,
       'recordsFiltered'=>$count_total,
       'data'=>$data,
   ];
   return response()->json($json,200);

   return Datatables::eloquent($data)
            /*->with([
            "recordsTotal" => $count_total,
            "recordsFiltered" => $count_total,
        ])*/
        ->addIndexColumn()
        ->editColumn('isactive', function($data) {
            $today = date("Y-m-d H:i:s");

            if( $data->end_date<=$today OR $data->start_date>=$today) {
                $isactive=false;
            }else{
                $isactive=true;

            }


            $status =($isactive)?'Active':'Not Active';

            if($status=="Active"){

                $btn = "<label  class='label label-primary label-rounded' >$status</label>";
            }else
            {
                $btn = "<label  class='label label-danger label-rounded' >$status</label>";
            }


            return ($btn);
        })
        ->addColumn('action', function($row){
            $delete =  route('companies.destroy',$row->id);
            $update = route('companies.edit',$row->id);
            $btn = "<a href='{$update}' class='btn btn-primary btn-xs' ><i class=\"icon-pencil\"   ></i></a>". '   '. "<a href='{$delete}' class='btn btn-danger btn-xs'><i class=\"icon-trash\"  ></i></a>";
            return $btn;
        })


        ->addColumn('view_teams', function($row){

            $update = route('companies.show',$row->id);
            $btn ="<a href='{$update}' class='edit' ><i class=\"fa fa-eye\" aria-hidden=\"true\" style='font-size:20px;'></i></a>";

            return $btn;
        })

        ->rawColumns(['action', 'view_teams','isactive'])
        ->make(true);
        // }
        // return redirect()->route('companies.index');
        // return view('campaigns');
    }

    public function getNextPageToken($project_id,$device_id,$token){
          $response = $this->get_event_history($project_id,$device_id,$token,'');
          $nextPageToken = isset($response['nextPageToken'])?$response['nextPageToken']:'';
          $response2 = $this->get_event_history($project_id,$device_id,$token,$nextPageToken);
          $nextPageToken2 = isset($response2['nextPageToken'])?$response2['nextPageToken']:'';
          $response3 = $this->get_event_history($project_id,$device_id,$token,$nextPageToken2);
          $nextPageToken3 = isset($response3['nextPageToken'])?$response3['nextPageToken']:'';
          $response4 = $this->get_event_history($project_id,$device_id,$token,$nextPageToken3);
          $nextPageToken4 = isset($response4['nextPageToken'])?$response4['nextPageToken']:'';
          $response5 = $this->get_event_history($project_id,$device_id,$token,$nextPageToken4);
          $nextPageToken5 = isset($response5['nextPageToken'])?$response5['nextPageToken']:'';
          $response6 = $this->get_event_history($project_id,$device_id,$token,$nextPageToken5);
          $nextPageToken6 = isset($response6['nextPageToken'])?$response6['nextPageToken']:'';
          $response7 = $this->get_event_history($project_id,$device_id,$token,$nextPageToken6);
          $nextPageToken7 = isset($response7['nextPageToken'])?$response7['nextPageToken']:'';

          $response8 = $this->get_event_history($project_id,$device_id,$token,$nextPageToken7);
          $nextPageToken8 = isset($response8['nextPageToken'])?$response8['nextPageToken']:'';

          $response9 = $this->get_event_history($project_id,$device_id,$token,$nextPageToken8);
          $nextPageToken9 = isset($response9['nextPageToken'])?$response9['nextPageToken']:'';

          /*$response10 = $this->get_event_history($project_id,$device_id,$token,$nextPageToken9);
          $nextPageToken10 = isset($response10['nextPageToken'])?$response10['nextPageToken']:'';*/
          
          $nextPageTokenArr = array($nextPageToken,$nextPageToken2,$nextPageToken3,$nextPageToken4,$nextPageToken5,$nextPageToken6,$nextPageToken7,$nextPageToken8,$nextPageToken9);
          return $nextPageTokenArr;
    }

    public function get_event_history($project_id,$device_id,$token,$nextPageToken){
        /*if($nextPageToken==''){
          return [];
        }*/
        $timestamp1 = strtotime(date('Y-m-d H:i:s', strtotime('-1 month')));
        $timestamp2 = strtotime(date('Y-m-d H:i:s'));
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $timestamp1);
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $timestamp2);
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL            => "https://api.disruptive-technologies.com/v2/projects/".$project_id."/devices/".$device_id."/events?startTime=$startTime&endTime=$endTime&pageSize=1000&pageToken=$nextPageToken",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING       => "",
          CURLOPT_MAXREDIRS      => 10,
          CURLOPT_TIMEOUT        => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST  => "GET",
          CURLOPT_HTTPHEADER     => array(
             "Authorization: Bearer ".$token,
             "Accept: application/json"
          ),
        ));

        $response = curl_exec($curl);     

        if (curl_errno($curl)) {
          echo 'Error:' . curl_error($curl);
          curl_close($curl);
        }
        else{
          $result = json_decode($response,true);
          return $result;
          curl_close($curl);
        }

    }

    public function get_sensors_clouds($project_id,$token){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL            => "https://api.disruptive-technologies.com/v2/projects/".$project_id."/devices",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING       => "",
          CURLOPT_MAXREDIRS      => 10,
          CURLOPT_TIMEOUT        => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST  => "GET",
          CURLOPT_HTTPHEADER     => array(
             "Authorization: Bearer ".$token,
             "Accept: application/json"
          ),
        ));

        $response = curl_exec($curl);     

        if (curl_errno($curl)) {
          echo 'Error:' . curl_error($curl);
          curl_close($curl);
        }
        else{
          $result = json_decode($response,true);
          return $result;
          curl_close($curl);
        }

    }

    public function get_jwt_token($service_account_id,$service_account_email,$secret_key){
        
        $header = json_encode([
            "alg" => "HS256",
            "kid" => $service_account_id,
        ]);

        // Create token payload as a JSON string
        $payload = json_encode([
            "iat"=> strtotime("now"),
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

    public function get_access_token($jwt){

        $curl = curl_init();
        
        $arr=[
            'assertion'=> $jwt,
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
          CURLOPT_POSTFIELDS =>$encoded_post,
          CURLOPT_HTTPHEADER => array(
            "Content-Type: application/x-www-form-urlencoded"
          ),
        ));
        
        $response = curl_exec($curl);
        if(curl_exec($curl) === false){
            echo 'Curl error: ' . curl_error($curl);
            curl_close($curl);
        }
        else{                   
            $result = json_decode($response,true);
            return $result;
            curl_close($curl);
        }
    }

    public function loadSearch(Request $request){
        $search = isset($request->search)?$request->search:'';
        $counter=1;
        $query = "select c.name, c.company_id,
                  count(IF(d.event_type='ccon',1,null)) as connTotal,
                  count(IF(d.event_type='temperature',1,null)) as sensorTotal
                  from companies c
                  left join devices d on (d.company_id=c.company_id)
                  where c.is_active=1
                  group by c.company_id";
        $company = DB::select($query);
    }

    public function Test(Request $request){
      return [];
      $service_account_id = 'c90q4hr8m12000e1jmhg';
      $service_account_email = 'buhptsj24te000b250bg@buh71lgoonrl27r1frqg.serviceaccount.d21s.com';
      $secret_key = '3357d008cad9410383d50b5e1658914b';
      $service_account_id = 'cb1aqhicu95g00er1cog';
      $service_account_email = 'buhptsj24te000b250bg@buh71lgoonrl27r1frqg.serviceaccount.d21s.com';
      $secret_key = '4b695c1990f74c39a011396962c186f8';
      $token = $this->get_jwt_token($service_account_id,$service_account_email,$secret_key);
      $token = $token['access_token'];
      $project_id = isset($request->id)?$request->id:'buh71lgoonrl27r1frqg';
      $deviceId = 'c095rnh6895g00821n90';
      return $result = $this->get_sensors_clouds($project_id,$token);
      /*$date = date('Y-m-d H:i:s', strtotime('2022-03-30T06:41:33.979000Z'));
      return $this->time_elapsed_string($date);*/
      // $result = $this->getNextPageToken($project_id,$deviceId,$token);
      // foreach($result as $page){
        return $history = $this->get_event_history($project_id,$deviceId,$token,'');
        $events = isset($history['events'])?$history['events']:[];
        /*echo '<pre>';
        print_r($history);
        echo '</pre>';*/
      // }
    }
}