<div id="page-balance">
    <h2 class="page-title">
      Запрос на выплату `<?php echo $data["payment_id"];?>` от <?php echo $data["username"] . " " . $status; ?>
      <?php if ($data["changed"]) : ?> <span class="label label-lg label-danger">Изменен</span> <?php endif;?>
    </h2>

    <div class="page-bar">
      <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="/admin/balance/history">История выплат</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="javascript:;">Запрос на выплату `<?php echo $data["payment_id"];?>` от <?php echo $data["username"];?></a>
        </li>
      </ul>
    </div>
</div>

<?php if ($data["can_be_approved"]) : ?>
  <form action="/ajax/change-user-payment/" method="POST" id="approve-payment-form" style="display:none;">
    <input type="hidden" name="payment_id" value="<?php echo $data["payment_id"];?>" />
    <input type="submit" name="approve" value="Одобрить выплату"/>
  </form>
<?php endif; ?>

<div class="row">
  <div class="col-sm-6">
    <form action="/ajax/change-user-payment/" method="POST">
      <div class="portlet light" id="payments-portlet">
        <?php if ($data["can_be_approved"] || $data["can_be_canceled"]) : ?>
          <div class="portlet-title">
            <div class="actions">
              <input type="hidden" name="payment_id" value="<?php echo $data["payment_id"];?>" />
              <?php if ($data["can_be_approved"]) : ?>
                <a href="javascript:;" class="btn green" id="approve-btn">Одобрить</a>
              <?php endif;?>
              <?php if ($data["can_be_canceled"]) : ?>
                <button type="submit" name="cancel" class="btn red">Отклонить</button>
              <?php endif;?>
            </div>
          </div>
        <?php endif;?>
        <div class="portlet-body">
            <div class="row static-info">
              <div class="col-md-4 col-xs-5 name">
                Вебмастер:
              </div>
              <div class="col-md-8 col-xs-7 value">
                <a href="/admin/user/<?php echo $data['user_id'];?>"><?php echo $data["username"];?></a>
              </div>      
            </div>

            <div class="row static-info">
              <div class="col-md-4 col-xs-5 name">
                Способ вывода:
              </div>
              <div class="col-md-8 col-xs-7 value">
                <?php echo $data["wallet"];?> 
                <a href="javascript:;" class="btn btn-circle btn-sm btn-default" id="copy-wallet" title="Копировать кошелек" data-wallet="<?php echo $data["wallet"];?>"><i class="fa fa-copy"></i></a>
              </div>      
            </div>

            <div class="row static-info" id="wallet-info">
              <div class="col-sm-8 col-sm-offset-4 col-xs-12 value">
                <?php if ($data["payed_count"] == 0) : ?>
                  <div class="alert alert-danger" style="margin-bottom:0">
                    Успешных выплат на этот кошелек не было!
                  </div>
                <?php else : ?>
                  <div>
                    Кошелек создан: <?php echo date("d.m.Y", $data["wallet_created"]); ?>
                  </div>
                  <div>
                    Успешных выплат <?php echo $data["payed_count"]; ?> на сумму <span class="money"><?php echo $data["payed_amount"];?></span>&nbsp;<?php echo $data["currency"];?>
                  </div>
                <?php endif; ?>
              </div>      
            </div>

            <div class="row static-info">
              <div class="col-md-4 col-xs-5 name">
                Баланс до:
              </div>
              <div class="col-md-8 col-xs-7 value">
                <?php if ($data["approved"]) : ?>
                  <span class="money"><?php echo $data["balance_before"];?></span> <?php echo $data["currency"]; ?>
                <?php else : ?>
                  <span class="money"><?php echo $data["balance"]["amount"];?></span> <?php echo $data["currency"]; ?>
                <?php endif; ?>
              </div>      
            </div>

            <div class="row static-info payment-amount">
              <div class="col-md-4 col-xs-5 name">
                Сумма выплаты:
              </div>
              <div class="col-md-8 col-xs-7 value">
                <span class="money"><?php echo $data["amount"];?></span> <?php echo $data["currency"]; ?>

                <?php if ($data["can_be_changed"]) : ?>
                <a href="javascript:;" id="toggle-edit-payment" class="btn btn-sm btn-circle btn-default" title="Редактировать сумму выплаты" data-amount="<?php echo $data["amount"];?>">
                  <i class="fa fa-edit"></i>
                </a>
                <?php endif;?>
              </div>      
            </div>

            <?php if ($data["can_be_changed"]) : ?>
            <div class="row static-info form-inline" id="edit-payment-block">
              <div class="col-md-8 col-md-offset-4 col-xs-12 value">
                <div class="input-group">
                  <div>
                    <input type="text" name="amount" value="<?php echo $data["amount"];?>" class="form-control">
                  </div>
                  <span class="input-group-btn">
                      <button type="submit" name="edit_amount" class="btn yellow-crusta">Сохранить</button>
                  </span>
                </div>
              </div>      
            </div> 
            <?php endif;?>

            <div class="row static-info">
              <div class="col-md-4 col-xs-5 name">
                Баланс после:
              </div>
              <div class="col-md-8 col-xs-7 value">
                <?php if ($data["approved"]) : ?>
                  <span class="money"><?php echo $data["balance_after"];?></span> <?php echo $data["currency"]; ?>
                <?php else : ?>
                  нет данных
                <?php endif; ?>
              </div>      
            </div>

            <div class="row static-info">
              <div class="col-md-4 col-xs-5 name">
                Запрос создан:
              </div>
              <div class="col-md-8 col-xs-7 value">
                <?php echo date("d.m.Y H:i", $data["created"]); ?>
              </div>      
            </div>

            <?php if ($data["approved"] > 0) : ?>
              <div class="row static-info">
                <div class="col-md-4 col-xs-5 name">
                  Запрос одобрен:
                </div>
                <div class="col-md-8 col-xs-7 value">
                  <?php echo date("d.m.Y H:i", $data["approved"]); ?> (<?php echo $data["approved_by_username"] ?>)
                </div>      
              </div>
            <?php endif;?>

            <?php if ($data["changed"] > 0) : ?>
              <div class="row static-info">
                <div class="col-md-4 col-xs-5 name">
                  Запрос изменен:
                </div>
                <div class="col-md-8 col-xs-7 value">
                  <?php echo date("d.m.Y H:i", $data["changed"]); ?> (<?php echo $data["changed_by_username"] ?>)
                </div>      
              </div>
            <?php endif;?>
        </div>
      </div>
    </form>
  </div>
  <div class="col-sm-6">
    <form action="/ajax/change-user-payment/" method="POST">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">
            Комментарий
          </div>
          <div class="actions">
            <input type="hidden" name="payment_id" value="<?php echo $data["payment_id"];?>" />
            <button type="submit" name="add-comment" class="btn yellow-crusta">Сохранить</button>
          </div>
        </div>
        <div class="portlet-body">
          <textarea class="form-control" rows="10" name="comment"><?php echo $data["comment"];?></textarea>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="row">
  <div class="col-sm-12">
    <div class="table-container table-responsive">
      <div class="portlet light">
        <div class="portlet-title">
          <div class="caption">История выплат</div>
        </div>
        <div class="portlet-body">
          <table class="table table-striped table-bordered table-hover" id="user-payments-table" data-user="<?php echo $data['user_id'];?>">
            <thead>
              <tr role="row" class="heading">
                <th width="3%">ID</th>
                <th width="13%">Дата</th>
                <th width="12%">Баланс&nbsp;до</th>
                <th width="12%" class="colorme">Сумма</th>
                <th width="12%">Баланс&nbsp;после</th>
                <th width="12%">Способ&nbsp;вывода</th>
                <th width="10%">Статус</th>
                <th width="10%"></th>         
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
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="confirmModalLabel">Подтверждение действия</h4>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
        <button type="button" class="btn red" id="confirm-approve-btn">Одобрить выплату</button>
      </div>
    </div>
  </div>
</div>
