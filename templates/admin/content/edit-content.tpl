<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">
  Редактирование {if $type == "landing"}лендинга{else}блога{/if} {if $content.name}`{$content.name}`{/if}
</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          {if $type == "landing"}
            <a href="/admin/landings/">Лендинги</a>
          {else}
            <a href="/admin/blogs/">Блоги</a>
          {/if}
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Редактирование {if $type == "landing"}лендинга{else}блога{/if} {if $content.name}`{$content.name}`{/if}</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      Редактирование {if $type == "landing"}лендинга{else}блога{/if} {if $content.name}`{$content.name}`{/if}
    </div>
    <div class="actions">
      <a href="/admin/{$type}s/new/" target="_blank" class="btn btn-default btn-circle">
        <i class="fa fa-plus"></i> Новый {if $type == "landing"}лендинг{else}блог{/if}
      </a>
    </div>
  </div>
  <div class="portlet-body">
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

  </div>
</div>

