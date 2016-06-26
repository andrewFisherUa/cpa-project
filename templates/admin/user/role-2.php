<h3 class="page-title">
    Информация пользователя <strong>`<?php echo $info["login"]; ?>`</strong>
</h3>

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="/admin"><i class="fa fa-home"></i></a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="/admin/users">Пользователи</a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="#"><?php echo $info["login"]; ?></a>
        </li>
    </ul>
</div>

<div class="row">
	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat green">
            <div class="visual">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup"><?php echo $info['approved_orders_count'];?></span>
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
	                <span class="money"><?php echo $info["profit"]; ?></span>&nbsp;<?php echo $info["default_currency"]; ?></div>
	            <div class="desc">Прибыль</div>
	        </div>
	    </div>
	</div>
	<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat blue">
            <div class="visual">
                <i class="fa fa-globe"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span><?php echo $info['ref_count'];?></span></div>
                <div class="desc">Рефералы</div>
            </div>
        </div>
    </div>

    <?php if (User::isAdmin()) : ?>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
	    <div class="dashboard-stat purple">
	        <div class="visual">
	            <i class="fa fa-globe"></i>
	        </div>
	        <div class="details">
	            <div class="number">
	                <span class="money"><?php echo $info["payed"]; ?></span>&nbsp;<?php echo $info["default_currency"]; ?></div>
	            <div class="desc">Выплачено</div>
	        </div>
	    </div>
	</div>
	<?php endif; ?>
</div>


<div class="row">
	<div class="col-md-4">
		<div class="portlet light">
		  <div class="portlet-body">
		  	<div class="row static-info">
		  		<div class="col-sm-4 name">Логин:</div>
		  		<div class="col-sm-8 value"><?php echo $info["login"]; ?></div>
		  	</div>
		  	<div class="row static-info">
		  		<div class="col-sm-4 name">Email:</div>
		  		<div class="col-sm-8 value"><?php echo $info["email"]; ?></div>
		  	</div>
		  	<div class="row static-info">
		  		<div class="col-sm-4 name">Имя:</div>
		  		<div class="col-sm-8 value"><?php echo $info["first_name"]; ?></div>
		  	</div>
		  	<div class="row static-info">
		  		<div class="col-sm-4 name">Фамилия:</div>
		  		<div class="col-sm-8 value"><?php echo $info["last_name"]; ?></div>
		  	</div>
		  	<div class="row static-info">
		  		<div class="col-sm-4 name">Skype:</div>
		  		<div class="col-sm-8 value"><?php echo $info["skype"]; ?></div>
		  	</div>
		  	<div class="row static-info">
		  		<div class="col-sm-4 name">Телефон:</div>
		  		<div class="col-sm-8 value"><?php echo $info["phone"]; ?></div>
		  	</div>
		  	<div class="row static-info">
		  		<div class="col-sm-4 name">Валюта:</div>
		  		<div class="col-sm-8 value"><?php echo $info["default_currency"]; ?></div>
		  	</div>
		  	<div class="row static-info">
		  		<div class="col-sm-4 name">Регистрация:</div>
		  		<div class="col-sm-8 value"><?php echo $info["regdate"]; ?></div>
		  	</div>
		  </div>
		</div>
	</div>
	<div class="col-md-5">
		<div class="portlet light">
		  <div class="portlet-body">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th></th>
						<th class="text-center">Холд</th>
						<th class="text-center">Баланс</th>
						<th class="text-center">Реф. баланс</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($balance as $country_code=>$b) : ?>
						<tr>
							<td><span class="flag flag-<?php echo $country_code; ?>"></td>
							<td class="text-right"><span class="money"><?php echo $b->getHold(); ?></span> <?php echo $b->getCurrencyCode(); ?></td>
							<td class="text-right"><span class="money"><?php echo $b->getCurrent(); ?></span> <?php echo $b->getCurrencyCode(); ?></td>
							<td class="text-right"><span class="money"><?php echo $b->getReferal(); ?></span> <?php echo $b->getCurrencyCode(); ?></td>
						</tr>
					<?php endforeach; ?>
					<tr>
						<td class="bold">Всего:</td>
						<td class="text-right bold"><span class="money"><?php echo $main_balance->getHold();?></span> <?php echo $main_balance->getCurrencyCode(); ?></td>
						<td class="text-right bold"><span class="money"><?php echo $info['profit'];?></span> <?php echo $main_balance->getCurrencyCode(); ?></td>
						<td class="text-right bold"><span class="money"><?php echo $main_balance->getReferal();?></span> <?php echo $main_balance->getCurrencyCode(); ?></td>
					</tr>
				</tbody>
			</table>
		  </div>
		</div>
	</div>

	<div class="col-md-3">
		<div class="portlet light">
		  <div class="portlet-body">
			<?php echo $rates_widget; ?>
		  </div>
		</div>

	</div>
</div>

<input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

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
            <input type="text" class="form-control" name="from" value="<?php echo $stat_range['from']; ?>">
            <span class="input-group-addon"> to </span>
            <input type="text" class="form-control" name="to" value="<?php echo $stat_range['to']; ?>">
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

<div class="row">
  <div class="col-md-12">
    <!-- Begin: life time stats -->
    <div class="portlet light">
      <div class="portlet-title">
        <div class="caption">
          Общая статистика
        </div>
      </div>
      <div class="portlet-body">
        <div class="table-container" id="datatable_stats_wrapper">
          <div class="settings-wrap">
            <div class="filters-section">
            <ul>
              <li id="filter-offers" class="filter-item select2-container">
                <div class="popdown offers form-item">
                  <label class="toggler input">
                    <div class="preview">Офферы <span class="count"></span></div>
                  </label>
                  <div class="popover hide">
                    <div class="popover-content">
                      <select id="offers-filter" class="filter-select" multiple="multiple" name="offer">
                      	<?php foreach ($filters["offers"] as $a=>$b) : ?>
                          <option value="<?php echo $a;?>"><?php echo $b;?></option>
                        <?php endforeach; ?>
                      </select>
                      <div class="popdown-controls">
                        <div class="actions">
                          <button class="btn blue apply btn-sm">Применить</button>&nbsp;<button class="btn btn-sm btn-default clear">Очистить</button>
                        </div>
                        <div class="close-popup">
                          <span class="clickable close-popdown">Закрыть</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              <li id="filter-streams" class="filter-item select2-container">
                <div class="popdown streams form-item">
                  <label class="toggler input">
                    <div class="preview">Потоки <span class="count"></span></div>
                  </label>
                  <div class="popover hide">
                    <div class="popover-content">
                      <select id="streams-filter" class="filter-select" multiple="multiple" name="stream">
                      	<?php foreach ($filters["streams"] as $a=>$b) : ?>
                          <option value="<?php echo $a;?>"><?php echo $b;?></option>
                        <?php endforeach; ?>
                      </select>
                      <div class="popdown-controls">
                        <div class="actions">
                          <button class="btn blue apply btn-sm">Применить</button>&nbsp;<button class="btn btn-sm btn-default clear">Очистить</button>
                        </div>
                        <div class="close-popup">
                          <span class="clickable close-popdown">Закрыть</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              <li id="filter-landings" class="filter-item select2-container">
                <div class="popdown landings form-item">
                  <label class="toggler input">
                    <div class="preview">Лендинги <span class="count"></span></div>
                  </label>
                  <div class="popover hide">
                    <div class="popover-content">
                      <select id="landings-filter" class="filter-select" multiple="multiple" name="landing">
                      	<?php foreach ($filters["landings"] as $a=>$b) : ?>
                          <option value="<?php echo $b['landing']['id'];?>" data-offer="<?php echo $b['offer']['id'];?>"><?php echo $b["offer"]["name"];?> - <?php echo $b["landing"]["name"];?></option>
                        <?php endforeach; ?>
                      </select>
                      <div class="popdown-controls">
                        <div class="actions">
                          <button class="btn blue apply btn-sm">Применить</button>&nbsp;<button class="btn btn-sm btn-default clear">Очистить</button>
                        </div>
                        <div class="close-popup">
                          <span class="clickable close-popdown">Закрыть</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              <li id="filter-blogs" class="filter-item select2-container">
                <div class="popdown blogs form-item">
                  <label class="toggler input">
                    <div class="preview">Блоги <span class="count"></span></div>
                  </label>
                  <div class="popover hide">
                    <div class="popover-content">
                      <select id="blogs-filter" class="filter-select" multiple="multiple" name="blog">
                      	<?php foreach ($filters["blogs"] as $a=>$b) : ?>
                          <option value="<?php echo $b['blog']['id'];?>" data-offer="<?php echo $b['offer']['id'];?>"><?php echo $b["offer"]["name"];?> - <?php echo $b["blog"]["name"];?></option>
                        <?php endforeach; ?>
                      </select>
                      <div class="popdown-controls">
                        <div class="actions">
                          <button class="btn blue apply btn-sm">Применить</button>&nbsp;<button class="btn btn-sm btn-default clear">Очистить</button>
                        </div>
                        <div class="close-popup">
                          <span class="clickable close-popdown">Закрыть</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              <li id="filter-spaces" class="filter-item select2-container">
                <div class="popdown spaces form-item">
                  <label class="toggler input">
                    <div class="preview">Источники трафика <span class="count"></span></div>
                  </label>
                  <div class="popover hide">
                    <div class="popover-content">
                      <div class="popdown-controls">
                        <select id="spaces-filter" class="filter-select" multiple="multiple" name="source">
	                        <?php foreach ($filters["spaces"] as $a=>$b) : ?>
	                          <option value="<?php echo $a;?>"><?php echo $b;?></option>
	                        <?php endforeach; ?>
                        </select>
                        <div class="actions">
                          <button class="btn blue apply btn-sm">Применить</button>&nbsp;<button class="btn btn-sm btn-default clear">Очистить</button>
                        </div>
                        <div class="close-popup">
                          <span class="clickable close-popdown">Закрыть</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              <li id="filter-subid" class="filter-item select2-container">
                <div class="popdown subid form-item">
                  <label class="toggler input">
                    <div class="preview">Subid <span class="count"></span></div>
                  </label>
                  <div class="popover hide">
                    <div class="popover-content">
                      <div class="form form-horizontal">
                      	<?php foreach ($filter["subid"] as $a=>$b) : ?>
                        <div class="form-group">
                          <label for="" class="control-label col-sm-4"><?php echo $a;?></label>
                          <div class="col-sm-8">
                            <select id="spaces-filter" name="<?php echo $a;?>" class="filter-select subid" multiple="multiple">
                            </select>
                          </div>
                        </div>
                        <?php endforeach; ?>
                      </div>
                      <div class="popdown-controls">
                        <div class="actions">
                          <button class="btn blue apply btn-sm">Применить</button>&nbsp;<button class="btn btn-sm btn-default clear">Очистить</button>
                        </div>
                        <div class="close-popup">
                          <span class="clickable close-popdown">Закрыть</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>

          <div class="filters-section">
            <ul class="grouping-list">
              <li class="grouping-item group active"><button class="btn green-meadow date" data-group-by="date" data-text="Дата">Дата</button></li>
              <li class="grouping-item group "><button class="btn default offer_id" data-group-by="offer" data-text="Оффер">Оффер</button></li>
              <li class="grouping-item group "><button class="btn default stream_id" data-group-by="stream" data-text="Поток">Поток</button></li>
              <li class="grouping-item group "><button class="btn default space_id" data-group-by="source" data-text="Источники">Источники</button></li>
              <li class="grouping-item group "><button class="btn default sid1" data-group-by="subid1" data-text="SUB 1">SUB 1</button></li>
              <li class="grouping-item group "><button class="btn default sid2" data-group-by="subid2" data-text="SUB 2">SUB 2</button></li>
              <li class="grouping-item group "><button class="btn default sid3" data-group-by="subid3" data-text="SUB 3">SUB 3</button></li>
              <li class="grouping-item group "><button class="btn default sid4" data-group-by="subid4" data-text="SUB 4">SUB 4</button></li>
              <li class="grouping-item group "><button class="btn default sid5" data-group-by="subid5" data-text="SUB 5">SUB 5</button></li>
              <li class="grouping-item group "><button class="btn default" data-group-by="landing" data-text="Лендинг">Лендинг</button></li>
              <li class="grouping-item group "><button class="btn default" data-group-by="blog" data-text="Блог">Блог</button></li>
              <li><button class="btn green-meadow" id="filter-submit">Показать статистику</button></li>
              <li><button class="btn red" id="filter-reset">Сбросить</button></li>
          </div>

          </div>

          <div class="input-group input-large date-picker input-daterange pull-left" data-date="10-11-2012" data-date-format="mm-dd-yyyy">
            <span class="input-group-btn">
              <button class="btn default" type="button">
                <i class="fa fa-calendar"></i>
              </button>
            </span>
            <input type="text" class="form-control" name="from" value="<?php echo date("d-m-Y");?>">

            <span class="input-group-addon"> - </span>
            <input type="text" class="form-control" name="to" value="<?php echo date("d-m-Y");?>">
          </div>

          <div class="date-range-section">
            <ul class="fixed-range">
              <li class="today fixed-range-item">
                <button class="btn default" data-shortcut="today" class="date-shortcut">За сегодня</button>
              </li>
              <li class="yesterday fixed-range-item">
                <button class="btn default" data-shortcut="yesterday" class="date-shortcut">За вчера</button>
              </li>
              <li class="this-week fixed-range-item">
                <button class="btn default" data-shortcut="week" class="date-shortcut">За неделю</button>
              </li>
              <li class="this-month fixed-range-item">
                <button class="btn default" data-shortcut="month" class="date-shortcut">За месяц</button>
              </li>
            </ul>
          </div>

          <div class="clearfix"></div>

          <table class="table table-striped table-bordered table-hover" id="datatable_stats" data-user="<?php echo $user_id;?>">
          <thead>
          <tr role="row" class="heading">
            <th rowspan="2" class="text-center sorting-asc" data-sort-by="name" width="170">Дата</th>
            <th colspan="2" class="text-center">Трафик</th>
            <th colspan="5" class="text-center">Конверсии</th>
            <th colspan="3" class="text-center">Коэффициенты</th>
            <th colspan="3" class="text-center">Доход</th>
          </tr>
          <tr>
            <th class="sorting" data-sort-by="all">Хиты</th>
            <th class="sorting" data-sort-by="unique">Хосты</th>
            <th><i class="fa fa-check"></i></th>
            <th><i class="fa fa-clock-o"></i></th>
            <th><i class="fa fa-close"></i></th>
            <th>Всего</th>
            <th>Фейк</th>
            <th>EPC</th>
            <th>CR %</th>
            <th>Ap. %</th>
            <th><i class="fa fa-check"></i></th>
            <th><i class="fa fa-clock-o"></i></th>
            <th><i class="fa fa-close"></i></th>
          </tr>
          <tbody>
          </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- End: life time stats -->
  </div>
</div>