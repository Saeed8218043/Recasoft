<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
	 /**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	 protected $table = 'companies';

	 /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	 protected $guarded = [];

	 /*
	 * Relationships
	 */
    public function company_devices()
    {
    	return $this->hasMany('App\Device','company_id','id')->orderBy('name','ASC');
    }
    public function settings()
    {
    	return $this->hasMany('App\CompanySetting','company_id','company_id');
    }
    public function childs()
    {
    	return $this->hasMany('App\Company','parent_id','id');
    }
    
}
