<h3 class="page-title">Тест курса валют</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Взаиморасчеты</a>
		  <i class="fa fa-angle-right"></i>
        </li>
		<li>
          <a href="#">Тест курса валют</a>
        </li>
    </ul>
</div>


<div class="row">
	<div class="col-md-6">
		<div class="portlet light">
		  <div class="portlet-title">
			<div class="caption">
			  Курс валют
			</div>
		  </div>
		  <div class="portlet-body">
            <div class="row">
                <div class="col-md-6">
                    <div id="exchange-widget-default">
                        {$default_exchange_widget}
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="exchange-widget-usd">
                        {$widget.usd}
                    </div>
                </div>
            </div>
		  </div>
		</div>
	</div>
    <div class="col-md-6">
        <div class="portlet light">
          <div class="portlet-body">
            <div class="row">
                {foreach from=$widget key=c item=w}
                    {if $c != "usd"}
                    <div class="col-md-6">
                        <div id="exchange-widget-{$c}">
                            {$w}
                        </div>
                    </div>
                    {/if}
                {/foreach}
            </div>
          </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
          <div class="portlet-body">
            {$t}
          </div>
        </div>
    </div>
</div>