<h2 class="page-title">История выплат</h2>

<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="/admin">Главная</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="/admin/balance">Баланс</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">История выплат</a>
    </li>
  </ul>
</div>

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      Выплаты
    </div>
    <div class="actions">
      <div class="form-inline">
        <label class="control-label">Показать заявки:</label>
        <div class="form-group">
          <input type="checkbox" data-status="moderation" checked> На модерации
        </div>
        <div class="form-group">
          <input type="checkbox" data-status="approved"> Одобрена
        </div>
        <div class="form-group">
          <input type="checkbox" data-status="canceled"> Отклонена
        </div>
      </div>
    </div>
  </div>
  <div class="portlet-body">
    <div class="table-container table-responsive">
      <table class="table table-striped table-bordered table-hover" id="datatable_payments-history">
      <thead>
        <tr role="row" class="heading">
          <th width="3%">ID</th>
          <th width="15%">Вебмастер</th>
          <th width="13%">Дата</th>
          <th width="12%">Баланс&nbsp;до</th>
          <th width="12%" class="colorme">Сумма</th>
          <th width="12%">Баланс&nbsp;после</th>
          <th width="12%">Способ&nbsp;вывода</th>
          <th width="10%">Статус</th>
          <th width="10%"></th>
        </tr>
      </thead>
      <tbody>

      </tbody>
    </table>
  </div>
</div>