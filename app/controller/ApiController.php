<?php

namespace Controller;
use \Models\Matrix;
use \Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use \Box\Spout\Common\Entity\Row;
use \Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use \Box\Spout\Common\Entity\Style\CellAlignment;
use \Box\Spout\Common\Entity\Style\Color;
use \Box\Spout\Common\Entity\Style\Border;
use \Box\Spout\Writer\Common\Creator\Style\BorderBuilder;

class ApiController
{
	
	public function json($method,$matrix_id,$level=0) {
		if (!isset($level)) $level=0;
		$Matrix = new Matrix();	
		$matrix_data = $Matrix->get($matrix_id);	
		switch($method) {
			case 'table':
				\Flight::json($matrix_data->data);
			break;
			case 'methods':
				\Flight::json($matrix_data[$level]->methods);
			break;
			case 'objects':
				\Flight::json($matrix_data[$level]->objects);
			break;
			case 'count':
				\Flight::json($matrix_data[$level]->count);
			break;
			case 'title':
				\Flight::json($matrix_data[$level]->title);
			break;
			case 'description':
				\Flight::json($matrix_data[$level]->description);
			break;
			case 'autor':
				\Flight::json($matrix_data[$level]->autor);
			break;
		}
	}
	
	public function export($matrix_id) 
	{
		$Matrix = new Matrix();
		$matrix = $Matrix->get($matrix_id);
		if (empty($matrix)) return FALSE;
		
		$fileName = 'matrix'.$matrix_id.'.xlsx';
		$writer = WriterEntityFactory::createXLSXWriter();
		$writer->setShouldCreateNewSheetsAutomatically(true);	
		
		$border = (new BorderBuilder())
			->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_DASHED)
			->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_DASHED)
			->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_DASHED)
			->build();

		$defaultStyle = (new StyleBuilder())
			->setFontSize(10)
			->setCellAlignment(CellAlignment::CENTER)
			->build();
		
		$writer->setDefaultRowStyle($defaultStyle);	
		$writer->openToBrowser(realpath('temp').'/'.$fileName);
		
		$style_header = (new StyleBuilder())
           ->setFontBold()
           ->setFontSize(14)        
           ->setShouldWrapText()
           ->setCellAlignment(CellAlignment::LEFT)         
           ->build();
        
        $style_methods = (new StyleBuilder())
           ->setFontBold()
           ->setFontSize(12)
           ->setFontColor(Color::BLACK)
           ->setShouldWrapText()
           ->setCellAlignment(CellAlignment::CENTER)
           ->setBackgroundColor(Color::YELLOW)
           ->setBorder($border)
           ->build(); 
        
         $style_diag = (new StyleBuilder())
           ->setFontBold()
           ->setFontSize(10)
           ->setFontColor(Color::BLACK)
           ->setShouldWrapText()
           ->setCellAlignment(CellAlignment::CENTER)
           ->setBackgroundColor(Color::BLUE)           
           ->build();         
           
        $style_row = (new StyleBuilder())
		   ->setFontItalic()
           ->setShouldWrapText()
           ->setCellAlignment(CellAlignment::LEFT)
           ->build(); 
          
        foreach ($matrix as $key=>$level) {	
			//создаем новый лист
			$title_cell = $autor_cell = $description_cell =[];
			$sheet = $writer->getCurrentSheet();
			if ($key>0) $sheet = $writer->addNewSheetAndMakeItCurrent();
			$sheet_name = preg_replace('/[\\/?*:[]]/','',$level->title);
			$sheet_name = mb_substr($sheet_name,0,28).$key;							
			$sheet->setName($sheet_name);
			//заполняем заголовки
			$title_cell[] = WriterEntityFactory::createCell($level->title,$style_header);
			$autor_cell[] = WriterEntityFactory::createCell($level->autor,$style_header);
			$description_cell[] = WriterEntityFactory::createCell($level->description,$style_header);	
			$writer->addRow(WriterEntityFactory::createRow($title_cell, $style_row));
			$writer->addRow(WriterEntityFactory::createRow($autor_cell, $style_row));
			$writer->addRow(WriterEntityFactory::createRow($description_cell, $style_row));
			//обходим саму таблицу
			foreach ($level->table as $row=>$cols) {			
				$cells=[];		
				foreach ($cols as $col=>$data) {					
					$cell_data = (!empty($data->type)) ? $data->title.PHP_EOL.'::'.$data->type.'::'.$data->data : $data->title;
					if ($row==$col and $col!==0) $cells[] = WriterEntityFactory::createCell($cell_data,$style_diag); else 
					$cells[] = ($row==0 or $col==0) ? WriterEntityFactory::createCell($cell_data,$style_methods) : WriterEntityFactory::createCell($cell_data);					
				}
				$writer->addRow(WriterEntityFactory::createRow($cells));
			}
		}	
		$writer->close();		
	}
	
	public function import($file) 
	{
		
	}

    
}
