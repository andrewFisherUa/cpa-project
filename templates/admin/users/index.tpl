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
    <div class="actions">
      <button class="btn red-mint" id="get-emails-list" data-toggle="modal" data-target="#emails-modal">Список email</button>
      <a href="/admin/users/balance" class="btn blue-hoki">Баланс</a>
      <a href="/admin/hold" class="btn green-haze">Холд</a>
      <a href="/admin/permissions" class="btn yellow-mint">Права</a>
      <!--<a href="/admin/users/add" class="btn btn-circle btn-default"><i class="fa fa-plus"></i> Добавить</a>-->
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
          <th width="10%">
            Роль
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
          <th width="4%">Реф.</th>
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
              <select class="form-control form-filter input-sm" name="role">
                <option value="-1">Все</option>
                {foreach from=$roles item=role}
                  <option value="{$role.role_id}">{$role.alias}</option>
                {/foreach}
              </select>
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
            <select class="form-control form-filter input-sm" name="status">
              <option value="-1">Все</option>
              {foreach from=$status item=data}
                <option value="{$data.id}">{$data.name}</option>
              {/foreach}
            </select>
          </td>
          <td>
              <input type="text" class="form-control form-filter input-sm" name="skype">
          </td>
          <td></td>
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

<!-- Modal -->
<div class="modal fade" id="profile-modal" tabindex="-1" role="dialog" aria-labelledby="profile-modalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="profile-modalLabel">Профиль пользователя</h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn default" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn blue" id="save-profile">Сохранить</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="emails-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Список email</h4>
      </div>
      <div class="modal-body">
        <textarea id="list" rows="20" class="form-control"></textarea>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->