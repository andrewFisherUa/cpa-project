<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">{if $flow->getId()}Редактирование потока `{$flow->getName()}`{else}Создание потока{/if}</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="/admin/flows">Потоки</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">{if $flow->getId()}Редактирование потока `{$flow->getName()}`{else}Создание потока{/if}</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->
<div class="row">
  <div class="col-md-12">
    <div class="portlet light">
      <div class="portlet-title">
        <div class="caption">
         {if $flow->getId()}Редактирование потока `{$flow->getName()}`{else}Создание потока{/if}
        </div>
      </div>
      <div class="portlet-body">
        <div id="single-stream">
          <div class="alert alert-danger" style="display:none"></div>
          <div class="alert alert-success" style="display:none"></div>
          {$form}
       </div>
      </div>
    </div>
  </div>
</div>