<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Настройка целей</h3>
<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="/admin">Главная</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="/admin/offers/">Офферы</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">Настройка целей</a>
    </li>
  </ul>
</div>
<!-- END PAGE HEADER-->

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      Настройка целей (по товарам)
    </div>
    <div class="actions">
      <a href="javascript:;" class="btn btn-circle blue" id="save" data-filter="offer">Сохранить</a>
    </div>
  </div>
  <div class="portlet-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-horizontal">
          <div class="form-group">
            <label class="col-md-4" style="line-height:35px;">
              <input type="checkbox" value="1" class="check-all" data-target="scroller-1">Выбрать все
            </label>
            <div class="col-md-8">
              <select class="select2me form-control search" data-target="scroller-1">
                {foreach from=$offers item=item}
                  <option value="{$item.id}">{$item.id}: {$item.name}</option>
                {/foreach}
              </select>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="form-control height-auto">
            <div class="scroller" style="height:275px;" data-always-visible="1" id="scroller-1">
              <ul class="list-unstyled">
                  {foreach from=$offers item=item}
                  <li>
                      <label>
                          <input type="checkbox" value="{$item.id}" class="filter" data-filter="offer">{$item.id}: {$item.name}
                      </label>
                  </li>
                  {/foreach}
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-horizontal">
          <div class="form-group">
            <label class="col-md-4" style="line-height:35px;">
              <input type="checkbox" value="1" class="check-all" data-target="scroller-2">Выбрать все
            </label>
            <div class="col-md-8">
              <select class="select2me form-control search" data-target="scroller-3">
                {foreach from=$users item=item}
                  <option value="{$item.id}">{$item.id}: {$item.login}</option>
                {/foreach}
              </select>
            </div>
          </div>
        </div>
        <div class="form-control height-auto">
          <div class="scroller" style="height:275px;" data-always-visible="1" id="scroller-2">
            <ul class="list-unstyled" data-filter="offer">
                {foreach from=$users item=item}
                <li>
                    <label>
                        <input type="checkbox" value="{$item.id}" class="result" data-filter="offer">{$item.id}: {$item.login}
                    </label>
                </li>
                {/foreach}
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      Настройка целей (по вебмастерам)
    </div>
    <div class="actions">
      <a href="javascript:;" class="btn btn-circle blue" id="save" data-filter="webmaster">Сохранить</a>
    </div>
  </div>
  <div class="portlet-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-horizontal">
          <div class="form-group">
            <label class="col-md-4" style="line-height:35px;">
              <input type="checkbox" value="1" class="check-all" data-target="scroller-3">Выбрать все
            </label>
            <div class="col-md-8">
              <select class="select2me form-control search" data-target="scroller-3">
                {foreach from=$users item=item}
                  <option value="{$item.id}">{$item.id}: {$item.login}</option>
                {/foreach}
              </select>
            </div>
          </div>
        </div>

        <div class="form-control height-auto">
          <div class="scroller" style="height:275px;" data-always-visible="1" id="scroller-3">
            <ul class="list-unstyled" data-filter="offer">
                {foreach from=$users item=item}
                <li>
                    <label>
                        <input type="checkbox" value="{$item.id}" class="filter" data-filter="webmaster">{$item.id}: {$item.login}
                    </label>
                </li>
                {/foreach}
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-horizontal">
          <div class="form-group">
            <label class="col-md-4" style="line-height:35px;">
              <input type="checkbox" value="1" class="check-all" data-target="scroller-4">Выбрать все
            </label>
            <div class="col-md-8">
              <select class="select2me form-control search" data-target="scroller-4">
                {foreach from=$offers item=item}
                  <option value="{$item.id}">{$item.id}: {$item.name}</option>
                {/foreach}
              </select>
            </div>
          </div>
        </div>
        <div class="form-control height-auto">
          <div class="scroller" style="height:275px;" data-always-visible="1" id="scroller-4">
            <ul class="list-unstyled">
                {foreach from=$offers item=item}
                <li>
                    <label>
                        <input type="checkbox" value="{$item.id}" class="result" data-filter="webmaster">{$item.id}: {$item.name}
                    </label>
                </li>
                {/foreach}
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>