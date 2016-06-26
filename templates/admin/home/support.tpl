<h3 class="page-title">
    Главная страница
</h3>

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="/admin"><i class="fa fa-home"></i></a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="#">Главная</a>
        </li>
    </ul>
</div>

<div class="row" style="margin-bottom: 20px;">
    <div class="col-md-12 text-center">
        <img src="/misc/images/images/summer-banner.jpg" alt="" style="width: 100%">
    </div>
</div>

<div class="portlet light">
  <div class="portlet-body">
    <h4>Топ офферы:</h4>
    <div class="row" id="top-offers">
        {foreach from=$top_offers item=a}
        <div class="offer-block col-md-2 col-sm-3 col-xs-6">
            <a href="/offers/view/{$a.id}">
                <div class="pic thumbnail">
                    <img src="/misc/images/goods/{$a.image}" alt="{$a.name}" class="img-responsive">
                </div>
                <div class="title">
                    {$a.name}
                </div>
            </a>
        </div>
        {/foreach}
    </div>
  </div>
</div>