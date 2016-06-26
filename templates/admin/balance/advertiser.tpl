<div id="page-balance">
    <h2 class="page-title">Баланс <small>текущий баланс, история и вывод средств</small></h2>

    <div class="page-bar">
      <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Баланс</a>
        </li>
      </ul>
    </div>

    <div class="row">
      <div class="col-sm-6">
        <div class="portlet light">
          <div class="portlet-title">
            <div class="caption">
              Перевод
            </div>
            <div class="actions"></div>
          </div>
          <div class="portlet-body">
            <form action="/admin/balance" method="post" id="make_transfer_frm">
              <div class="alert" style="display:none"></div>
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" id="account_from_flag">
                        <i class="glyphicon glyphicon-flag"></i>
                      </span>
                      <select name="account_from" id="account_from" class="form-control">
                        <option value="-1" data-code="">С баланса</option>
                        {foreach from=$balance key=k item=b}
                          <option value="{$k}" data-amount="{$b.amount}" data-code="{$b.code}">{$k}</option>
                        {/foreach}
                      </select>
                    </div>
                    <div class="direction" style="position:absolute;right:-22px;top:4px;font-size:20px;">
                      <i class="fa fa-angle-double-right"></i>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="" class="control-label">Отдаете:</label>
                    <div class="input-group">
                      <input type="text" name="amount_from" id="transfer_amount_from" placeholder="Сумма перевода" class="form-control numbers-only">
                      <span class="input-group-addon" id="transfer_from_currency">
                        <i class="fa fa-money"></i>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-group">
                    <div class="input-group">
                      <span class="input-group-addon" id="account_to_flag">
                        <i class="glyphicon glyphicon-flag"></i>
                      </span>
                      <select name="account_to" id="account_to" class="form-control">
                        <option value="-1" data-code="">На баланс</option>
                        {foreach from=$balance key=k item=b}
                          <option value="{$k}" data-code="{$b.code}">{$k}</option>
                        {/foreach}
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="" class="control-label">Получаете:</label>
                    <div class="input-group">
                      <input type="text" name="amount_to" id="transfer_amount_to" placeholder="Сумма перевода" class="form-control">
                      <span class="input-group-addon" id="transfer_to_currency">
                        <i class="fa fa-money"></i>
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-sm-12">
                  <button class="btn blue pull-right" id="make_transfer">Перевести</button>
                  <span class="help-block" id="convert-rate-wrap" style="display:none">
                    Курс: <span id="convert-rate"></span>
                  </span>
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
       <div class="col-sm-6">
        <div class="portlet light">
          <div class="portlet-body">
            <div class="row">
              <div class="col-md-6">
                  <table class="table">
                    <thead>
                      <tr>
                        <th></th>
                        <th>Баланс</th>
                        <th>Расходы на холде</th>
                      </tr>
                    </thead>
                    <tbody>
                      {foreach from=$accounts item=a}
                        <tr>
                          <td><i class="flag flag-{$a->getCountryCode()}"></i></td>
                          <td><span class="money">{$a->getCurrent()}</span>&nbsp;{$a->getCurrencyCode()}</td>
                          <td><span class="money">{$a->getHold()}</span>&nbsp;{$a->getCurrencyCode()}</td>
                        </tr>
                      {/foreach}
                    </tbody>
                  </table>
              </div>
              <div class="col-md-6">
                {$widget}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-6">
        <div class="portlet light">
          <div class="portlet-body">
            <form action="/admin/balance" method="post" id="change_default_account_frm">
              <div class="alert" style="display:none;"></div>
              <div class="form-group">
                <label class="control-label">Валюта по умолчанию: </label>
                <div>
                  <input type="hidden" name="user_id" value="{$user_id}">
                  <select class="form-control" name="account_currency">
                    {if !$default_balance}<option value="-1">Выбор валюты</option>{/if}
                    {foreach from=$countries item=c}
                      <option value="{$c.code}" {if $default_balance == $c.code}selected{/if}>{$c.currency_code}</option>
                    {/foreach}
                  </select>
                </div>
              </div>
              <div>
                <button type="submit" class="btn blue" name="submit" id="change_default_account">Применить</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-sm-6">

      </div>
    </div>
</div>