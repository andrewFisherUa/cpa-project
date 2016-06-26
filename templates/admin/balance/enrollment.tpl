<h2 class="page-title">История пополнений</h2>

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
      <a href="#">История пополнений</a>
    </li>
  </ul>
</div>


<div class="portlet light">
  <div class="portlet-body">
    <table class="table table-striped table-bordered table-hover" id="datatable_enrollment">
      <thead>
        <tr>
          <th>ID</th>
          <th>Дата</th>
          {if $admin}
            <th>User</th>
          {/if}
          <th>Баланс до</th>
          <th>Баланс после</th>
          <th>Сумма пополнения</th>
          <th>Комментарий</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>