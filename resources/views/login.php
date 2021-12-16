 <?php echo $login_header;?>
    <section class="hero is-success is-fullheight">
        <div class="hero-body passport">
            <div class="container has-text-centered">
                <div class="column is-4 is-offset-4">
                    <h3 class="title has-text-black">Вход</h3>
                    <hr class="login-hr">
                    <p class="subtitle has-text-black">Please login to proceed.</p>
                    <div class="box">
                        <figure class="avatar">
                            <img class="avatar_img" src="/resources/img/avatar.jpg">
                        </figure>
                        <form method="post">
                            <div class="field">
                                <div class="control">
                                    <input name="login" class="input is-large" type="text" placeholder="Логин" autofocus="">
                                </div>
                            </div>

                            <div class="field">
                                <div class="control">
                                    <input name="password" class="input is-large" type="password" placeholder="Пароль">
                                </div>
                            </div>
                            <div class="field">
                                <label class="checkbox">
                  <input type="checkbox">
					Запомнить меня
                </label>
                            </div>
                            <button name="submit" class="button is-block is-info is-large is-fullwidth">Войти <i class="fa fa-sign-in" aria-hidden="true"></i></button>
                        </form>
                    </div>             
                </div>
            </div>
        </div>
    </section>
    
    <div id="message_modal" class="modal <?php if (empty($error)) echo 'is-hidden'; else echo 'is-active'?>">
		<div class="modal-background"></div>
		<div class="modal-card">
			<header class="modal-card-head">
				<p class="modal-card-title">Внимание!</p>
				<button class="delete closer" aria-label="close"></button>
			</header>
			<section class="modal-card-body has-background-warning has-text-black">
				<div id="message" class="content is-size-5"><?php if (!empty($error)) echo $error;?></div>
			</section>
			<footer class="modal-card-foot">
				<button class="button success is-success">Закрыть</button>
			</footer>
		</div>
	</div>
<?php echo $footer;?>
