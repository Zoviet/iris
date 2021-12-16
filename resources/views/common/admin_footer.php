 <footer class="footer">
    <div class="container-fluid">
      <div class="level">
        <div class="level-left">
          <div class="level-item">
            © 2021
          </div>
          <div class="level-item">
            <a href="" style="height: 20px">
              <img src="https://img.shields.io/github/v/release/vikdiesel/admin-one-bulma-dashboard?color=%23999">
            </a>
          </div>
        </div>
        <div class="level-right">
          <div class="level-item">
            <div class="logo">
          
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</div>

<div id="delete-modal" class="modal">
  <div class="modal-background jb-modal-close"></div>
  <div class="modal-card">
    <header class="modal-card-head has-background-danger">
      <p class="modal-card-title has-text-white"><b>Подтвердите удаление</b></p>
      <button class="delete jb-modal-close" aria-label="close"></button>
    </header>
    <section class="modal-card-body">
      <p>Удалить <b><span id="delete-title"></span></b>?</p> 
    </section>
    <footer class="modal-card-foot">
      <button class="button jb-modal-close">Отменить</button>
      <button id="delete" class="button is-danger jb-modal-close">Удалить</button>
    </footer>
  </div>
  <button class="modal-close is-large jb-modal-close" aria-label="close"></button>
</div>

	<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
	<script src="<?php echo $base_url;?>resources/js/jquery.tablesorter.js"></script>
	<script src="<?php echo $base_url;?>resources/js/jquery.tablesorter.widgets.js"></script>
    <script src="<?php echo $base_url;?>resources/js/main.js"></script>
    <script type="text/javascript" src="<?php echo $base_url;?>resources/js/admin.min.js"></script>
</body>
</html>
