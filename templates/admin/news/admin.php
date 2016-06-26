<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Все новости</h3>

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <i class="fa fa-home"></i>

            <a href="/admin">Главная</a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="#">Новости</a>
        </li>
    </ul>
</div>
<!-- END PAGE HEADER-->

<div class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            Новости
        </div>
        <div class="actions">
            <a href="javascript:;" class="action btn btn-default btn-circle" data-news='0' data-action='edit'><i
                class="fa fa-plus"></i> Добавить
            </a>
        </div>
    </div>
    <div class="portlet-body">
        <div class="table-container">
            <table class="table table-striped table-condensed table-bordered" id="news_datatable">
                <thead>
                <tr role="row" class="heading">
                    <th width="4%">ID</th>
                    <th width="23%">Название</th>
                    <th width="20%">Тип</th>
                    <th width="15%">Статус</th>
                    <th width="15%">Дата редакт.</th>
                    <th width="15%">Дата активации</th>
                    <th width="8%">Просмотр</th>
                    <th width="15%">Действия</th>
                </tr>
                <tr role="row" class="filter">
                    <td></td>
                    <td>
                        <input type="text" class="form-control form-filter input-sm" name="title">
                    </td>
                    <td>
                        <select name="type" class="form-control form-filter input-sm">
                            <option value="-1">Тип</option>
                            <option value="1">Новый оффер</option>
                            <option value="2">Приостановка оффера</option>
                            <option value="3">Изменение оффера</option>
                            <option value="4">Новые лендинги</option>
                            <option value="5">Новости системы</option>
                            <option value="6">Важное</option>
                        </select>
                    </td>
                    <td>
                        <select name="status" class="form-control form-filter input-sm">
                            <option value="-1">Статус</option>
                            <option value="1">Модерация</option>
                            <option value="2">Активно</option>
                            <option value="3">Архив</option>
                        </select>
                    </td>
                    <td>
                        <div class="input-group date date-picker margin-bottom-5" data-date-format="dd/mm/yyyy">
                            <input type="text" class="form-control form-filter input-sm" readonly name="dateFrom"
                                   placeholder="От">
                                                    <span class="input-group-btn">
                                                    <button class="btn btn-sm default" type="button"><i
                                                                class="fa fa-calendar"></i></button>
                                                    </span>
                        </div>
                        <div class="input-group date date-picker" data-date-format="dd/mm/yyyy">
                            <input type="text" class="form-control form-filter input-sm" readonly name="dateTo"
                                   placeholder="До">
                                                    <span class="input-group-btn">
                                                    <button class="btn btn-sm default" type="button"><i
                                                                class="fa fa-calendar"></i></button>
                                                    </span>
                        </div>
                    </td>
                    <td></td>
                    <td></td>
                    <td>
                        <div class="margin-bottom-5">
                            <button class="btn btn-sm yellow filter-submit margin-bottom"><i class="fa fa-search"></i> Поиск
                            </button>
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




<div id="form-news" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Создание новости</h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>

