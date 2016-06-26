<h2 class="page-title">Заказы</h2>

<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="/admin">Главная</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">Заказы</a>
    </li>
  </ul>
</div>

<!-- BEGIN PAGE CONTENT-->
<div class="row">
  <div class="col-md-12">
    <!-- Begin: life time stats -->
    <div class="portlet light">
      <div class="portlet-title">
        <div class="caption">
          <i class="fa fa-shopping-cart"></i>Таблица заказов
        </div>
        <div class="actions">
          {if !$admin}
            <a href="/admin/orders/new" class="btn btn-circle btn-default">
              <i class="fa fa-plus"></i> <span class="hidden-480"> Новый заказ </span>
            </a>
          {/if}
        </div>
      </div>
      <div class="portlet-body">
        {if $message}
        <div class="note note-danger">
          {$message}
        </div>
        {/if}

        <div id="config">

        </div>

        <div class="table-container table-responsive">
          <table class="table table-striped table-bordered table-hover" id="datatable_orders">
          <thead>
          <tr role="row" class="heading">
            <th width="5%">
              id
            </th>
            <th width="5%">
              oid
            </th>
            <th width="13%">
              Создан
            </th>
            <th width="10%">
              Статус
            </th>
            <th width="13%">
              Сумма
            </th>
            <th width="8%">
              Комиссия
            </th>
            <th width="10%">
              Логин
            </th>
            <th width="13%">Холд</th>
            <th width="8%">Источник</th>
            <th width="10%">
              Действия
            </th>
          </tr>
          <tr role="row" class="filter">
            <td>
              <input type="text" class="form-control form-filter input-sm" name="id">
            </td>
             <td>
              <input type="text" class="form-control form-filter input-sm" name="oid">
            </td>
            <td>
              <div class="input-group date date-picker margin-bottom-5" data-date-format="dd/mm/yyyy">
                <input type="text" class="form-control form-filter input-sm" readonly name="date_from" placeholder="От">
                <span class="input-group-btn">
                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                </span>
              </div>
              <div class="input-group date date-picker" data-date-format="dd/mm/yyyy">
                <input type="text" class="form-control form-filter input-sm" readonly name="date_to" placeholder="До">
                <span class="input-group-btn">
                <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                </span>
              </div>
            </td>
            <td>
              <select name="status" class="form-control form-filter input-sm">
                <option value="-1">Статус</option>
                {foreach from=$status item=s name=si}
                  <option value="{$s.status}">{$s.label}</option>
                {/foreach}
              </select>
            </td>
            <td>
              <div class="margin-bottom-5">
                <input type="text" class="form-control form-filter input-sm" name="amount_from" placeholder="От"/>
              </div>
              <input type="text" class="form-control form-filter input-sm" name="amount_to" placeholder="До"/>
            </td>
            <td>
              <div class="margin-bottom-5">
                <input type="text" class="form-control form-filter input-sm" name="commission_from" placeholder="От"/>
              </div>
              <input type="text" class="form-control form-filter input-sm" name="commission_to" placeholder="До"/>
            </td>
            <td>
              {if $users}
              <select name="user_id" class="form-control form-filter input-sm select2me">
                <option value="-1">Логин</option>
                {foreach from=$users item=user}
                  <option value="{$user.id}">{$user.login}</option>
                {/foreach}
              </select>
              {/if}
            </td>
            <td></td>
            <td>
              <select name="source" class="form-control form-filter input-sm select2me">
                <option value="-1">Источник</option>
                <option value="stream">Поток</option>
                <option value="api">API</option>
              </select>
            </td>
            <td>
              <div class="margin-bottom-5">
                <button class="btn btn-sm yellow filter-submit margin-bottom"><i class="fa fa-search"></i> Поиск</button>
              </div>
              <button class="btn btn-sm red filter-cancel"><i class="fa fa-times"></i> Отмена</button>
            </td>
          </tr>
          </thead>
          <tbody>
          </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- End: life time stats -->
  </div>
</div>
<!-- END PAGE CONTENT-->