<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NotificationDevice extends Model
{
	protected $fillable=[
		'maintenance_sent','device_id','notification_id','last_deviate_time','resolve_sent',
	];

	
}
