<h3 class="page-title">
    Главная страница
</h3>

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="/admin"><i class="fa fa-home"></i> Главная</a>
        </li>
    </ul>
</div>

<div class="row" style="margin-bottom: 20px;">
    <div class="col-md-12 text-center">
        <img src="/misc/images/images/summer-banner.jpg" alt="" style="width: 100%">
    </div>
</div>

<div class="row">
  <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="home-exchange-widget">
    <?php echo Converter::getWidget(); ?>
    </div>
  </div>
  <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat green">
            <div class="visual">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span><?php echo $template_data["confirmed_orders_count"]; ?></span>
                </div>
                <div class="desc">Подтвержденные заказы</div>
            </div>
        </div>
  </div>
  <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="dashboard-stat red">
      <div class="visual">
        <i class="fa fa-bar-chart-o"></i>
      </div>
      <div class="details">
        <div class="number">
          <span class="money" maxlength="17" autocomplete="off"><?php echo $template_data["total_balance"]["amount"]; ?></span>&nbsp;<?php echo $template_data["total_balance"]["currency"]; ?></div>
          <div class="desc">Прибыль партнеров</div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="dashboard-stat purple">
      <div class="visual">
        <i class="fa fa-money"></i>
      </div>
      <div class="details">
        <div class="number">
          <span class="money" maxlength="17" autocomplete="off"><?php echo $template_data["approximate_payments"]["amount"]; ?></span>&nbsp;<?php echo $template_data["approximate_payments"]["currency"]; ?></div>
          <div class="desc">&asymp; Сумма выплат</div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="portlet light">
      <div class="portlet-title">
        <div class="caption">Конверсии</div>
        <div class="actions">
          <div class="input-group input-large date-picker input-daterange pull-left" data-date-format="dd-mm-yyyy">
            <span class="input-group-btn">
              <button class="btn default" type="button">
                <i class="fa fa-calendar"></i>
              </button>
            </span>
            <input type="text" class="form-control" name="from" value="<?php echo $template_data['stat_range']['from'];?>">
            <span class="input-group-addon"> to </span>
            <input type="text" class="form-control" name="to" value="<?php echo $template_data['stat_range']['to'];?>">
          </div>
        </div>
      </div>
      <div class="portlet-body">
        <div id="conversion_chart" class="chart"></div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="portlet light">
      <div class="portlet-title">
        <div class="caption">Трафик</div>
      </div>
      <div class="portlet-body">
        <div id="traffic_chart" class="chart"></div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="portlet light">
      <div class="portlet-title">
        <div class="caption">Approve, %</div>
      </div>
      <div class="portlet-body">
        <div id="approve_chart" class="chart"></div>
      </div>
    </div>
  </div>
</div>