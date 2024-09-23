<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminCompany extends Model
{
	 /**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	 protected $table = 'admin_companies';

	 /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	 protected $guarded = [];

	 /*
	 * Relationships
	 */
    // public function company_devices()
    // {
    // 	return $this->hasMany('App\Device','company_id','id')->orderBy('name','ASC');
    // }
}
