<?php echo $header;?>

		<section class="section">
                <div class="container">
					<div class="columns">
						<div class="column is-8">
							<div class="container">
								<h1>Текст для обработки:</h1>							
									<div class="field is-grouped">										
										<div class="control">
											<button id="submittext" class="button is-info">Анализировать</button>
										</div>
									</div>
									<div class="field is-grouped">									
											<textarea id="textset" name="text" class="textarea" rows="20"></textarea>									
									</div>						
							</div>
						</div>
						<div class="column is-4">
							<h2 class="title">Результаты:</h2>
							<div class="content" id="result">
							
							</div>
						</div>
					</div>
                </div> 
        </section>

<div id="status" data-status="<?php echo $status;?>"></div>
<?php echo $footer;?>
