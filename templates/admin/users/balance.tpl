<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Все пользователи</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="/admin/users">Пользователи</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Баланс</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      Баланс (RUB)
    </div>
  </div>
  <div class="portlet-body">
    <div class="table-container">      
      <table class="table table-striped table-bordered table-hover" id="datatable_check_balance">
      <thead>
        <tr class="heading" >
          <th class="text-center">
            #
          </th>
          <th class="text-center">
            UID
          </th>
          <th class="text-center">
            Логин
          </th>
          <th class="text-center">
            Прибыль
          </th>
          <th class="text-center">
            Холд
          </th>
          <th class="text-center">
            Баланс
          </th>
          <th class="text-center">
            Выплачено
          </th>
          <th class="text-center">
            Реф.
          </th>
          <th class="text-center">
            Отклонено
          </th>
          <th>Разница</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
      </table>
    </div>
  </div>
</div>

<div class="portlet light" id="requests">
  <div class="portlet-title">
    <div class="caption">
      Запросы на изменение валюты по умолчанию
    </div>
  </div>
  <div class="portlet-body">
    <table class="table table-striped table-bordered table-hover" id="datatable_account_requests">
    <thead>
      <tr role="row" class="heading" >
        <th width="3%" class="text-center">
          ID
        </th>
        <th width="11%" class="text-center">
          User ID
        </th>
        <th width="10%" class="text-center">
          Login
        </th>
        <th width="16%" class="text-center">
          Default Currency
        </th>
        <th width="12%" class="text-center">
          Request
        </th>
        <th width="16%" class="text-center">
          Created
        </th>
        <th width="16%" class="text-center">
          Status
        </th>
        <th width="16%" class="text-center">
          Changed
        </th>
      </tr>
    </thead>
    <tbody>
    </tbody>
    </table>
  </div>
</div>