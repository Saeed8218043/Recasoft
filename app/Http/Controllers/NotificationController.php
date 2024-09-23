<?php

namespace App\Http\Controllers;

use App\AlertHistory;
use Illuminate\Http\Request;
use App\Company;
use Carbon\Carbon;
use App\Device;
use App\Notification;
use App\NotificationDevice;
use App\CompanySettingEmail;
use App\NotificationEmail;
use DB;

class NotificationController extends Controller
{

    public function index(Request $request)
    {

        $company_id = isset($request->company_id)?$request->company_id:'';
        $company = Company::select('name','id','parent_id')->where('company_id',$company_id)->first();
        $company_name = isset($company->name)?$company->name:'';

        $notifications=Notification::where('company_id',$company_id)->get();
        foreach($notifications as $notification){
           $ND =  NotificationDevice::where('notification_id',$notification->id)->first();
          if($ND ==null){
            $action ="Delete";
            $message = "Notification ($notification->name) in company ($company_name) is deleted because it has no devices in list";
            SystemLogs($message,$notification->company_id,$action);
            $notification->delete();
          }
        }
        $notifications=Notification::where('company_id',$company_id)->get();
        return view('notifications.index',compact('company_id','company_name','company','notifications'));

    }
    public function alertHistory(Request $request){
      $company_id = isset($request->company_id)?$request->company_id:'';
      $alert_history = AlertHistory::where('company_id',$company_id)->orderBy('created_at','desc')->Paginate(30);
      $company = Company::where('company_id',$company_id)->first();
      $company_name =$company->name;
      return view('alert-history.index',compact('alert_history','company_id','company_name'));
    }
    public function createNotification(Request $request)
    {

        $company_id = isset($request->company_id)?$request->company_id:'0';
        $company = Company::select('name','id')->where('company_id',$company_id)->first();
        $company_name = isset($company->name)?$company->name:'';
        return view('notifications.create')->with('company_id',$company_id)->with('company_name',$company_name);
    }

    public function storeNotification(Request $request){
    	$isActive=false;
    	$isResolved=false;
    	if($request->isActive =='on'){
    		$isActive=true;
      }

    	if($request->isResolved =='on'){
    		$isResolved=true;
      }

		   	 // dd($request->all());
         $date = $request->input('repeat_date');
    $formattedDateTime = Carbon::parse($date)->format('Y-m-d H:i:s');
    $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
    	$notification=Notification::create([
        'isActive'=> $isActive,
    		'isResolved'=> $isResolved,
        'reminder_days'=>$request->reminder,
        'maintenance_repeat'=>$request->repeater,
        'm_date'=>$formattedDateTime
    	]+$request->all());
    	foreach ($request->ids as $device_id) {
    		NotificationDevice::create([
    			'device_id'=>$device_id,
    			'notification_id'=>$notification->id,

    		]);
    	}
    	if($request->has('emails')){

    		foreach ($request->emails as $key => $email) {
          $str = implode (",", $request->emails[$key]);
    		NotificationEmail::create([
    			'notification_id'=>$notification->id,
    			'email'=>$str,
    			'subject'=>$request->subjects[$key+1],
    			'content'=>$request->contents[$key+1],
    			'notification_type'=>0
    		]);
    	   }
    	}

    	if($request->has('sms')){
    		foreach ($request->sms as $key => $message) {
          $str = implode (",", $request->sms[$key]);
    		NotificationEmail::create([
    			'notification_id'=>$notification->id,
    			'email'=>$str,
    			'content'=>$request->smscontents[$key+1],
    			'notification_type'=>1
    		]);
    	   }
    	}
      $user =auth()->user()->name;
        $action ="Create";
        $company = Company::where('company_id',$notification->company_id)->first();
        $message = "$user created a new notification ($notification->name) in $company->name.";
        SystemLogs($message,$notification->company_id,$action);


    	return redirect()->back()->with('message','Notification Saved Successfully');
    }

    public function getDevices(Request $request){
    	// $company=Company::where('company_id',$request->company_id)->first();
    	 $where='';
        $search = isset($request->search)?$request->search:'';
        $company_id = isset($request->company_id)?$request->company_id:'';
        $company = Company::select('name','id')->where('company_id',$company_id)->first();
        $company_name = isset($company->name)?$company->name:'';
        $cID = isset($company->id)?$company->id:'';
        $user_id = \Auth::user()->id;
        $user_email = \Auth::user()->email;
        $not_connected=[];
        $gateways=[];
        // Log::info('My SensorsMessage',['users' => [$user_id,$user_email]]);
	        if($request->alert_type=='Device Monitoring (Beta)'){
	          $connected = Device::where(array('company_id'=>$company_id))->where('event_type','equipment')->where('sensor_id', '!=', '0')->orderBy('name','ASC')->get();
	          $gateways = Device::where(array('company_id'=>$company_id))->where('event_type','ccon')->orderBy('name','ASC')->get();
            }
            else if($request->alert_type=='Maintenance'){
              $connected = Device::where(array('company_id'=>$company_id))->where('event_type','equipment')->where('sensor_id', '!=', '0')->orderBy('name','ASC')->get();
	          $not_connected = Device::where(array('company_id'=>$company_id))->where('event_type','equipment')->where('sensor_id','0')->orderBy('name','ASC')->get();
              // $sensors = Device::where(array('company_id'=>$company_id))->whereIn('event_type', ['equipment'])->orderBy('name','ASC')->get();
            }else{
              $connected = Device::where(array('company_id'=>$company_id))->where('event_type','equipment')->where('sensor_id', '!=', '0')->orderBy('name','ASC')->get();
	      	// $sensors = Device::where(array('device_status'=>1,'company_id'=>$company_id,'event_type'=>'temperature'))->whereIn('event_type', ['temperature', 'ccon'])->orderBy('name','ASC')->get();
	      }

        if($search!=''){
            $connected = $connected->where('name','LIKE',"%{$search}%");
        }
        // $sensors = $sensors->get();


        $curr_date = date('Y-m-d H:i:s');
        foreach($connected as $row){
          $sensor = Device::where('device_id',$row->sensor_id)->first();
            $updated_at = isset($sensor->temeprature_last_updated)?date('Y-m-d H:i:s',strtotime($sensor->temeprature_last_updated)):'';
            $time_ago = $this->time_diff_string($updated_at,$curr_date);
            if($row->event_type!='ccon' && $row->event_type!='temperature'){
                $row->signal = $this->get_singal($sensor->signal_strength,$sensor->is_active);
            }
            if($sensor->event_type=='temperature'){
              $row->time_ago = $time_ago;
            }else{
              $row->time_ago = '';
            }

            if($sensor->event_type!='ccon'){
              $milliseconds = strtotime($updated_at)*1000;
              $row->milliseconds = $milliseconds;
            }
        }
        foreach($gateways as $row2){
          if($row2->event_type=='ccon'){
            if($row2->is_active==1){
              $row2->signal = $this->get_sensor_singal($row2->signal_strength);
            }else{
              $row2->signal = '';
            }

        }
        }



    	// if($request->alert_type=='Temperature'){
    	// 	$devices=Device::where('company_id',$request->company_id)->where('event_type','temperature')->get();

    	// }else{
    	// 	$devices=Device::where('company_id',$request->company_id)->get();

    	// }
    	$html='';
    	if(count($connected)>0){

         $html  = view('notifications.get-devices')->with('sensors',$connected)->with('type',$request->alert_type)->with('not_connected',$not_connected)->with('gateways',$gateways)->with('alert_type',$request->alert_type)->with('ids',$request->ids);

         $html  = $html->render();
    	}


    	return response()->json(['html'=>$html]);
    }

    public function searchDevices(Request $request){
    	$where='';
        $search = isset($request->search)?$request->search:'';
        $company_id = isset($request->company_id)?$request->company_id:'';
        $company = Company::select('name','id')->where('company_id',$company_id)->first();
        $company_name = isset($company->name)?$company->name:'';
        $cID = isset($company->id)?$company->id:'';
        $user_id = \Auth::user()->id;
        $user_email = \Auth::user()->email;
        // Log::info('My SensorsMessage',['users' => [$user_id,$user_email]]);
        // if (($user_id == 1) && ($user_email ==  "admin@recasoft.com")) {
	      //  if($request->alert_type!='Temperature'){
	      //   $sensors = Device::select('name','id','company_id','device_id','temperature','event_type','signal_strength','temeprature_last_updated','is_active')->where(array('device_status'=>1,'company_id'=>$company_id))->where('name','LIKE',"%{$search}%");
        //    }else{
        //    	  $sensors = Device::select('name','id','company_id','device_id','temperature','event_type','signal_strength','temeprature_last_updated','is_active')->where(array('device_status'=>1,'company_id'=>$company_id,'event_type'=>'temperature'))->where('name','LIKE',"%{$search}%");
        //    }
        // } else {
	      //   if($request->alert_type!='Temperature'){
	      //     $sensors = Device::select('name','id','company_id','device_id','temperature','event_type','signal_strength','temeprature_last_updated','is_active')->where(array('device_status'=>1,'company_id'=>$company_id))->where('name','LIKE',"%{$search}%");
	      // }else{
	      // 	$sensors = Device::select('name','id','company_id','device_id','temperature','event_type','signal_strength','temeprature_last_updated','is_active')->where(array('device_status'=>1,'company_id'=>$company_id,'event_type'=>'temperature'))->where('name','LIKE',"%{$search}%");
	      // }

        // }

          $connected=[];
          $not_connected=[];
          $gateways=[];
        if($request->alert_type=='Device Monitoring (Beta)'){
          $connected = Device::where(array('company_id'=>$company_id))->where('event_type','equipment')->where('sensor_id', '!=', '0')->where('name','LIKE',"%{$search}%")->orderBy('name','ASC')->get();
          $gateways = Device::where(array('company_id'=>$company_id))->where('event_type','ccon')->where('name','LIKE',"%{$search}%")->orderBy('name','ASC')->get();
          }
          else if($request->alert_type=='Maintenance'){
            $connected = Device::where(array('company_id'=>$company_id))->where('event_type','equipment')->where('sensor_id', '!=', '0')->where('name','LIKE',"%{$search}%")->orderBy('name','ASC')->get();
          $not_connected = Device::where(array('company_id'=>$company_id))->where('event_type','equipment')->where('sensor_id','0')->where('name','LIKE',"%{$search}%")->orderBy('name','ASC')->get();
            // $sensors = Device::where(array('company_id'=>$company_id))->whereIn('event_type', ['equipment'])->orderBy('name','ASC')->get();
          }else{
            $connected = Device::where(array('company_id'=>$company_id))->where('event_type','equipment')->where('sensor_id', '!=', '0')->where('name','LIKE',"%{$search}%")->orderBy('name','ASC')->get();
        // $sensors = Device::where(array('device_status'=>1,'company_id'=>$company_id,'event_type'=>'temperature'))->whereIn('event_type', ['temperature', 'ccon'])->orderBy('name','ASC')->get();
      }



        $curr_date = date('Y-m-d H:i:s');
        foreach($connected as $row){

            $updated_at = isset($row->temeprature_last_updated)?date('Y-m-d H:i:s',strtotime($row->temeprature_last_updated)):'';
            $time_ago = $this->time_diff_string($updated_at,$curr_date);
            if($row->event_type=='ccon'){
                if($row->is_active==1){
                  $row->signal = $this->get_sensor_singal($row->signal_strength);
                }else{
                  $row->signal = '';
                }

            }else{
                $row->signal = $this->get_singal($row->signal_strength,$row->is_active);
            }
            if($row->event_type=='temperature'){
              $row->time_ago = $time_ago;
            }else{
              $row->time_ago = '';
            }

            if($row->event_type!='ccon'){
              $milliseconds = strtotime($updated_at)*1000;
              $row->milliseconds = $milliseconds;
            }


        }




    	// if($request->alert_type=='Temperature'){
    	// 	$devices=Device::where('company_id',$request->company_id)->where('event_type','temperature')->get();

    	// }else{
    	// 	$devices=Device::where('company_id',$request->company_id)->get();

    	// }
    	$html='';
    	if(count($connected)>0){

         $html  = view('notifications.get-devices')->with('sensors',$connected)->with('connected',$connected)->with('gateways',$gateways)->with('not_connected',$not_connected)->with('alert_type',$request->alert_type)->with('ids',$request->ids);
         $html  = $html->render();
    	}

    	return response()->json(['html'=>$html]);


    }

    public function get_sensor_singal($signal){
        if($signal<=25){
            $signal_div = '<div class="sensor-bar">
                           <li class="active"></li>
                           <li class=""></li>
                           <li class=""></li>
                           <li class=""></li>
                           </div>';
        }elseif($signal>25 && $signal<=50){
            $signal_div = '<div class="sensor-bar">
                           <li class="active"></li>
                           <li class="active"></li>
                           <li class=""></li>
                           <li class=""></li>
                           </div>';
        }elseif($signal>50 && $signal<=75){
            $signal_div = '<div class="sensor-bar">
                           <li class="active"></li>
                           <li class="active"></li>
                           <li class="active"></li>
                           <li class=""></li>
                           </div>';
        }elseif($signal>75){
            $signal_div = '<div class="sensor-bar">
                           <li class="active"></li>
                           <li class="active"></li>
                           <li class="active"></li>
                           <li class="active"></li>
                           </div>';
        }else{
            $signal_div = '';
        }

        return $signal_div;
    }

    public function get_singal($signal,$is_active){
        $active='active';
        if(isset($is_active) && $is_active==0){
            $active='';
        }
        if($signal<=20){
            $signal_div = '<li class="'.$active.'"></li>
                           <li class=""></li>
                           <li class=""></li>
                           <li class=""></li>
                           <li class=""></li>';
        }elseif ($signal>20 && $signal<=40) {
            $signal_div = '<li class="'.$active.'"></li>
                           <li class="'.$active.'"></li>
                           <li class=""></li>
                           <li class=""></li>
                           <li class=""></li>';
        }elseif($signal>40 && $signal<=60){
            $signal_div = '<li class="'.$active.'"></li>
                           <li class="'.$active.'"></li>
                           <li class="'.$active.'"></li>
                           <li class=""></li>
                           <li class=""></li>';
        }elseif($signal>60 && $signal<=80){
            $signal_div = '<li class="'.$active.'"></li>
                           <li class="'.$active.'"></li>
                           <li class="'.$active.'"></li>
                           <li class="'.$active.'"></li>
                           <li class=""></li>';
        }elseif($signal>80){
            $signal_div = '<li class="'.$active.'"></li>
                           <li class="'.$active.'"></li>
                           <li class="'.$active.'"></li>
                           <li class="'.$active.'"></li>
                           <li class="'.$active.'"></li>';
        }else{
            $signal_div = '';
        }
        return $signal_div;
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



    public function getSingleDevice(Request $request)
    {
      $device=Device::find($request->device_id);
    	$html='';
    	 $curr_date = date('Y-m-d H:i:s');
    	 $updated_at = isset($device->temeprature_last_updated)?date('Y-m-d H:i:s',strtotime($device->temeprature_last_updated)):'';
           $time_ago = $this->time_diff_string($updated_at,$curr_date);

           if($device->event_type=='temperature'){
              $device->time_ago = $time_ago;
            }else{
              $device->time_ago = '';
            }

            if($device->event_type!='ccon'){
              $milliseconds = strtotime($updated_at)*1000;
              $device->milliseconds = $milliseconds;
            }

         $html  = view('notifications.added_device_list')->with('device',$device);
         $html  = $html->render();


    	return response()->json(['html'=>$html]);
    }

    public function addEmail(Request $request){
      

      // $filter_data ="SELECT ne.id, ne.notification_id,ne.subject,ne.email,n.company_id
      // FROM notification_emails ne
      // LEFT JOIN notifications n ON n.id =ne.notification_id
      // WHERE n.company_id='$request->company_id'
      //  AND ne.email LIKE
      // '%$request->email%'
      // AND ne.notification_type=0
      // GROUP BY ne.email";
      // $allEmail = DB::select($filter_data);
      // $emails =[];
      // $unique_emails=[];
      // foreach($allEmail as $key=> $value){
      //   $emails[] = explode(',',$value->email);
      //   foreach($emails as $un_email){
      //     foreach($un_email as $eml){
      //       if(!in_array($eml, $unique_emails)){
      //         array_push($unique_emails , $eml);
      //       }
      //     }

      //   }
      // }

    //   dd($emails);
      // $input = array_map("unserialize", array_unique(array_map("serialize", $emails)));
      // $serialized = array_map('serialize', $emails);
      // $unique = array_unique($serialized);
      $suggested_emails = CompanySettingEmail::where('company_id',$request->company_id)->get();
    	$html  = view('notifications.add_email')->with('notification_emails',$suggested_emails)->with('email_counter',$request->email_counter)->with('company_id',$request->company_id);
         $html  = $html->render();
         return response()->json(['html'=>$html]);
    }
   
    public function AddNotificationEmail(Request $request){
      $name = isset($request->name)?$request->name:'';
      $email = isset($request->email)?$request->email:'';
      $phone = isset($request->phone)?$request->phone:'';
      $company_name = isset($request->company_name)?$request->company_name:'';
      $company_id = isset($request->company_id)?$request->company_id:'';

      $unique_name = CompanySettingEmail::where('name',$name)->where('company_id',$company_id)->first();
      if($unique_name!=null ||$unique_name!=''){
        return redirect()->back()->with('error',"Name is already Exist. Name,email and phone should be unique.")->with('title','Already exist');
      }

      $unique_phone = CompanySettingEmail::where('phone',$phone)->where('company_id',$company_id)->first();
      if($unique_phone!=null ||$unique_phone!=''){
        return redirect()->back()->with('error',"Phone is already Exist. Name,email and phone should be unique.")->with('title','Already exist');
      }

      $unique_email = CompanySettingEmail::where('email',$email)->where('company_id',$company_id)->first();
      if($unique_email ==null || $unique_email==''){
        $company_emails = new CompanySettingEmail;
        $company_emails->email =$email;
        $company_emails->name =$name;
        $company_emails->phone =$phone;
        $company_emails->company_id =$company_id;
        $company_emails->save();
        $user =auth()->user()->name;
        $action ="Create";
        $message = "$user added email ($email) in company setting for company ($company_name).";
        SystemLogs($message,$company_id,$action);

        return redirect()->back()->with('success',"Recipient has been added successfully")->with('title','Recipient added');
      }else{
        return redirect()->back()->with('error',"This Recipient is already added in this company");
      }
    }

    public function editRecipient(Request $request){
      $id = isset($request->r_id)?$request->r_id:'';
      $name = isset($request->name)?$request->name:'';
      $email = isset($request->email)?$request->email:'';
      $phone = isset($request->phone)?$request->phone:'';
      $company_id = isset($request->company_id)?$request->company_id:'';
      $company_emails = CompanySettingEmail::where('id',$id)->first();
      $company_emails->update([
        'name'=> $name,
        'email'=> $email,
        'phone'=> $phone,
        'company_id'=> $company_id,
      ]);
      return redirect()->back()->with('success',"Recipient has been updated successfully")->with('title','Recipient updated');
       
  }

    public function suggestions(Request $request){
      return response()->json($request->all());
      $filter_data ="SELECT ne.id, ne.notification_id,ne.subject,ne.email,n.company_id
      FROM notification_emails ne
      LEFT JOIN notifications n ON n.id =ne.notification_id
      WHERE n.company_id='$request->company_id'
       AND ne.notification_type=1
      -- AND ne.email LIKE '%$request->number%'
      GROUP BY ne.email";
       $allEmail = DB::select($filter_data);
      //  dd($allEmail);
       $emails =[];
       $unique_emails=[];
       foreach($allEmail as $key=> $value){
         $emails[] = explode(',',$value->email);
         foreach($emails as $un_email){
           foreach($un_email as $eml){
             if(!in_array($eml, $unique_emails)){
               array_push($unique_emails , $eml);
             }
           }

         }
       }
      return response()->json($emails);
      // $html  = view('notifications.add_email')->with('emails',$emails);
      // $html  = $html->render();
      // return response()->json(['html'=>$html]);

    }
    public function addSMS(Request $request){
    
      $suggested_emails = CompanySettingEmail::where('company_id',$request->company_id)->get();
    	$html  = view('notifications.add_sms')->with('sms_counter',$request->sms_counter)->with('company_id',$request->company_id)->with('notification_numbers',$suggested_emails);
      // ->with('emails',$unique_emails);
         $html  = $html->render();
         return response()->json(['html'=>$html]);
    }
 
    public function Detail(Request $request){

      $notification=Notification::findOrFail($request->id);

    	$devices=$notification->devices;
    	$output='';
    	foreach ($devices as $notificationDevice) {
    		$device=Device::find($notificationDevice->device_id);
        if(isset($device) && $device->event_type!='Maintenance'){
    		$device->last_deviate_time=$notificationDevice->last_deviate_time;
        }
    		$curr_date = date('Y-m-d H:i:s');
    	 $updated_at = isset($device->temeprature_last_updated)?date('Y-m-d H:i:s',strtotime($device->temeprature_last_updated)):'';
           $time_ago = $this->time_diff_string($updated_at,$curr_date);

           if(isset($device) && $device->event_type=='temperature'){
              $device->time_ago = $time_ago;
            }else{
              isset($device)?$device->time_ago:'';
            }

            if(isset($device) && $device->event_type!='ccon'){
              $milliseconds = strtotime($updated_at)*1000;
              $device->milliseconds = $milliseconds;
            }
    		$html  = view('notifications.added_device_list')->with('device',$device);
            $output  .= $html->render();
    	}

    	$emails=$notification->emails;
    	$emailsHtml='';
    	$smsHtml='';
    	$email_counter=0;
      $select_counter=1;
    	$sms_counter=0;
      $array =[];
      $emailArray =[];
      $smsArray =[];
    	foreach ($emails as $email) {
    		 if($email->notification_type==0){

          $filter_data ="SELECT ne.id, ne.notification_id,ne.subject,ne.email,n.company_id
          FROM notification_emails ne
          LEFT JOIN notifications n ON n.id =ne.notification_id
          WHERE n.company_id='$request->company_id'
           AND ne.email LIKE
          '%$request->email%'
          AND ne.notification_type=0
          GROUP BY ne.email";
          $allEmail = DB::select($filter_data);
          // dd($allEmail);
          $emailss =[];
          $unique_emails=[];
          foreach($allEmail as $key=> $value){
            $emailss[] = explode(',',$value->email);
            foreach($emailss as $un_email){
              foreach($un_email as $eml){
                if(!in_array($eml, $unique_emails)){
                  array_push($unique_emails , $eml);
                }
              }

            }
          }
          $separated_email = [];
          $separated_email[] = explode(',', $email->email);
          foreach ($separated_email as $uemail) {
              foreach ($uemail as $allemails) {
                  $emailArray[$email_counter][] = $allemails;
              }
          }
  
          // Remove duplicates from the email array
          $emailArray[$email_counter] = array_unique($emailArray[$email_counter]);
              // $suggested_emails = array_diff($unique_emails,$array);
      $suggested_emails = CompanySettingEmail::where('company_id',$request->company_id)->get();
    		 	$html  = view('notifications.add_email')->with('email_counter',$email_counter)->with('email',$email)->with('company_id',$notification->company_id)->with('notification_emails',$suggested_emails)->with('seperated_email',$emailArray);

                $emailsHtml  .= $html->render();
                $email_counter++;
                $select_counter++;
    		 }
    		  if($email->notification_type==1){

            $filter_data ="SELECT ne.id, ne.notification_id,ne.subject,ne.email,n.company_id
            FROM notification_emails ne
            LEFT JOIN notifications n ON n.id =ne.notification_id
            WHERE n.company_id='$request->company_id'
             AND ne.email LIKE
            '%$request->email%'
            AND ne.notification_type=1
            GROUP BY ne.email";
            $allEmail = DB::select($filter_data);
            // dd($allEmail);
            $emailss =[];
          $unique_numbers=[];
          foreach($allEmail as $key=> $value){
            $emailss[] = explode(',',$value->email);
            foreach($emailss as $un_email){
              foreach($un_email as $eml){
                if(!in_array($eml, $unique_numbers)){
                  array_push($unique_numbers , $eml);
                }
              }

            }
          }

            $seperated_numbers =[];
            $seperated_numbers[] = explode(',',$email->email);
            foreach($seperated_numbers as $uemail){
              foreach($uemail as $allemails){
                $smsArray[$sms_counter][] = $allemails;
              }
            }
            $suggested_numbers = array_diff($unique_numbers,$array);
                      // Remove duplicates from the email array
          $smsArray[$sms_counter] = array_unique($smsArray[$sms_counter]);

      $notification_numbers = CompanySettingEmail::where('company_id',$request->company_id)->get();
    		 	$shtml  = view('notifications.add_sms')->with('sms_counter',$sms_counter)->with('sms',$email)->with('company_id',$notification->company_id)->with('notification_numbers',$notification_numbers);

                $smsHtml  .= $shtml->render();
                $sms_counter++;
                $select_counter++;
    		 }

    	}
    	 $company_id = isset($request->company_id)?$request->company_id:'';
        $company = Company::select('name','id')->where('company_id',$company_id)->first();
        $company_name = isset($company->name)?$company->name:'';
        return view('notifications.detail')->with('notification',$notification)->with('company_id',$company_id)->with('company_name',$company_name)->with('devicesHtml',$output)->with('devices',$devices)->with('emailsHtml',$emailsHtml)->with('email_counter',$email_counter)->with('sms_counter',$sms_counter)->with('seperated_email',$emailArray)->with('seperated_numbers',$smsArray)->with('smsHtml',$smsHtml);
        // ->with('emails',$emails);
    }

    public function updateNotification(Request $request){
      // dd($request->all());
    	$notification=Notification::find($request->notification_id);
    	if(isset($notification) && $notification!=null){

			$isActive=false;
			$isResolved=false;
    	if($request->isActive =='on'){
    		$isActive=true;
      }
    	if($request->isResolved =='on'){
    		$isResolved=true;
      }
      $date = $request->input('repeat_date');
      $formattedDateTime = Carbon::parse($date)->format('Y-m-d');
      $currentTime = Carbon::now()->format('H:i:s');
      $dateTimeToSave = $formattedDateTime . ' ' . $currentTime;
    	$notification->update([
    		'isActive'=> $isActive,
    		'isResolved'=> $isResolved,
        'reminder_days'=>$request->reminder,
        'maintenance_repeat'=>$request->repeater,
        'm_date'=>$dateTimeToSave
    	]+$request->all());

    	DB::table('notification_devices')->where('notification_id', $notification->id)->delete();
    	foreach ($request->ids as $device_id) {

    			NotificationDevice::create([
    			'device_id'=>$device_id,
    			'notification_id'=>$notification->id,

    		]);
    	}
    	if($request->has('emails')){
        DB::table('notification_emails')->where('notification_id', $notification->id)->where('notification_type',0)->delete();
    		foreach ($request->emails as $key => $email) {
          $str = implode (",", $request->emails[$key]);
    		NotificationEmail::create([
    			'notification_id'=>$notification->id,
    			'email'=>$str,
    			'subject'=>$request->subjects[$key+1],
    			'content'=>$request->contents[$key+1],
    			'notification_type'=>0
    		]);
    	   }
    	}

    	if($request->has('sms')){
    		DB::table('notification_emails')->where('notification_id', $notification->id)->where('notification_type',1)->delete();
    		foreach ($request->sms as $key => $message) {
          $str = implode (",", $request->sms[$key]);
    		NotificationEmail::create([
    			'notification_id'=>$notification->id,
    			'email'=>$str,
    			'content'=>$request->smscontents[$key+1],
    			'notification_type'=>1
    		]);
    	   }
    	}



      $user =auth()->user()->name;
      $action ="Update";
      $company = Company::where('company_id',$notification->company_id)->first();
      $message = "$user updated notification ($request->name) in $company->name.";
      SystemLogs($message,$notification->company_id,$action);
    	}
    	return redirect()->back()->with('message','Notification Updated Successfully');
    }

    public function deleteNotification($id){
    	$notification=Notification::find($id);

      $user =auth()->user()->name;
      $action ="Delete";
      $company = Company::where('company_id',$notification->company_id)->first();
      $message = "$user deleted notification ($notification->name) in $company->name.";
      SystemLogs($message,$notification->company_id,$action);
    	 DB::table('notification_devices')->where('notification_id', $notification->id)->delete();
    	 DB::table('notification_emails')->where('notification_id', $notification->id)->delete();
    	$notification->delete();
    	return redirect()->back()->with('message','Notification Deleted Successfully');

    }
}

