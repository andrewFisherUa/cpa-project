<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Редактирование группы</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="/admin/groups/">Группы</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Редактирование группы</a>
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

{if $group}
<div class="row">
  <div class="col-md-12">
      <form action="/admin/groups/" method="post">
        <input type="hidden" name="group_id" value="{$group.g_id}">
        <div class="form-group">
          <label class="control-label">Название группы: </label>
          <div>
            <input type="text" class="form-control input-medium" name="group_name" value="{$group.name}">
          </div>
        </div>
        <div class="form-group">
          <button class="btn default" name="save_group">Сохранить</button>
          <button class="btn default" name="remove_group">Удалить</button>
        </div>
      </form>
  </div>
</div>
{/if}