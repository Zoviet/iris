<?php
/**
 * Created by PhpStorm.
 * User: didar
 * Date: 8/12/18
 * Time: 3:43 PM
 */

namespace Models;

use Illuminate\Database\Eloquent\Model;


class MatrixCell extends Model
{
	protected $table = 'matrix_cell';
	
	protected $fillable = [
		'title',
		'method_id',
		'object_id',
		'addons_id',		
	];	
}
