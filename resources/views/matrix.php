<?php echo $header;?>
    
		  <div class="container">
            <div class="tabs is-boxed is-centered" id="nav">
                <ul>
				  <?php foreach (array_keys($matrix) as $level) {?>
                    <li data-target="pane-<?php echo $level;?>" id="level<?php echo $level;?>" <?php if ($level==0) {?>class="is-active"<?php }?>>
                        <a>
                            <span class="icon is-small"><i class="fa fa-image"></i></span>
                            <span><?php echo $matrix[$level]->title;?></span>
                        </a>
                    </li>               
                   <?php }?>
                </ul>
            </div>     
          </div>

          <div class="tab-content">
     <?php foreach ($matrix as $level=>$sheet) {?> 
        
                <div class="tab-pane  <?php if ($level==0) {?>is-active<?php }?>" id="pane-<?php echo $level;?>">
					
		<section class="section">
                <div class="container">
					<div class="columns">
						
						<?php if ($status=='show') {?>
						<div class="column is-half">							
							<h1 class="title">
								<?php echo $sheet->title;?>
							</h1>
							<h2 class="subtitle">
								<?php echo $sheet->description;?>
							</h2>
							<h4 class="subtitle">
								<?php echo $sheet->autor;?>
							</h4>
						</div>
						<div class="column is-half">							
							<a href="/iris/json/table/<?php echo $matrix_id;?>/" class="button is-danger">JSON</a>
							<a href="" class="button is-danger">Скачать в XLSX</a>							
						</div>
						<?php }?>
						
						<?php if ($status=='edit') {?>
						<div class="column is-half">							
						<form method="post">	
								<div class="field">
									<label class="label">Заголовок</label>
									<div class="control">
										<input class="input is-hidden" name="mainlevel" type="text" placeholder="" value="<?php echo $level;?>">
										<input class="input" name="maintitle" type="text" placeholder="" value="<?php echo $sheet->title;?>">
									</div>
								</div>	
								<div class="field">
									<label class="label">Автор</label>
									<div class="control">
										<input class="input" name="mainautor" type="text" placeholder="" value="<?php echo $sheet->autor;?>">
									</div>
								</div>	
								<div class="field">
									<label class="label">Описание</label>
									<div class="control">
										<textarea name="maindescription" class="textarea" placeholder="" value=""><?php echo $sheet->description;?></textarea>
									</div>
								</div>
								
								<button name="mainsubmit" class="button success is-success">Сохранить</button>
						</form>
						</div>
						<div class="column is-half">							
							<a href="/iris/json/table/<?php echo $matrix_id;?>/" class="button is-danger">JSON</a>
							<a href="/iris/export/<?php echo $matrix_id;?>/" class="button is-danger">Скачать в XLSX</a>
						</div>
						<?php }?>
						
					</div>
                </div>							
        </section>  
				<table class="table is-striped is-bordered is-hoverable is-fullwidth">						
					<?php foreach ($sheet->table as $row=>$cols) {?>
						<?php if ($row==0) {?>
						<thead>
						<?php }?>
						<?php if ($row==1) {?>
						</thead>
						<tbody>
						<?php }?>
						<tr>
							<?php foreach ($cols as $col=>$cell) {?>
								<?php if (empty($cell->title) and $status=='edit') $cell->title = 'Не указано'; ?>
								<td <?php if($col==$row and $row!=0) echo 'class="has-background-info"'?>>
								<?php if ($row==0) {?>
								<b>
									<div class="object" data-addons_id ="<?php echo $cell->addons_id;?>" data-controller="Object" data-col="<?php echo $cell->col;?>" data-id="<?php echo $cell->id;?>"><?php echo $cell->title;?></div> 
								</b>
								<?php } else {?>								
									<?php if ($col==0) {?>
									<b>
									<div class="method" data-addons_id ="<?php echo $cell->addons_id;?>" data-controller="Method" data-row="<?php echo $cell->row;?>" data-id="<?php echo $cell->id;?>"><?php echo $cell->title;?></div> 
									</b>
									<?php } else {?>
										<div class="cell" data-addons_id ="<?php echo $cell->addons_id;?>" data-controller="Cell" data-method="<?php echo $cell->method_id;?>" data-object="<?php echo $cell->object_id;?>"><?php echo $cell->title;?></div> 
									<?php }?>
								<?php }?>
								</td>
							<?php }?>
						</tr>						
					<?php }?>				
						</tbody>
					</table>
                </div>
 
       
<?php }?>
 </div>
 
<?php if ($status=='show') {?>
	<div id="show" class="modal is-hidden">
		<div class="modal-background"></div>
		<div class="modal-card">
			<header class="modal-card-head">
				<p class="modal-card-title title"></p>
				<button class="delete closer" aria-label="close"></button>
			</header>
			<span class="is-hidden id"></span>
			<section class="modal-card-body has-background-light has-text-black">
				<p><b>ТИП:</b> <span class="type"></span></p>
				<p><b>ЗНАЧЕНИЕ:</b> <span class="data"></span></p>
			</section>			
			<footer class="modal-card-foot">
				<button class="button success is-success">Закрыть</button>				
			</footer>
		</div>
	</div>
<?php }?>



<?php if ($status=='edit') {?>
	<div id="edit" class="modal is-hidden">
		<div class="modal-background"></div>
		<form method="post">		
		<div class="modal-card">
			<header class="modal-card-head">																			
			<input class="input id is-hidden" name="id" type="text" placeholder="" value="">
			<input class="input controller is-hidden" name="controller" type="text" placeholder="" value="">	
			<input class="input addons_id is-hidden" name="addons_id" type="text" placeholder="" value="">			
			</header>
			<section class="modal-card-body has-background-light has-text-black">
				<div class="field">
					<label class="label">Заголовок</label>
					<div class="control">
						<textarea name="title" class="textarea title" placeholder="" value=""></textarea>					
					</div>
				</div>	
				
				<div class="field">
					<label class="label">ТИП:</label>
				<div class="control">
				<div class="select">
					<select name="type">
						<option class="selector" value="TEXT">TEXT</option>
						<option class="selector" value="LIST">LIST</option>
						<option class="selector" value="FUNCTION">FUNCTION</option>
						<option class="selector" value="DICT">DICT</option>
						<option class="selector" value="EVENT">EVENT</option>
					</select>
				</div>			
				</div>
				<div class="field">
					<label class="label">Значение для типа</label>
					<div class="control">
						<textarea name="data" class="textarea data" placeholder="" value=""></textarea>
					</div>
				</div>	
			</section>			
			<footer class="modal-card-foot">					
				<button name="submit" id="save" class="button success is-success">Сохранить</button>			
			</form>
				<a class="button success is-success">Закрыть</a>	
			</footer>
		</div>		
	</div>
<?php }?>


<div id="status" data-status="<?php echo $status;?>"></div>
<?php echo $footer;?>
