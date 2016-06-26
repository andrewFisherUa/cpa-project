<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">EPC / CR</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="/admin/offers">Офферы</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="javascript:;">EPC / CR</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">EPC / CR</div>
  </div>
  <div class="portlet-body">

    <div class="form-group">
      <select name="offer" class="select2me form-control">
        <option value="-1">Оффер</option>
        {foreach from=$items item=a}
          <option value="{$a.id}">{$a.id}: {$a.name}</option>
        {/foreach}
      </select>
    </div>

    <div class="table-container">
      <table class="table table-bordered" id="statindex_table">
        <thead>
          <tr class="heading">
            <th rowspan="2" width="24%" class="text-center">Оффер</th>
            <th colspan="3" width="30%" class="text-center">EPC</th>
            <th colspan="3" width="30%" class="text-center">CR</th>
            <th rowspan="2" width="6%"></th>
          </tr>
          <tr class="heading">
            <th class="text-center">Статистика</th>
            <th class="text-center">Пользовательское</th>
            <th class="text-center">н&nbsp;/&nbsp;д</th>
            <th class="text-center">Статистика</th>
            <th class="text-center">Пользовательское</th>
            <th class="text-center">н&nbsp;/&nbsp;д</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>