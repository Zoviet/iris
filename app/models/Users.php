<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Capsule\Manager as DB;


class Users extends Model
{
	protected $table = 'users';
	
	protected $fillable = [
		'id',
		'login',
		'first_name',
		'last_name',
		'password',
		'data',
		'role'
	];	
	public static $errors=[];
	
	
}
