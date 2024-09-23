<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanySettingEmail extends Model
{
    	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	 protected $table = 'company_setting_emails';

	 /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	 protected $guarded = [];
}
