<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Capsule\Manager as DB;


class Api_keys extends Model
{
	protected $table = 'api_keys';
	
	protected $fillable = [
		'id',
		'key',
		'user_id'		
	];	
	public static $errors=[];
}
