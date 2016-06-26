<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Создание нового контента</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          {if $content.type == "blogs"}
          <a href="/admin/blogs/">Блоги</a>
          {else}
          <a href="/admin/landings/">Лендинги</a>
          {/if}
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Создание контента</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->

<!-- START NOTIFICATIONS -->
<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      Создание контента
    </div>
    <div class="actions"></div>
  </div>
  <div class="portlet-body">
    <div class="row">
      <div class="col-md-12">
        <div class="alert alert-danger" {if !$message}style="display:none"{/if}>{$message}</div>
      </div>
    </div>
    <!-- END NOTIFICATIONS -->

    <div class="row">
      <div class="col-md-6">
        {$form}
      </div>
    </div>
  </div>
</div>

