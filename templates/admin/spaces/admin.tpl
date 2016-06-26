<h2 class="page-title">Источники трафика</h2>

<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="/admin">Главная</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">Источники трафика</a>
    </li>
  </ul>
</div>

<div class="portlet light">
  <div class="portlet-body">
    <div class="form-inline">
      <div class="form-group margin-bottom-10">
        <input type="text" name="id" class="form-control tbl-filter" placeholder="ID">
      </div>
      <div class="form-group margin-bottom-10">
          <select name="status" class="form-control tbl-filter">
              <option value="-1">Select status</option>
              <option value="processing">processing</option>
              <option value="moderation">moderation</option>
              <option value="active">active</option>
              <option value="canceled">canceled</option>
          </select>
      </div>
      <div class="form-group margin-bottom-10">
          <select name="user_id" class="form-control tbl-filter">
              <option value="-1">Select user</option>
              {foreach from=$users item=u}
                <option value="{$u.id}">{$u.id}: {$u.login}</option>
              {/foreach}
          </select>
      </div>
      <div class="form-group margin-bottom-10">
          <select name="type" class="form-control tbl-filter">
              <option value="-1">Select type</option>
              {foreach from=$types item=v}
                <option value="{$v.value}">{$v.alias}</option>
              {/foreach}
          </select>
      </div>
      <div class="form-group margin-bottom-10">
          <select name="source" class="form-control tbl-filter">
              <option value="-1">Select source</option>
              {foreach from=$sources key=k item=v}
                <option value="{$k}">{$v}</option>
              {/foreach}
          </select>
      </div>
      <br>
      <div class="form-group margin-bottom-10">
        <div class="input-group date date-picker" data-date-format="dd/mm/yyyy">
            <input name="changed_from" type="text" size="16" readonly="" class="form-control" placeholder="Changed From">
            <span class="input-group-btn">
                <button class="btn default date-set" type="button">
                    <i class="fa fa-calendar"></i>
                </button>
            </span>
        </div>
      </div>
      <div class="form-group margin-bottom-10">
        <div class="input-group date date-picker" data-date-format="dd/mm/yyyy">
            <input name="changed_to" type="text" size="16" readonly="" class="form-control" placeholder="Changed To">
            <span class="input-group-btn">
                <button class="btn default date-set" type="button">
                    <i class="fa fa-calendar"></i>
                </button>
            </span>
        </div>
      </div>
      <br>
      <button class="btn blue submit-filters"><i class="fa fa-search"></i> Поиск</button>
      <button class="btn red reset-filters"><i class="fa fa-times"></i> Отмена</button>
    </div>
    <div class="table-container">
      <table class="table table-bordered table-striped" id="datatable_spaces">
        <thead>
          <tr class="heading">
            <th width="5%">ID</th>  <!--*-->
            <th width="16%">Название</th>
            <th width="10%">Changed</th> <!--*-->
            <th width="13%">User</th> <!--*-->
            <th width="13%">Тип</th>
            <th width="13%">Источник трафика</th><!--*-->
            <th width="10%">URL</th>
            <th width="7%">Статус</th>
            <th width="14%">Действия</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal fade" id="space-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body"></div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
