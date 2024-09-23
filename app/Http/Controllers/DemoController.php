<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use DB;
use App\Device;
use App\DeviceTemperature;
use DateTime;
class DemoController extends Controller
{
    /**
     * Show the application demo dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index2(Request $request)
    {
        $company_id = isset($request->company_id)?$request->company_id:'';
        $company = Company::select('name')->where('company_id',$company_id)->first();
        $company_name = isset($company->name)?$company->name:'';
        $email = isset($company->email)?$company->email:'';
        $sql = DB::table('company_members_invite')->where('email',$email)->orderBy('id','asc')->get();
        if(isset($sql) && count($sql)>0){
        $device_id = isset($sql[0]->company_id)?$sql[0]->company_id:'';
        }elseif($email=='admin@recasoft.com'){
            $query = DB::select("select * from companies order by id desc limit 1");
            $device_id = isset($query[0]->company_id)?$query[0]->company_id:'';
        }else{
            $device_id=md5(time());
        }

        $sensors = Device::select('is_active','id','name','company_id','device_id','temperature','event_type','signal_strength','temeprature_last_updated')->where(array('device_status'=>1,'company_id'=>$company_id,'event_type'=>'temperature'))->orderBy('sort', 'ASC')->get();
        $sensors_list=[];
        foreach($sensors as $row){
            $sensors_list[]=array('company_id'=>$row->company_id);
        }


        return view('dashboard2', compact('company_id','company_name','device_id','sensors','sensors_list'));
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
            $startTime = date('Y-m-d H:i:s', strtotime(' -15 day',strtotime($startTime)));
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
        $min_value=@min($temperature_array);
        $max_value=@max($temperature_array);
        $average=0;
        if(count($temperature_array)>0){
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


