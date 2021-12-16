<?php echo $admin_menu;?>    
	<div class="card">
		<header class="card-header">              
			<div class="columns card-header-title">
				<div class="column is-8">
					<h1 class="title">Загрузка новой матрицы из файла</h1>				
				</div>
				<div class="column is-4 has-text-right">				
				</div>
			</div>
		</header>
	</div>  			  		  
	<div class="container">
		<div class="columns">
			<div class="column is-5">
				<div class="field is-grouped">
					<form enctype="multipart/form-data" method="post" action="/admin/fileupload">
						<div class="field has-addons">
							<div class="control">
								<div class="file is-success">
									<label class="file-label">							
										<input id="upload" class="file-input is-large" type="file" size="32" name="file" value="">
											<span class="file-cta">
												<span class="file-icon">
													<i class="fas fa-upload"></i>
												</span>
												<span id="uploadlabel" class="file-label">
													Выбрать файл (xlsx,ods,csv,xls)
												</span>
											</span>																					
									</label>
								</div>
							</div>
						<div class="control">
							<button id="filesubmit" class="is-dark button" type="submit" name="submit_upload">Загрузить матрицу</button>
						</div>
						</div>
					</form>
                </div>	                
                <div class="content">			
					<?php if (!empty($errors)) {?>
						<div class="box has-background-danger">
							<?php foreach ($errors as $error) {?>
								<p><?php echo $error;?></p>
							<?php }?>
						</div>						
					<?php } else echo $formats;?>
				</div>
			</div>
			<div class="column is-7">
				<div class="content">
					<?php echo $content;?>
				</div>
			</div>			
		</div>
    </div>
<?php echo $admin_footer;?>
