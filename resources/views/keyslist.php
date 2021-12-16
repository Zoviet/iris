<?php echo $admin_menu;?>
<section class="section is-main-section">
	<h1>Выданные ключи API</h1>
	<table class="table is-bordered is-striped">
		<thead>
			<tr class="has-background-dark">
				<td class="has-text-white"><b>Пользователь</b></td>
				<td class="has-text-white"><b>Ключ</b></td>
				<td class="has-text-white"><b>Дата выдачи</b></td>						
				<td class="has-text-white"><b>Удалить.</b></td>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($keys as $key) {?>
				<tr>
					<td><?php echo $key->login;?></td>
					<td><?php echo $key->key;?></td>
					<td><?php echo $key->created_at;?></td>			
					<td><a href="/admin/keys/delete/<?php echo $key->id;?>"><i class="fa fa-remove has-text-danger"></i></a></td>
				</tr>				
			<?php }?>
		</tbody>
	</table>
</section>
<?php echo $admin_footer;?>
