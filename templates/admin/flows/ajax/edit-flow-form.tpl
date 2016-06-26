<input type="hidden" id="flow_uid" value="{$flow->getUserId()}">
<input type="hidden" id="flow_id" value="{$flow->getId()}">

<div class="form-group">
  <label for="" class="control-label">Название потока: <span class="required">*</span></label>
  <div>
    <input type="text" class="form-control" id="flow_name" value="{$flow->getName()}">
  </div>
</div>

<div class="form-group">
  <label for="" class="control-label">Источник трафика: <span class="required">*</span></label>
  <div>
    <select id="flow_space" class="form-control select2me">
      {foreach from=$spaces item=s}
        <option value="{$s->getId()}" {if $s->getId() == $flow->getSpace()}selected{/if}>{$s->getName()} - {$s->getTypeAlias()}</option>
      {/foreach}
    </select>
  </div>
</div>


<div class="form-group" id="flow_link_wrap" {if !$flow->getLink()}style="display:none"{/if}>
  <label for="" class="control-label">Ссылка: </label>
  <div>
    <div class="input-group">
      <input type="text" class="form-control" id="flow_full_link" value="{$flow->getFullLink()}" disabled>
      <input type="hidden" id="flow_link" value="{$flow->getLink()}">
      <span class="input-group-btn">
        <button class="btn green" type="button" id="copy-link-btn"><i class="fa fa-copy"></i></button>
      </span>
    </div>
    <p class="alert alert-info text-center" style="display:none">Ссылка скопирована в буфер обмена!</p>
  </div>
</div>

{if $content.offers}
  <div class="form-group">
    <label for="" class="control-label">Оффер: <span class="required">*</span></label>
    <select id="flow_oid" class="form-control select2">
      <option value="0">Выберите оффер</option>
      {foreach from=$content.offers item=offer}
        <option value="{$offer.id}">#{$offer.id}: {$offer.name}</option>
      {/foreach}
    </select>
    <p class="help-block">Показаны только те офферы, у которых есть лендинги</p>
  </div>
{else}
  <input type="hidden" id="flow_oid" value="{$flow->getOfferId()}">
{/if}


<div class="form-group" id="landings-wrap" {if !$flow->getOfferId()}style="display:none;"{/if}>
  <label for="" class="control-label">
    Лендинг: <span class="required">*</span>
    <a href="javascript:;" class="btn btn-xs blue btn-reset" data-target="flow_landing[]">Сбросить выбор</a>
  </label>
  <div class="row">
    <div class="col-sm-6">
      <table class="table table-striped">
        <thead>
          <tr class="heading">
            <th width="50%"></th>
            <th class="text-center">EPC</th>
            <th class="text-center">CR</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$content.landings item=lp}
          <tr>
            <td>
              <label><input type="radio" name="flow_landing[]" value="{$lp.c_id}" id="content-{$lp.c_id}" {if $lp.c_id == $flow->getLandingId()}checked{/if}></label>
              <a href="{$lp.preview}" target="_blank">{$lp.name}</a>
            </td>
            <td class="text-center">-</td>
            <td class="text-center">-</td>
          </tr>
        {/foreach}
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="form-group" id="blogs-wrap" {if !$flow->getOfferId()}style="display:none;"{/if}>
  <label for="" class="control-label">
    Блог:
    <a href="javascript:;" class="btn btn-xs blue btn-reset" data-target="flow_blog[]">Сбросить выбор</a>
  </label>
  <div class="row">
    <div class="col-sm-6">
      <table class="table table-striped">
        <thead>
          <tr class="heading">
            <th width="50%"></th>
            <th class="text-center">EPC</th>
            <th class="text-center">CR</th>
          </tr>
        </thead>
        <tbody>
          {foreach from=$content.blogs item=lp}
          <tr>
            <td>
              <label><input type="radio" name="flow_blog[]" id="content-{$lp.c_id}" value="{$lp.c_id}" {if $lp.c_id == $flow->getBlogId()}checked{/if}></label>
              <a href="{$lp.preview}" target="_blank">{$lp.name}</a>
            </td>
            <td class="text-center">-</td>
            <td class="text-center">-</td>
          </tr>
        {/foreach}
        </tbody>
      </table>
    </div>
  </div>
</div>

<div id="comebacker-wrap" class="checkbox" {if !$flow->getBlogId()}style="display:none;"{/if}>
  <label>
    <input type="checkbox" class="checkbox-item" id="comebacker" {if $flow->hasComebacker()}checked{/if}> Включить Comebacker
  </label>
</div>

<hr>

<!--
<div class="checkbox">
  <label>
    <input type="checkbox" class="checkbox-item" data-toggle="show-block" data-block="subaccount-wrap" {if $flow->getSubaccountId()}checked{/if}> Добавить SUB аккаунт
  </label>
</div>

<div id="subaccount-wrap" class="form-group" {if !$flow->getSubaccountId()}style="display:none"{/if}>
  <label for="" class="control-label">SUB аккаунт:</label>
  <div class="row">
    <div class="col-md-6">
      <select class="form-control select2me" data-placeholder="Select..." id="subaccount_id">
        <option value="0"> </option>
        {foreach from=$subaccounts item=sub}
          <option value="{$sub.s_id}" {if $sub.s_id == $flow->getSubaccountId()} selected{/if}>{$sub.name}</option>
        {/foreach}
      </select>
    </div>
    <div class="col-md-6">
      <div class="input-group">
        <input id="subaccount_name" class="form-control" type="text">
        <span class="input-group-btn">
          <button id="add-subaccount" class="btn btn-success" type="button" disabled>Добавить</button>
        </span>
      </div>
    </div>
  </div>
</div>
-->

<div class="checkbox">
  <label>
    <input type="checkbox" class="checkbox-item" data-toggle="show-block" data-block="subid-wrap" {if $flow->getSubId(1)}checked{/if}> Добавить SUB ID
  </label>
</div>

<div id="subid-wrap" class="form-group" {if !$flow->getSubId(1)}style="display:none"{/if}>
  <label for="" class="control-label">SUB ID</label>
  <div class="row">
    <div class="col-md-2">
      <div class="input-group flow-sub_id">
        <input type="text" class="form-control" id="subid1" placeholder="SUB 1" value="{$flow->getSubId(1)}">
        <a class="input-group-addon" data-action="clear" data-target="subid1"> <i class="fa fa-times"></i> </a>
      </div>
    </div>
    <div class="col-md-2">
      <div class="input-group flow-sub_id">
        <input type="text" class="form-control" id="subid2" placeholder="SUB 2" value="{$flow->getSubId(2)}">
        <a class="input-group-addon" data-action="clear" data-target="subid2"> <i class="fa fa-times"></i> </a>
      </div>
    </div>
    <div class="col-md-2">
      <div class="input-group flow-sub_id">
        <input type="text" class="form-control" id="subid3" placeholder="SUB 3" value="{$flow->getSubId(3)}">
        <a class="input-group-addon" data-action="clear" data-target="subid3"> <i class="fa fa-times"></i> </a>
      </div>
    </div>
    <div class="col-md-2">
      <div class="input-group flow-sub_id">
        <input type="text" class="form-control" id="subid4" placeholder="SUB 4" value="{$flow->getSubId(4)}">
        <a class="input-group-addon" data-action="clear" data-target="subid4"> <i class="fa fa-times"></i> </a>
      </div>
    </div>
    <div class="col-md-2">
      <div class="input-group flow-sub_id">
        <input type="text" class="form-control" id="subid5" placeholder="SUB 5" value="{$flow->getSubId(5)}">
        <a class="input-group-addon" data-action="clear" data-target="subid5"> <i class="fa fa-times"></i> </a>
      </div>
    </div>
  </div>
</div>

<div class="checkbox">
  <label>
    <input type="checkbox" data-toggle="show-block" data-block="metrica-wrap" {if $flow->getYandexId() || $flow->getGoogleId() }checked{/if}> Добавить метрику
  </label>
</div>

<div id="metrica-wrap" class="form-group" {if !$flow->hasMetrics() }style="display:none"{/if}>
  <div class="row form-inline">
    <div class="col-md-4">
      <label for="" class="control-label">Yandex ID:</label>
      <div class="input-group">
        <input type="text" class="form-control" id="yandex_id" value="{$flow->getYandexId()}"/>
        <a class="input-group-addon" data-action="clear" data-target="yandex_id"> <i class="fa fa-times"></i> </a>
      </div>
    </div>
    <div class="col-md-4">
      <label for="" class="control-label">Google ID:</label>
      <div class="input-group">
        <input type="text" class="form-control" id="google_id" value="{$flow->getGoogleId()}"/>
        <a class="input-group-addon" data-action="clear" data-target="google_id"> <i class="fa fa-times"></i> </a>
      </div>
    </div>
    <div class="col-md-4">
      <label for="" class="control-label">Mail.ru ID:</label>
      <div class="input-group">
        <input type="text" class="form-control" id="mail_id" value="{$flow->getMailId()}"/>
        <a class="input-group-addon" data-action="clear" data-target="mail_id"> <i class="fa fa-times"></i> </a>
      </div>
    </div>
  </div>
</div>

<div class="checkbox">
  <label>
    <input type="checkbox" data-toggle="show-block" data-block="redirect-traffic-wrap" {if $flow->hasRedirectTraffic()}checked{/if}> При отключении оффера:
  </label>
</div>

<div id="redirect-traffic-wrap" class="form-group" {if !$flow->hasRedirectTraffic()}style="display:none"{/if}>
  <div class="form-group">
    <label class="control-label">Ссылка для перенаправления трафика: </label>
    <div>
      <input type="text" class="form-control" id="redirect_traffic" value="{$flow->getRedirectTrafficLink()}">
    </div>
    <p class="help-block">
      <strong>Пример url: </strong>http://www.domain.name/1.php<br/>
      <strong>Применение: </strong>При отключении оффера весь трафик по данному потоку будет перенаправлен по указанной ссылке
    </p>
  </div>
</div>


<div class="checkbox">
  <label>
    <input type="checkbox" data-toggle="show-block" data-block="trafficback-wrap" {if $flow->hasTrafficback()}checked{/if}> Trafficback
  </label>
</div>

<div id="trafficback-wrap" class="form-group" {if !$flow->hasTrafficback()}style="display:none"{/if}>
  <div class="form-group">
    <label class="control-label">Ссылка: </label>
    <div>
      <input type="text" class="form-control" id="trafficback" value="{$flow->getTrafficback()}">
    </div>
    <p class="help-block">Ссылка на которую направляется весь трафик не подошедший по гео-таргетингу</p>
  </div>
</div>


<div class="checkbox">
  <label>
    <input type="checkbox" data-toggle="show-block" data-block="alias-wrap" {if $flow->getLandingAlias() || $flow->getBlogAlias()}checked{/if}> Добавить название посадочных страниц для пользователей
  </label>
</div>

<div id="alias-wrap" class="form-group" {if !$flow->getLandingAlias() && !$flow->getBlogAlias()}style="display:none"{/if}>
  <div class="row">
    <div class="col-md-6">
      <div class="form-group" id="landing_alias_group">
        <label for="" class="control-label">Лендинг: </label>
        <div class="input-group">
          <span class="input-group-addon">{$flow->getStreamUrl()}{$flow->getUserId()}</span>
          <input type="text" class="form-control" id="landing_alias" value="{$flow->getLandingAlias()}">
        </div>
        <p class="help-block" style="display:none">Папка с таким названием уже существует</p>
      </div>
    </div>
    <div class="col-md-6">
      <div class="form-group" id="blog_alias_group">
        <label for="" class="control-label">Блог: </label>
        <div class="input-group">
          <span class="input-group-addon">{$flow->getStreamUrl()}{$flow->getUserId()}</span>
          <input type="text" class="form-control" id="blog_alias" value="{$flow->getBlogAlias()}"/>
        </div>
        <p class="help-block" style="display:none">Папка с таким названием уже существует</p>
      </div>
    </div>
  </div>
  <p class="help-block"><em>* можно использовать только латиницу, например blog-anfisa</em></p>
</div>

<div class="form-group">

  <div class="checkbox">
    <label>
      <input type="checkbox" {if $flow->useGlobalPostback() || $flow->getID() == 0}checked{/if} id="postback-check"> Использовать глобальный постбек
    </label>
  </div>


  <div class="form-group">
    <label for="" class="control-label">Постбек: </label>
    <div>
      <input type="text" id="postback-link" value="{$postback->getUrl()}" class="form-control" {if $flow->useGlobalPostback()}disabled{/if} />
    </div>
  </div>

  <div id="postback-triggers-wrap" {if $flow->useGlobalPostback() || $flow->getID() == 0}style="display:none"{/if}>
    <div class="checkbox">
      <label>
        <input type="checkbox" id="postback-send-on-create" {if $postback->sendOnCreate()}checked{/if}> Отправлять запрос при создании заказа
      </label>
    </div>
    <div class="checkbox">
      <label>
        <input type="checkbox" id="postback-send-on-confirm" {if $postback->sendOnConfirm()}checked{/if}> Отправлять запрос при подтверждении заказа
      </label>
    </div>
    <div class="checkbox">
      <label>
        <input type="checkbox" id="postback-send-on-cancel" {if $postback->sendOnCancel()}checked{/if}> Отправлять запрос при отмене заказа
      </label>
    </div>
  </div>

</div>

<hr />

<div id="prices-wrap">
  {$prices}
</div>

<div class="form-group clearfix">
  <button id="save-flow" class="btn green">Сохранить поток</button>
</div>