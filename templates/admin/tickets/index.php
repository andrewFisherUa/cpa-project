<h2 class="page-title">Тикеты <small>поддержка партнеров</small></h2>

<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <a href="/admin"><i class="fa fa-home"></i></a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">Тикеты</a>
    </li>
  </ul>
</div>

<?php if (!empty($success)) : ?>
<div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<?php if ($isSupport) : ?>
  <div class="well-sm well">
    <div class="form-body form-inline">
        <div class="form-group">
            <label for="bemail" class="control-label">Email службы поддержки: </label>
            <input type="text" id="bemail" name="bemail" value="<?php echo $support_email; ?>" class="form-control">
        </div>
        <button class="btn green" onclick="changeSupportEmail()">Сохранить</button>
    </div>
  </div>
<?php endif; ?>

<?php if (!$isSupport) : ?>
<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      Новый тикет
    </div>
    <div class="actions"></div>
  </div>
  <div class="portlet-body">
    <div class="row">
      <div class="col-md-7 col-sm-12">
        <div class="portlet gren">
          <div class="portlet-title">
            <div class="caption">
              <i class="fa fa-edit"></i> Написать новый тикет
            </div>
            <div class="tools">
              <a href="javascript:;" class="collapse" data-original-title="" title=""></a>
            </div>
          </div>
          <div class="portlet-body">
            <div class="note note-info">
              Перед тем, как задать свой вопрос, пожалуйста, убедитесь, что на него нет ответа в разделе <a href="/admin/faq">FAQ</a>.
            </div>

            <form action="/admin/tickets/" method="POST" id="new-ticket-form" enctype="multipart/form-data">
              <div class="alert alert-danger form-alert" style="display:none"></div>
              <input type="hidden" name="add_ticket" value="1" />
              <div class="row">
                <div class="col-md-8 col-xs-7 form-group">
                  <input type="text" name="ticket[subject]" class="form-control" placeholder="Тема" required>
                </div>
              </div>
              <div class="separator bottom"></div>
              <div class="row">
                  <div class="col-md-12 form-group">
                      <textarea name="ticket[message]" class="form-control" placeholder="Сообщение" rows="5" required></textarea>

                      <div>
                        <div class="checkbox">
                          <label>
                            <input type="checkbox" name="ticket[urgent]" value="1"> Срочный
                          </label>
                        </div>
                      </div>
                  </div>
              </div>
              <div class="right">
                <button type="submit" name="submit" class="btn btn-primary">Отправить</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif;?>

<!-- Begin: life time stats -->
<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      <i class="icon-bubbles"></i>Ваши тикеты
    </div>
    <div class="actions">
    </div>
  </div>
  <div class="portlet-body">
    <div class="inbox">
      <div class="inbox-content">
      <div class="table-container">
        <table class="table table-striped table-bordered table-hover" id="datatable_tickets">
        <thead>
        <tr role="row" class="heading">
          <th width="7%">#</th>
          <th width="25%">Тема</th>
          <th width="5%">Отв.</th>
          <th width="15%">Создано</th>
          <?php if ($isSupport) : ?>
            <th width="15%">Логин</th>
          <?php endif;?>
          <th width="24%">Последний ответ</th>
          <th width="10%">Cтатус</th>
        </tr>
        <tr role="row" class="filter">
          <td>
              <input type="text" class="form-control form-filter input-sm" name="id">
          </td>
          <td>
              <input type="text" class="form-control form-filter input-sm" name="subject">
          </td>
          <td>
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
           <?php if ($isSupport) : ?>
           <td>
             <select class="form-control form-filter input-sm select2me" name="user_id">
              <option value="-1">Логин</option>
              <?php foreach ($filters['users'] as $a) : ?>
                <option value="<?php echo $a['user_id'];?>"><?php echo $a['login'];?></option>
              <?php endforeach;?>
            </select>
           </td>
          <?php endif;?>
          <td>
          </td>
          <td>
            <select class="form-control form-filter input-sm" name="closed">
              <option value="-1">Статус</option>
              <option value="0">Открыт</option>
              <option value="1">Закрыт</option>
            </select>
          </td>
        </tr>
        </thead>
        <tbody>
        </tbody>
        </table>
      </div>
    </div></div>
  </div>
</div>
<!-- End: life time stats -->