<div id="page-balance">
    <h2 class="page-title">Баланс <small>текущий баланс и вывод средств</small></h2>

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
      {if $can_ask_for_payment}
        <div class="col-sm-6">
          <div class="portlet light">
            <div class="portlet-title">
              <div class="caption">
                Запрос на выплату
              </div>
            </div>
            <div class="portlet-body">
              {if $wallets}
              <form id="ask-payment-form">
                {if $low_balance}
                <div class="alert alert-danger">
                  Сумма на балансе меньше минимальной суммы выплаты 1000 RUB
                </div>
                {else}
                <div class="alert" style="display:none;"></div>
                {/if}
                <div class="form-group">
                  <label for="" class="control-label">Баланс:</label>
                  <div class="form-group">
                    <select name="p_type" class="form-control">
                      <option value="all" data-amount="{$defaultAccount.balance}">Общий баланс: {$defaultAccount.balance}&nbsp;{$defaultAccount.currency_code}</option>
                      <option value="account" data-amount="{$defaultAccount.current}">Основной баланс: {$defaultAccount.current}&nbsp;{$defaultAccount.currency_code}</option>
                      <option value="referal" data-amount="{$defaultAccount.referal}">Реферальный баланс: {$defaultAccount.referal}&nbsp;{$defaultAccount.currency_code}</option>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="" class="control-label">Валюта:</label>
                  <div class="form-group">
                    <select name="p_wallet_type" class="form-control" disabled>
                      <option value="WMR">WMR</option>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="" class="control-label">Кошелек:</label>
                  <div class="form-group">
                    <select name="p_wallet_id" class="form-control">
                      {foreach from=$wallets item=a}
                        <option value="{$a.wallet}">{$a.wallet}</option>
                      {/foreach}
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="" class="control-label">Сумма выплаты:</label>
                  <div class="form-group">
                    <input type="text" name="p_amount" class="form-control numbers-only">
                  </div>
                  <p class="help-block">Минимальная сумма выплаты - 1000 RUB</p>
                </div>

                <button id="ask-payment-btn" class="btn green" {if $low_balance}disabled{/if}>Отправить запрос</button>
              </form>
              {else}
                <div class="alert alert-danger">
                  Для запроса выплаты неободимо добавить WMR кошелек во вкладке "Платежная информация" на странице <a href="/admin/profile">Профиль</a>
                </div>
              {/if}
            </div>
          </div>
        </div>
      {/if}
      <div class="col-sm-6">
        {if $can_ask_for_payment}
          <div class="note note-success">
            <p>На данный момент запрос на вывод средств формируется автоматически каждый понедельник в 23:59 на кошелек по умолчанию, настроить который вы можете в <a href="/admin/profile/">настройках профиля</a>.</p>
            <br/>
            <p>Вывод средств будет осуществлятся во вторник. Запросы и историю смотрите на странице <a href="/admin/balance/history">История выплат</a>.</p>
          </div>
        {/if}
        <div class="portlet light">
          <div class="portlet-body">
            <table class="table">
              <thead>
                <th>Страна</th>
                <th>Баланс</th>
                <th>Холд</th>
                <th>Реферальный баланс</th>
              </thead>
              <tbody>
                {foreach from=$accounts key=a item=b}
                <tr>
                  <td><i class="flag flag-{$a}"></i> {$b->getCountryName()}</td>
                  <td class="text-center">{$b->getCurrent()}&nbsp;{$b->getCurrencyCode()}</td>
                  <td class="text-center">{$b->getHold()}&nbsp;{$b->getCurrencyCode()}</td>
                  <td class="text-center">{$b->getReferal()}&nbsp;{$b->getCurrencyCode()}</td>
                </tr>
                {/foreach}
              </tbody>
              <tfoot>
                <tr>
                  <td>Всего:</td>
                  <td class="text-center">{$defaultAccount.current}&nbsp;{$defaultAccount.currency_code}</td>
                  <td class="text-center">{$defaultAccount.hold}&nbsp;{$defaultAccount.currency_code}</td>
                  <td class="text-center">{$defaultAccount.referal}&nbsp;{$defaultAccount.currency_code}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>

        <div class="portlet light">
          <div class="portlet-title">
            <div class="caption">
              Основная валюта
            </div>
          </div>
          <div class="portlet-body">
            <form action="/admin/balance" method="post" id="change_default_account_frm">
              <div class="alert" style="display:none"></div>
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
                <input type="hidden" name="change_default_account" value="1">
                <button type="submit" class="btn blue" name="submit" id="change_default_account">Применить</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      {if !$can_ask_for_payment}
        <div class="col-sm-6">
          <div class="note note-success">
            <p>На данный момент запрос на вывод средств формируется автоматически каждый понедельник в 23:59 на кошелек по умолчанию, настроить который вы можете в <a href="/admin/profile/">настройках профиля</a>.</p>
            <br/>
            <p>Вывод средств будет осуществлятся во вторник. Запросы и историю смотрите на странице <a href="/admin/balance/history">История выплат</a>.</p>
          </div>
        </div>
      {/if}
    </div>
</div>