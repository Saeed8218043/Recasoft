<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	 protected $table = 'notes';

	 /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	 protected $guarded = [];

	 /*
	 * Relationships
	 */
   
}
