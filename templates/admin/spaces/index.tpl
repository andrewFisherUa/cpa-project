<h2 class="page-title">Источники трафика</h2>

<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="/admin">Главная</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">Источники трафика</a>
    </li>
  </ul>
</div>

{if $notes}
  <div class="note note-danger">
    <h3>Неактивные источники:</h3>
    {foreach from=$notes item=n}
      <p>{$n}</p>
    {/foreach}
  </div>
{/if}

{if $message}
  <div class="alert alert-{$message.class}">{$message.text}</div>
{/if}

<div class="portlet light" id="spaces-list">
  <div class="portlet-title">
    <div class="caption">
      Мои источники трафика
    </div>
    <div class="actions">
      <a href="javascript:;" class="btn blue {if $hasSpaces}expand-btn{else}collapse-btn{/if}"><i class="fa fa-plus"></i> Добавить источник трафика</a>
    </div>
  </div>
  <div class="portlet-body" {if $hasSpaces}style="display:none;"{/if}>
    <div class="clearfix">
      <a href="/admin/spaces/new/site" class="btn blue pull-right"><i class="fa fa-plus"></i> Добавить</a>
      1. Если вы хотите добавить свой Вебсайт, например: www.mysite.ru
    </div>
    <div class="clearfix">
      <a href="/admin/spaces/new/context" class="btn blue pull-right"><i class="fa fa-plus"></i> Добавить</a>
      2. Если вы хотите рекламировать программы через контекстную рекламу, например Google AdWords
    </div>
    <div class="clearfix">
      <a href="/admin/spaces/new/public" class="btn blue pull-right"><i class="fa fa-plus"></i> Добавить</a>
      3. Если вы используете социальные сети (приложения, группы, тизерная реклама), например: vkontakte.ru/app
    </div>
    <div class="clearfix">
      <a href="/admin/spaces/new/doorway" class="btn blue pull-right"><i class="fa fa-plus"></i> Добавить</a>
      4. Если вы хотите рекламировать программы через сеть дорвеев
    </div>
    <div class="clearfix">
      <a href="/admin/spaces/new/arbitrage" class="btn blue pull-right"><i class="fa fa-plus"></i> Добавить</a>
      5. Если вы используете арбитражные системы (баннерные, тизерные и другие сети)
    </div>
    <div class="clearfix">
      <a href="/admin/spaces/new/other" class="btn blue pull-right"><i class="fa fa-plus"></i> Добавить</a>
      6. Если вы используете другой источник трафика (Мобильный, Email, SMS рассылки, Adult трафик, Мотивированый трафик, Pop-up, Click-Under)
    </div>
  </div>
</div>

<div class="portlet light">
  <div class="portlet-body">
    <div class="table-container">
      <table class="table table-striped" id="datatable_user_spaces">
        <thead>
          <tr class="heading">
            <th width="5%">ID</th>
            <th width="15%">Название</th>
            <th width="15%">Источник</th>
            <th width="15%">Ссылка</th>
            <th width="15%">Тип</th>
            <th width="10%">Статус</th>
            <th width="10%">Действия</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>