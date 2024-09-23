<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyMembers;
use App\Device;
use App\DeviceTemperature;
use App\User;
use DB;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use stdClass;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $user_id = Auth::id();
        $company_id = isset($request->company_id)?$request->company_id:'';
        $company = Company::select('name','id')->where('company_id',$company_id)->first();
        $company_name = isset($company->name)?$company->name:'';
        $cID = isset($company->id)?$company->id:0;
        $email = \Auth::user()->email;
        $sql = DB::table('company_members_invite')->where('email',$email)->orderBy('id','asc')->get();
        if(isset($sql) && count($sql)>0){
            $device_id = isset($sql[0]->company_id)?$sql[0]->company_id:'';
        }elseif($email=='admin@recasoft.com'){
            $query = DB::select("select * from companies where parent_id=0 order by id desc limit 1");
            $device_id = isset($query[0]->company_id)?$query[0]->company_id:'';
        }else{
            $device_id=md5(time());
        }
        // if($email=='admin@recasoft.com'){
            $sensors = Device::select('is_active','id','name','company_id','sensor_id','device_id','temperature','event_type','signal_strength','temeprature_last_updated')->where(array('device_status'=>1,'event_type'=>'temperature'));
            if($user_id==1){
                $sensors->where(function($q) use($company_id,$cID){
                    $q->where(array('company_id'=>$company_id));
                    if($cID>0){
                      $q->orWhere(array('coming_from_id'=>$cID));
                  }

              });
            }else{
                $sensors->where('company_id',$company_id);
            }
            $sensors = $sensors->orderBy('sort', 'ASC')->get();
        // }
        $sensors_list=[];
        foreach($sensors as $row){
            $sensors_list[]=array('company_id'=>$row->company_id);
        }

        if(\Auth::user()->id!=1){
            $currentCompany= Company::where('company_id',$company_id)->first();
            if(isset($currentCompany)){

                $par_comp= Company::where('id',$currentCompany->parent_id)->first();
            }
        if(isset($par_comp)){
          $setting = \App\CompanySetting::where('company_id',$par_comp->company_id)->where('meta_key','email')->first();
          $CompanyAdminEmail = isset($setting->meta_value)?$setting->meta_value:'';
        }else{
          $setting = \App\CompanySetting::where('company_id',$company_id)->where('meta_key','email')->first();
          $CompanyAdminEmail = isset($setting->meta_value)?$setting->meta_value:'';
        }
            return view('dashboard', compact('company_id','company_name','device_id','sensors','sensors_list','CompanyAdminEmail','currentCompany'));
        }
        else{


            $companies=Company::where('parent_id',0)->get();
            $array=[];
            foreach($companies as $company){
                $query = "select c.id,c.email,c.parent_id,c.name, c.company_id, d.device_id,
                count(IF(d.event_type='ccon',1,null)) as connTotal,
                count(IF(d.event_type='temperature' OR d.event_type='proximity',1,null)) as sensorTotal
                from companies c
                left join devices d on ( d.company_id = c.company_id AND d.device_status = 1)
                where c.is_active=1 AND c.parent_id =0
                group by c.company_id";
                $array= DB::select($query);
                // if($companiesData[0]->company_id ==null){
                //     continue;
                // }else{
                //     $array[] =$companiesData;
                // }
            }
              $company_id = isset($request->company_id)?$request->company_id:'';
        $query = "select c.*,
                  count(IF(d.event_type='ccon',1,null)) as connTotal,
                  count(IF(d.event_type='temperature' OR d.event_type='proximity',1,null)) as sensorTotal
                  from companies c
                  left join devices d on (d.company_id=c.company_id)
                  where c.is_active=1 and c.company_id=? limit 1";
        $company = DB::select($query,[$company_id]);
        $company_name = isset($company[0]->name)?$company[0]->name:'';

        $members = "select * from company_members where company_id=? order by id desc";
        $members = DB::select($members,[$company_id]);
            return view('AdminDashboard', compact('company_id','company_name','device_id','sensors','sensors_list','company','array','members'));

        }
    }


    public function CloneDashboard(Request $request)
    {
        $user_id = Auth::id();
        $company_id = isset($request->company_id)?$request->company_id:'';
        $company = Company::select('name','id')->where('company_id',$company_id)->first();
        $company_name = isset($company->name)?$company->name:'';
        $cID = isset($company->id)?$company->id:0;
        $email = \Auth::user()->email;
        $sql = DB::table('company_members_invite')->where('email',$email)->orderBy('id','asc')->get();
        if(isset($sql) && count($sql)>0){
            $device_id = isset($sql[0]->company_id)?$sql[0]->company_id:'';
        }elseif($email=='admin@recasoft.com'){
            $query = DB::select("select * from companies where parent_id=0 order by id desc limit 1");
            $device_id = isset($query[0]->company_id)?$query[0]->company_id:'';
        }else{
            $device_id=md5(time());
        }
        // if($email=='admin@recasoft.com'){
            $sensors = Device::select('is_active','id','name','company_id','device_id','temperature','event_type','signal_strength','temeprature_last_updated')->where(array('device_status'=>1,'event_type'=>'temperature'));
            if($user_id==1){
                $sensors->where(function($q) use($company_id,$cID){
                    $q->where(array('company_id'=>$company_id));
                    if($cID>0){
                      $q->orWhere(array('coming_from_id'=>$cID));
                  }

              });
            }else{
                $sensors->where('company_id',$company_id);
            }
            $sensors = $sensors->orderBy('sort', 'ASC')->get();
        // }
        $sensors_list=[];
        foreach($sensors as $row){
            $sensors_list[]=array('company_id'=>$row->company_id);
        }

        if(\Auth::user()->id!=1){
            $currentCompany= Company::where('company_id',$company_id)->first();
            if(isset($currentCompany)){

                $par_comp= Company::where('id',$currentCompany->parent_id)->first();
            }
        if(isset($par_comp)){
          $setting = \App\CompanySetting::where('company_id',$par_comp->company_id)->where('meta_key','email')->first();
          $CompanyAdminEmail = isset($setting->meta_value)?$setting->meta_value:'';
        }else{
          $setting = \App\CompanySetting::where('company_id',$company_id)->where('meta_key','email')->first();
          $CompanyAdminEmail = isset($setting->meta_value)?$setting->meta_value:'';
        }
            return view('CloneDashboard', compact('company_id','company_name','device_id','sensors','sensors_list','CompanyAdminEmail','currentCompany'));
        }
        else{


            $companies=Company::where('parent_id',0)->get();
            $array=[];
            foreach($companies as $company){
                $query = "select c.id,c.email,c.parent_id,c.name, c.company_id, d.device_id,
                count(IF(d.event_type='ccon',1,null)) as connTotal,
                count(IF(d.event_type='temperature' OR d.event_type='proximity',1,null)) as sensorTotal
                from companies c
                left join devices d on ( d.company_id = c.company_id AND d.device_status = 1)
                where c.is_active=1 AND c.parent_id =0
                group by c.company_id";
                $array= DB::select($query);
                // if($companiesData[0]->company_id ==null){
                //     continue;
                // }else{
                //     $array[] =$companiesData;
                // }
            }
              $company_id = isset($request->company_id)?$request->company_id:'';
        $query = "select c.*,
                  count(IF(d.event_type='ccon',1,null)) as connTotal,
                  count(IF(d.event_type='temperature' OR d.event_type='proximity',1,null)) as sensorTotal
                  from companies c
                  left join devices d on (d.company_id=c.company_id)
                  where c.is_active=1 and c.company_id=? limit 1";
        $company = DB::select($query,[$company_id]);
        $company_name = isset($company[0]->name)?$company[0]->name:'';

        $members = "select * from company_members where company_id=? order by id desc";
        $members = DB::select($members,[$company_id]);
            return view('AdminDashboard', compact('company_id','company_name','device_id','sensors','sensors_list','company','array','members'));

        }
    }
    public function getEvents(Request $request){
        ob_start('ob_gzhandler');
        $json = array();
        $temperature_array=[];
        $event_id = isset($request->event_id)?$request->event_id:0;

        $startTime=date('Y-m-d H:i:s');

        $endTime=date('Y-m-d H:i:s', strtotime(' -1 day',strtotime($startTime)));

        if($event_id==1){
            $startTime = date('Y-m-d H:i:s', strtotime(' -1 day',strtotime($startTime)));
            $endTime=date('Y-m-d H:i:s', strtotime(' +1 day',strtotime($startTime)));
        }
        if($event_id==2){
            $endTime=date('Y-m-d H:i:s', strtotime(' +1 day',strtotime($startTime)));
            $startTime = date('Y-m-d H:i:s', strtotime( ' -7 day',strtotime($startTime))); 
        }
        if($event_id==3){
            $endTime=date('Y-m-d H:i:s', strtotime(' +1 day',strtotime($startTime)));
            $startTime = date('Y-m-d H:i:s', strtotime(' -30 day',strtotime($startTime)));
        }
        // echo  $startTime ;
        // echo '--';
        // echo  $endTime ;
     

        $device_id = isset($request->device_id)?$request->device_id:'';

        $sensor = Device::select('is_active','temperature','event_type','temeprature_last_updated')->where(array('device_status'=>1,'device_id'=>$device_id,'event_type'=>'temperature'))->first();
        if($event_id==0){
            $query = "select temperature, created_at from device_temperature where type='temperature' and device_id=?  order by created_at asc";
            $query = DB::select($query,array($device_id));
        }else{
            $query = "select temperature, created_at from device_temperature where type='temperature' and device_id=? and (created_at >=? and created_at <=? ) order by created_at asc";
            $query = DB::select($query,array($device_id, $startTime,$endTime));
        }
        $timeAr=[];
        if(isset($query) && count($query)>0){
            foreach($query as $key=>$row){
                $temperature = isset($row->temperature)?(float)$row->temperature:0;
                // $keyy = mt_rand(1,30);
                $timeAr[]=$row->created_at;
                $created_at = isset($row->created_at)?strtotime($row->created_at):'';
                // $created_at = strtotime("+$key minutes",time());
                $created_at = (int)$created_at;
                $time_stamp = $created_at*1000;
                $json[] = array($time_stamp,$temperature);
                $temperature_array[]=$temperature;
            }
        }

        $average=0;
        if(count($temperature_array)>0){
                $min_value=@min($temperature_array);
                 $max_value=@max($temperature_array);
            $average = @array_sum($temperature_array)/count($temperature_array);
        }
        $lastUpdatedTime = isset($sensor->temeprature_last_updated)?date('Y-m-d H:i:s',strtotime($sensor->temeprature_last_updated)):'';
        $temeprature_last_updated= $this->time_elapsed_string($sensor->temeprature_last_updated) ;
        return array(
            'device_status'=> isset($sensor->is_active)?$sensor->is_active:0,
        'data'=>$json,
        // 'timeAr'=>$timeAr,
        'temperature'=>isset($sensor->temperature)?@number_format($sensor->temperature,2):0,
        'temeprature_last_updated'=>$temeprature_last_updated,
        'lastUpdatedTime'=>$lastUpdatedTime,
        'milliseconds'=>strtotime($lastUpdatedTime)*1000,
        'average'=>@number_format($average,2),
        'min_value'=>@isset($min_value)?number_format($min_value,2):0,
        'max_value'=>@isset($max_value)?number_format($max_value,2):0
        );
    }

    public function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
    
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

    public function updateOrder(Request $request){
        $position = isset($request->position)?$request->position:[];
        $i=1;
        foreach($position as $k=>$v){
            DB::table('devices')->where('id',$v)->update(array('sort'=>$i));
            $i++;
        }
    }
}
