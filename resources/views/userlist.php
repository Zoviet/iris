<?php echo $admin_menu;?>
<section class="section is-main-section">
	<h1>Все пользователи</h1>
	<table class="table is-bordered is-striped">
		<thead>
			<tr class="has-background-dark">
				<td class="has-text-white"><b>Роль</b></td>
				<td class="has-text-white"><b>Логин</b></td>
				<td class="has-text-white"><b>Имя</b></td>
				<td class="has-text-white"><b>Фамилия</b></td>
				<td class="has-text-white"><b>Мета-данные</b></td>				
				<td class="has-text-white"><b>Ред.</b></td>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($users as $user) {?>
				<tr>
					<td><?php echo $user->role;?></td>
					<td><?php echo $user->login;?></td>
					<td><?php echo $user->first_name;?></td>
					<td><?php echo $user->last_name;?></td>
					<td><?php echo $user->data;?></td>
					<td><a href="/admin/user/edit/<?php echo $user->id;?>"><i class="fa fa-edit has-text-danger"></i></a></td>
				</tr>				
			<?php }?>
		</tbody>
	</table>
</section>
<?php echo $admin_footer;?>
