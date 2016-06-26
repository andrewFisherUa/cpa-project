<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Мои потоки</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Потоки</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->
<div class="row" id="page-flows">
  <div class="col-md-12">

    <div class="portlet light">
      <div class="portlet-title">
        <div class="caption">
          Создание и редактирование потоков
        </div>
        <div class="actions">
          <form action="/admin/flows" method="post">
            <button class="btn blue" type="submit" name="update_all">Обновить все</button>
          </form>
        </div>
      </div>
      <div class="portlet-body">
        <div class="alert alert-danger" style="display:none"></div>
        <div class="alert alert-success" style="display:none"></div>

        <div class="table-container" style="overflow:hidden;">
          <div class="table-actions-wrapper">
            <div class="form-inline">
              <div class="form-group">
                <select class="form-control table-group-action-input input-sm" data-placeholder="Название потока" name="flow_id">
                  <option value="0">Выберите поток</option>
                  {foreach from=$filters.streams item=item}
                    <option value="{$item.id}">{$item.id}: {$item.name}</option>
                  {/foreach}
                </select>
              </div>
              <div class="form-group">
                <select class="form-control table-group-action-input input-sm" data-placeholder="Оффер" name="offer_id">
                  <option value="0">Выберите оффер</option>
                  {foreach from=$filters.offers item=offer}
                    <option value="{$offer.id}">{$offer.id}: {$offer.name}</option>
                  {/foreach}
                </select>
              </div>
              <div class="form-group">
                <select class="form-control table-group-action-input input-sm" data-placeholder="Название потока" name="user_id">
                  <option value="0">Выберите вебмастера</option>
                  {foreach from=$filters.users item=item}
                    <option value="{$item.id}">{$item.id}: {$item.login}</option>
                  {/foreach}
                </select>
              </div>

              <button class="btn btn-sm red table-group-action-submit reset-filters"><i class="fa fa-times"></i> Сбросить</button>
            </div>
          </div>
          <table class="table table-striped table-bordered table-hover" id="datatable_streams">
            <thead>
              <tr role="row" class="heading">
                <th width="10%">Название потока</th>
                <th width="15%">Дата изменения</th>
                <th width="20%">Оффер</th>
                <th width="15%">Вебмастер</th>
                <th width="20%">Ссылка</th>
                <th width="20%">Действия</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="flowModal" role="dialog" aria-labelledby="dialogLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="dialogLabel">Редактирование потока</h4>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger" style="display:none"></div>
        <div class="alert alert-success" style="display:none"></div>
        <div class="form-container"></div>
      </div>
    </div>
  </div>
</div>