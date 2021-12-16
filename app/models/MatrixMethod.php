<?php
/**
 * Created by PhpStorm.
 * User: didar
 * Date: 8/12/18
 * Time: 3:43 PM
 */

namespace Models;

use Illuminate\Database\Eloquent\Model;


class MatrixMethod extends Model
{
	protected $table = 'matrix_method';
	
	protected $fillable = [
		'title',
		'addons_id',
		'dataset_id',
		'row',					
	];	
}
