<h2 class="page-title">F.A.Q.</h2>

<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <a href="/admin"><i class="fa fa-home"></i></a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">F.A.Q.</a>
    </li>
  </ul>
</div>

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
        Рубрики
    </div>
    <div class="actions">
        <a href="#edit-rubrics" data-rubric="0" class="btn btn-circle btn-default"> <i class="fa fa-plus"></i> Добавить </a>
    </div>
  </div>
  <div class="portlet-body">
    <table class="table table-striped table-bordered" id="datatable_rubrics">
        <thead>
            <tr>
                <th width="3%">#</th>
                <th width="60%">Название</th>
                <th width="13%">Вес</th>
                <th width="24%">Действия</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
  </div>
</div>

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
        Статьи
    </div>
    <div class="actions">
        <a href="#edit-articles" data-article="0" class="btn btn-circle btn-default"> <i class="fa fa-plus"></i> Добавить </a>
    </div>
  </div>
  <div class="portlet-body">
    <table class="table table-striped table-bordered" id="datatable_articles">
        <thead>
            <tr>
                <th width="3%">#</th>
                <th width="27%">Название</th>
                <th width="15%">Рубрика</th>
                <th width="13%">Статус</th>
                <th width="12%">Вес</th>
                <th width="18%">Дата редактирования</th>
                <th width="12%">Действия</th>
            </tr>
            <tr role="row" class="filter">
              <td></td>
              <td>
                  <input type="text" class="form-control form-filter input-sm" name="title">
              </td>
              <td>
                  <select name="rubric_id" class="form-control form-filter input-sm">
                      <option value="0">Рубрика</option>
                      {foreach from=$rubrics item=rubric}
                        <option value="{$rubric.rubric_id}">{$rubric.name}</option>
                      {/foreach}
                  </select>
              </td>
              <td>
                  <select name="status" class="form-control form-filter input-sm">
                      <option value="0">Статус</option>
                      {foreach from=$status key=k item=v}
                          <option value="{$k}">{$v}</option>
                      {/foreach}
                  </select>
              </td>
              <td>
                  <input type="text" class="form-control form-filter input-sm" name="weight">
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
                <div class="margin-bottom-5">
                  <button class="btn btn-sm yellow filter-submit margin-bottom"><i class="fa fa-search"></i> Поиск</button>
                </div>
                <button class="btn btn-sm red filter-cancel"><i class="fa fa-times"></i> Отмена</button>
              </td>
          </tr>
        </thead>
        <tbody></tbody>
    </table>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="edit-articles" tabindex="-1" role="edit-articles" aria-labelledby="edit-articlesLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="edit-articlesLabel">Добавить статью в раздел F.A.Q</h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn btn-primary btn-save">Сохранить</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="edit-rubrics" tabindex="-1" role="edit-rubrics" aria-labelledby="edit-rubricsLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="edit-rubricsLabel">Добавить рубрику в раздел F.A.Q</h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn btn-primary btn-save">Сохранить</button>
      </div>
    </div>
  </div>
</div>