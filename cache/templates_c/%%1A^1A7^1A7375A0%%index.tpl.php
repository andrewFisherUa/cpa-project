<?php /* Smarty version 2.6.22, created on 2016-06-14 16:22:41
         compiled from admin%5Cflows%5Cindex.tpl */ ?>
<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Мои потоки</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Потоки</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->
<div class="row" id="page-flows">
  <div class="col-md-12">

    <div class="portlet light">
      <div class="portlet-title">
        <div class="caption">
          Создание и редактирование потоков
        </div>
        <div class="actions">
          <a href="/admin/flows/new" class="btn blue add-item"><i class="fa fa-plus"></i> Создать поток</a>
          <a href="/admin/flows/postback" class="btn green-meadow">Глобальный Postback</a>
        </div>
      </div>
      <div class="portlet-body">
        <div class="alert alert-danger" style="display:none"></div>
        <div class="alert alert-success" style="display:none"></div>

        <input type="hidden" id="flow_uid" value="<?php echo $this->_tpl_vars['user_id']; ?>
">

        <div class="table-container" style="overflow:hidden;">
          <table class="table table-striped table-bordered table-hover" id="datatable_flows" data-uid="<?php echo $this->_tpl_vars['user_id']; ?>
" data-oid="0">
            <thead>
              <tr role="row" class="heading">
                <th width="20%">Название потока</th>
                <th width="14%">Дата изменения</th>
                <th width="15%">Источники</th>
                <th width="20%">Оффер</th>
                <th width="17%">Ссылка</th>
                <th width="14%">Действия</th>
              </tr>
              <tr role="row" class="filter">
                <td>
                  <select class="form-control select2me table-filter input-sm" data-placeholder="Название потока" name="flow_id">
                    <option value="0">Выберите поток</option>
                    <?php $_from = $this->_tpl_vars['flows']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['flow']):
?>
                      <option value="<?php echo $this->_tpl_vars['flow']['f_id']; ?>
"><?php echo $this->_tpl_vars['flow']['name']; ?>
</option>
                    <?php endforeach; endif; unset($_from); ?>
                  </select>
                </td>
                <td></td>
                <td></td>
                <td>
                  <select class="form-control select2me table-filter input-sm" data-placeholder="Оффер" name="offer_id">
                    <option value="0">Выберите оффер</option>
                    <?php $_from = $this->_tpl_vars['offers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['offer']):
?>
                      <option value="<?php echo $this->_tpl_vars['offer']->getId(); ?>
"><?php echo $this->_tpl_vars['offer']->getName(); ?>
</option>
                    <?php endforeach; endif; unset($_from); ?>
                  </select>
                </td>
                <td></td>
                <td>
                  <!--<button class="btn btn-sm yellow filter-submit margin-bottom"><i class="fa fa-search"></i> Поиск</button>-->
                  <button class="btn btn-sm red reset-filters"><i class="fa fa-times"></i> Отмена</button>
                </td>
            </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="flowModal" role="dialog" aria-labelledby="dialogLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="dialogLabel">Редактирование потока</h4>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger" style="display:none"></div>
        <div class="alert alert-success" style="display:none"></div>
        <div class="form-container"></div>
      </div>
    </div>
  </div>
</div>