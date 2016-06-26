<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Роли</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Редактирование ролей</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->

<!-- START NOTIFICATIONS -->
{if $message}
  <div class="row">
    <div class="col-md-12">
      <div class="alert alert-{$message.class_name}">
        {$message.text}
      </div>
    </div>
  </div>
{/if}
<!-- END NOTIFICATIONS -->

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      Настройка ролей
    </div>
    <div class="actions">
      <a href="javascript:;" class="btn btn-circle btn-default btn-edit" data-role="0">
        <i class="fa fa-plus"></i> <span class="hidden-480">Новая роль</span>
      </a>
    </div>
  </div>
  <div class="portlet-body">
    <div class="row">
      <div class="col-md-12">
        <table class="table table-condensed table-striped table-bordered" id="datatable_roles">
          <thead>
            <tr>
              <th width="2%"></th>
              <th width="5%">#</th>
              <th width="50%">Имя пользователя</th>
              <th width="43%">Действия</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="edit-role" tabindex="-1" role="dialog">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
              <h4 class="modal-title">Редактирование роли</h4>
          </div>
          <div class="modal-body">

          </div>
          <div class="modal-footer">
            <button class="btn green" id="save-role">Сохранить</button>
          </div>
      </div>
      <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>