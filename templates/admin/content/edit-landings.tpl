<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Редактирование лендинга {if $content.name}`{$content.name}`{/if}</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="/admin/landings/">Лендинги</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Редактирование лендинга {if $content.name}`{$content.name}`{/if}</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->

<!-- START NOTIFICATIONS -->
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
