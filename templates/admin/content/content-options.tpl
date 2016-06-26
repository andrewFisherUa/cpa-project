<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Настройки контента</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Настройки контента</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->

<!-- END NOTIFICATIONS -->
<form action="/admin/content-options" method="post">
  <div class="portlet light">
    <div class="portlet-title">
      <div class="caption">
        Настройки контента
      </div>
      <div class="actions">
        <button class="btn btn-circle blue" type="submit" name="save">Сохранить</button>
      </div>
    </div>
    <div class="portlet-body">
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
      <div class="row">
        <div class="col-md-10">
          {$options_list}
        </div>
      </div>
    </div>
  </div>
</form>
