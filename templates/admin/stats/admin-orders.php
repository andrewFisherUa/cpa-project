<h2 class="page-title">Статистика</h2>

<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="/admin">Главная</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">Статистика по лидам</a>
    </li>
  </ul>
</div>

<!-- BEGIN PAGE CONTENT-->
<div class="row">
  <div class="col-md-12">
    <!-- Begin: life time stats -->
    <div class="portlet light">
      <div class="portlet-title">
        <div class="caption">
          Статистика по лидам
        </div>
        <div class="actions">
          <a href="/admin/admin_statistics" class="btn-circle btn green-meadow">Общая статистика</a>
        </div>
      </div>
      <div class="portlet-body">
        <div class="table-container" id="datatable_stats_wrapper" style="marrgin-bottom: 15px;">
          <div class="settings-wrap">
            <div class="filters-section">
            <ul>
              <?php if ($isAdmin) : ?>
              <li id="filter-webmasters" class="filter-item select2-container">
                <div class="popdown offers form-item">
                  <label class="toggler input">
                    <div class="preview">Вебмастера <span class="count"></span></div>
                  </label>
                  <div class="popover hide">
                    <div class="popover-content">
                      <select id="webmasters-filter" class="filter-select" multiple="multiple" name="webmaster">
                        <?php foreach ($users as $user) { ?>
                          <option value="<?php echo $user['user_id'];?>"><?php echo $user['user_id'];?>: <?php echo $user['login'];?></option>
                        <?php } ?>
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
              <?php endif; ?>
              <li id="filter-offers" class="filter-item select2-container">
                <div class="popdown offers form-item">
                  <label class="toggler input">
                    <div class="preview">Офферы <span class="count"></span></div>
                  </label>
                  <div class="popover hide">
                    <div class="popover-content">
                      <select id="offers-filter" class="filter-select" multiple="multiple" name="offer">
                        <?php foreach ($filters["offers"] as $a=>$b) { ?>
                          <option value="<?php echo $a;?>"><?php echo $b;?></option>
                        <?php } ?>
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
                        <?php foreach ($filters["streams"] as $a=>$b) { ?>
                          <option value="<?php echo $a;?>"><?php echo $b;?></option>
                        <?php } ?>
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
                        <?php foreach ($filters["landings"] as $a=>$b) { ?>
                          <option value="<?php echo $b['landing_id'];?>" data-offer="<?php echo $b['offer_id'];?>">
                            <?php echo $b['offer_name'];?> - <?php echo $b['landing_name'];?>
                          </option>
                        <?php } ?>
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
                        <?php foreach ($filters["blogs"] as $a=>$b) { ?>
                          <option value="<?php echo $b['blog_id'];?>" data-offer="<?php echo $b['offer_id'];?>">
                            <?php echo $b['offer_name'];?> - <?php echo $b['blog_name'];?>
                          </option>
                        <?php } ?>
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
                          <?php foreach ($filters["sources"] as $a=>$b) { ?>
                            <option value="<?php echo $a;?>"><?php echo $b;?></option>
                          <?php } ?>
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
            </ul>

            <button class="btn green-meadow" id="filter-submit">Показать статистику</button>
            <button class="btn red" id="filter-reset">Сбросить</button>

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

          <table class="table table-striped table-bordered table-hover" id="datatable_order_stat">
          <thead>
          <tr role="row" class="heading">
            <th width="5%">ID</th>
            <?php if ($isAdmin) : ?>
            <th width="5%">OID</th>
            <?php endif;?>
            <th width="10%">Дата заказа</th>
            <th width="15%">Оффер</th>
            <th width="7%">Статус</th>
            <th width="15%">Гео/IP</th>
            <th width="25%">Данные заказа</th>
            <th width="13%">Доп.статус</th>
            <th width="10%">Доход</th>
          </tr>
           <tr role="row" class="filter">
            <td></td>
            <?php if ($isAdmin) : ?>
              <td></td>
            <?php endif;?>
            <td></td>
            <td></td>
            <td>
              <select id="status-filter" class="filter form-control" name="status">
                <option value="-1">Выбор</option>
                <?php foreach ($filters['status'] as $a=>$b) { ?>
                  <option value="<?php echo $a;?>"><?php echo $b;?></option>
                <?php } ?>
              </select>
            </td>
            <td></td>
            <td></td>
            <td>
              <select id="status2-filter" class="filter form-control" name="status2">
                <option value="-1">Выбор</option>
                <?php foreach ($filters['status2'] as $b) { ?>
                  <option value="<?php echo $b;?>"><?php echo $b;?></option>
                <?php } ?>
              </select>
            </td>
            <td>
            </td>
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
<!-- END PAGE CONTENT-->