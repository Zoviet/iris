<?php echo $admin_menu;?>    
	<div class="card">
		<header class="card-header">              
			<div class="columns card-header-title">
				<div class="column is-8">
					<h1 class="title"><?php echo $matrix->title;?></h1>
					<?php if (!empty($matrix->description)) {?>
					<span class="icon is-small"><i class="fa fa-info"></i></span> <?php echo $matrix->description;?><br/>
					<?php }?>
					<?php if (!empty($matrix->name)) {?>
					<span class="icon is-small"><i class="fa fa-user"></i></span> <?php echo $matrix->name;?><br/>
					<?php }?>
				</div>
				<div class="column is-4 has-text-right">
					<a href="/json/table/<?php echo $matrix->id;?>/" class="button is-primary">JSON типа iris</a>
					<a href="" class="button is-success">Скачать в XLSX</a>	
				</div>
			</div>
		</header>
	</div>  			  		  
	<div class="container">
		<div class="tabs is-boxed" id="nav">
			<ul>
			<?php foreach (array_keys($matrix->data) as $level) {?>
				<li data-target="pane-<?php echo $level;?>" id="level<?php echo $level;?>" <?php if ($level==0) {?>class="is-active"<?php }?>>
					<a>
                        <span class="icon is-small"><i class="fa fa-table"></i></span>
                        <span><?php echo $matrix->data[$level]->title;?></span>
                    </a>
                    </li>               
             <?php }?>
             </ul>
        </div>     
		<div class="tab-content">
			<?php foreach ($matrix->data as $level=>$sheet) {?>         
            <div class="tab-pane  <?php if ($level==0) {?>is-active<?php }?>" id="pane-<?php echo $level;?>">								
				<table class="table is-striped is-hoverable is-fullwidth">						
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
								<?php if (empty($cell->title)) $cell->title = 'Не указано'; ?>
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
					<?php if (!empty($sheet->description)) {?>
					<div class="box"><p><span class="icon is-small"><i class="fa fa-info"></i></span> <?php echo $sheet->description;?></p></div>
					<?php }?>
                </div>
<?php }?>
		</div>
    </div>
<?php echo $admin_footer;?>
