<div id="page-balance">

<h2 class="page-title">Баланс UM</h2>

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

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
        Баланс
    </div>
    <div class="actions">
    </div>
  </div>
  <div class="portlet-body">
    <div class="row">
      <div class="col-md-5">
        {if $success}
            <div class="alert alert-success">Баланс пополнен</div>
        {/if}
        <!--Форма зачисления на баланс-->
        <form action="/admin/balance/" method="post" id="make_replenishmen_frm">
            <div class="alert alert-danger" style="display:none"></div>
            <input type="hidden" name="user_id" value="0">
            <div class="form-group">
                <label class="control-label">Валюта:</label>
                <div>
                    <select class="form-control" name="country_code">
                        <option value="-1">Выбор валюты</option>
                        {foreach from=$countries item=country}
                            <option value="{$country.code}">{$country.currency_code}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">Сумма:</label>
                <div>
                    <input type="text" class="form-control" name="amount" value="0">
                </div>
            </div>
            <input type="hidden" name="make_replenishment" value = "1">
            <input type="submit" name="submit" class="btn blue" value="Зачислить на баланс">
        </form>
      </div>
      <div class="col-md-7">
        <table class="table table-hover table-striped">
          <thead>
            <tr>
              <th></th>
              <th>Баланс</th>
              <th>Пополнение</th>
              <th>Заработок</th>
              <th>Холд</th>
              <th>Баланс (отклонено)</th>
              <th>Расход</th>
            </tr>
          </thead>
          <tbody>
            {foreach from=$balance item=item}
              <tr>
                <td><i class="flag flag-{$item->getCountryCode()}"></i></td>
                <td>{$item->getCurrent()}&nbsp;{$item->getCurrencyCode()}</td>
                <td>{$item->getAccountBalance()}&nbsp;{$item->getCurrencyCode()}</td>
                <td>{$item->getProfit()}&nbsp;{$item->getCurrencyCode()}</td>
                <td>{$item->getHold()}&nbsp;{$item->getCurrencyCode()}</td>
                <td>{$item->getCanceled()}&nbsp;{$item->getCurrencyCode()}</td>
                <td>{$item->getExpense()}&nbsp;{$item->getCurrencyCode()}</td>
              </tr>
          {/foreach}
          </tbody>
        </table>
      </div>
    </div>
  </div>
  </div>