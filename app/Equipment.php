<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\DeviceSetting;

class Equipment extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	 protected $table = 'equipments';

	 /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	 protected $guarded = [];
	 protected $appends = ['UpdatedTime'];

	 /*
	 * Relationships
	 */
    public function get_company()
    {
    	return $this->belongsTo('App\Company','id','company_id');
    }
    public function getUpdatedTimeAttribute(){
    	$date = Carbon::parse($this->device_updated_time);
    	$diff = $date->diffInSeconds(Carbon::now());
    	// return $this->device_updated_time;
    	if($this->device_updated_time!='1970-01-01 00:00:00' && $this->device_updated_time!='' && $this->device_updated_time!=null){


    		$then = new \DateTime(date('Y-m-d H:i:s', strtotime($this->device_updated_time)));
    		$now = new \DateTime(date('Y-m-d H:i:s', time()));
    		$diff = $then->diff($now);
    		$time='';
    		if($diff->y>0){
    			if($diff->y > 1){
    				$unit='years';
    			}else{
    				$unit='year';
    			}
    			$time=$diff->y;
    		}elseif($diff->m>0){
    			if($diff->m > 1){
    				$unit='months';
    			}else{
    				$unit='month';
    			}
    			$time=$diff->m;
    		}elseif($diff->d>0){
    			if($diff->d > 1){
    				$unit='days';
    			}else{
    				$unit='day';
    			}
    			$time=$diff->d;
    		}elseif($diff->h>0){
    			if($diff->h > 1){
    				$unit='hours';
    			}else{
    				$unit='hour';
    			}
    			$time=$diff->h;
    		}elseif($diff->i>0){
    			if($diff->i > 1){
    				$unit='minutes';
    			}else{
    				$unit='minute';
    			}
    			$time=$diff->i;
    		}elseif($diff->s>0){
    			if($diff->s > 1){
    				$unit='seconds';
    			}else{
    				$unit='second';
    			}
    			$time=$diff->s;
    		}
    		return [
    			'unit'=> $unit,
    			'time'=> $time
    		];
    		/*return array(
    			($diff->y>1?'years':'year') => $diff->y, 
    			($diff->m>1?'months':'month') => $diff->m, 
    			($diff->d>1?'days':'day') => $diff->d, 
    			($diff->h>1?'hours':'hour') => $diff->h,
    			($diff->i>1?'minutes':'minute') => $diff->i,
    			($diff->s>1?'seconds':'second') => $diff->s
    		);*/
    	}
    	return '';

    	
    }
    public function settings()
		{
		    return $this->hasMany(DeviceSetting::class, 'device_id', 'device_id');
		}
}
