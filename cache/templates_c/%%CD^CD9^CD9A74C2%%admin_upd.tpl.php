<?php /* Smarty version 2.6.22, created on 2016-06-14 16:24:54
         compiled from admin/stats/admin_upd.tpl */ ?>
<h2 class="page-title">Статистика</h2>

<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="/admin">Главная</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">Общая статистика</a>
    </li>
  </ul>
</div>

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      Статистика по вебмастерам
    </div>
    <div class="actions"></div>
  </div>
  <div class="portlet-body">
    <div class="row">
      <div class="col-md-6">
        <select id="choose-webmaster" class="form-control">
          <?php $_from = $this->_tpl_vars['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['user']):
?>
            <option value="<?php echo $this->_tpl_vars['user']['user_id']; ?>
"><?php echo $this->_tpl_vars['user']['user_id']; ?>
: <?php echo $this->_tpl_vars['user']['login']; ?>
</option>
          <?php endforeach; endif; unset($_from); ?>
        </select>
      </div>
      <div class="col-md-6">
        <a href="/admin/admin_statistics/webmaster/" id="show-webmaster-stat" class="btn red" disabled>Показать статистику</a>
      </div>
    </div>
  </div>
</div>

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      Общая статистика
    </div>
    <div class="actions">
      <a href="/admin/admin_statistics/orders" class="btn-circle btn green-meadow">Статистика по лидам</a>
    </div>
  </div>
  <div class="portlet-body">
    <div class="table-container" id="datatable_stats_wrapper">
      <div class="settings-wrap">
        <div class="filters-section">
        <ul>
          <li id="filter-webmasters" class="filter-item select2-container">
            <div class="popdown offers form-item">
              <label class="toggler input">
                <div class="preview">Вебмастера <span class="count"></span></div>
              </label>
              <div class="popover hide">
                <div class="popover-content">
                  <select id="webmasters-filter" class="filter-select" multiple="multiple" name="webmaster">
                    <?php $_from = $this->_tpl_vars['users']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['user']):
?>
                      <option value="<?php echo $this->_tpl_vars['user']['user_id']; ?>
"><?php echo $this->_tpl_vars['user']['user_id']; ?>
: <?php echo $this->_tpl_vars['user']['login']; ?>
</option>
                    <?php endforeach; endif; unset($_from); ?>
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

          <li id="filter-offers" class="filter-item select2-container">
            <div class="popdown offers form-item">
              <label class="toggler input">
                <div class="preview">Офферы <span class="count"></span></div>
              </label>
              <div class="popover hide">
                <div class="popover-content">
                  <select id="offers-filter" class="filter-select" multiple="multiple" name="offer">
                    <?php $_from = $this->_tpl_vars['filters']['offers']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['a'] => $this->_tpl_vars['b']):
?>
                      <option value="<?php echo $this->_tpl_vars['a']; ?>
"><?php echo $this->_tpl_vars['b']; ?>
</option>
                    <?php endforeach; endif; unset($_from); ?>
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
                    <?php $_from = $this->_tpl_vars['filters']['streams']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['a'] => $this->_tpl_vars['b']):
?>
                      <option value="<?php echo $this->_tpl_vars['a']; ?>
"><?php echo $this->_tpl_vars['b']; ?>
</option>
                    <?php endforeach; endif; unset($_from); ?>
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
                    <?php $_from = $this->_tpl_vars['filters']['landings']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['a'] => $this->_tpl_vars['b']):
?>
                      <option value="<?php echo $this->_tpl_vars['b']['landing']['id']; ?>
" data-offer="<?php echo $this->_tpl_vars['b']['offer']['id']; ?>
"><?php echo $this->_tpl_vars['b']['offer']['name']; ?>
 - <?php echo $this->_tpl_vars['b']['landing']['name']; ?>
</option>
                    <?php endforeach; endif; unset($_from); ?>
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
                    <?php $_from = $this->_tpl_vars['filters']['blogs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['a'] => $this->_tpl_vars['b']):
?>
                      <option value="<?php echo $this->_tpl_vars['b']['blog']['id']; ?>
" data-offer="<?php echo $this->_tpl_vars['b']['offer']['id']; ?>
"><?php echo $this->_tpl_vars['b']['offer']['name']; ?>
 - <?php echo $this->_tpl_vars['b']['blog']['name']; ?>
</option>
                    <?php endforeach; endif; unset($_from); ?>
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
                      <?php $_from = $this->_tpl_vars['filters']['spaces']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['a'] => $this->_tpl_vars['b']):
?>
                        <option value="<?php echo $this->_tpl_vars['a']; ?>
"><?php echo $this->_tpl_vars['b']; ?>
</option>
                      <?php endforeach; endif; unset($_from); ?>
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
                    <?php $_from = $this->_tpl_vars['filters']['subid']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['a'] => $this->_tpl_vars['b']):
?>
                    <div class="form-group">
                      <label for="" class="control-label col-sm-4"><?php echo $this->_tpl_vars['a']; ?>
</label>
                      <div class="col-sm-8">
                        <select id="spaces-filter" name="<?php echo $this->_tpl_vars['a']; ?>
" class="filter-select subid" multiple="multiple">
                        </select>
                      </div>
                    </div>
                    <?php endforeach; endif; unset($_from); ?>
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
          <li id="filter-countries" class="filter-item select2-container">
            <div class="popdown countries form-item">
              <label class="toggler input">
                <div class="preview">Страна <span class="count"></span></div>
              </label>
              <div class="popover hide">
                <div class="popover-content">
                  <div class="popdown-controls">
                    <select id="countries-filter" class="filter-select" multiple="multiple" name="country">
                      <?php $_from = $this->_tpl_vars['filters']['countries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['a'] => $this->_tpl_vars['b']):
?>
                        <option value="<?php echo $this->_tpl_vars['a']; ?>
"><?php echo $this->_tpl_vars['b']; ?>
</option>
                      <?php endforeach; endif; unset($_from); ?>
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
      </div>

      <div class="filters-section">
        <ul class="grouping-list">
          <li class="grouping-item group active"><button class="btn date green-meadow" data-group-by="date" data-text="Дата">Дата</button></li>
          <li class="grouping-item group"><button class="btn webmaster" data-group-by="webmaster" data-text="Вебмастер">Вебмастер</button></li>
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
          <li class="grouping-item group "><button class="btn default" data-group-by="country" data-text="Страна">Страна</button></li>
          <li class="grouping-item group "><button class="btn default" data-group-by="api-stream" data-text="API поток">API поток</button></li>
          <li class="grouping-item group "><button class="btn default" data-group-by="referer" data-text="Referer">Referer</button></li>
          <li><button class="btn green-meadow" id="filter-submit">Показать статистику</button></li>
          <li><button class="btn red" id="filter-reset">Сбросить</button></li>
        </ul>
      </div>

      <div class="filters-section">
        <ul class="grouping-list">
          <li>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="order_type" value="stream" checked> Заказы с потоков
              </label>
            </div>
          </li>
          <li>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="order_type" value="api"> API заказы
              </label>
            </div>
          </li>
        </ul>
      </div>
    </div>

      <div class="input-group input-large date-picker input-daterange pull-left" data-date="10-11-2012" data-date-format="mm-dd-yyyy">
        <span class="input-group-btn">
          <button class="btn default" type="button">
            <i class="fa fa-calendar"></i>
          </button>
        </span>
        <input type="text" class="form-control" name="from" value="<?php echo $this->_tpl_vars['today']; ?>
">

        <span class="input-group-addon"> - </span>
        <input type="text" class="form-control" name="to" value="<?php echo $this->_tpl_vars['today']; ?>
">
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

      <table class="table table-striped table-bordered table-hover" id="datatable_stats">
      <thead>
      <tr role="row" class="heading">
        <th rowspan="2" class="text-center sorting-asc" data-sort-by="name" width="180">Дата</th>
        <th colspan="2" class="text-center">Трафик</th>
        <th colspan="7" class="text-center">Конверсии</th>
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
        <th>забр.</th>
        <th>% забр.</th>
        <th>EPC</th>
        <th>CRS %</th>
        <th>Ap. %</th>
        <th><i class="fa fa-check"></i></th>
        <th><i class="fa fa-clock-o"></i></th>
        <th><i class="fa fa-close"></i></th>
      </tr>
      <tbody></tbody>
      </table>
    </div>
  </div>
</div>