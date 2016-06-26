<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Категории</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="/admin/shop/">Магазин</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Категории</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      Все категории
    </div>
    <div class="actions">
      <a href="/admin/cats/new" class="btn btn-circle btn-default"><i class="fa fa-plus"></i> Добавить</a>
    </div>
  </div>
  <div class="portlet-body">
    <div class="table-container">
      <div class="table-actions-wrapper">
        <div class="form-inline">
          <div class="form-group">
            <select name="type" class="table-group-action-input form-control input-inline input-sm">
              <option value="-1">Тип</option>
              <option value="shop_category">Магазин</option>
              <option value="offer_category">Оффер</option>
            </select>
          </div>
          <button class="btn btn-sm green table-group-action-submit submit-filters">Поиск</button>
          <button class="btn btn-sm red table-group-action-submit reset-filters"><i class="fa fa-times"></i> Сбросить</button>
        </div>
      </div>
      <table class="table table-striped table-bordered table-hover table-condensed" id="datatable_categories">
      <thead>
        <tr role="row" class="heading">
          <th colspan="6" style="border-bottom:1px solid #ddd;"></th>
          <th colspan="4" style="border-bottom:1px solid #ddd;" class="text-center">Количество</th>
          <th></th>
        </tr>
        <tr role="row" class="heading">
           <th width="5%" class="text-center">
            #
          </th>
          <th width="34%" class="text-center">
            Название
          </th>
          <th width="10%" class="text-center">
            Тип
          </th>
          <th width="5%" class="text-center">
            Css
          </th>
          <th width="5%" class="text-center">
            Вес
          </th>
          <th width="8%" class="text-center">
            Видимость
          </th>
          <th width="6%" class="text-center"><span class="flag flag-ua"></span></th>
          <th width="6%" class="text-center"><span class="flag flag-ru"></span></th>
          <th width="6%" class="text-center"><span class="flag flag-by"></span></th>
          <th width="6%" class="text-center"><span class="flag flag-kz"></span></th>
          <th width="6%" class="text-center"><span class="flag flag-uz"></span></th>
          <th width="9%">Действия</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
      </table>
    </div>
  </div>
</div>