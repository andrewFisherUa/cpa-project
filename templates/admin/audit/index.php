<h2 class="page-title">Аудит</h2>

<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="/admin">Главная</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">Аудит</a>
    </li>
  </ul>
</div>

<!-- BEGIN PAGE CONTENT-->
<div class="row">
  <div class="col-md-12">
    <!-- Begin: life time stats -->
    <div class="portlet light">
      <div class="portlet-title">
        <div class="caption">Аудит</div>
        <div class="actions">
          <a href="javascript:;" class="btn btn-sm yellow-crusta" id="show-settings"><i class="icon-wrench"></i> Настроить</a>
          <button class="btn btn-sm red reset-filters"><i class="fa fa-times"></i> Сбросить фильтры</button>
        </div>
      </div>
      <div class="portlet-body">
        <div id="audit-settings" style="display:none">
          <div class="form row">
            <div class="col-md-6">
              <div class="note note-postback">
                <p><strong>Список событий: </strong><br>
                    1. Вход в кабинет - найдет все события входа <br />
                    1. Вход в кабинет|Регистрация - найдет все события входа и регистрации <br />
                    2. Просмотр страницы%|Регистрация - найдет все события, начинающиеся со слов "Просмотр страницы" или "Регистрация"</p>
                <p><strong>Показать / Скрыть</strong> - применяет фильтр списка событий для показа / сокрытия страниц</p>
                <p><strong>Показать только важные</strong> - показывает только важные сообщения</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="control-label">Список событий</label>
                <div>
                  <textarea name="pages" class="form-control" cols="30" rows="2" placeholder="Список страниц через '|'"></textarea>
                </div>
              </div>
              
              <div class="form-group">
                <label><input type="radio" name="include" value="include" checked> Показать</label>
                <label><input type="radio" name="include" value="exclude"> Скрыть</label>
              </div>

              <div class="form-group">
               <label><input type="checkbox" name="show_important"> Показать только важные</label>
              </div>

              <div class="form-group">
                <button class="btn green" id="apply-settings">Применить</button>
              </div>
            </div>
          </div>
        </div>

        <div class="table-container">
          <table class="table table-striped table-bordered table-condensed table-hover" id="datatable_audit">
          <thead>
          <tr role="row" class="heading">
            <th width="5%">
              ID
            </th>
            <th width="12%">
              Дата
            </th>
            <th width="15%">
              Пользователь
            </th>
            <th width="15%">
              Админ
            </th>
            <th width="28%">
              Действие
            </th>
            <th width="15%">
              IP
            </th>
            <th width="10%">
              Детали
            </th>
          </tr>
          <tr role="row" class="filter">
            <td>
              <input type="text" class="form-control form-filter input-sm" name="id">
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
              <input type="text" class="form-control form-filter input-sm" name="user_id">
            </td>
             <td>
              <input type="text" class="form-control form-filter input-sm" name="admin_id">
            </td>
            <td>
              <input type="text" class="form-control form-filter input-sm" name="action">
            </td>
            <td>
              <input type="text" class="form-control form-filter input-sm" name="ip"></td>
            <td>
              <button class="btn btn-sm green" id="apply-filters">Поиск</button>
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

<!-- Modal -->
<div class="modal fade" id="audit-details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Детали</h4>
      </div>
      <div class="modal-body">
        
      </div>
    </div>
  </div>
</div>