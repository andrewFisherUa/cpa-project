<h3 class="page-title">Настройка курса валют</h3>
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
          <a href="#">Настройка курса валют</a>
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
    <div class="col-md-6">
        <div class="portlet light">
          <div class="portlet-title">
            <div class="caption">
              Изменить значения
            </div>
          </div>
          <div class="portlet-body">
            <form action="/admin/exchange_rates" method="post" id="add_rate">
                <div class="alert alert-danger" style="display:none;"></div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <select id="from_currency" class="form-control" readonly>
                                <option value="USD" selected>USD</option>
                            </select>
                            <div class="direction" style="position:absolute;right:-22px;top:4px;font-size:20px;">
                              <i class="fa fa-angle-double-right"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <select id="to_currency" class="form-control">
                                <option value="-1">Выбор валюты</option>
                                {foreach from=$currencies item=v}
                                    <option value="{$v}">{$v}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" id="bid" value="" class="form-control bid-field numbers-only" placeholder="Bid">
                            <!--<p class="help-block">Значение по умолчанию: </p>-->
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" id="ask" value="" class="form-control ask-field numbers-only" placeholder="Ask">
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group date datetime">
                                <input id="start" type="text" size="16" readonly="" class="form-control" placeholder="Начало действия">
                                <span class="input-group-btn">
                                    <button class="btn default date-set" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group date datetime">
                                <input id="end" type="text" size="16" readonly="" class="form-control" placeholder="Конец действия">
                                <span class="input-group-btn">
                                    <button class="btn default date-set" type="button">
                                        <i class="fa fa-calendar"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button name="submit" class="btn red pull-right">Применить</button>
                </div>
                <div class="clearfix"></div>
            </form>
          </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
        <div class="portlet-title">
            <div class="actions">
                <div class="btn-group">
                    <button class="btn btn-circle blue btn-approve">Одобрить</button>
                </div>
                <div class="btn-group">
                    <button class="btn btn-circle red btn-cancel">Отклонить</button>
                </div>
            </div>
        </div>
          <div class="portlet-body">
            <div class="table-container">
                <div class="table-actions-wrapper">
                    <div class="form-inline">
                        <div class="form-group">
                            <select name="status" class="form-control tbl-filter">
                                <option value="-1">Select status</option>
                                <option value="moderation">moderation</option>
                                <option value="active">active</option>
                                <option value="waiting">waiting</option>
                                <option value="canceled">canceled</option>
                                <option value="archive">archive</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="from" class="form-control tbl-filter">
                                <option value="-1">From currency</option>
                                <option value="USD">USD</option>
                                {foreach from=$currencies item=v}
                                    <option value="{$v}">{$v}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="to" class="form-control tbl-filter">
                                <option value="-1">To currency</option>
                                <option value="USD">USD</option>
                                {foreach from=$currencies item=v}
                                    <option value="{$v}">{$v}</option>
                                {/foreach}
                            </select>
                        </div>
                        <button class="btn red reset-filters"><i class="fa fa-times"></i> Отмена</button>
                    </div>
                  </div>
                  <table class="table table-bordered table-condensed" id="datatable_rates">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>From/To</th>
                            <th>Bid</th>
                            <th>Ask</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>State</th>
                            <th>User</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
          </div>
        </div>
    </div>
</div>