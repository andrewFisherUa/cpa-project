<?php /* Smarty version 2.6.22, created on 2016-06-13 17:44:45
         compiled from admin%5Cnavigation%5Cindex.tpl */ ?>
<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Меню</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Меню</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->

<div id="menu-links-table-wrap">
	<form action="/admin/navigation" method="post">
		<div class="portlet light">
		  <div class="portlet-title">
		    <div class="caption">
				Меню
		    </div>
		    <div class="actions">
		    	<a data-id="0" data-action="add" class="btn btn-default btn-circle add-item"><i class="fa fa-plus"></i> Добавить страницу</a>
		    	<button class="btn green btn-circle" type="submit" name="save_perms">Сохранить</button>
		    </div>
		  </div>
		  <div class="portlet-body">
			<div class="margin-top-5">
				<table class="table table-bordered table-stripped table-header-fixed" id="menu-links-table">
					<thead>
						<tr>
							<th width="22%">Страница</th>
							<th width="5%">Вес</th>
							<th width="50%" colspan="<?php echo $this->_tpl_vars['rolesNum']; ?>
">
								<table class="table table-condensed">
									<thead>
										<th colspan="<?php echo $this->_tpl_vars['rolesNum']; ?>
">
											Доступ к странице
										</th>
										<?php if ($this->_tpl_vars['labels']): ?>
											<tr>
												<?php $_from = $this->_tpl_vars['labels']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['label']):
?>
													<th width="<?php echo $this->_tpl_vars['colWidth']; ?>
%"><?php echo $this->_tpl_vars['label']; ?>
</th>
												<?php endforeach; endif; unset($_from); ?>
											</tr>
										<?php endif; ?>
									</thead>
								</table>
							</th>
							<th width="25%">Действия</th>
						</tr>
					</thead>
					<tbody>
						<?php echo $this->_tpl_vars['links']; ?>

					</tbody>
				</table>
			</div>
		  </div>
		</div>
	</form>
</div>

<!-- Modal -->
<div class="modal fade" id="menu-modal" role="dialog" aria-labelledby="menu-modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="menu-modalLabel">Добавить страницу</h4>
      </div>
      <div class="modal-body">
      	<form class="form">
      		<input type="hidden" id="menu-id" name="menu_id" value="0'">
		  <div class="form-group">
		    <label class="control-label">Название страницы:</label>
		    <input type="text" class="form-control" name="menu_title" id="menu-title">
		  </div>
		  <div class="form-group">
		    <label class="control-label">Ссылка:</label>
		    <input type="text" class="form-control" name="menu_link" id="menu-link">
		  </div>
		  <div class="form-group">
		    <label class="control-label">Описание:</label>
		    <p class="help-block">Показывается при наведении курсора на категорию</p>
		    <textarea col="10" rows="4" class="form-control" name="menu_desc" id="menu-desc"></textarea>
		  </div>
		  <div class="form-group">
		    <label class="control-label">Родительская страница:</label>
		    <div class="row">
		    	<div class="col-md-3">
		    		<select class="form-control col-md-3" name="menu_parent" id="menu-parent" style="min-width:100%">
		    			<?php $_from = $this->_tpl_vars['parents']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['link']):
?>
		    			<option value="<?php echo $this->_tpl_vars['link']['m_id']; ?>
"><?php echo $this->_tpl_vars['link']['title']; ?>
</option>
		    			<?php endforeach; endif; unset($_from); ?>
				    </select>
		    	</div>
		    </div>
		  </div>
		  <div class="form-group">
		    <label class="control-label">Вес:</label>
		    <p class="help-block">Страницы с меньшим весом выводятся раньше страниц с большим весом</p>
		    <div class="row">
		    	<div class="col-md-3">
		    		<select class="form-control" name="menu_weight" id="menu-weight">
				    	<option value="0">0</option>
				    	<option value="1">1</option>
				    	<option value="2">2</option>
				    	<option value="3">3</option>
				    	<option value="4">4</option>
				    	<option value="5">5</option>
				    </select>
		    	</div>
		    </div>

		  </div>

		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn btn-primary" onclick="addCategory()">Сохранить</button>
      </div>
    </div>
  </div>
</div>