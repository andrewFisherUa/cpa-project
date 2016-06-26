<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">
  Просмотр заказа <small>просмотр деталей заказа</small>
</h3>
<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="/admin">Главная</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="/admin/orders">Заказы</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">Просмотр заказа #{$order->getId()}</a>
    </li>
  </ul>
</div>
<!-- END PAGE HEADER-->

<!-- BEGIN PAGE CONTENT-->
<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      <span class="caption-subject bold uppercase">Заказ #{$order->getId()} </span>
      <span class="caption-helper">{$order->getCreated(true)}</span>
    </div>
    <div class="actions">
      <a href="/admin/orders/" class="btn btn-default btn-circle">
        <i class="fa fa-angle-left"></i>
        <span class="hidden-480">Назад </span>
      </a>
      <a href="/admin/orders/new" class="btn btn-circle btn-default">
        <i class="fa fa-plus"></i> <span class="hidden-480"> Новый заказ </span>
      </a>
    </div>
  </div>
  <div class="portlet-body">
    <div class="row">
        <div class="col-md-6 col-sm-12">
          <div class="portlet green box">
            <div class="portlet-title">
              <div class="caption">
                Детали заказа
              </div>
            </div>
            <div class="portlet-body">
              <div class="row static-info">
                <div class="col-md-5 name">
                   Заказ #:
                </div>
                <div class="col-md-7 value">
                   {$order->getId()}
                </div>
              </div>
              <div class="row static-info">
                <div class="col-md-5 name">
                   Дата заказа:
                </div>
                <div class="col-md-7 value">
                   {$order->getCreated(true)}
                </div>
              </div>
                            <div class="row static-info">
                <div class="col-md-5 name">
                  Страна:
                </div>
                <div class="col-md-7 value">
                 {$country}&nbsp;<i class="flag flag-{$order->getCountryCode()}"></i>
                </div>
              </div>
              <div class="row static-info">
                <div class="col-md-5 name">
                  Источник:
                </div>
                <div class="col-md-7 value">
                  <a href="{$stream_link}">{$stream_link}</a>
                </div>
              </div>
              <div class="row static-info">
                <div class="col-md-5 name">
                   Статус заказа:
                </div>
                <div class="col-md-7 value">
                  <div class="input-group">
                    <span class="label label-sm label-{$order->getStatusAlias()}">{$order->getStatusLabel()}</span>
                  </div>
                </div>
              </div>
              <div class="row static-info">
                <div class="col-md-5 name">
                   Количество товаров:
                </div>
                <div class="col-md-7 value">
                   {$order->getProductsCount()}
                </div>
              </div>
              <div class="row static-info">
                <div class="col-md-5 name">
                   Общая сумма:
                </div>
                <div class="col-md-7 value">
                   {$order->getAmount()}&nbsp;{$order->getCurrency()}
                </div>
              </div>
              <div class="row static-info">
                <div class="col-md-5 name">
                   Комиссия UM:
                </div>
                <div class="col-md-7 value">
                   {$order->getCommission()}&nbsp;{$order->getCurrency()}
                </div>
              </div>
              <div class="row static-info">
                <div class="col-md-5 name">
                   Комиссия вебмастера:
                </div>
                <div class="col-md-7 value">
                   {$order->getWebmasterCommission()}&nbsp;{$order->getCurrency()}
                </div>
              </div>
              {if $order->wasModified()}
              <div class="row static-info">
                <div class="col-md-5 name">
                   Комментарий изменен:
                </div>
                <div class="col-md-7 value">
                   {$order->getModified(true)}
                </div>
              </div>
              {/if}
              {if $order->wasChanged()}
              <div class="row static-info">
                <div class="col-md-5 name">
                   Статус изменен:
                </div>
                <div class="col-md-7 value">
                   {$order->getChanged(true)}
                </div>
              </div>
              {/if}
            </div>
          </div>
        </div>
        <div class="col-md-6 col-sm-12">
          <div class="portlet blue-hoki box">
            <div class="portlet-title">
              <div class="caption">
                Данные покупателя
              </div>
            </div>
            <div class="portlet-body">
              <div class="row static-info">
                <div class="col-md-4 name">
                   Фамилия:
                </div>
                <div class="col-md-8 value">
                   {$order->getLastName()}
                </div>
              </div>
              <div class="row static-info">
                <div class="col-md-4 name">
                   Имя:
                </div>
                <div class="col-md-8 value">
                   {$order->getFirstName()}
                </div>
              </div>
              <div class="row static-info">
                <div class="col-md-4 name">
                   Телефон:
                </div>
                <div class="col-md-8 value">
                   {$order->getPhone()}
                </div>
              </div>
              <div class="row static-info">
                <div class="col-md-4 name">
                   Email:
                </div>
                <div class="col-md-8 value">
                   {$order->getEmail()}
                </div>
              </div>
              <div class="row static-info">
                <div class="col-md-4 name">
                   IP:
                </div>
                <div class="col-md-8 value">
                   {$order->getIp()}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="portlet blue-hoki box">
            <div class="portlet-title">
              <div class="caption">
                Корзина
              </div>
            </div>
            <div class="portlet-body">
              <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped">
                <thead>
                <tr>
                  <th>#</th>
                  <th>Название</th>
                  <th>Цена</th>
                  <th>Количество</th>
                  <th>Комиссия вебмастера</th>
                  <th>Комиссия UM</th>
                  <th>Сумма</th>
                </tr>
                </thead>
                <tbody>
                  {foreach from=$order->getProducts() item=good name=g}
                  <tr>
                      <td>{$smarty.foreach.g.iteration}</td>
                      <td><a href='/admin/offers/view/{$good.good_id}'>{$good.product_name}</a></td>
                      <td>{$good.price}&nbsp;{$order->getCurrency()}</td>
                      <td>{$good.qty}</td>
                      <td>{$good.webmaster_commission}</td>
                      <td>{$good.commission}</td>

                      <td>{$good.total_amount}&nbsp;{$order->getCurrency()}</td>
                  </tr>
                  {/foreach}
                </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="portlet red box">
            <div class="portlet-title">
              <div class="caption">Комментарии</div>
              <div class="tools">
                <a href="javascript:;" class="collapse" data-original-title="" title=""></a>
              </div>
            </div>
            <div class="portlet-body">
              {if $log}
              <table class="table">
                <thead>
                  <th>Статус</th>
                  <th>Комментарий</th>
                  <th>Добавлен</th>
                </thead>
                <tbody>
                  {foreach from=$log item=a}
                  <tr>
                    <td>{$a.status_name}</td>
                    <td>{$a.comment}</td>
                    <td>{$a.created}</td>
                  </tr>
                  {/foreach}
                </tbody>
              </table>
              {else}
              Нет комментариев
              {/if}
            </div>
          </div>
        </div>
      </div><!-- /.row -->
  </div>
</div>