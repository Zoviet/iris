<?php
namespace Dataparser;

class Xml {		
	
	public static function get($file_path) {
		$xml = simplexml_load_file($file_path);
		if ($xml===FALSE) throw new \Exception('Ошибка интерпретации файла '.$file_path.' как Excel XML');
		$data =[];
		$i=0;
		foreach ($xml->Worksheet as $sheet) {	
			$sheet_data = [];				
			$j=0;
			foreach ($sheet->Table->Row as $row) {				
				$z=0;
				foreach ($row->Cell as $cell) {							
					$sheet_data[$j][$z] = $cell->Data->__toString();					
					$z++;
				}
				$j++;	
			}							
			$sheet_name = $sheet->xpath('@ss:Name')[0]->Name[0]->__toString();			
			if (!empty($sheet_name)) {
				$data[$sheet_name] = $sheet_data;
			} else {
				$data[$i] = $sheet_data;
			}
			$i++;
		}
		return $data;		
	}

} ?>
