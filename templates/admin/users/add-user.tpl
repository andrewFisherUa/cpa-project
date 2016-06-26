<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Новый пользователь</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Добавление пользователя</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">Новый пользователь</div>
  </div>
  <div class="portlet-body">
    <div class="alert {if $message}alert-{$message.class_name}{else}alert-danger{/if}" {if !$message}style="display:none"{/if}>
      {$message.text}
    </div>
    <div class="row">
      <div class="col-md-6">
        <form action="/admin/users/add/" class="form" method="post" id="add-user-frm">
          <div class="form-group">
            <label for="" class="control-label">E-mail: <span class="required">*</span></label>
            <input type="text" class="form-control" name="email">
          </div>
          <div class="form-group">
            <label for="" class="control-label">Пароль: <span class="required">*</span></label>
            <input type="text" class="form-control" name="password">
          </div>
          <div class="form-group">
            {foreach from=$roles item=role}
              <div class="checkbox">
                <label> <input type="checkbox" name="roles[]" class="role" value="{$role.role_id}"> {$role.role_name} </label>
              </div>
            {/foreach}
          </div>
          <div class="form-group">
            <button class="btn green" name="add_user">Сохранить</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
