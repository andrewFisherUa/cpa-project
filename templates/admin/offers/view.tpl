<div id="page-offer-view">
  <!-- BEGIN PAGE HEADER-->
  <h3 class="page-title">
    {$offer->getName()}
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
              <a href="#">{$offer->getName()}</a>
          </li>
        </ul>

        <div class="actions btn-set pull-right">

        </div>
  </div>
  <!-- END PAGE HEADER-->

  <!-- BEGIN PAGE CONTENT-->
  <div class="portlet light">
    <div class="portlet-title">
      <div class="caption">
        {$offer->getName()}
      </div>

      <div class="actions">

        <button {if !$canBeConnected}style="display:none"{/if} data-action="reload" class="btn default btn-sm green add-user-good" data-g_id="{$offer->getId()}" data-rules="{$options->get('show_rules')}"><i class="fa fa-plus"></i> Подключить</button>
        <a {if !$is_connected || $admin}style="display:none"{/if} href="#flows" data-toggle="tab" class="btn awesome-green btn-sm get-offer-link"><i class="icon-link"></i> Получить ссылку</a>
        <button {if !$is_connected}style="display:none"{/if} data-action="reload" class="btn default btn-sm default remove-user-good" data-g_id="{$offer->getId()}"><i class="fa fa-times"></i> Отключить</button>
        {if $admin}
        <a href="/admin/offers/edit/{$offer->getId()}" class="btn btn-default btn-circle"><i class="fa fa-edit"></i> Редактировать</a>
        {/if}
      </div>
    </div>
    <div class="portlet-body">
      <div class="clearfix">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist" id="offer-tabs">
          <li role="presentation" class="active"><a href="#info" aria-controls="info" role="tab" data-toggle="tab">Информация</a></li>
          {if $is_connected}
          <li role="presentation"><a href="#create-flow" aria-controls="create-flow" role="tab" data-toggle="tab">Создать поток</a></li>
          <li role="presentation"><a href="#flows" aria-controls="flows" role="tab" data-toggle="tab">Потоки</a></li>
          {/if}
          <li role="presentation"><a href="#news" aria-controls="news" role="tab" data-toggle="tab">Новости</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
          <!-- #info tab -->
          <div role="tabpanel" class="tab-pane active" id="info">
            <div class="row" >
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-3">
                    <!-- .widget -->
                    <div class="widget prices-widget light bg-inverse">
                      <div class="widget-head">
                        <div class="caption">
                          <i class="fa fa-money"></i>
                          <span class="caption-subject"> Выплаты</span>
                        </div>
                      </div>
                      <div class="widget-body">
                        {foreach from=$targets item=target}
                          <h5>{$target.name}</h5>
                          <ul>
                            {foreach from=$target.values key=code item=value}
                            <li><span class="flag flag-{$code}"></span> {$value}</li>
                            {/foreach}
                          </ul>
                        {/foreach}
                      </div>
                    </div><!-- /.widget -->

                    <!-- .widget -->
                    <div class="widget light bg-inverse">
                      <div class="widget-head">
                        <div class="caption">
                          <i class="fa fa-info-circle"></i>
                          <span class="caption-subject"> Инфо</span>
                        </div>
                      </div>
                      <div class="widget-body list">
                        <div class="list-row">
                          <div class="list-label">PostClick cookie:</div>
                          <div class="list-value">{$options->get('postclick_cookie')} дней</div>
                        </div>
                      </div>
                    </div><!-- /.widget -->

                    <!-- .widget -->
                    <div class="widget light bg-inverse">
                      <div class="widget-head">
                        <div class="caption">
                          <i class="fa fa-folder-open"></i>
                          <span class="caption-subject"> Категория</span>
                        </div>
                      </div>
                      <div class="widget-body list">
                        <ul class="widget-list semibold cat-list">
                        {foreach from=$offer->getCategories() item=cat}
                          <li>{$cat.name}</li>
                        {/foreach}
                        </ul>
                      </div>
                    </div><!-- /.widget -->

                    <!-- .widget -->
                    <div class="widget light bg-inverse">
                      <div class="widget-head">
                        <div class="caption">
                          <i class="fa fa-globe"></i>
                          <span class="caption-subject"> Трафик</span>
                        </div>
                      </div>
                      <div class="widget-body list">
                        {foreach from=$offer->getTrafficSources() item=t}
                          <div class="list-row">
                            <div class="list-label">{$t.name}</div>
                            <div class="list-value">
                              {if $t.selected} <i class="fa fa-check font-green"></i>{else}<i class="fa fa-times font-red"></i>{/if}
                            </div>
                          </div>
                        {/foreach}
                      </div>
                    </div><!-- /.widget -->

                  </div>
                  <div class="col-md-9">
                    <div class="row">
                      <div class="col-md-8">
                        <!-- .widget -->
                        <div class="widget light bg-inverse">
                          <div class="widget-head">
                            <div class="caption">
                              <i class="fa fa-globe"></i>
                              <span class="caption-subject"> Описание</span>
                            </div>
                          </div>
                          <div class="widget-body">
                            {$offer->getDescription()}

                            {if $countries}
                              <ul class="geo">
                                  <li><strong>Гео: </strong></li>
                                {foreach from=$countries item=c}
                                  <li><span class="flag flag-{$c.code}"></span> {$c.name}</li>
                                {/foreach}
                              </ul>
                            {/if}
                          </div>
                        </div><!-- /.widget -->
                      </div>
                      <div class="col-md-4">

                        <div class="offer-image thumbnail">
                          <a class="fancybox" href="{$offer->getMainImagePath()}">
                            <img src="{$offer->getMainImagePath()}" class="image-responsive" alt="">
                          </a>
                        </div>

                        <!-- .widget -->
                        <div class="widget light bg-inverse">
                          <div class="widget-head">
                            <div class="caption">
                              <i class="fa fa-globe"></i>
                              <span class="caption-subject"> Лендинги</span>
                            </div>
                          </div>
                          <div class="widget-body list">
                            <ul class="widget-list">
                            {foreach from=$content.landings item=lp}
                              <li><a href="{$lp.preview}" target="_blank"><i class="fa fa-external-link"></i> {$lp.name}</a></li>
                            {/foreach}
                            </ul>
                          </div>
                        </div><!-- /.widget -->

                        <!-- .widget -->
                        <div class="widget light bg-inverse">
                          <div class="widget-head">
                            <div class="caption">
                              <i class="fa fa-globe"></i>
                              <span class="caption-subject"> Блоги</span>
                            </div>
                          </div>
                          <div class="widget-body list">
                            <ul class="widget-list">
                            {foreach from=$content.blogs item=b}
                              <li><a href="{$b.preview}" target="_blank"><i class="fa fa-external-link"></i> {$b.name}</a></li>
                            {/foreach}
                            </ul>
                          </div>
                        </div><!-- /.widget -->
                      </div>
                    </div>
                  </div>
                </div><!-- /.row -->
              </div>
          </div>
          </div>
          <!-- /.#info -->
           {if $is_connected}
          <!-- #create-flow tab -->
          <div role="tabpanel" class="tab-pane" id="create-flow">
            <div class="col-md-10">

              <div class="alert alert-danger" style="display:none"></div>
              <div class="alert alert-success" style="display:none"></div>

              <div class="form-container">
                {$flow_form}
               </div>
            </div>
          </div>
          <!-- /.#create-flow -->
          <!-- #flows tab -->
          <div role="tabpanel" class="tab-pane" id="flows">
            {if !$flows}
              <div class="alert alert-danger">
                Вы еще не создали ни одного потока, для получения ссылки на оффер нажмите <a href="#create-flow" data-toggle="tab">Создать поток</a>.
              </div>
            {/if}
            <div class="table-container">
              <table class="table table-striped table-bordered table-hover" id="datatable_flows" data-uid="{$owner}" data-oid="{$offer->getId()}">
                <thead>
                  <tr role="row" class="heading">
                    <th width="30%">Название потока</th>
                    <th width="15%">Дата изменения</th>
                    <th width="15%">Источники</th>
                    <th width="25%">Ссылка</th>
                    <th width="15%">Действия</th>
                  </tr>
                  <tr role="row" class="filter">
                    <td>
                      <select class="form-control select2me table-filter input-sm" data-placeholder="Select..." name="flow_id">
                        <option value="0">Название потока</option>
                        {foreach from=$flows item=flow}
                          <option value="{$flow.f_id}">{$flow.name}</option>
                        {/foreach}
                      </select>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                      <!--<button class="btn btn-sm yellow filter-submit margin-bottom"><i class="fa fa-search"></i> Поиск</button>-->
                      <button class="btn btn-sm red reset-filters"><i class="fa fa-times"></i> Отмена</button>
                    </td>
                </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
          <!-- /.#flows -->
          {/if}
          <!-- #news tab -->
          <div role="tabpanel" class="tab-pane" id="news">
            <div id="news-wrapper">
              <!--
              {if !empty($offer_news.0)}
                {foreach from=$offer_news item=v key=k}
                  <div class="portlet light bordered">
                    <div class="portlet-title">
                      <div class="caption">
                          <span class="type"> {$v.type_icon} </span> {$v.name}
                      </div>
                      <div class="actions">
                          {$v.date}
                      </div>
                    </div>
                    <div class="portlet-body">
                      {$v.text}
                    </div>
                  </div>
                {/foreach}
              {else}
            -->

    <!-- <div class="note note-info">У данного оффера нет новостей -->

          <div class="timeline">
                {$get_html_news}
          </div>

  <!-- </div> -->
              <!--{/if}-->
            </div>
          </div>
          <!-- /.#news -->
        </div>
      </div>
    </div>
  </div>


  <!-- END PAGE CONTENT-->

  <!-- Modal -->
  <div class="modal fade" id="rulesModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Добавить оффер</h4>
        </div>
        <div class="modal-body">
          <p class="note note-success">
            Добавив данный оффер, вы автоматически подтверждаете, что согласны с нижеприведёнными правилами.
          </p>
          <div id="rules-wrap"></div>
        </div>
        <div class="modal-footer">
          <a href="javascript:;" class="btn blue" id="add-offer">Добавить</a>
          <a href="javascript:;" class="btn btn-default" data-dismiss="modal">Отмена</a>
        </div>
      </div>
    </div>
  </div>

</div>


<!-- Modal -->
<div class="modal fade" id="linkModal" tabindex="-1" role="dialog" aria-labelledby="dialogLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="linkModalLabel">Сгенерированный поток</h4>
            </div>
            <div class="modal-body">
              <form class="form-inline" id="flow_link_wrap">
                <div class="alert alert-info text-center" style="display:none">Ссылка скопирована в буфер обмена!</div>
                <div class="form-group">
                  <label for="" class="control-label">Ссылка на поток: </label>
                </div>
                <br>
                <div class="form-group">
                  <input type="hidden" class="form-control" id="flow_link" value="">
                  <input type="text" style="min-width: 300px;" class="form-control" id="flow_full_link" readonly>
                </div>
                <a href="javascript:;" class="btn green" id="copy-link-btn">Копировать</a>
              </form>
            </div>
        </div>
    </div>
</div>
