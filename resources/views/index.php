<?php echo $header;?>
<section class="hero is-success is-fullheight matrix">
  <!-- Hero head: will stick at the top -->
  <div class="hero-head">
    <header class="navbar">
      <div class="container">
        <div class="navbar-brand">
          <a href="/iris/" style="margin-top:2rem">
            <img class="iris_logo" src="/resources/img/logo.png" alt="Logo">
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
    </header>
  </div>

  <!-- Hero content: will be in the middle -->
  <div class="hero-body">
    <div class="container has-text-centered">	
         <div class="column is-4 is-offset-4">			   
					<form method="post" action="/iris/search">
					<div class="field has-addons">
						<div class="control">
							<input name="string" class="input is-dark is-large" type="text" placeholder="Понятие">
						</div>
						<div class="control">
							<button id="searchsubmit" class="is-dark is-large button" type="submit" name="submit_search">Поиск</button>
						</div>						
					</div>
					</form>
					    <hr class="login-hr">
                       </div>
	       
    </div>
  </div>

  <!-- Hero footer: will stick at the bottom -->
  <div class="hero-foot">
    <nav class="tabs is-boxed is-fullwidth">
      <div class="container">
        <ul>
          <li><a href="/iris/docs">Документация</a></li>
          <li><a href="/iris/catalog">Каталог матриц</a></li>
          <li><a href="/iris/format">Образец формата файла матрицы</a></li>
          <li><a href="/iris/generator">Генератор матриц</a></li>        
          <li><a href="/iris/login">Вход</a></li>
        </ul>
      </div>
    </nav>
  </div>
</section>
<?php echo $footer;?>
