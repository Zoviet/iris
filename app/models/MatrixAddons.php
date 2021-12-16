<?php
/**
 * Created by PhpStorm.
 * User: didar
 * Date: 8/12/18
 * Time: 3:43 PM
 */

namespace Models;

use Illuminate\Database\Eloquent\Model;


class MatrixAddons extends Model
{
	protected $table = 'matrix_addons';
	
	protected $fillable = [
		'data',
		'type'							
	];	
}
