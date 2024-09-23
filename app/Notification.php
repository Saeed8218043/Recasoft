<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notification extends Model
{
	protected $fillable=[
		'm_date','maintenance_repeat','isActive','isResolved','company_id','name','alert_type','temp_range','upper_celcius','lower_celcius','delay_time','reminder_days'
	];

	public function devices(){
		return $this->hasMany(NotificationDevice::class,'notification_id');
	}
	public function emails(){
		return $this->hasMany(NotificationEmail::class,'notification_id');
	}
}
