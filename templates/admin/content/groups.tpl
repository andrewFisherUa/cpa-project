<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Группы контента</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Редактирование групп контента</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">Группы контента</div>
    <div class="actions">
      <a href="/admin/landings" class="btn blue-hoki">Лендинги</a>
      <a href="/admin/blogs" class="btn green-meadow">Блоги</a>
      <a href="javascript:;" class="btn yellow-crusta btn-edit" data-group="0"><i class="fa fa-plus"></i> Новая группа</a>
    </div>
  </div>
  <div class="portlet-body">

    <div class="row">
      <div class="col-md-12">
          <table class="table table-condensed table-striped table-bordered" id="datatable_groups">
            <thead>
              <tr>
                <th width="5%">#</th>
                <th width="85%">Группа</th>
                <th width="10%">Действия</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="edit-group" tabindex="-1" role="dialog">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
              <h4 class="modal-title">Редактирование группы</h4>
          </div>
          <div class="modal-body">

          </div>
          <div class="modal-footer">
            <button class="btn green" id="save-group">Сохранить</button>
          </div>
      </div>
      <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
