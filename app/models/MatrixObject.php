<?php
/**
 * Created by PhpStorm.
 * User: didar
 * Date: 8/12/18
 * Time: 3:43 PM
 */

namespace Models;

use Illuminate\Database\Eloquent\Model;


class MatrixObject extends Model
{
	protected $table = 'matrix_object';
	
	protected $fillable = [
		'title',
		'addons_id',	
		'dataset_id',
		'col'				
	];	
}
