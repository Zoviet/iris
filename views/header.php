<?php ?><!DOCTYPE html>
<html lang="ru">

<head>
	<meta name="robots" content="index, follow">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=7">
	<link rel="stylesheet" href="/views/css/style.css">
	<meta name="language" content="ru-RU">
	<meta name="author" content="Asustem.ru">
	<title><?php echo $title;?></title>	
	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
</head>

<body>
	<header class="hero">
		<div class="hero-head">
			<nav class="navbar has-shadow" role="navigation" aria-label="main navigation">
				<div class="navbar-brand">
					<a href="<?php echo $base_url;?>" class="navbar-item is--brand">
						Стенд
					</a>
					<a class="navbar-item is-tab is-hidden-mobile is-active"><span class="icon is-medium"><i class="fa fa-home"></i></span>Главная</a>				
					<button class="button navbar-burger" data-target="navMenu">
						<span></span>
						<span></span>
						<span></span>
					</button>
				</div>
				<div class="navbar-menu navbar-end" id="navMenu">
					<a class="navbar-item is-tab is-hidden-tablet is-active">Home</a>
					<a class="navbar-item is-tab is-hidden-tablet" href="https://github.com/mazipan/bulma-admin-dashboard-template">Github</a>
					<a class="navbar-item is-tab is-hidden-tablet" href="https://mazipan.github.io/bulma-resume-template/">Resume Template</a>
					<a class="navbar-item is-tab is-hidden-tablet" href="#about">About</a>
					<div class="navbar-item has-dropdown is-hoverable">
						<a class="navbar-link" href="https://mazipan.space/">
							<figure class="image is-32x32" style="margin-right:.5em;">
								<img src="https://avatars1.githubusercontent.com/u/7221389?v=4&s=32">
							</figure>
							Admin
						</a>
						<div class="navbar-dropdown is-right">
							<a class="navbar-item">
								<span class="icon is-small">
									<i class="fa fa-user-o"></i>
								</span>
								&nbsp; Профиль
							</a>
							<hr class="navbar-divider">
							<a class="navbar-item">
								<span class="icon is-small">
									<i class="fa fa-power-off"></i>
								</span>
								&nbsp; Выход
							</a>
						</div>
					</div>
				</div>
			</nav>
		</div>
	</header>
