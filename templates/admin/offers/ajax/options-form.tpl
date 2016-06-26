<div id="countries-tabs" class="tabbable-line">
 <div class="col-md-3 col-sm-3 col-xs-3">
  <ul class="nav nav-tabs tabs-left">
    {foreach from=$options item=country}
    <li class="">
      <a href="#tab_{$country.code}" data-toggle="tab">
        <span class="flag flag-{$country.code}"></span> {$country.name}
      </a>
    </li>
    {/foreach}
  </ul>
 </div>
 <div class="col-md-9 col-sm-9 col-xs-9">
    <div class="tab-content">
      {foreach from=$options item=country}
        <div class="tab-pane" id="tab_{$country.code}">
          <div class="form-horizontal" role="form">
            <div class="form-body">
              <div class="form-group">
                <label class="col-md-3 control-label">Телефон</label>
                <div class="col-md-9">
                  <input type="text" class="form-control" name="options[phone][{$country.code}]" placeholder="Введите телефон" value="{$country.phone}">
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Адрес</label>
                <div class="col-md-9">
                  <textarea name="options[address][{$country.code}]" cols="30" rows="3" class="form-control" placeholder="Введите адрес">{$country.address}</textarea>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label">Сроки доставки</label>
                <div class="col-md-9">
                  <input type="text" name="options[delivery_time][{$country.code}]" class="form-control" placeholder="Введите сроки доставки" value="{$country.delivery_time}">
                </div>
              </div>
            </div>
          </div>
        </div>
      {/foreach}
    </div>
  </div>
</div>