<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">
{if $offer->getId() == 0}
Создание оффера
{else}
Редактирования оффера #{$offer->getId()} `{$offer->getName()}`
{/if}
</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="/admin/offers/">Офферы</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="#">{if $offer->getId() == 0}Создание оффера{else}#{$offer->getId()} `{$offer->getName()}`{/if}</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->

<!-- BEGIN PAGE CONTENT -->
<form id="add-offer-form" action="#" class="form-horizontal" method="post" data-changed="false">
<input type="hidden" name="offer[id]" value="{$offer->getId()}">

<div class="portlet light">
  <div class="portlet-title">
      <div class="caption">
          <i class="fa fa-shopping-cart"></i>{if $offer->getId() == 0}Создание оффера{else}#{$offer->getId()} `{$offer->getName()}`{/if}
      </div>
      <div class="actions btn-set">
        <button type="submit" class="btn blue btn-circle" name="save-offer">Сохранить</button>
      </div>
  </div>
    <div class="portlet-body">
      <ul class="nav nav-tabs">
        <li class="active">
          <a href="#tab-general" data-toggle="tab" aria-expanded="true">Основное </a>
        </li>
        <li class="">
          <a href="#tab-content" data-toggle="tab" aria-expanded="false">Контент </a>
        </li>
        {if $offer->getId()!=0}
        <li class="">
          <a href="#tab-options" data-toggle="tab" aria-expanded="false">Настройки контента </a>
        </li>
        <li class="">
          <a href="#tab-profit" data-toggle="tab" aria-expanded="false">Прибыль </a>
        </li>
        {/if}
      </ul>
      <div class="tab-content">
        <div class="tab-pane fade active in" id="tab-general">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label">ID товара: <span class="required">*</span></label>
                            <div class="col-md-4">
                                <input type="text" value="{$offer->getGID()}" name="offer[gid]" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Статус: <span class="required">* </span></label>
                            <div class="col-md-5">
                                <select class="form-control" name="offer[status]">
                                    {foreach from=$statusList item=s}
                                    <option value="{$s.status}" {if $offer->getStatus() == $s.status}selected{/if}>{$s.label}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="col-md-5">
                                <div class="checkbox">
                                    <label>
                                      <input type="checkbox" name="offer[available_in_shop]" value="1" {if $offer->isAvailableInShop() == 1}checked{/if}> Видимость в магазине
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                      <input type="checkbox" name="offer[available_in_offers]" value="1" {if $offer->isAvailableInOffers() == 1}checked{/if}> Видимость в офферах
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Тип оффера: <span class="required">* </span></label>
                            <div class="col-md-5">
                                <select class="form-control" name="offer[type]" id="offer-type">
                                   {foreach from=$typeList item=s}
                                    <option value="{$s.type}" {if $offer->getType() == $s.type}selected{/if}>{$s.label}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="col-md-5">
                                <div id="available_webmaster" class="checkbox" {if $offer->getType() != 2}style="display:none"{/if}>
                                    <label>
                                      <input type="checkbox" name="options[available_webmasters]" value="1" {if $options->get('available_webmasters') == 1}checked{/if}> Доступно вебмастерам
                                    </label>
                                </div>

                                <div id="webmaster-list" {if $offer->getType() != 3}style="display:none"{/if}>
                                    <select name="" id="webmaster-name" class="form-control select2me" >
                                        {foreach from=$webmasters item=user}
                                        <option value="{$user.user_id}" data-login="{$user.login}">{$user.user_id}: {$user.login}</option>
                                        {/foreach}
                                    </select>

                                    <table id="webmaster-table" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th width="10%">ID</th>
                                                <th width="75%">Логин</th>
                                                <th width="15%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {foreach from=$selected_webmasters item=user}
                                                <tr><td><input type='hidden' name='webmaster_list[]' value='{$user.user_id}'>{$user.user_id}</td><td>{$user.login}</td><td><span class='remove' data-id='{$user.user_id}'><i class='fa fa-close'></i></a></td></tr>
                                            {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Рекламодатель: <span class="required">* </span></label>
                            <div class="col-md-5">
                                <select class="form-control" name="offer[user_id]">
                                    {foreach from=$partners item=p }
                                        <option value="{$p.user_id}" {if $offer->getOwnerId() == $p.user_id}selected{/if}>{$p.login}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Название: <span class="required">* </span></label>
                            <div class="col-md-10">
                                <input type="text" class="form-control"name="offer[name]" placeholder="Название" value="{$offer->getName()}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Краткое описание (для вебмастеров): </label>
                            <div class="col-md-10">
                                <textarea id="ckeditor-description" name="offer[description]">{$offer->getDescription()}</textarea>
                                {literal}
                                <script type="text/javascript">
                                  var editor1 = CKEDITOR.replace('ckeditor-description');
                                  AjexFileManager.init({
                                    returnTo: 'ckeditor',
                                    editor: editor1
                                  });
                                </script>
                                {/literal}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Категории: <span class="required">* </span></label>
                            <div class="col-md-10">
                                <div class="form-control height-auto">
                                    <div class="scroller" style="height:275px;" data-always-visible="1">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="list-unstyled">
                                                    {foreach from=$categories.shop_category item=cat}
                                                    <li>
                                                        <label>
                                                            <input type="checkbox" name="offer[categories][]" value="{$cat.id}" {if $cat.selected} checked{/if}>{$cat.name}
                                                        </label>
                                                    </li>
                                                    {/foreach}
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-unstyled">
                                                    {foreach from=$categories.offer_category item=cat}
                                                    <li>
                                                        <label>
                                                            <input type="checkbox" name="offer[categories][]" value="{$cat.id}" {if $cat.selected} checked{/if}>{$cat.name}
                                                        </label>
                                                    </li>
                                                    {/foreach}
                                                </ul>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                  <label class="col-md-4 control-label">Логотип: </label>
                                  <div class="col-md-8">
                                      <input type="hidden" id="offer-logo" name="offer[logo]" value="{$main_image.name}">
                                      <div id="pic-progress-wrap" class="fileinput-new" style="width: 240px">
                                        <div id="picbox" class="thumbnail">
                                            <img src="{$offer->getMainImagePath()}" alt="">
                                        </div>
                                      </div>

                                      <input type="button" id="upload-btn" class="btn btn-large clearfix green" value="Выбрать файл">
                                      <div class="help-block">Квадратное изображение, до 1000x1000px, до 1мб</div>
                                      <div id="errormsg" class="clearfix redtext" style="padding-top: 5px;"></div>
                                      <div id="progressOuter" class="progress progress-striped active" style="display:none;">
                                        <div id="progressBar" class="progress-bar progress-bar-success"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                        </div>
                                      </div>
                                  </div>
                                </div>
                            </div>
                        </div>

                        <div class="note box-blue text-center">
                            <p>Пометьте виды источников трафика, которые принимаются данной программой</p>
                        </div>

                        <div class="form-group">
                            <div class="col-md-10 col-md-offset-1">
                                <div class="row">
                                    {foreach from=$traffic_sources item=part}
                                    <div class="col-md-4">
                                        {foreach from=$part item=t}
                                            <label><input type="checkbox" name="offer[traffic_sources][]" value="{$t.id}" {if $t.selected} checked{/if}>{$t.name}</label> <br>
                                        {/foreach}
                                    </div>
                                    {/foreach}
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row form-horizontal">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label col-md-6">PostClick cookie: </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control numbers-only" name="options[postclick_cookie]" value="{$options->get('postclick_cookie')}"placeholder="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-6">Дней на обработку заказа: </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control numbers-only" name="options[order_processing_time]" value="{$options->get('order_processing_time')}" placeholder="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-6">Максимум заказов в сутки: </label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control numbers-only" name="options[orders_per_day]" value="{$options->get('orders_per_day')}" placeholder="">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="col-md-10 col-md-offset-1">

                                        <div id="countries-list" class="form-group">
                                            <label for="" class="control-label">
                                                География:
                                                <a href="javascript:;" class="btn btn-circle btn-default btn-new">
                                                <i class="fa fa-plus"></i> Добавить </a>
                                            </label>
                                            <div id="geo-list">
                                                {foreach from=$countries item=item}
                                                <div class="clearfix margin-bottom-5">
                                                    <span class="flag flag-{$item.code}"></span> {$item.name}
                                                    <a data-toggle="tooltip" data-code="{$item.code}" data-placement="left" title="Удалить цены по стране" class="btn btn-sm btn-circle btn-icon-only btn-default btn-remove pull-right" href="javascript:;">
                                                        <i class="icon-trash"></i>
                                                    </a>
                                                    <a data-toggle="tooltip" data-code="{$item.code}" data-placement="left" title="Редактировать цену" class="btn btn-sm btn-circle btn-icon-only btn-default btn-edit pull-right" href="javascript:;">
                                                        <i class="icon-pencil"></i>
                                                    </a>
                                                </div>
                                                {/foreach}
                                            </div>
                                        </div>

                                        <div id="targets-list" class="form-group">
                                            <label class="control-label">Цели:</label>
                                            <div  id="targets" class="margin-top-5">
                                                {foreach from=$targets item=target}
                                                <div class="clearfix margin-bottom-5">
                                                    {$target.name}
                                                    <a data-toggle="tooltip" data-placement="left" title="Редактировать цель" class="btn btn-sm btn-circle btn-icon-only btn-default btn-edit pull-right" data-target="{$target.id}" href="javascript:;">
                                                        <i class="icon-pencil"></i>
                                                    </a>
                                                </div>
                                            {/foreach}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="table-container" id="offers-table-wrapper">
                                {$offer->getPricesView()}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="tab-content">
            <div class="row">
                <div class="col-md-6">
                    <div>
                      <label class="control-label">Группа</label>
                      <div>
                        <select class="form-control" data-placeholder="Выбор группы" id="content_group" data-load="ajax">
                        </select>
                      </div>
                    </div>

                    <div>
                        <label class="control-label">Лендинг</label>
                        <div>
                          <div class="input-group">
                            <select class="form-control" data-placeholder="Выбор лендинга" id="content_landing" class="form-control"></select>
                            <span class="input-group-btn">
                              <button id="add-landing" class="btn btn-success" type="button" disabled>Добавить</button>
                            </span>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="tree_2" class="tree-demo margin-top-5"></div>
                </div>
            </div>
        </div>

        {if $offer->getId()!=0}
        <div class="tab-pane fade" id="tab-profit">

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text-center" rowspan="2" width="15%">Страна</th>
                        <th class="text-center" rowspan="2" width="10%">Цена</th>
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
                    {foreach from=$refoptions item=item}
                    <tr>
                        <td><span class="flag flag-{$item.code}"></span> {$item.name}</td>
                        <td class="text-right">{$item.price}&nbsp;{$item.currency}</td>
                        <td>
                            <select size="1" name="options[refprofit_type][{$item.code}]" class="form-control">
                                <option value="percent"{if $item.type == "percent"} selected{/if}>Процент</option>
                                <option value="fixed"{if $item.type == "fixed"} selected{/if}>Сумма</option>
                            </select>
                        </td>
                        <td>
                            <div class="input-group">
                                <input name="options[refprofit_level1][{$item.code}]" value="{$item.level1}" class="form-control numbers-only"/>
                                <span class="input-group-addon">{$item.currency}</span>
                              </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input name="options[refprofit_level2][{$item.code}]" value="{$item.level2}" class="form-control numbers-only"/>
                                <span class="input-group-addon">{$item.currency}</span>
                              </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input name="options[refprofit_level3][{$item.code}]" value="{$item.level3}" class="form-control numbers-only"/>
                                <span class="input-group-addon">{$item.currency}</span>
                              </div>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>

        <div class="tab-pane fade" id="tab-options">
          <div class="alert alert-info" style="display: none"><p></p></div>
          <div id="options-wrap">
              {$options_list}
          </div>
          <div class="clearfix"></div>
          <!--
          <div class="checkbox pull-right">
              <label>
                <input type="checkbox" name="save_all_adv_options"> Применить ко всем офферам этого рекламодателя
              </label>
          </div>
            <div class="clearfix"></div>
            <button class="btn green pull-right" id="reset-options">Восстановить параметры по умолчанию</button>
            <div class="clearfix"></div>
        </div>
        -->
        {/if}
      </div>
    </div>
    </form>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="edit-offer-modal" role="dialog" aria-labelledby="dialogLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="dialogLabel">Добавление / Редактирование цели</h4>
      </div>
      <div class="modal-body">
          <table class="table table-bordered table-hover" id="offer-price-table">
            <thead>
                <tr>
                    <th width="5%"></th>
                    <th width="15%">Страна</th>
                    <th width="20%">Цена на лендинге</th>
                    <th width="20%">Максимальная цена</th>
                    <th width="20%">Комиссия вебмастера</th>
                    <th width="20%">Комиссия Univermag</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn btn-primary" id="save-offer-target">Сохранить изменения</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="edit-geo" tabindex="-1" role="dialog" aria-labelledby="dialogLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="dialogLabel">Настройка страны</h4>
      </div>
      <div class="modal-body">
        <div class="form">
            <div class="alert alert-danger" style="display:none"></div>
            <div class="form-group">
                <label for="" class="control-label">Название <span class="required">*</span></label>
                <select class="form-control" id="country-name">
                    <option value="-1">Выбор страны </option>
                    {foreach from=$countries item=item}
                        <option value="{$item.code}">{$item.name}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <label for="" class="control-label">Цена на лендинге: <span class="required">*</span></label>
                <input type="text" class="form-control numbers-only" id="country-price" value="">
            </div>
            <div class="form-group">
                <label for="" class="control-label">Код цены: <span class="required">*</span></label>
                <input type="text" class="form-control" id="country-price_id" value="">
            </div>
            <div class="form-group">
                <label for="" class="control-label">Количество: <span class="required">*</span></label>
                <input type="text" class="form-control" id="country-qty" value="">
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn btn-primary btn-save">Сохранить</button>
      </div>
    </div>
  </div>
</div>