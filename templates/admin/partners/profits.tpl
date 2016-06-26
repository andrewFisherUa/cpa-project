<h2 class="page-title">Настройка реферальных начислений</h2>

<div class="page-bar">
  <ul class="page-breadcrumb">
      <li>
        <i class="fa fa-home"></i>
        <a href="/admin">Главная</a>
        <i class="fa fa-angle-right"></i>
      </li>
      <li>
        <a href="#">Настройка реферальных начислений</a>
      </li>
    </ul>
</div>


<form action="/admin/profits" method="post">
  <div class="portlet light">
    <div class="portlet-title">
      <div class="caption">Значения по умолчанию</div>
      <div class="actions">
        <button class="btn btn-circle blue" type="submit" name="save">Сохранить</button>
      </div>
    </div>
    <div class="portlet-body">
      <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th class="text-center" rowspan="2" width="15%">Страна</th>
                <th class="text-center" rowspan="2" width="15%">Тип прибыли</th>
                <th class="text-center" colspan="3" width="45%">Прибыль партнера от продажи реферала</th>
            </tr>
            <tr>
                <th class="text-center">1 уровня</th>
                <th class="text-center">2 уровня</th>
                <th class="text-center">3 уровня</th>
            </tr>
        </thead>
        <tbody>
          {foreach from=$refprofits item=item}
          <tr>
              <td><span class="flag flag-{$item.code}"></span> {$item.name}</td>
              <td>
                  <select size="1" name="defaults[{$item.code}][type]" class="form-control">
                      <option value="percent"{if $item.type == "percent"} selected{/if}>Процент</option>
                      <option value="fixed"{if $item.type == "fixed"} selected{/if}>Сумма</option>
                  </select>
              </td>
              <td>
                <div class="input-group">
                  <input name="defaults[{$item.code}][levels][1]" value="{$item.level1}" class="form-control numbers-only"/>
                  <span class="input-group-addon">{$item.currency}</span>
                </div>
              </td>
              <td>
                  <div class="input-group">
                    <input name="defaults[{$item.code}][levels][2]" value="{$item.level2}" class="form-control numbers-only"/>
                    <span class="input-group-addon">{$item.currency}</span>
                  </div>
              </td>
              <td>
                <div class="input-group">
                  <input name="defaults[{$item.code}][levels][3]" value="{$item.level3}" class="form-control numbers-only"/>
                  <span class="input-group-addon">{$item.currency}</span>
                </div>
              </td>
          </tr>
          {/foreach}
        </tbody>
      </table>

      <div class="checkbox pull-right">
        <label>
          <input type="checkbox" name="apply_to_all" value="1"> Применить ко всем
        </label>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
</form>