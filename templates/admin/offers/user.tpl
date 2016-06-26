<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Все офферы</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="/admin/offers/">Офферы</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Мои офферы</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      Мои офферы
    </div>
    <div class="actions"></div>
  </div>
  <div class="portlet-body">
    <div class="table-container">
      <div class="table-actions-wrapper">
        <div class="form-inline">
          <div class="form-group">
            <select name="id" class="table-group-action-input form-control input-inline input-sm" style="max-width:300px">
              <option value="-1">Название</option>
              {foreach from=$items item=item}
                <option value="{$item.id}">{$item.id}: {$item.name}</option>
              {/foreach}
            </select>
          </div>

          <div class="form-group">
            <select name="country_code" class="table-group-action-input form-control input-inline input-sm">
              <option value="-1">Гео</option>
              {foreach from=$country item=co}
                <option value="{$co.code}">{$co.name}</option>
              {/foreach}
            </select>
          </div>

          <div class="form-group">
            <select name="category" class="table-group-action-input form-control input-inline input-sm">
              <option value="-1">Категория</option>
              {foreach from=$cats item=cat}
                <option value="{$cat->getId()}">{$cat->getName()}</option>
              {/foreach}
            </select>
          </div>

          <button class="btn btn-sm red table-group-action-submit reset-filters"><i class="fa fa-times"></i> Сбросить</button>

        </div>

      </div>
      <table class="table table-striped user-table" data-records="user" id="datatable_offers">
      <thead>
      <tr role="row" class="heading">
        <th width="10%" class="text-center"></th>
        <th width="20%">Оффер</th>
        <th width="5%">EPC</th>
        <th width="5%">CR</th>
        <th width="50%">
          <table class="table table-condensed offers-table">
            <thead>
              <tr role="row" class="heading">
                <th width="20%">Страна</th>
                <th>Цена</th>
                <th width="28%">Тип</th>
                <th>Отчисления</th>
              </tr>
            </thead>
          </table>
        </th>
        <th width="10%"></th>
      </tr>
      </thead>
      <tbody>
      </tbody>
      </table>
    </div>
  </div>
</div>