<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RequestLog extends Model
{
    /**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	 protected $table = 'request_logs';

	 /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	 protected $guarded = [];
}
