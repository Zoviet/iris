<?php
/**
 * Created by PhpStorm.
 * User: didar
 * Date: 8/12/18
 * Time: 2:37 PM
 */

namespace Controller;
use \Lexicon\Semantic;
use \Lexicon\Speller;
use \Lexicon\Freq;

class LexiconController
{
	
	public function test() {
		$text = 'РАБОЧИЙ ДОМ. ЧТО ЖЕ ЭТО ТАКОЕ?

Организация инфраструктуры рабочего дома
Инфраструктура рабочего дома минимальна. Для начала работы требуется лишь помещение, как правило, съемное, площадью, достаточной для размещения ожидаемого набора постояльцев, исходя из 2,5 — 3
квадратных метров на человека. Чаще всего, это четырех-, реже трехкомнатные квартиры , площадью не менее 70-и квадратных метров, или частные котеджи, поскольку содержание в рабочем доме менее двадцати человек, организаторы, видимо, считают нерентабельным. Квартиры снимаются обычно на нижних этажах, чтобы не досаждать соседям излишним шумом (нередко постояльцам приходится и отправляться на работу по ночам, и возвращаться ночью). Курить в помещениях рабочих домов запрещено, курение в подъезде большого количества людей вызовет недовольство соседей и может привлечь внимание полиции или чиновников. Поэтому курить разрешается только во дворе и не более, чем по трое. В некоторых рабочих домах постояльцам разрешено курить на балконах или в лоджиях. Организаторы рабочих домов, размещающие рабочие дома на собственных площадях мне не встречались. Но это и вполне объяснимо: они, организаторы (руководители) рабочих домов, в которых приходилось житьть автору будь то в Казани или в Ульяновске, сами иногородние. Живут они, обычно, в этих же домах, занимая отдельную комнату, или снимают жилье поблизости. 
Повышению вместимости РД способствует размещение на снимаемой жилплощади двух-, а то и трех-ярусных кроватей, которые принято называть шконками. Подобный тип организации бытования характерен также для разнообразных дешевых хостелов и общежитий. В этом плане
требования работного дома к помещению мало отличаются от них и столь же
минимальны. Это:
1. свободные площади для размещения спальных мест
2. наличие кухни
3. наличие душевых и туалетных кабин, достаточных для обслуживания
прикидочного количества посетителей.
С последим пунктом в съемном жилье всегда возникают проблемы. В туалет и ванную всегда очередь, особенно по утрам. Что нередко создает нервозную обстановку и даже вызывает конфликты. Например, последним местом пребывания автора был двухэтажный комфортабельный котедж. Но он не предназначался для проживания в неи 25-и человек. Бак в душевой вмещает всего сто литров воды. Вернувшиеся с работы первыми могут помыться нормально. Последним доставалась только холодная вода. Однако спустя немного времени, хозяин коттеджа, ссылаясь на то, что колодец переполняется слишком быстро';
	$array_text = Freq::prepare($text);
	var_dump($array_text);

	}
	
	
	public function text($status) {	
		\Flight::render('text.php',['status'=>$status]);  
	}
	
	public function textstat() {
		if (\Flight::request()->data->text) {
			$Semantic = new Semantic();		
			$text = \Flight::request()->data->text; 
			try {
				$result = $Semantic->text($text)->explore()->result;							
			} catch(\Exception $e) {
				return FALSE;
			}			
			$dataset = '<ul>';				
			$dataset .= '<li><b>Количество существительных:</b> '.count($result['NOUNS']).'</li>'; //количество существительных
			$dataset .= '<li><b>Количество прилагательных:</b> '.count($result['ADJS']).'</li>'; //количество прилагательных
			$dataset .= '<li><b>Количество глаголов:</b> '.count($result['VERBS']).'</li>'; //количество глаголов
			$dataset .= '</ul>';			
			$preds = explode('.',$text); 
			$dataset .= '
			<h5 class="title">Суть:</h5>
			<p>';	
			$short = '';
			foreach ($preds as $pred) {												
				$res = $Semantic->text($pred)->words()->explore();														
				$short .= ' '.$Semantic->subject();
				$short .= ' '.$Semantic->definition();
				$short .= ' '.$Semantic->predict();					
			}		
			$dataset .= $short;
			$dataset .= '</p>';
				$dataset .= '
			<h5 class="title">Словарь:</h5>
			<p>';			
			$stems = [];
			foreach ($result['NOUNS'] as $noun){
				$stem = $Semantic::remove_ending($noun,'NOUNS');
				if (isset($stems[$stem]) and strlen($stem)>2) {
					$stems[$stem] = $stems[$stem]+1;
				} else {
					$stems[$stem] = 1;
				}
			
			}
			arsort($stems);			
			$stems = array_slice(array_keys($stems),0,ceil(count($stems)));
			$dataset .= implode(' ',$stems);
			$dataset .= '</p>';
			
			$dataset = mb_convert_encoding($dataset, 'UTF-8', 'UTF-8');
			\Flight::json($dataset);			
		}
	}
	
	public function textfind() {
		$Speller = new Speller();
		echo 'textstat';
	}
    
}