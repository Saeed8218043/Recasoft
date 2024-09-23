<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyMembers;
use App\NotificationDevice;
use App\InventoryEquipment;
use App\Note;
use App\DeviceDocument;
use App\Device;
use App\DeviceTemperature;
use App\Http\Controllers\Controller;
use App\Mail\RegisterUser;
use App\User;
use Cache;
use Carbon\Carbon;
use DB;
use DataTables;
use Faker\Factory as Faker;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;
use Image;
use Mail;

class SensorsController extends Controller
{
  public function __construct()
    {
         // $this->middleware('CheckAdmin');
    }

    public function index(Request $request)
    {

        $parent_company='';
        $where='';
        $search_type = isset($request->searchTypes)?$request->searchTypes:'';
        $search = isset($request->search)?$request->search:'';
        $equipment_search = isset($request->equipment_search)?$request->equipment_search:'';
        $company_id = isset($request->company_id)?$request->company_id:'';
        $company = Company::select('name','id','parent_id')->where('company_id',$company_id)->first();

        $company_name = isset($company->name)?$company->name:'';
        $cID = isset($company->id)?$company->id:'';
        $parent_id = isset($company->parent_id)?$company->parent_id:'';
        $user_id = \Auth::user()->id;
        $user_email = \Auth::user()->email;
        if (($user_id == 1) && ($user_email ==  "admin@recasoft.com")) {
          $parent_company = $company_id;
        $sensors = Device::select('name','company_id','device_id','temperature','event_type','signal_strength','temeprature_last_updated','is_active')->where(array('device_status'=>1,'company_id'=>$company_id))->orderBy('name','ASC');
        } else {
          $parent_company = $company_id;
          $member = CompanyMembers::where('comp_id',$cID)->where('user_id',$user_id)->first();
          $sensors = Device::select('name','company_id','device_id','temperature','event_type','signal_strength','temeprature_last_updated','is_active')->where(array('device_status'=>1,'company_id'=>$company_id))->orderBy('name','ASC');

        }
       if(isset($search_type) && $search_type==1){
         if($search!=''){
             $sensors = $sensors->where('name','LIKE',"%{$search}%");
            }
          }else{
             $sensors = $sensors->where('device_id','LIKE',"%{$search}%");
       }
        $sensors = $sensors->orderBy('name','ASC')->get();
        $curr_date = date('Y-m-d H:i:s');
        foreach($sensors as $row){
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
        if($parent_id>0 && $user_id > 1){
          $comp2  = Company::where('id',$parent_id)->select('company_id')->first();
          $parent_company = isset($comp2->company_id)?$comp2->company_id:'';
        }

        $currentCompany = Company::select('id','name','parent_id','company_id')->where('company_id',$company_id)->first();
        $currentCompany_name = isset($currentCompany->name)?$currentCompany->name:'';
        $currentCompany_id = isset($currentCompany->company_id)?$currentCompany->company_id:'';

         $selectedParent = isset($company->parent_id)?$company->parent_id:0;
         $members = "select * from company_members where (company_id=? or comp_id=?) and user_id=? and role=2 order by id desc";
        $members = DB::select($members,[$company_id,$selectedParent,$user_id]);
        $can_manage_users=0;
        if(isset($members[0]->id)  || $user_id==1){
            $can_manage_users=1;
        }

        $nonEmptyDevices = $sensors->filter(function ($sensor) {
          return !empty($sensor['name']);
      });
      
      $emptyNameDevices = $sensors->filter(function ($sensor) {
          return empty($sensor['name']);
      });
      
      // Sort the non-empty name devices by name in ascending order
      $sortedNonEmptyDevices = $nonEmptyDevices->sortBy('name');
      
      // Sort the empty name devices by device_id in ascending order
      $sortedEmptyNameDevices = $emptyNameDevices->sortBy('device_id');
      
      // Merge the two sorted collections into a single collection
      $sensors = $sortedNonEmptyDevices->concat($sortedEmptyNameDevices);
      $connected_sensor = [];
      $gateways = [];
      $not_connected = [];
      
      foreach ($sensors as $sensor) {
          if ($sensor->event_type == "temperature") {
              $connection = Device::where('sensor_id', $sensor->device_id)->first();
              if ($connection != null || $connection != '') {
                  $connected_sensor[] = $sensor;
              } else {
                  $not_connected[] = $sensor;
              }
          } elseif ($sensor->event_type == "ccon") {
              $gateways[] = $sensor;
          }
      }
        $equipments = Device::select('id','name','description','specification','device_id')->where('event_type','equipment')->where('company_id',$company_id);
        if($equipment_search!=''){
          $equipments =$equipments->where('name','LIKE',"%{$equipment_search}%");
      }
      $equipments = $equipments->orderBy('name','ASC')->get();
        return view('sensors.index', compact('parent_company','not_connected','connected_sensor','search_type','gateways','currentCompany_name','currentCompany_id','company_id','company_name','sensors','search','equipment_search','equipments','currentCompany','user_id','can_manage_users','selectedParent','cID'));
    }


      public function equipments(Request $request){

        $image =  QrCode::format('png');

        $qrcode = $image->format('png')
            ->errorCorrection('H')
            ->generate('123456');
    $parent_company='';
        $where='';
        $search_type = isset($request->searchTypes)?$request->searchTypes:'';
        $search = isset($request->search)?$request->search:'';
        $search_inventory = isset($request->search_inventory)?$request->search_inventory:'';
        $equipment_search = isset($request->equipment_search)?$request->equipment_search:'';
        $company_id = isset($request->company_id)?$request->company_id:'';
        $company = Company::select('name','id','parent_id')->where('company_id',$company_id)->first();

        $company_name = isset($company->name)?$company->name:'';
        $cID = isset($company->id)?$company->id:'';
        $parent_id = isset($company->parent_id)?$company->parent_id:'';
        $user_id = \Auth::user()->id;
        $user_email = \Auth::user()->email;
        if (($user_id == 1) && ($user_email ==  "admin@recasoft.com")) {
          $parent_company = $company_id;
        $sensors = Device::select('name','company_id','device_id','temperature','event_type','signal_strength','temeprature_last_updated','is_active')->where(array('device_status'=>1,'company_id'=>$company_id))->orderBy('name','ASC');
        // ->orWhere('coming_from_id',$cID);
        } else {
          $parent_company = $company_id;
          $member = CompanyMembers::where('comp_id',$cID)->where('user_id',$user_id)->first();
        
          $sensors = Device::select('name','company_id','device_id','temperature','event_type','signal_strength','temeprature_last_updated','is_active')->where(array('device_status'=>1,'company_id'=>$company_id))->orderBy('name','ASC');

        }
        $sensors = $sensors->orderBy('name','ASC')->get();
        $curr_date = date('Y-m-d H:i:s');
        foreach($sensors as $row){
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
        if($parent_id>0 && $user_id > 1){
          $comp2  = Company::where('id',$parent_id)->select('company_id')->first();
          $parent_company = isset($comp2->company_id)?$comp2->company_id:'';
        }

        function highlightSearchQuery($text, $searchQuery) {
          return preg_replace("/($searchQuery)/i", "<strong>$1</strong>", $text);
      }

        $currentCompany = Company::select('id','name','parent_id','company_id')->where('company_id',$company_id)->first();
        $currentCompany_name = isset($currentCompany->name)?$currentCompany->name:'';
        $currentCompany_id = isset($currentCompany->company_id)?$currentCompany->company_id:'';

         $selectedParent = isset($company->parent_id)?$company->parent_id:0;
         $members = "select * from company_members where (company_id=? or comp_id=?) and user_id=? and role=2 order by id desc";
        $members = DB::select($members,[$company_id,$selectedParent,$user_id]);
        $can_manage_users=0;
        if(isset($members[0]->id)  || $user_id==1){
            $can_manage_users=1;
        }
      
        $connected_equipments = Device::where('event_type','equipment')->where('sensor_id','!=','0')->where('company_id',$company_id);
        $equipments = Device::where('event_type','equipment')->where('sensor_id','=','0')->where('company_id',$company_id);
        if($equipment_search!=''){
       if($search_type==1){
          $connected_equipments =$connected_equipments->where('name','LIKE',"%{$equipment_search}%");
          $equipments =$equipments->where('name','LIKE',"%{$equipment_search}%");
        }else{
         $connected_equipments =$connected_equipments->where('device_id','LIKE',"%{$equipment_search}%");
         $equipments =$equipments->where('device_id','LIKE',"%{$equipment_search}%");
       }
        // Modify the inventory_equipments collection to include the highlighted and bolded search term
  
      }
      $connected_equipments = $connected_equipments->orderBy('name','ASC')->get();
      $equipments = $equipments->orderBy('name','ASC')->get();
      if($equipment_search!=''){
      foreach ($connected_equipments as $equipment) {
        $equipment->name = highlightSearchQuery($equipment->name, $equipment_search);
        $equipment->description = highlightSearchQuery($equipment->description, $equipment_search);
        $equipment->specification = highlightSearchQuery($equipment->specification, $equipment_search);
      }
      foreach ($equipments as $equipment) {
        $equipment->name = highlightSearchQuery($equipment->name, $equipment_search);
        $equipment->description = highlightSearchQuery($equipment->description, $equipment_search);
        $equipment->specification = highlightSearchQuery($equipment->specification, $equipment_search);
      }
    }



      $query ='';
      if(auth()->user()->id ==1){
        $query = Device::where('company_id',$company_id)->where('event_type','inventory');
        }else{
        $query = Device::where('company_id', $company_id)->where('event_type', 'inventory')->where('user_id', auth()->user()->id);
      }

     
    $inventory_equipments = $query->get();
      if ($search_inventory!='') {
              $query->where(function($query) use ($search_inventory) {
                $query->where('name', 'LIKE', "%{$search_inventory}%")
                        ->orWhere('description', 'LIKE', "%{$search_inventory}%")
                        ->orWhere('specification', 'LIKE', "%{$search_inventory}%");
              });
          $inventory_equipments = $query->get();
    // Modify the inventory_equipments collection to include the highlighted and bolded search term
    foreach ($inventory_equipments as $equipment) {
        $equipment->name = highlightSearchQuery($equipment->name, $search_inventory);
        $equipment->description = highlightSearchQuery($equipment->description, $search_inventory);
        $equipment->specification = highlightSearchQuery($equipment->specification, $search_inventory);
    }
  }

        return view('equipments.index', compact('parent_company','search_type','search_inventory','inventory_equipments','currentCompany_name','currentCompany_id','company_id','company_name','sensors','search','equipment_search','equipments','connected_equipments','currentCompany','user_id','qrcode','can_manage_users','selectedParent','cID'));
      }
    function compareDevices($device1, $device2) {
      if (empty($device1['name'])) {
          return 1;
      } elseif (empty($device2['name'])) {
          return -1;
      } else {
          return strcasecmp($device1['name'], $device2['name']);
      }
  }
  
    public function equipmentStore(Request $request){
        // dd($request->all());
        $device =New Device;
        $device->name = isset($request->equipment_name)?$request->equipment_name:'';
        $device->company_id = isset($request->company_id)?$request->company_id:'';
        $device->description = isset($request->description)?$request->description:'';
        $device->specification = isset($request->specification)?$request->specification:'';
        $device->device_id = isset($request->equipment_id)?$request->equipment_id:'';
        $device->event_type = 'equipment';
        $device->save();
        $user =auth()->user()->name;
        $action ="Create";
        $company = Company::where('company_id',$device->company_id)->first();
        $message = "$user create equipment ($device->name) in $company->name";
        SystemLogs($message,$device->company_id,$action);

        return back();
    }
    public function inventoryStore(Request $request){
        $device =New Device;
        $device->name = isset($request->equipment_name)?$request->equipment_name:'';
        $device->company_id = isset($request->company_id)?$request->company_id:'';
        $device->description = isset($request->description)?$request->description:'';
        $device->specification = isset($request->specification)?$request->specification:'';
        $device->device_id = isset($request->equipment_id)?$request->equipment_id:'';
        $device->event_type = 'inventory';
        $device->user_id = auth()->user()->id;
        $device->save();
        $name = isset($request->sensor_doc_name)?$request->sensor_doc_name:'';
        $request->validate([
          'sensor_doc_name' => 'required',
          'sensor_doc' => 'max:5128',
          'sensor_doc' => 'mimes:doc,pdf,docx,zip,png,jpg,jpeg'
      ]);
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
        $message = "$user create equipment inventory ($device->name) in $company->name";
        SystemLogs($message,$device->company_id,$action);

        return back();
    }
    public function deleteEquipment(Request $request){
      $id = isset($request->eID)?$request->eID:'';
      $device = Device::where('id',$id)->first();
      $action ="Delete";
      $user =auth()->user()->name;
      $company = Company::where('company_id',$device->company_id)->first();
      $message = "$user delete equipment ($device->name) in $company->name";
      SystemLogs($message,$device->company_id,$action);
      DeviceDocument::where('device_id',$device->id)->delete();
      Note::where('device_id',$device->id)->delete();
      NotificationDevice::where('device_id',$id)->delete();
      Device::where('id',$id)->delete();
      return back();
    }
    public function inventoryDelete(Request $request){
      $inventory_id = isset($request->eID)?$request->eID:'';
      $device = Device::where('id',$inventory_id)->first();
      $action ="Delete";
      $user =auth()->user()->name;
      $company = Company::where('company_id',$device->company_id)->first();
      $message = "$user delete equipment inventory ($device->name) in $company->name";
      SystemLogs($message,$device->company_id,$action);
      DeviceDocument::where('device_id',$device->id)->delete();
      Note::where('device_id',$device->id)->delete();
      NotificationDevice::where('device_id',$inventory_id)->delete();
      Device::where('id',$inventory_id)->delete();
      return back();
    }

    public function Details(Request $request){
      $parent_company='';
        $curr_date = date('Y-m-d H:i:s');
        $company_id = isset($request->company_id)?$request->company_id:'';
        $device_id = isset($request->device_id)?$request->device_id:'';
        $currentCompany = $company = Company::select('name','parent_id','company_id')->where('company_id',$company_id)->first();
        $device = Device::where('device_id',$device_id)->first();
        $company_name = isset($company->name)?$company->name:'';
        // 'device_status'=>1,
        if(\Auth::id()==1){
          $parent_company=$company_id;
          $sensor = Device::where(array('device_id'=>$device_id))->first();
        }else{
          $sensor = Device::where(array('company_id'=>$company_id,'device_id'=>$device_id))->first();
        }

        if(!$sensor){
          return redirect()->route('home',['company_id'=>$company_id]);
        }
        $battery_updated_datetime = '';
        if($sensor != ''){
          $battery_updated_datetime = $this->time_diff_string($sensor->battery_updated_datetime,$curr_date);
        }
        $email = \Auth::user()->email;
        $sql = DB::table('company_members_invite')->where('email',$email)->pluck('company_id')->toArray();
        $companies = Company::where('is_active',1)->select('id','company_id','name','parent_id');
        if(isset($sql) && count($sql)>0){
            $companies = $companies->whereIN('company_id',$sql);
        }
        $companies = $companies->orderBy('name','ASC')->get();
        $cloudConnectors = DB::select('select distinct cloudConnector from device_temperature where device_id = ?', [$device_id]);
        $connectors = $connectorAr = [];
        if(isset($cloudConnectors) && count($cloudConnectors)>0){
          foreach($cloudConnectors as $cloudConnector){
            if(isset($cloudConnector->cloudConnector) && $cloudConnector->cloudConnector!=''){
              $connectorAr[] = $cloudConnector->cloudConnector;
            }
          }
          //dd($connectorAr);
          if(count($connectorAr)>0){
            $connectors = Device::whereIn('device_id', $connectorAr)->get();
    
            // Add missing connectors to $connectors array
            $missingConnectors = array_diff($connectorAr, $connectors->pluck('device_id')->toArray());
            foreach ($missingConnectors as $missingConnector) {
                $missingDevice = new Device();
                $missingDevice->device_id = $missingConnector;
                $connectors->push($missingDevice);
            }
            
           // dd($connectors);
          }
        }
        $user_id = \Auth::user()->id;
        $company = Company::select('name','id','parent_id')->where('company_id',$company_id)->first();
        $company_name = isset($company->name)?$company->name:'';
        $selectedParent = isset($company->parent_id)?$company->parent_id:0;
        $cID = isset($company->id)?$company->id:0;
        if($user_id==1){

          $sensors = Device::select('name','company_id','device_id','event_type','is_active')->where(array('device_status'=>1,'company_id'=>$company_id))->orderBy('name','ASC')->get();
          // $sensors = Device::select('name','company_id','device_id','event_type','is_active')->where(array('device_status'=>1))
          // ->where(function($q) use($company_id,$cID){
          //   $q->where(array('company_id'=>$company_id));//'device_status'=>1,
          //   if($cID>0){
          //     $q->orWhere(array('coming_from_id'=>$cID));//'device_status'=>1,
          //   }

          // })
          // ->orderBy('name','ASC')
          // ->get();
        }else{
          $sensors = Device::select('name','company_id','device_id','event_type','is_active')->where(array('device_status'=>1,'company_id'=>$company_id))->orderBy('name','ASC')->get();
        }

        $members = "select * from company_members where (company_id=? or comp_id=?) and user_id=? and role=2 order by id desc";
        $members = DB::select($members,[$company_id,$selectedParent,$user_id]);
        $can_manage_users=0;
        if(isset($members[0]->id)  || $user_id==1){
            $can_manage_users=1;
        }

        $members = "select * from company_members where comp_id=? and user_id=? and role=2 order by id desc";
        $members = DB::select($members,[$selectedParent,$user_id]);
        $can_move_sensor=1;
        if(isset($members[0]->id)){
            $can_move_sensor=0;
        }
        if($selectedParent>0){
          $comp2  = Company::where('id',$selectedParent)->select('company_id')->first();
          $parent_company = isset($comp2->company_id)?$comp2->company_id:'';
        }
        $currentComp= Company::where('company_id',$company_id)->first();
        $par_comp= Company::where('id',$currentComp->parent_id)->first();
        if(isset($par_comp)){
          $setting = \App\CompanySetting::where('company_id',$par_comp->company_id)->where('meta_key','email')->first();
          $CompanyAdminEmail = isset($setting->meta_value)?$setting->meta_value:'';
        }else{
          $setting = \App\CompanySetting::where('company_id',$company_id)->where('meta_key','email')->first();
          $CompanyAdminEmail = isset($setting->meta_value)?$setting->meta_value:'';
        }
        //dd($connectors);
        $json = array();
      $device_id = isset($request->device_id)?$request->device_id:'';
      $val = isset($request->val)?$request->val:'';

      $Device = Device::where('device_id',$device_id)->select('event_type')->first();
      $device_type = isset($Device->event_type)?$Device->event_type:'';
        $startTime = date('Y-m-d H:i:s', strtotime('-30 day'));
          $endTime = date('Y-m-d H:i:s');
          $where = " and (created_at>='".$startTime."' and created_at<='".$endTime."') ";
    $query = "select signal_strength, created_at,cloudConnector from device_temperature where signal_strength>0 and device_id=? and type !='temperature' $where order by created_at asc";
      $query = DB::select($query,array($device_id));
      $availed = $final=[];
      $finalData=[];
      $available_ccon =[];
      if(isset($query) && count($query)>0){
        $timeGroup=[];
          foreach($query as $row){
            $cloudConnector = isset($row->cloudConnector)?$row->cloudConnector:'';
            $created_at = isset($row->created_at)?strtotime($row->created_at):'';
            $timeGroup[$created_at][]=$row;
          }
          foreach($timeGroup as $time=>$ar){
            $connectedPicked=$availed;
          foreach($ar as $key=>$row){
              $cloudConnector = isset($row->cloudConnector)?$row->cloudConnector:'';
              
              $signal_strength = isset($row->signal_strength)?(float)$row->signal_strength:0;
              $created_at = isset($row->created_at)?strtotime($row->created_at):'';
              $time_stamp = $created_at*1000;
              if(($cloudConnector!='' && $device_type!='ccon') OR $device_type=='ccon'){
                if(!isset($availed[$cloudConnector])){
                  $available_ccon[$cloudConnector]=$cloudConnector;
                  
                }
                $final[$cloudConnector][]=array($time_stamp,$signal_strength);
                if(isset($connectedPicked[$cloudConnector])){
                  unset($connectedPicked[$cloudConnector]);
                }
             
                
              }
              
              }
              foreach($connectedPicked as $rem){
                $time_stamp = $time*1000;
                  $final[$rem][]=array($time_stamp,null);
              }

              //$json[] = array($time_stamp,$signal_strength);
            }
          }
          $nonEmptyDevices = $sensors->filter(function ($sensor) {
            return !empty($sensor['name']);
        });
        
        $emptyNameDevices = $sensors->filter(function ($sensor) {
            return empty($sensor['name']);
        });
        
        // Sort the non-empty name devices by name in ascending order
        $sortedNonEmptyDevices = $nonEmptyDevices->sortBy('name');
        
        // Sort the empty name devices by device_id in ascending order
        $sortedEmptyNameDevices = $emptyNameDevices->sortBy('device_id');
        
        // Merge the two sorted collections into a single collection
        $sensors = $sortedNonEmptyDevices->concat($sortedEmptyNameDevices);

        $connected = [];
        $gateways = [];
        $not_connected = [];
        
        foreach ($sensors as $single_sensor) {
          if ($single_sensor->event_type == "temperature") {
              $connection = Device::where('sensor_id', $single_sensor->device_id)->first();
              if ($connection != null || $connection != '') {
                  $connected[] = $single_sensor;
              } else {
                  $not_connected[] = $single_sensor;
              }
          } elseif ($single_sensor->event_type == "ccon") {
              $gateways[] = $single_sensor;
          }
      }
        $equipments = Device::select('name','company_id','device_id','event_type')->where('company_id',$company_id)->where('event_type','equipment')->orderBy('name','ASC')->get();
        
        $equipment = Device::where('sensor_id',$sensor->device_id)->first();
        $role2='';
        $is_valid = 0;
        $currentRouteName = $request->route()->getName();
        $company = \App\Company::where(['company_id' => $company_id])->first();
        if (isset($company->id)) {
            $is_valid = 1;
        }

        $user_ID = \Auth::user()->id;
            $user_Role = '';
            if ($company_id != '') {
                $user_Role = \App\CompanyMembers::where([
                    'company_id' => $company_id,
                    'user_id' => $user_ID,
                    // , 'company_name' => $company_name
                ])
                    ->select('role')
                    ->first();
            }

            if( isset($company) && $company->parent_id !=0){
                $child_company = \App\Company::where(['company_id' => $company_id])->first();
            }

            if(isset($child_company) && $child_company->parent_id !=0){
                $role2 = 'valid';
            }
        return view('sensors.details', compact('parent_company','currentCompany','role2','connected','gateways','not_connected','companies','company_id','company_name','sensors','sensor','battery_updated_datetime','connectors','available_ccon','can_manage_users','user_id','cID','equipments','equipment','selectedParent','can_move_sensor','CompanyAdminEmail'));
    }

    public function equipmentDetails(Request $request){
      $parent_company='';
        $curr_date = date('Y-m-d H:i:s');
        $company_id = isset($request->company_id)?$request->company_id:'';
        $device_id = isset($request->device_id)?$request->device_id:'';
        $currentCompany = $company = Company::select('name','parent_id','company_id')->where('company_id',$company_id)->first();
        $device = Device::where('device_id',$device_id)->first();
        $company_name = isset($company->name)?$company->name:'';
        // 'device_status'=>1,
        if(\Auth::id()==1){
          $parent_company=$company_id;
          $sensor = Device::where(array('device_id'=>$device_id))->first();
        $sensor_id = isset($sensor->sensor_id)?$sensor->sensor_id:'';
          $connected_sensor = Device::where('device_id',$sensor_id)->first();
        }else{
          $sensor = Device::where(array('company_id'=>$company_id,'device_id'=>$device_id))->first();
        $sensor_id = isset($sensor->sensor_id)?$sensor->sensor_id:'';
          $connected_sensor = Device::where('device_id',$sensor_id)->first();
        }

        if(!$sensor){
          return redirect()->route('home',['company_id'=>$company_id]);
        }
        $battery_updated_datetime = '';
        if($sensor != ''){
          $battery_updated_datetime = $this->time_diff_string($sensor->battery_updated_datetime,$curr_date);
        }
        $email = \Auth::user()->email;
        $sql = DB::table('company_members_invite')->where('email',$email)->pluck('company_id')->toArray();
        $companies = Company::where('is_active',1)->select('id','company_id','name','parent_id');
        if(isset($sql) && count($sql)>0){
            $companies = $companies->whereIN('company_id',$sql);
        }
        $connected_sensor = Device::where('device_id',$sensor->sensor_id)->first();
        $connection_id = isset($connected_sensor)?$connected_sensor->device_id:'';
        $companies = $companies->orderBy('name','ASC')->get();
        $cloudConnectors = DB::select('select distinct cloudConnector from device_temperature where device_id = ?', [$connection_id]);
        $connectors = $connectorAr = [];
        if(isset($cloudConnectors) && count($cloudConnectors)>0){
          foreach($cloudConnectors as $cloudConnector){
            if(isset($cloudConnector->cloudConnector) && $cloudConnector->cloudConnector!=''){
              $connectorAr[] = $cloudConnector->cloudConnector;
            }
          }
          if(count($connectorAr)>0){
            $connectors = Device::whereIn('device_id', $connectorAr)->get();
    
            // Add missing connectors to $connectors array
            $missingConnectors = array_diff($connectorAr, $connectors->pluck('device_id')->toArray());
            foreach ($missingConnectors as $missingConnector) {
                $missingDevice = new Device();
                $missingDevice->device_id = $missingConnector;
                $connectors->push($missingDevice);
            }
            
           // dd($connectors);
          }
        }

        $user_id = \Auth::user()->id;
        $company = Company::select('name','id','parent_id')->where('company_id',$company_id)->first();
        $company_name = isset($company->name)?$company->name:'';
        $selectedParent = isset($company->parent_id)?$company->parent_id:0;
        $cID = isset($company->id)?$company->id:0;
        if($user_id==1){

          $sensors = Device::where(array('device_status'=>1,'company_id'=>$company_id))->orderBy('name','ASC')->get();
          // $sensors = Device::select('name','company_id','device_id','event_type','is_active')->where(array('device_status'=>1))
          // ->where(function($q) use($company_id,$cID){
          //   $q->where(array('company_id'=>$company_id));//'device_status'=>1,
          //   if($cID>0){
          //     $q->orWhere(array('coming_from_id'=>$cID));//'device_status'=>1,
          //   }

          // })
          // ->orderBy('name','ASC')
          // ->get();
        }else{
          $sensors = Device::where(array('device_status'=>1,'company_id'=>$company_id))->orderBy('name','ASC')->get();
        }

        $members = "select * from company_members where (company_id=? or comp_id=?) and user_id=? and role=2 order by id desc";
        $members = DB::select($members,[$company_id,$selectedParent,$user_id]);
        $can_manage_users=0;
        if(isset($members[0]->id)  || $user_id==1){
            $can_manage_users=1;
        }

        $members = "select * from company_members where comp_id=? and user_id=? and role=2 order by id desc";
        $members = DB::select($members,[$selectedParent,$user_id]);
        $can_move_sensor=1;
        if(isset($members[0]->id)){
            $can_move_sensor=0;
        }
        if($selectedParent>0){
          $comp2  = Company::where('id',$selectedParent)->select('company_id')->first();
          $parent_company = isset($comp2->company_id)?$comp2->company_id:'';
        }
        $currentComp= Company::where('company_id',$company_id)->first();
        $par_comp= Company::where('id',$currentComp->parent_id)->first();
        if(isset($par_comp)){
          $setting = \App\CompanySetting::where('company_id',$par_comp->company_id)->where('meta_key','email')->first();
          $CompanyAdminEmail = isset($setting->meta_value)?$setting->meta_value:'';
        }else{
          $setting = \App\CompanySetting::where('company_id',$company_id)->where('meta_key','email')->first();
          $CompanyAdminEmail = isset($setting->meta_value)?$setting->meta_value:'';
        }
        //dd($connectors);
        $json = array();
      $device_id = isset($request->device_id)?$request->device_id:'';
      $val = isset($request->val)?$request->val:'';

      $Device = Device::where('device_id',$device_id)->select('event_type')->first();
      $device_type = isset($Device->event_type)?$Device->event_type:'';
        $startTime = date('Y-m-d H:i:s', strtotime('-30 day'));
          $endTime = date('Y-m-d H:i:s');
          $where = " and (created_at>='".$startTime."' and created_at<='".$endTime."') ";
    $query = "select signal_strength, created_at,cloudConnector from device_temperature where signal_strength>0 and device_id=? and type !='temperature' $where order by created_at asc";
      $query = DB::select($query,array($device_id));
      $availed = $final=[];
      $finalData=[];
      $available_ccon =[];
      if(isset($query) && count($query)>0){
        $timeGroup=[];
          foreach($query as $row){
            $cloudConnector = isset($row->cloudConnector)?$row->cloudConnector:'';
            $created_at = isset($row->created_at)?strtotime($row->created_at):'';
            $timeGroup[$created_at][]=$row;
          }
          foreach($timeGroup as $time=>$ar){
            $connectedPicked=$availed;
          foreach($ar as $key=>$row){
              $cloudConnector = isset($row->cloudConnector)?$row->cloudConnector:'';
              
              $signal_strength = isset($row->signal_strength)?(float)$row->signal_strength:0;
              $created_at = isset($row->created_at)?strtotime($row->created_at):'';
              $time_stamp = $created_at*1000;
              if(($cloudConnector!='' && $device_type!='ccon') OR $device_type=='ccon'){
                if(!isset($availed[$cloudConnector])){
                  $available_ccon[$cloudConnector]=$cloudConnector;
                  
                }
                $final[$cloudConnector][]=array($time_stamp,$signal_strength);
                if(isset($connectedPicked[$cloudConnector])){
                  unset($connectedPicked[$cloudConnector]);
                }
            
                
              }
              
              }
              foreach($connectedPicked as $rem){
                $time_stamp = $time*1000;
                  $final[$rem][]=array($time_stamp,null);
              }

            }
          }

          $connected_equipments = Device::where('event_type','equipment')->where('sensor_id','!=','0')->where('company_id',$company_id)->orderBy('name','ASC')->get();
          $other_equipments = Device::where('event_type','equipment')->where('sensor_id','=','0')->where('company_id',$company_id)->orderBy('name','ASC')->get();
          if(auth()->user()->id ==1){
            $inventory_equipments = Device::where('company_id',$company_id)->where('event_type','inventory')->get();
          }else{
            $inventory_equipments = Device::where('company_id',$company_id)->where('event_type','inventory')->where('user_id',auth()->user()->id)->get();
          }
        $equipments = Device::select('name','company_id','device_id','event_type')->where('company_id',$company_id)->where('event_type','equipment')->orderBy('name','ASC')->get();
        return view('equipments.details', compact('parent_company','inventory_equipments','connected_sensor','currentCompany','companies','company_id','company_name','sensors','sensor','battery_updated_datetime','connectors','other_equipments','connected_equipments','available_ccon','can_manage_users','user_id','cID','equipments','selectedParent','can_move_sensor','CompanyAdminEmail'));
    }
 
    public function connectWithSensor(Request $request){
      $user_id = \Auth::user()->id;
        $company_id = isset($request->company_id) ? $request->company_id : '';
        $currentCompany = Company::select('name', 'company_id')->where('company_id', $company_id)->first();
        $parent_company='';
        $where='';
        $company_id = isset($request->company_id)?$request->company_id:'';
        $company = Company::select('name','id','parent_id')->where('company_id',$company_id)->first();

        $company_name = isset($company->name)?$company->name:'';
        $cID = isset($company->id)?$company->id:'';
        $parent_id = isset($company->parent_id)?$company->parent_id:'';
        $user_id = \Auth::user()->id;
        $user_email = \Auth::user()->email;
        //Log::info('My SensorsMessage',['users' => [$user_id,$user_email]]);
        if (($user_id == 1) && ($user_email ==  "admin@recasoft.com")) {
          $parent_company = $company_id;
        $sensors = Device::select('name','company_id','device_id','temperature','event_type','signal_strength','temeprature_last_updated','is_active')->where(array('device_status'=>1,'company_id'=>$company_id,'event_type'=>'Temperature'))->orderBy('name','ASC');
        // ->orWhere('coming_from_id',$cID);
        } else {
          $parent_company = $company_id;
          $member = CompanyMembers::where('comp_id',$cID)->where('user_id',$user_id)->first();
          if(!$member && $parent_id==0){
            abort(404);
          }elseif(!$member && $parent_id>0){
            $member = CompanyMembers::where('comp_id',$parent_id)->where('user_id',$user_id)->first();
            if(!$member){
              abort(404);
            }
          }
          $sensors = Device::select('name','company_id','device_id','temperature','event_type','signal_strength','temeprature_last_updated','is_active')->where(array('device_status'=>1,'company_id'=>$company_id,'event_type'=>'Temperature'))->orderBy('name','ASC');

        }
        $sensors = $sensors->orderBy('name','ASC')->get();
        $html = view('equipments.connectionTable',compact('parent_company','company_id','company_name','sensors','currentCompany','user_id','cID'));
        $html = $html->render();

        return response()->json(['html'=>$html]);
    }

    public function connectWithEquipment(Request $request){
      $company_id = isset($request->company_id) ? $request->company_id : '';
      $equipments = Device::where('company_id',$company_id)->where('event_type','equipment')->orderBy('name','ASC')->get();
      //dd($equipments);
        $html = view('sensors.connectionTable',compact('equipments','company_id'));
        $html = $html->render();

        return response()->json(['html'=>$html]);
    }
public function sensorConnection(Request $request){
  $device_id = isset($request->eID)?$request->eID:'';
  $connection_id = isset($request->sensor_id)?$request->sensor_id:'';
  $device = Device::where('device_id',$device_id)->first();
  $equipment = Device::where('device_id',$connection_id)->first();
  $company = Company::where('company_id',$device->company_id)->first();
  $user =auth()->user()->name;
  $action ="Create";
  $d_name='';
  $message='';
  if($device->event_type =='temperature'){
    $d_name = !empty($device->name)?$device->name:$device->device_id;
    $message = "$user create connection Sensor ($d_name) with equipment ($equipment->name) in $company->name";
  }else{
    $d_name = !empty($equipment->name)?$equipment->name:$equipment->device_id;
    $message = "$user create connection Sensor ($d_name) with equipment ($device->name) in $company->name";
  }
  
  SystemLogs($message,$device->company_id,$action);
  if(isset($device) && $device->event_type =='equipment'){
    $already_connect = Device::where('sensor_id',$connection_id)->first();
    if($already_connect!=null || $already_connect!=''){
      $already_connect->update([
        'sensor_id'=>0
      ]);
    }
    $device->update([
      'sensor_id'=>$connection_id
    ]);
  }elseif(isset($device) && $device->event_type =='temperature'){
    $already_connect = Device::where('sensor_id',$device_id)->first();
    if($already_connect!=null || $already_connect!=''){
      $already_connect->update([
        'sensor_id'=>0
      ]);
    }

    $equipment->update([
      'sensor_id'=>$device_id
    ]);
  }

  return back();
}

public function sensorDisconnect(Request $request){
  $equipment_id = isset($request->eID)?$request->eID:'';
  $device = Device::where('device_id',$equipment_id)->first();
  $sensor = Device::where('device_id',$device->sensor_id)->first();
  $d_name ='';
  if($device->event_type =='temperature'){
    $notification_devices=NotificationDevice::where('device_id',$device->id)->delete();
    $d_name = !empty($device->name)?$device->name:$device->device_id;
    $equipment = Device::where('sensor_id',$device->device_id)->first();
  }else{
    $notification_devices=NotificationDevice::where('device_id',$sensor->id)->delete();
    $d_name = !empty($sensor->name)?$sensor->name:$sensor->device_id;
    $equipment = $device;
  }
   $company = Company::where('company_id',$device->company_id)->first();
   $user =auth()->user()->name;
   $action ="Delete";
   $message = "$user disconnect Sensor ($d_name) with equipment ($equipment->name) in $company->name";
   SystemLogs($message,$device->company_id,$action);

  $equipment->update([
    'sensor_id'=>0
  ]);
  return back();
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

    public function getHistoryData(Request $request){
      ob_start('ob_gzhandler');
      $json = array();
      $device_id = isset($request->device_id)?$request->device_id:'';
      $val = isset($request->val)?$request->val:'week';
      $where='';
      $chart_duration = 7 * 24 * 60 * 60 * 1000; // default chart duration is 1 week
  
      if($val=='hour'){
          $startTime = date('Y-m-d H:i:s', strtotime('-1 hour'));
          $endTime = date('Y-m-d H:i:s');
          $where = " and ((created_at)>='".$startTime."' and (created_at)<='".$endTime."') ";
          $chart_duration = 60 * 60 * 1000; // chart duration is 1 hour
      }
      elseif($val=='day'){
          $startTime = date('Y-m-d H:i:s', strtotime('-1 day'));
          $endTime = date('Y-m-d H:i:s');
          $where = " and ((created_at)>='".$startTime."' and (created_at)<='".$endTime."') ";
          $chart_duration = 24 * 60 * 60 * 1000; // chart duration is 1 day
      }
      elseif($val=='week'){
          $startTime = date('Y-m-d H:i:s', strtotime('-7 day'));
          $endTime = date('Y-m-d H:i:s');
          $where = " and ((created_at)>='".$startTime."' and (created_at)<='".$endTime."') ";
          $chart_duration = 7 * 24 * 60 * 60 * 1000; // chart duration is 1 week
      }
      elseif($val=='month'){
          $startTime = date('Y-m-d H:i:s', strtotime('-30 day'));
          $endTime = date('Y-m-d H:i:s');
          $where = " and (created_at>='".$startTime."' and created_at<='".$endTime."') ";
          $chart_duration = 30 * 24 * 60 * 60 * 1000; // chart duration is 1 month
      }
      
      $query = "select temperature, created_at from device_temperature where temperature is not null and device_id=? and type='temperature' $where order by created_at asc";
      $query = DB::select($query,array($device_id));
  
      if(isset($query) && count($query)>0){
          $json = array();
          $previous_timestamp = 0;
          foreach($query as $row){
              $temperature = isset($row->temperature)?(float)$row->temperature:0;
              $created_at = isset($row->created_at)?strtotime($row->created_at):'';
              $time_stamp = $created_at*1000;
  
              // Check if there is a gap of more than 30 minutes since the previous value
              if ($previous_timestamp > 0 && $time_stamp - $previous_timestamp > 30 * 60 * 1000) {
                  // Add null values at 15-minute intervals
                  $null_timestamp = $previous_timestamp + 15 * 60 * 1000;
                  while ($null_timestamp < $time_stamp) {
                      $json[] = array($null_timestamp, null);
                      $null_timestamp += 15 * 60 * 1000;
                  }
              }
  
              $json[] = array($time_stamp,$temperature);
              $previous_timestamp = $time_stamp;
          }
  
          // Add null values after
          return $json;
        }
}
    public function getHistoryDataConnector(Request $request){
        $json = array();
        $device_id = isset($request->device_id)?$request->device_id:'';
        $val = isset($request->val)?$request->val:'';
        $where='';
        if($val=='5min'){
            $startTime = date('Y-m-d H:i:s', strtotime('-5 minutes'));
            $endTime = date('Y-m-d H:i:s');
            $where = " and ((created_at)>='".$startTime."' and (created_at)<='".$endTime."') ";
        }
        if($val=='hour'){
            $startTime = date('Y-m-d H:i:s', strtotime('-1 hour'));
            $endTime = date('Y-m-d H:i:s');
            $where = " and ((created_at)>='".$startTime."' and (created_at)<='".$endTime."') ";
        }
        if($val=='day'){
            $startTime = date('Y-m-d H:i:s', strtotime('-1 day'));
            $endTime = date('Y-m-d H:i:s');
            $where = " and (created_at>='".$startTime."' and created_at<='".$endTime."') ";
        }
        if($val=='week'){
            $startTime = date('Y-m-d H:i:s', strtotime('-7 day'));
            $endTime = date('Y-m-d H:i:s');
            $where = " and (created_at>='".$startTime."' and created_at<='".$endTime."') ";
        }
        if($val=='month'){
            $startTime = date('Y-m-d H:i:s', strtotime('-30 day'));
            $endTime = date('Y-m-d H:i:s');
            $where = " and (created_at>='".$startTime."' and created_at<='".$endTime."') ";
        }
        $query = "select signal_strength, created_at from device_temperature where signal_strength>0 and device_id=? and type !='temperature' $where order by created_at asc";
        $query = DB::select($query,array($device_id));
        if(isset($query) && count($query)>0){
            foreach($query as $row){
                $signal_strength = isset($row->signal_strength)?(float)$row->signal_strength:0;
                $created_at = isset($row->created_at)?strtotime($row->created_at):'';
                $time_stamp = $created_at*1000;
                $json[] = array($time_stamp,$signal_strength);
            }
        }

        return $json;
    }

    public function getHistoryDataConnector_sensor(Request $request){
      $json = array();
      $device_id = isset($request->device_id)?$request->device_id:'';
      $val = isset($request->val)?$request->val:'';

      $Device = Device::where('device_id',$device_id)->select('event_type')->first();
      $device_type = isset($Device->event_type)?$Device->event_type:'';

      $where='';
      if($val=='5min'){
          $startTime = date('Y-m-d H:i:s', strtotime('-5 minutes'));
          $endTime = date('Y-m-d H:i:s');
          $where = " and ((created_at)>='".$startTime."' and (created_at)<='".$endTime."') ";
      }
      if($val=='hour'){
          $startTime = date('Y-m-d H:i:s', strtotime('-1 hour'));
          $endTime = date('Y-m-d H:i:s');
          $where = " and ((created_at)>='".$startTime."' and (created_at)<='".$endTime."') ";
      }
      if($val=='day'){
          $startTime = date('Y-m-d H:i:s', strtotime('-1 day'));
          $endTime = date('Y-m-d H:i:s');
          $where = " and (created_at>='".$startTime."' and created_at<='".$endTime."') ";
      }
      if($val=='week'){
          $startTime = date('Y-m-d H:i:s', strtotime('-7 day'));
          $endTime = date('Y-m-d H:i:s');
          $where = " and (created_at>='".$startTime."' and created_at<='".$endTime."') ";
      }
      if($val=='month'){
          $startTime = date('Y-m-d H:i:s', strtotime('-30 day'));
          $endTime = date('Y-m-d H:i:s');
          $where = " and (created_at>='".$startTime."' and created_at<='".$endTime."') ";
      }
      // if($device_type!='te')
      $query = "select signal_strength, created_at,cloudConnector from device_temperature where signal_strength>0 and device_id=? and type !='temperature' $where order by created_at asc";
      $query = DB::select($query,array($device_id));
      $availed = $final=[];
      $finalData=[];
      if(isset($query) && count($query)>0){
        $timeGroup=[];
          foreach($query as $row){
            $cloudConnector = isset($row->cloudConnector)?$row->cloudConnector:'';
            $created_at = isset($row->created_at)?strtotime($row->created_at):'';
            $timeGroup[$created_at][]=$row;
          }
          foreach($timeGroup as $time=>$ar){
            $connectedPicked=$availed;
          foreach($ar as $row){
              $cloudConnector = isset($row->cloudConnector)?$row->cloudConnector:'';
              
              $signal_strength = isset($row->signal_strength)?(float)$row->signal_strength:0;
              $created_at = isset($row->created_at)?strtotime($row->created_at):'';
              $time_stamp = $created_at*1000;
              if(($cloudConnector!='' && $device_type!='ccon') OR $device_type=='ccon'){
                if(!isset($availed[$cloudConnector])){
                  $availed[$cloudConnector]=$cloudConnector;
                }
                $final[$cloudConnector][]=array($time_stamp,$signal_strength);
                if(isset($connectedPicked[$cloudConnector])){
                  unset($connectedPicked[$cloudConnector]);
                }
                // $final[$cloudConnector][$time_stamp]=array($time_stamp,$signal_strength);
                /*foreach ($availed as $cloudConnectorVal) {
                  if($cloudConnectorVal==$cloudConnector){
                    $final[$cloudConnectorVal][]=array($time_stamp,$signal_strength);
                  }else{
                    $final[$cloudConnectorVal][]=array($time_stamp,null);
                  }
                  
                }*/
                
              }
              }
              foreach($connectedPicked as $rem){
                $time_stamp = $time*1000;
                  $final[$rem][]=array($time_stamp,null);
              }

              //$json[] = array($time_stamp,$signal_strength);
          }
      }
      $availedColors=[];
      if(count($final)>0){
        $lp=0;
        $colors=['#2B4B5D','#74A7C6','#8B635C','#DB624D'];
        foreach($final as $device=>$data){
            $availedColors[]=[
              'id'=>$device,
              'color'=>$colors[$lp]
            ];
            $finalData[]=[
              "color"=>$colors[$lp],
              "lineColor"=>$colors[$lp],
              'name'=>$device,
              'data'=>$data,
            ];
            $lp++;
        }
      }
      return ['data'=>$finalData,'availed'=>array_values($availed),'availedColors'=>$availedColors];
  }


    public function Export(Request $request){
        $company_id = isset($request->company_id)?$request->company_id:'';
        $company = Company::select('name','id','parent_id')->where('company_id',$company_id)->first();
        $company_name = isset($company->name)?$company->name:'';

        $cID = isset($company->id)?$company->id:0;
        $parent_id = isset($company->parent_id)?$company->parent_id:'';

        $user_id = \Auth::id();
        if($user_id==1){
          $query = "select d.id,d.event_type,d.name,min(date(dt.created_at)) as minDate,max(date(dt.created_at)) as maxDate,dt.device_id from devices d inner join device_temperature dt on (dt.device_id=d.device_id) where  d.device_status=1 and (d.company_id='".$company_id."' OR d.coming_from_id='".$cID."') and d.event_type!='ccon' group by d.id";
        }else{
          $query = "select d.id,d.event_type,d.name,min(date(dt.created_at)) as minDate,max(date(dt.created_at)) as maxDate,dt.device_id from devices d inner join device_temperature dt on (dt.device_id=d.device_id) where d.device_status=1 and d.company_id='".$company_id."' and d.event_type!='ccon' group by d.id";
        }

        $query = DB::select($query);

         $result= collect($query)->sortBy('name');
        return view('sensors.export', compact('company_id','company_name','result'));
    }

    public function ExportCSVByDate(Request $request){
      $where='';
      $startdate = isset($request->startdate)?$request->startdate:'';
      $enddate = isset($request->enddate)?$request->enddate:'';
      if($startdate!='' && $enddate!='' && $startdate!=$enddate){
        $where .= " and date(dt.created_at)>='".$startdate."' and date(dt.created_at)<='".$enddate."' ";
      }else{
        if($startdate!=''){
          $where .= " and date(dt.created_at)>='".$startdate."' ";
        }
        if($enddate!=''){
          $where .= " and date(dt.created_at)<='".$enddate."' ";
        }
      }
        $company_id = isset($request->company_id)?$request->company_id:'';
        $fileName = 'Sensors.csv';
        $query = "select d.device_id,d.event_type,d.name,d.description,dt.created_at,dt.temperature from devices d inner join device_temperature dt on (dt.device_id=d.device_id) where d.company_id='".$company_id."' and d.event_type!='ccon' and dt.temperature!=0 $where order by date(dt.created_at) desc";
        $query = DB::select($query);
        $tasks = $query;
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Equipment','Equipment ID','Connected sensor', 'Sensor Type','Sensor ID', 'Description', 'Timestamp', 'Event Type', 'Temperature');
        $callback = function() use($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
              $equipment = Device::where('sensor_id',$task->device_id)->first();
              $sensor_name = !empty($task->name)?$task->name:$task->device_id;
              $equipment_name = isset($equipment)?$equipment->name:$sensor_name;
                $row['Equipment'] = $equipment_name;
                $row['Equipment ID'] = isset($equipment->device_id)?$equipment->device_id:'';
                $row['Connected sensor'] = $sensor_name;
                $row['Sensor Type'] = isset($task->event_type)?$task->event_type:'';
                $row['Sensor ID'] = isset($task->device_id)?$task->device_id:$task->device_id;
                $row['Description'] = isset($equipment->description)?$equipment->description:'';
                $row['Timestamp']  = isset($task->created_at)?$task->created_at:'';
                $row['Event Type']  = isset($task->event_type)?$task->event_type:'';
                $row['Temperature']  = isset($task->temperature)?@number_format($task->temperature,2):'';

                fputcsv($file, array($row['Equipment'],$row['Equipment ID'],$row['Connected sensor'], $row['Sensor Type'],$row['Sensor ID'], $row['Description'], $row['Timestamp'], $row['Event Type'], $row['Temperature']));
          }

            fclose($file);
        };

        $user =auth()->user()->name;
        $action ="Csv export";
        $company = Company::where('company_id',$company_id)->first();
        $message = "$user exported csv from ($startdate) to ($enddate) in $company->name";
        SystemLogs($message,$company_id,$action);

        return response()->stream($callback, 200, $headers);
    }

    public function copyEquipments(Request $request){
      $devices = isset($request->device_ids)?$request->device_ids:'';
      foreach($devices as $device_id){
        $equipment = Device::where('device_id',$device_id)->where('event_type','inventory')->first();
        $company_id = isset($request->transfer_sensor)?$request->transfer_sensor:'';
        $company = Company::where('company_id',$company_id)->first();
        $company_name = isset($company->name)?$company->name:'';
        $device =New Device;
        $device->name = $equipment->name;
          $device->company_id = $company_id;
          $device->description = isset($equipment->description)?$equipment->description:'';
          $device->specification = isset($equipment->specification)?$equipment->specification:'';
          $device->device_id = Str::random(30);
          if($company->parent_id==0){
            $device->event_type = 'inventory';
          }else{
            $device->event_type = 'equipment';
          }
          $device->save();
          
      $resources = DeviceDocument::where('device_id',$equipment->id)->get();
      foreach($resources as $device_resource){
        $resource =New DeviceDocument;
        $resource->name = $device_resource->name;
        $resource->url = $device_resource->url;
        $resource->device_id = $device->id;
        $resource->save();
      }
      $notes = Note::where('device_id',$equipment->id)->get();
      foreach($notes as $device_note){
        $note =New Note;
        $note->name = $device_note->name;
        $note->notes = $device_note->notes;
        $note->device_id = $device->id;
        $note->save();
      }
      }
    	return response()->json([
        'success' => true,
        'message' => "Copied equipments to {$company->name} successfully!"
    ]);
        // return redirect()->back()->with('message',"Copied equipment to $company->name successfully!");
    }
 

    public function ExportCSV(Request $request){
        $company_id = isset($request->company_id)?$request->company_id:'';
        $startdate = isset($request->startdate)?$request->startdate:'';
        $enddate = isset($request->enddate)?$request->enddate:'';
        $device_id = isset($request->device_id)?$request->device_id:'';
        $file_name = isset($request->file_name)?$request->file_name:'';
        /*if(strpos($file_name,'.')===false){
          $file_name = $device_id.'.'.$file_name;
        }*/
        $fileName = $file_name;
        $query = "select d.device_id,d.event_type,d.name,d.description,dt.created_at,dt.temperature from devices d inner join device_temperature dt on (dt.device_id=d.device_id) where d.company_id='".$company_id."' and date(dt.created_at)>='".$startdate."' and date(dt.created_at)<='".$enddate."' and dt.device_id='".$device_id."' and d.event_type!='ccon' and dt.temperature!=0 order by date(dt.created_at) desc";
        $query = DB::select($query);
        $tasks = $query;
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Sensors & Equipments','Equipment ID','Connected sensor', 'Sensor Type','Sensor ID', 'Description', 'Timestamp', 'Event Type', 'Temperature');
        $callback = function() use($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $equipment = Device::where('sensor_id',$task->device_id)->first();
                $sensor_name = !empty($task->name)?$task->name:$task->device_id;
                $equipment_name = isset($equipment)?$equipment->name:$sensor_name;
                if($equipment==null || $equipment==''){
                  $sensor_name = $sensor_name = '';
                }
                  $row['Sensors & Equipments'] = $equipment_name;
                $row['Equipment ID'] = isset($equipment->device_id)?$equipment->device_id:'';
                  $row['Connected sensor'] = $sensor_name;
                  $row['Sensor Type'] = isset($task->event_type)?$task->event_type:'';
                  $row['Sensor ID'] = isset($task->device_id)?$task->device_id:$task->device_id;
                  $row['Description'] = isset($equipment->description)?$equipment->description:'';
                  $row['Timestamp']  = isset($task->created_at)?$task->created_at:'';
                  $row['Event Type']  = isset($task->event_type)?$task->event_type:'';
                  $row['Temperature']  = isset($task->temperature)?@number_format($task->temperature,2):'';
  
                  fputcsv($file, array($row['Sensors & Equipments'],$row['Equipment ID'],$row['Connected sensor'], $row['Sensor Type'],$row['Sensor ID'], $row['Description'], $row['Timestamp'], $row['Event Type'], $row['Temperature']));
                
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function updateSensorDetails(Request $request){
      // dd($request->all());
        $update=[];
        $company_id='';
        $device_id = isset($request->device_id)?$request->device_id:'';
        $device = Device::where('device_id',$device_id)->first();
        $device_name = !empty($device->name)?$device->name:$device->device_id;
        if($device_id!=''){
          $specification = isset($request->specification)?$request->specification:'';
          $company = Company::where('company_id',$request->company_id)->first();
          if($company->parent_id!=0){
            $parent_company = Company::where('id',$company->parent_id)->first();
            $company_id = isset($parent_company->company_id)?$parent_company->company_id:'';
          }else{
            $company_id = isset($request->company_id)?$request->company_id:'';
          }

          if (array_key_exists('name', $request->all())) {
            $user =auth()->user()->name;
            $action ="Update";
            $company = Company::where('company_id',$company_id)->first();
            $message = "$user updated device ($device_id) name ($device->name) to ($request->name) in $company->name";
            SystemLogs($message,$company_id,$action);
            if ($request->name == '') {
              $update['name'] = ' ';
              $Name = 'name';
          } else {

              $update['name'] = $request->name;
              $Name = 'name';
          }
          }
          if (array_key_exists('specification', $request->all())) {
            $user =auth()->user()->name;
            $action ="Update";
            $company = Company::where('company_id',$company_id)->first();
            $message = "$user updated device ($device_name) specification ($device->specification) to ($request->specification) in $company->name";
            SystemLogs($message,$company_id,$action);

            if (isset($request->specification)) {


            $update['specification'] = $specification;
            $Description = '';
          } else {
            $update['specification'] = ' ';
            $Description = 'desc';
          }
        }

          if (array_key_exists('description', $request->all())) {
            $user =auth()->user()->name;
            $action ="Update";
            $company = Company::where('company_id',$company_id)->first();
            $message = "$user updated device ($device_name) description ($device->description) to ($request->description) in $company->name";
            SystemLogs($message,$company_id,$action);

            if ($request->description == '') {
              $update['description'] = ' ';
              $Description = 'description';
          } else {


              $update['description'] = $request->description;
              $Description = 'description';
          }
          }

          
            
          $NameOrDescription = $Name??$Description;
          $value ='';
          if($NameOrDescription =='name'){
              $value = $request->name;
            }elseif($NameOrDescription =='description'){
              $value = $request->description;
            }
            if($NameOrDescription =='name' || $NameOrDescription =='description'){
              $service_account_id = 'cabk40aa385g00amb1k0';
              $service_account_email = 'buhqt2r24te000b250gg@blegsq6ec0m00097e3sg.serviceaccount.d21s.com';
              $secret_key = '701638510b26437d9fc47d7b787aed9a';
              $token = $this->get_jwt_token($service_account_id, $service_account_email, $secret_key);
              
              $this->UpdateSensorLabel($token['access_token'],$company_id,$device_id,$value,$NameOrDescription);
            }

            DB::table('devices')->where('device_id',$device_id)->update($update);
            
        }
    }

    public function UpdateSensorLabel($token,$company_id,$device_id,$value,$NameOrDescription){
    	$curl = curl_init();
      $obj = (object) [
        'key' => $NameOrDescription,
        'name' => "/projects/$company_id/devices/$device_id/labels/$NameOrDescription",
        'value' => $value,
    ];
      $company = Company::where('company_id',$company_id)->first();
      if($company->parent_id==0 && $company!=null){
        $project_id=$company_id;
        $to_company = null;
      }else{
        $parent_company = Company::where('id',$company->parent_id)->first();
        $project_id=$parent_company->company_id;
        $to_company = isset($company_id)?$company_id:'';
      }
		$encoded_post  = json_encode($obj);
      curl_setopt_array($curl, array(
        CURLOPT_URL =>  "https://api.disruptive-technologies.com/v2/projects/$project_id/devices/$device_id/labels/$NameOrDescription?updateMask=value",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "PATCH",
        CURLOPT_POSTFIELDS => $encoded_post,
        CURLOPT_HTTPHEADER => array(
          "Authorization: Bearer ".$token,
          "Content-Type: application/x-www-form-urlencoded"
        ),
      ));
      $response = curl_exec($curl);
      $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      if($httpCode == 200){
        $data=json_decode($response, true);
      }else{
        
        curl_setopt_array($curl, array(
           CURLOPT_URL =>  "https://api.disruptive-technologies.com/v2/projects/$company_id/devices/$device_id/labels",
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_ENCODING => "",
           CURLOPT_MAXREDIRS => 10,
           CURLOPT_TIMEOUT => 0,
           CURLOPT_FOLLOWLOCATION => true,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => "POST",
           CURLOPT_POSTFIELDS => $encoded_post,
           CURLOPT_HTTPHEADER => array(
             "Authorization: Bearer ".$token,
             "Content-Type: application/x-www-form-urlencoded"
           ),
         ));
         $response = curl_exec($curl);
         $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
         $data=json_decode($response, true);
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
    public function checkSensor(Request $request)
    {
    	 // $sensors=Device::where('device_status',1)->where('company_id',$request->company_id)->get();
    	 $sensorData=Device::where('device_id',$request->device_id)->first();
      //  dd($sensorData); 
    	$isCheck=false;
      $output='';
      // dd($sensorData);
      if(isset($sensorData) && $sensorData!=null){
        $company=Company::where('company_id',$sensorData->company_id)->first();
        $html = view('sensors.single_equipment',compact('sensorData','company'));
    	 	if(Auth::user()->id==1){
    			$isCheck=true;
          $output .= $html->render();
	    	 	return response()->json(['isCheck'=>$isCheck,'company_id'=>$sensorData->company_id,'html'=>$output]);
    		}

    	 	 $myCompany=Company::where('company_id',$sensorData->company_id)->orWhere('id',$sensorData->coming_from_id)->first();

    	 	 if(isset($myCompany) && $myCompany!=null){
    	 	 	 if($myCompany->user_id==Auth::user()->id){
	    	 	 	$isCheck=true;
	    	 	 	return response()->json(['isCheck'=>$isCheck,'company_id'=>$sensorData->company_id]);
	    	 	 }else{

	    	 	 	$companyMembers=CompanyMembers::where('company_id',$myCompany->company_id)->get();
	    	 	 	// return response()->json($companyMembers);
              
	    	 	 	foreach ($companyMembers as $member) {
						if($member->user_id==Auth::user()->id){
    	     
              $output .= $html->render();
							$isCheck=true;
							 return response()->json(['isCheck'=>$isCheck,'company_id'=>$sensorData->company_id,'html'=>$output]);
							break;
						}
	    	 	 	}
	    	 	 }
    	 	 }


    	 }

    	 return response()->json(['isCheck'=>$isCheck]);

    }




    public function generateQR(Request $request){
        // $qrcode=QrCode::format('png')->generate('123456');

        $image =  QrCode::format('png');

        $image = $image->format('png')
            ->errorCorrection('H')
            ->generate('123456');
        return response($image)->header('Content-type','image/png');
    }
}
