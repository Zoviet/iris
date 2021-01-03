<?php
/**
 * @author   Zoviet
 */ 
namespace iris\Config;

class Errors {			
	
	public const reestr = array(
		'R01' => 'Ошибка при добавлении матрицы в реестр: для матрицы не установлен ID',
		'R02' => 'Ошибка при инициализации реестра: не найдены данные',
		'R03' => 'Ошибка при инициализации реестра: не удалось инициализировать матрицу',
	);
	
	public const matrix = array(
		'M01' => 'Для матрицы не установлен ID',
		'M02' => 'Не удалось инициализировать реестр',
		'M03' => 'Ошибка инициализации матрицы',
		'M04' => 'Ошибка в размерности матрицы: количество уровней не соответствует количеству уровней предметов (специалистов)',
		'M05' => 'Попытка удаления уровня у матрицы, имеющей производные',
		'M06' => 'Для работы автогенерации все уровни должны быть представлены строками',
		'M07' => 'Для работы автогенерации все предметы должны быть представлены строками',
	);
	
}