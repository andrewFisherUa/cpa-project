<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Редактирование роли</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Редактирование роли</a>
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

<div class="row">
  <div class="col-md-12">
      <form action="/admin/permissions/roles/" method="post">
        <input type="hidden" name="role_id" value="{$role_id}">
        <div class="form-group">
          <label class="control-label">Название роли: </label>
          <div>
            <input type="text" class="form-control input-medium" name="role_name" value="{$role_name}">
          </div>
        </div>
        <div class="form-group">
          <button class="btn default" name="save_role">Сохранить</button>
          <button class="btn default" name="remove_role">Удалить</button>
        </div>
      </form>
  </div>
</div>