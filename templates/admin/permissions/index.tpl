<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Права доступа</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Права доступа</a>
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

<form action="/admin/permissions/" method="post" name="edit_perms">
  <div class="portlet light">
    <div class="portlet-title">
      <div class="caption">
        Права доступа
      </div>
      <div class="actions">
        <button type="submit" name="save_perms" class="btn btn-circle blue">Сохранить</button>
      </div>
    </div>
    <div class="portlet-body">
      <div class="table-wrapper">
       <table class="table tables-hover table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Group</th>
                    <th>Description</th>
                    <th>Name</th>
                    {foreach from=$roles item=role}
                    <th class="text-center">{$role.role_name}</th>
                    {/foreach}
                </tr>
            </thead>
            <tbody>
               {$rows}
            </tbody>
       </table>
      </div>
    </div>
  </div>
</form>


