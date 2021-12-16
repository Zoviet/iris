<?php echo $admin_menu;?>    
<div class="card has-table has-mobile-sort-spaced">
      <header class="card-header">
        <p class="card-header-title">
          <span class="icon"><i class="mdi mdi-account-multiple"></i></span>
          Матрицы
        </p>
        <a href="#" class="card-header-icon">
          <span class="icon"><i class="mdi mdi-reload"></i></span>
        </a>
      </header>
      <div class="card-content">
        <div class="b-table has-pagination">
          <div class="table-wrapper has-mobile-cards">
            <table class="table is-fullwidth is-striped is-hoverable is-sortable is-fullwidth">
              <thead>
              <tr>
                <th></th>
                <th>Автор</th>
                <th>Название</th>                               
                <th>Публ.</th>  
                <th>Обновлена</th>
                <th></th>
              </tr>
              </thead>
              <tbody>
			  <?php foreach ($matrixs as $matrix) {?>
              <tr id="matrix<?php echo $matrix->id;?>">
                <td style="width:5%" class="is-image-cell">
                  <div class="image">
                    <img src="https://avatars.dicebear.com/v2/initials/<?php echo $matrix->name;?>.svg" class="is-rounded">
                  </div>
                </td>
                <td style="width:15%" data-label="Autor"><?php echo $matrix->name;?></td>
                <td data-label="Name"><?php echo $matrix->title;?></td>                         
                <td data-label="Public" class="has-text-centered"><?php if ($matrix->public==1) {?>
				<span class="icon"><i class="fa fa-circle has-text-success"></i></span>	
				<?php } else {?>
				<span class="icon"><i class="fa fa-circle has-text-secondary"></i></span>		
				<?php }?>
				</td> 
                <td style="width:10%" data-label="Created">
                  <small class="has-text-grey is-abbr-like" title="Oct 25, 2020"><?php echo $matrix->updated_at;?></small>
                </td>
                <td style="width:15%" class="is-actions-cell">
                  <div class="buttons is-right">				
                    <button class="button is-small is-primary" type="button">
                      	 <a href="/admin/show/<?php echo $matrix->id;?>" title="Просмотр"><span class="icon"><i class="mdi mdi-eye"></i></span></a>
                    </button>
                     <button class="button is-small is-warning" type="button">
                      	 <a href="/admin/edit/<?php echo $matrix->id;?>" title="Редактирование"><span class="icon"><i class="mdi mdi-account-edit"></i></span></a>
                    </button>
                    <button data-id="<?php echo $matrix->id;?>" class="delete-matrix button is-small is-danger jb-modal" data-target="delete-modal" type="button">
                      <span class="icon"><i class="mdi mdi-trash-can"></i></span>
                    </button>
                  </div>
                </td>
              </tr>  
              <?php }?>          
              </tbody>
            </table>
          </div>
          <div class="notification">
            <div class="level">
              <div class="level-left">
                <div class="level-item">
                  <div class="buttons has-addons">
					<?php for ($p=1;$p<=$pages;$p++) {?>
						<a href="/admin/catalog/<?php echo $p-1;?>" type="button" class="button <?php if ($page==$p-1) echo 'is-active';?>"><?php echo $p;?></a>
                    <?php }?>           
                  </div>
                </div>
              </div>
              <div class="level-right">
                <div class="level-item">
                  <small>Страница <?php echo $page+1;?> из <?php echo $pages;?></small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
<?php echo $admin_footer;?>
