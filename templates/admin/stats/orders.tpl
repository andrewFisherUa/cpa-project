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
          <a href="/admin/statistics/" class="btn-circle btn green-meadow">Общая статистика</a>
        </div>
      </div>
      <div class="portlet-body">
        <div class="table-container" id="datatable_stats_wrapper" style="marrgin-bottom: 15px;">
          <div class="settings-wrap">
            <div class="filters-section">
            <ul>
              {if $admin}
              <li id="filter-webmasters" class="filter-item select2-container">
                <div class="popdown offers form-item">
                  <label class="toggler input">
                    <div class="preview">Вебмастера <span class="count"></span></div>
                  </label>
                  <div class="popover hide">
                    <div class="popover-content">
                      <select id="webmasters-filter" class="filter-select" multiple="multiple" name="webmaster">
                        {foreach from=$users item=user}
                          <option value="{$user.user_id}">{$user.user_id}: {$user.login}</option>
                        {/foreach}
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
              {/if}
              <li id="filter-offers" class="filter-item select2-container">
                <div class="popdown offers form-item">
                  <label class="toggler input">
                    <div class="preview">Офферы <span class="count"></span></div>
                  </label>
                  <div class="popover hide">
                    <div class="popover-content">
                      <select id="offers-filter" class="filter-select" multiple="multiple" name="offer">
                        {foreach from=$filters.offers key=a item=b}
                          <option value="{$a}">{$b}</option>
                        {/foreach}
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
                        {foreach from=$filters.streams key=a item=b}
                          <option value="{$a}">{$b}</option>
                        {/foreach}
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
                        {foreach from=$filters.landings key=a item=b}
                          <option value="{$b.landing.id}" data-offer="{$b.offer.id}">{$b.offer.name} - {$b.landing.name}</option>
                        {/foreach}
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
                        {foreach from=$filters.blogs key=a item=b}
                          <option value="{$b.blog.id}" data-offer="{$b.offer.id}">{$b.offer.name} - {$b.blog.name}</option>
                        {/foreach}
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
                          {foreach from=$filters.spaces key=a item=b}
                            <option value="{$a}">{$b}</option>
                          {/foreach}
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
                        {foreach from=$filters.subid key=a item=b}
                        <div class="form-group">
                          <label for="" class="control-label col-sm-4">{$a}</label>
                          <div class="col-sm-8">
                            <select id="spaces-filter" name="{$a}" class="filter-select subid" multiple="multiple">
                              {foreach from=$b item=c}
                                <option value="{$c}">{$c}</option>
                              {/foreach}
                            </select>
                          </div>
                        </div>
                        {/foreach}
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
            <input type="text" class="form-control" name="from" value="{$today}">

            <span class="input-group-addon"> - </span>
            <input type="text" class="form-control" name="to" value="{$today}">
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
            <td></td>
            <td></td>
            <td>
              <select id="status-filter" class="filter form-control" name="status">
                <option value="-1">Выбор</option>
                {foreach from=$filters.status key=a item=b}
                  <option value="{$a}">{$b}</option>
                {/foreach}
              </select>
            </td>
            <td></td>
            <td></td>
            <td>
              <select id="status2-filter" class="filter form-control" name="status2">
                <option value="-1">Выбор</option>
                {foreach from=$filters.status2 item=b}
                  <option value="{$b}">{$b}</option>
                {/foreach}
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