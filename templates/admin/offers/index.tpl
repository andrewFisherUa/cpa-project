<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Все офферы</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Офферы</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->


<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">Все офферы</div>
    <div class="actions">
      {if $admin}
      <a href="/admin/offers/new" class="btn btn-circle btn-default">
        <i class="fa fa-plus"></i>
        <span class="hidden-480"> Новый оффер </span>
      </a>
      {/if}
    </div>
  </div>

  <div class="portlet-body">
    <div class="table-container">
      <div class="table-actions-wrapper">
        <div class="form-inline">
          <div class="form-group">
            <select name="id" class="table-group-action-input form-control input-inline input-sm" style="max-width:300px">
              <option value="-1">Название</option>
              {foreach from=$items item=item}
                <option value="{$item.id}">{$item.id}: {$item.name}</option>
              {/foreach}
            </select>
          </div>

          <div class="form-group">
            <select name="country_code" class="table-group-action-input form-control input-inline input-sm">
              <option value="-1">Гео</option>
              {foreach from=$country item=co}
                <option value="{$co.code}">{$co.name}</option>
              {/foreach}
            </select>
          </div>

          {if $admin}
          <div class="form-group">
            <select name="target" class="table-group-action-input form-control input-inline input-sm">
              <option value="-1">Тип комиссии</option>
              <option value="0">Оплаченный заказ</option>
              <option value="1">Подтвержденный заказ</option>
              <option value="2">Регистрация</option>
            </select>
          </div>
          {/if}

          <div class="form-group">
            <select name="category" class="table-group-action-input form-control input-inline input-sm">
              <option value="-1">Категория</option>
              {foreach from=$cats item=cat}
                <option value="{$cat->getId()}">{$cat->getName()}</option>
              {/foreach}
            </select>
          </div>

          {if $admin}
          <div class="form-group">
            <select name="status" class="table-group-action-input form-control input-inline input-sm">
              <option value="-1">Статус</option>
              {foreach from=$status_list item=item}
              <option value="{$item.status}">{$item.label}</option>
              {/foreach}
            </select>
          </div>
          {/if}

          <button class="btn btn-sm red table-group-action-submit reset-filters"><i class="fa fa-times"></i> Сбросить</button>

        </div>

      </div>
      <div class="table-responsive">
        <table class="table table-striped {if $admin}admin-table{else}user-table{/if}" id="datatable_offers">
        <thead>
        <tr role="row" class="heading">
          <th width="14%" class="text-center"></th>
          <th width="18%">Оффер</th>
          <th width="3%">EPC</th>
          <th width="3%">CR</th>
          <th width="52%">
            <table class="table table-condensed offers-table">
              <thead>
                <tr role="row" class="heading">
                  <th width="20%">Страна</th>
                  <th>{if $admin}Базовая цена{else}Цена{/if}</th>
                  <th width="28%">Тип</th>
                  <th>{if $admin}Комиссия вебмастера{else}Отчисления{/if}</th>
                  {if $admin}
                  <th>Комиссия univermag</th>
                  {/if}
                </tr>
              </thead>
            </table>
          </th>
          <th width="4%"></th>
        </tr>
        </thead>
        <tbody>
        </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="linkModal" tabindex="-1" role="dialog" aria-labelledby="dialogLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="dialogLabel"></h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <input type="text" class="form-control" value="">
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="rulesModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Добавить оффер</h4>
      </div>
      <div class="modal-body">
        <p class="note note-success">
          Добавив данный оффер, вы автоматически подтверждаете, что согласны с нижеприведёнными правилами.
        </p>
        <div id="rules-wrap"></div>
      </div>
      <div class="modal-footer">
        <a href="javascript:;" class="btn blue" id="add-offer">Добавить</a>
        <a href="javascript:;" class="btn btn-default" data-dismiss="modal">Отмена</a>
      </div>
    </div>
  </div>
</div>
