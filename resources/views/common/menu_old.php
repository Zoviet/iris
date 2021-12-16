<?php echo $header;?>   
<nav class="navbar is-black" role="navigation" aria-label="main navigation">
      <div class="container">
        <div class="navbar-brand">
          <a href="/iris/" class="navbar-item">
            <img class="iris_logo" src="/iris/resources/img/logo.png" alt="Logo">
          </a>
          <span class="navbar-burger" data-target="navbarMenuHeroC">
            <span></span>
            <span></span>
            <span></span>
          </span>
        </div>
        <div id="navbarMenuHeroC" class="navbar-menu">
          <div class="navbar-end">  
            <div class="navbar-item">
            	<div class="field is-grouped">
					<form enctype="multipart/form-data" method="post" action="/iris/upload">
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
            </div> 
          </div>
        </div>
      </div>
    </nav>
