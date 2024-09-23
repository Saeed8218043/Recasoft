<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NotificationEmail extends Model
{
 protected $fillable=[
 	'email','subject','content','notification_id','notification_type'
 ];
}
