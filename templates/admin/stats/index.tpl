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

<!-- BEGIN PAGE CONTENT-->
<div class="row">
  <div class="col-md-12">
    <!-- Begin: life time stats -->
    <div class="portlet light">
      <div class="portlet-title">
        <div class="caption">
          Статистика
        </div>
        <div class="actions">
          <a href="/admin/statistics/orders" class="btn-circle btn green-meadow">Статистика по лидам</a>
        </div>
      </div>
      <div class="portlet-body">
        {if $message}
        <div class="note note-danger">
          {$message}
        </div>
        {/if}
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

          <table class="table table-striped table-bordered table-hover" id="datatable_stats">
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
            {foreach from=$stats item=v}
            <tr>
              <td>{$today}</td>
              <td>{$v.all}</td>
              <td>{$v.unique}</td>
              <td>{$v.count.approved}</td>
              <td>{$v.count.waiting}</td>
              <td>{$v.count.canceled}</td>
              <td>{$v.count.all}</td>
              <td>{$v.count.trash}</td>
              <td>{$v.k.epc}</td>
              <td>{$v.k.crs}</td>
              <td>{$v.k.approve}</td>
              <td class='approved'>{$v.amount.approved}</td>
              <td>{$v.amount.waiting}</td>
              <td class='canceled'>{$v.amount.canceled}</td>
            </tr>
            <tr>
              <td>Всего</td>
              <td>{$v.all}</td>
              <td>{$v.unique}</td>
              <td>{$v.count.approved}</td>
              <td>{$v.count.waiting}</td>
              <td>{$v.count.canceled}</td>
              <td>{$v.count.all}</td>
              <td>{$v.count.trash}</td>
              <td>{$v.k.epc}</td>
              <td>{$v.k.crs}</td>
              <td>{$v.k.approve}</td>
              <td class='approved'>{$v.amount.approved}</td>
              <td>{$v.amount.waiting}</td>
              <td class='canceled'>{$v.amount.canceled}</td>
            </tr>
            {/foreach}
          </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- End: life time stats -->
  </div>
</div>
<!-- END PAGE CONTENT-->