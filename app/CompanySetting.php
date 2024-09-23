<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
	 /**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	 protected $table = 'company_settings';

	 /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	 protected $guarded = [];

	 /*
	 * Relationships
	 */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}
