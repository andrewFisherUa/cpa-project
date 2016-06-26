<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">API</h3>

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <i class="fa fa-home"></i>

            <a href="/admin">Главная</a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="#">API</a>
        </li>
    </ul>
</div>
<!-- END PAGE HEADER-->

<div class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            Потоки
        </div>
    </div>
    <div class="portlet-body">
        <div class="table-container">
            <table class="table table-striped table-condensed table-bordered" id="api_streams_table">
                <thead>
                <tr role="row" class="heading">
                    <th width="5%">ID</th>
                    <th width="21%">Название</th>
                    <th width="13%">Дата изменения</th>
                    <th width="15%">Оффер</th>
                    <th width="12%">Вебмастер</th>
                    <th width="20%">Ключ</th>
                    <th width="13%">Действия</th>
                </tr>     
                <tr role="row" class="filter">
                    <td>
                      <input type="text" class="form-control form-filter input-sm" name="id" placeholder="ID">
                    </td>
                    <td>
                      <input type="text" class="form-control form-filter input-sm" name="name" placeholder="Название">
                    </td>
                    <td>
                      <div class="input-group date date-picker margin-bottom-5" data-date-format="dd/mm/yyyy">
                        <input type="text" class="form-control form-filter input-sm" readonly name="changed_from" placeholder="От">
                        <span class="input-group-btn">
                            <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                      <div class="input-group date date-picker" data-date-format="dd/mm/yyyy">
                        <input type="text" class="form-control form-filter input-sm" readonly name="changed_to" placeholder="До">
                        <span class="input-group-btn">
                            <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                    </td>
                    <td>
                      <select name="offer_id" class="form-control form-filter input-sm select2me">
                        <option value="-1">Оффер</option>
                        <?php foreach ($filters["offers"] as $k=>$v) : ?>
                          <option value="<?php echo $k;?>"><?php echo $k . ": " . $v;?></option>
                        <?php endforeach; ?>
                      </select>
                    </td>
                    <td>
                      <select name="user_id" class="form-control form-filter input-sm select2me">
                        <option value="-1">Вебмастер</option>
                        <?php foreach ($filters["partners"] as $k=>$v) : ?>
                          <option value="<?php echo $k;?>"><?php echo $v;?></option>
                        <?php endforeach; ?>
                      </select>
                    </td>
                    <td>
                      <input type="text" class="form-control form-filter input-sm" name="key" placeholder="Ключ">
                    </td>
                    <td>
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

<div class="portlet light">
    <div class="portlet-title">
        <div class="caption">
            API-доступы
        </div>
    </div>
    <div class="portlet-body">
        <div class="table-container">
            <table class="table table-striped table-condensed table-bordered" id="hash_table">
                <thead>
                <tr role="row" class="heading">
                    <th width="5%">ID</th>
                    <th width="18%">Вебмастер</th>
                    <th width="10%">Статус</th>
                    <th width="12%">Создан</th>
                    <th width="12%">Изменен</th>
                    <th width="7%">Тикет</th>
                    <th width="10%">Действия</th>
                </tr>            
                <tr role="row" class="filter">
                    <td>
                      <input type="text" class="form-control form-filter input-sm" name="id" placeholder="ID">
                    </td>
                    <td>
                      <select name="user_id" class="form-control form-filter input-sm select2me">
                        <option value="-1">Вебмастер</option>
                        <?php foreach ($filters["partners"] as $k=>$v) : ?>
                          <option value="<?php echo $k;?>"><?php echo $v;?></option>
                        <?php endforeach; ?>
                      </select>
                    </td>
                    <td>
                      <select name="status" class="form-control form-filter input-sm select2me">
                        <option value="-1">Статус</option>
                        <option value="moderation">Модерация</option>
                        <option value="accepted">Одобрен</option>
                        <option value="refused">Отклонен</option>
                      </select>
                    </td>
                    <td>
                      <div class="input-group date date-picker margin-bottom-5" data-date-format="dd/mm/yyyy">
                        <input type="text" class="form-control form-filter input-sm" readonly name="created_from" placeholder="От">
                        <span class="input-group-btn">
                            <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                      <div class="input-group date date-picker" data-date-format="dd/mm/yyyy">
                        <input type="text" class="form-control form-filter input-sm" readonly name="created_to" placeholder="До">
                        <span class="input-group-btn">
                            <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                    </td>
                    <td>
                      <div class="input-group date date-picker margin-bottom-5" data-date-format="dd/mm/yyyy">
                        <input type="text" class="form-control form-filter input-sm" readonly name="changed_from" placeholder="От">
                        <span class="input-group-btn">
                            <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                      <div class="input-group date date-picker" data-date-format="dd/mm/yyyy">
                        <input type="text" class="form-control form-filter input-sm" readonly name="changed_to" placeholder="До">
                        <span class="input-group-btn">
                            <button class="btn btn-sm default" type="button"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                    </td>
                    <td>
                        <input type="text" class="form-control form-filter input-sm" name="ticket_id" placeholder="Ticket"></td>
                    <td>
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