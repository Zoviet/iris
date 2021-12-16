<?php echo $admin_menu;?>
<section class="section is-main-section">
<h1 class="title">Добавление ключа API</h1>

<form method="post"> 
 	
		<div class="field">
  <label class="label">Пользователь</label>
  <div class="control">
	  <div class="select">
  <select name="data[user_id]">
	<?php foreach ($users as $user) {?>
		<option value="<?php echo $user->id;?>"><?php echo $user->login;?></option>
	<?php }?>
  </select>
</div>

  </div>
</div>

<div class="field is-horizontal">
    <div class="field">
      <div class="control">
        <button name="submit" class="button is-large is-primary">
          Сгенерировать и добавить
        </button>
      </div>
    </div>
</div>


</form>
</div>
<?php echo $admin_footer;?>
