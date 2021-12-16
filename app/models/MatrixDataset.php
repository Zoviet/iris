<?php
/**
 * Created by PhpStorm.
 * User: didar
 * Date: 8/12/18
 * Time: 3:43 PM
 */

namespace Models;

use Illuminate\Database\Eloquent\Model;


class MatrixDataset extends Model
{
	protected $table = 'matrix_dataset';
	
	protected $fillable = [
		'matrix_id',
		'level',	
		'autor_id',
		'title',
		'description'		
	];	
}
