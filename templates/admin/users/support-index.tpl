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
          <a href="#">Все пользователи</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      Все пользователи
    </div>
  </div>
  <div class="portlet-body">
    <div class="table-container table-responsive">
      <table class="table table-striped table-bordered table-hover table-condensed" id="datatable_users">
      <thead>
        <tr role="row" class="heading">
          <th width="5%">
            ID
          </th>
          <th width="15%">
            Логин
          </th>
          <th width="17%">
            Страна
          </th>
          <th width="11%">
            Регистрация
          </th>
          <th width="12%">
            Статус
          </th>
          <th width="12%">
            Skype
          </th>
          <th width="14%">Действия</th>
        </tr>
        <tr role="row" class="filter">
          <td>
              <input type="text" class="form-control form-filter input-sm" name="id">
          </td>
          <td>
              <input type="text" class="form-control form-filter input-sm" name="login">
          </td>
          <td>
            <select class="form-control form-filter input-sm" name="country">
              <option value="-1">Все</option>
              {foreach from=$countries item=country}
                <option value="{$country}">{$country}</option>
              {/foreach}
            </select>
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
            <select class="form-control form-filter input-sm" name="skype_status">
              <option value="-1">Все</option>
              <option value="0">На модерации</option>
              <option value="1">Не найден</option>
              <option value="2">В ожидании</option>
              <option value="3">Добавлен</option>
            </select>
          </td>
          <td>
              <input type="text" class="form-control form-filter input-sm" name="skype">
          </td>
          <td rowspan="1" colspan="1">
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