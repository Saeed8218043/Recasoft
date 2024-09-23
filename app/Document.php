<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{

	//use SoftDeletes;
	protected $with =['children'];
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	 protected $table = 'documents';

	 /**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	 protected $guarded = [];

	 /*
	 * Relationships
	 */
	public function children(){
		return $this->hasMany(self::class, 'parent_id')->with('children');
	}
	// public function grandchildren()
    // {
    //     return $this->children()->with('grandchildren');
    // }

}
