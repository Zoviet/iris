<?php echo $admin_menu;?>    
	<div class="card">
		<header class="card-header">              
			<div id="desc" class="columns card-header-title">
				<div class="column is-7">
					<h1 class="title"><input class="input" id="matrix-title" type="text" placeholder="Название матрицы" value="<?php echo $matrix->title;?>"></h1>					
					<input class="input" id="matrix-description" type="text" placeholder="Описание матрицы" value="<?php echo $matrix->description;?>"><br/> <input class="input" id="matrix-name" type="text" placeholder="Автор матрицы" value="<?php echo $matrix->name;?>"><br/>				
				</div>
				<div class="column is-3">
					<label class="checkbox">
					<input type="checkbox" id="matrix-public" value="<?php echo $matrix->public;?>" <?php if ($matrix->public==1) echo 'checked';?>>
						Публичный доступ
					</label>
					<?php if ($matrix->public==1) {?>
					<hr/>
					<label class="checkbox">
					<input type="checkbox" id="matrix-public" value="1" checked>
						Автоматический сбор словарей
					</label>
					<label class="checkbox">
					<input type="checkbox" id="matrix-public" value="1" checked>
						Эвристический анализатор
					</label>
					<label class="checkbox">
					<input type="checkbox" id="matrix-public" value="1" checked>
						Лексический прототипиратор
					</label>
					<label class="checkbox">
					<input type="checkbox" id="matrix-public" value="1">
						Исключить описания из поиска
					</label>
					<label class="checkbox">
					<input type="checkbox" id="matrix-public" value="1" checked>
						Самообучение
					</label>
					<?php }?>
				</div>
				<div class="column is-2 has-text-right">				
					<button type="submit" id="matrix-save" class="button is-success">Сохранить</button>	
				</div>
			</div>
		</header>
	</div>  			  		  
	<div class="container">
		<div class="tabs is-boxed" id="nav">
			<ul>
			<?php $i=0; foreach (array_keys($matrix->data) as $level) {?>
				<li id="tab<?php echo $level;?>" data-target="pane-<?php echo $level;?>" data-level="<?php echo $level;?>" <?php if ($i==0) { $start_level = $level;?>class="is-active"<?php }?>>
					<a>
                        <span class="icon is-small"><i class="fa fa-table"></i></span>
                        <span><?php echo $matrix->data[$level]->title;?></span>
                    </a>
                    </li>               
             <?php $i++; }?>
             	<li data-target="pane-<?php echo $i;?>" id="newlevel">
					<a>
                        <span class="icon is-small"><i class="fa fa-plus"></i></span>
                        <span class="has-text-success">Добавить уровень раскрытия</span>
                    </a>
                  </li>  
             
             </ul>
        </div>     
		<div class="tab-content" id="matrix" data-status="edit" data-id="<?php echo $matrix->id;?>" data-level="<?php echo $start_level;?>">
			<?php
			$add = ['title' => 'Добавить',	'id' => null, 
					'type' => NULL, 'data' => NULL,	'addons_id' => NULL,'method_id'=>NULL,'object_id'=>NULL];			
			 foreach ($matrix->data as $level=>$sheet) {?>         
            <div class="tab-pane section <?php if ($level==$start_level) {?>is-active<?php }?>" id="pane-<?php echo $level;?>">								
				<table data-level="<?php echo $level;?>" class="table is-striped is-hoverable is-fullwidth">						
					<?php $add['col']= 0;
						$add['row'] = count($sheet->table);
						foreach ($sheet->table[count($sheet->table)-1] as $col=>$val) {
							$add['col'] = $col;
							$sheet->table[$add['row']][$col]= (object) $add; 
						}				
						foreach ($sheet->table as $row=>$cols) {	
							$add['col'] = count($cols);
							$add['row'] = $row;						
							$cols[$add['col']] = (object) $add;					
						?>
						<?php if ($row==0) {?>
						<thead>
						<?php }?>
						<?php if ($row==1) {?>
						</thead>
						<tbody>
						<?php }?>
						<tr <?php if($row==count($sheet->table)-1) echo 'class="has-background-success"'?>>
							<?php foreach ($cols as $col=>$cell) {?>
								<?php if (empty($cell->title)) $cell->title = 'Не указано'; ?>
								<td <?php if($col==$row and $row!=0) echo 'class="has-background-info"'?> <?php if($col==count($cols)-1) echo 'class="has-background-success"'?>>
								<?php if ($row==0) {?>
								<b>
									<div class="object jb-modal" data-addons_id ="<?php echo $cell->addons_id;?>" data-controller="Object" data-col="<?php echo $cell->col;?>" data-target="edit-modal" data-id="<?php echo $cell->id;?>"><?php echo $cell->title;?></div> 
								</b>
								<?php } else {?>								
									<?php if ($col==0) {?>
									<b>
									<div class="method jb-modal" data-addons_id ="<?php echo $cell->addons_id;?>" data-controller="Method" data-row="<?php echo $cell->row;?>"  data-target="edit-modal" data-id="<?php echo $cell->id;?>"><?php echo $cell->title;?></div> 
									</b>
									<?php } else {?>
										<div class="cell jb-modal" data-addons_id ="<?php echo $cell->addons_id;?>" data-controller="Cell" data-method="<?php echo $cell->method_id;?>" data-target="edit-modal" data-object="<?php echo $cell->object_id;?>"><?php echo $cell->title;?></div> 
									<?php }?>
								<?php }?>
								</td>
							<?php }?>
						</tr>						
					<?php }?>				
						</tbody>
					</table>					
					<div class="sheet-edit box">
						<p><input class="input" id="sheet-title<?php echo $level;?>" type="text" placeholder="Заголовок текущего уровня матрицы" value="<?php echo $sheet->title;?>"></p>
						<p><input class="input" id="sheet-description<?php echo $level;?>" type="text" placeholder="Описание текущего уровня матрицы" value="<?php echo $sheet->description;?>"></p>
						<p><input class="input" id="sheet-name<?php echo $level;?>" type="text" placeholder="Автор текущего уровня матрицы" value="<?php echo $sheet->autor;?>"></p>
						<p class="has-text-right"><button class="save-sheet button is-success">Сохранить</button><button class="delete-sheet button is-danger jb-modal" data-target="delete-modal">Удалить уровень</button>
					</div>				
                </div>
<?php }?>
			</div>  
		</div>
  
	
<div id="edit-modal" class="modal">
  <div class="modal-background jb-modal-close"></div>
  <div class="modal-card">
    <header class="modal-card-head">
      <p class="modal-card-title"><input class="input" id="cell-title" type="text" placeholder="Название"></p>
      <button class="delete jb-modal-close" aria-label="close"></button>
    </header>
    <section class="modal-card-body">
      <p><div id="cell-type" class="select">
			<select>
				<option value="TEXT">Текст описания</option>	
				<option value="LIST">Список</option>
				<option value="DICT">Словарь</option>
				<option value="EVENT">Событие</option>				
				<option value="FUNCTION">Функция</option>
			</select>
		</div>
	</p>
    <p><textarea class="textarea" id="cell-data" placeholder="Содержание типа"></textarea></p>
    </section>
    <footer class="modal-card-foot">
      <button class="button jb-modal-close">Отменить</button>
      <button id="cell-save" class="button is-success">Сохранить</button>
    </footer>
  </div>
  <button class="modal-close is-large jb-modal-close" aria-label="close"></button>
</div>
	
	
	
<?php echo $admin_footer;?>
