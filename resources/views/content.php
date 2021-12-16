<?php echo $header;?>

					
		<section class="section">
                <div class="container">
					<div class="columns">
						<div class="column is-12">
							<div class="container">
								<h1><?php echo $title;?></h1>
								<?php echo $content;?>
							</div>
						</div>
					</div>
                </div> 
        </section>

<?php if(!empty($errors)) {?>
	<div class="modal is-active">
		<div class="modal-background"></div>
		<div class="modal-card">
			<header class="modal-card-head has-background-danger">
				<p class="modal-card-title title">Ошибка!</p>
				<button class="delete closer" aria-label="close"></button>
			</header>
			<span class="is-hidden id"></span>
			<section class="modal-card-body has-background-light has-text-black">
				<ul>
				<?php foreach ($errors as $error) {?>
					<li><?php echo $error;?></li>
				<?php }?>
				</ul>
			</section>			
			<footer class="modal-card-foot">
				<button class="button success is-success">Закрыть</button>				
			</footer>
		</div>
	</div>
	<?php }?>


<?php echo $footer;?>
