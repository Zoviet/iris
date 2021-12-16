<?php echo $admin_menu;?>
<section class="section is-main-section">
<h1 class="title">Добавление/редактирование пользователя</h1>

<form method="post">
 <input name="data[id]" class="input" value="<?php echo $user['id'];?>" type="hidden">
 	
		<div class="field">
  <label class="label">Роль</label>
  <div class="control">
	  <div class="select">
  <select name="data[role]">
    <option <?php if($user['role']=='admin') echo 'selected="selected"';?> value="admin">Администратор</option>
    <option <?php if($user['role']=='client') echo 'selected="selected"';?> value="client">Клиент API</option>
  </select>
</div>

  </div>
</div>
	
	
	<div class="field">
  <label class="label">Логин</label>
  <div class="control">
    <input name="data[login]" class="input" type="text" value="<?php echo $user['login'];?>">
  </div>
</div>

	<div class="field">
  <label class="label">Имя</label>
  <div class="control">
    <input name="data[first_name]" class="input" type="text" value="<?php echo $user['first_name'];?>">
  </div>
</div>

	<div class="field">
  <label class="label">Фамилия</label>
  <div class="control">
    <input name="data[last_name]" class="input" type="text" value="<?php echo $user['last_name'];?>">
  </div>
</div>

	<div class="field">
  <label class="label">Пароль</label>
  <div class="control">
    <input name="data[password]" class="input" type="password" value="<?php echo $user['password'];?>">
  </div>
</div>

		<div class="field">
  <label class="label">Описание (мета-данные: телефон, адрес, емайл)</label>
  <div class="control">
<textarea name="data[data]"  class="textarea"  rows="4"><?php echo $user['data'];?></textarea>
</div>
</div>

<div class="field is-horizontal">
    <div class="field">
      <div class="control">
        <button name="submit" class="button is-large is-primary">
          Сохранить
        </button>
      </div>
    </div>
     <div class="field">
      <div class="control">
        <button name="delete" class="button is-large is-danger">
          Удалить
        </button>
      </div>
    </div>
</div>


</form>
</section>
<?php echo $admin_footer;?>
