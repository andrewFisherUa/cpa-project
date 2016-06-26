<h2 class="page-title">История переводов</h2>

<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="/admin">Главная</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">Баланс</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">История переводов</a>
    </li>
  </ul>
</div>

<div class="portlet light">
  <div class="portlet-body">
    <table class="table table-striped table-bordered table-hover" id="datatable_transfer">
      <thead>
        <tr>
          <th>ID</th>
          <th>Дата</th>
          {if $admin}
            <th>User</th>
          {/if}
          <th>Сумма</th>
          <th>Остаток</th>
          <th>Курс</th>
          <th>Статус</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>