<div id="page-referals">
  <h2 class="page-title">Ваши рефералы</h2>
  <div class="page-bar">
    <ul class="page-breadcrumb">
      <li>
        <a href="/admin"><i class="fa fa-home"></i></a>
        <i class="fa fa-angle-right"></i>
      </li>
      <li>
        <a href="#">Ваши рефералы</a>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat2 ">
            <div class="display">
                <div class="number">
                    <h3 class="font-green-sharp">
                        <span data-counter="counterup">{$referal_profit.total}</span>
                        <small class="font-green-sharp">{$currency}</small>
                    </h3>
                    <small>Реф. баланс</small>
                </div>
                <div class="icon">
                    <i class="icon-pie-chart"></i>
                </div>
            </div>
        </div>

        <div class="dashboard-stat2 ">
            <div class="display">
                <div class="number">
                    <h3 class="font-purple-soft">
                        <span data-counter="counterup">{$referal_count}</span>
                    </h3>
                    <small>Количество рефералов</small>
                </div>
                <div class="icon">
                    <i class="icon-user"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
      <div class="portlet light">
        <div class="portlet-body">
          <table class="table" id="ref-balance">
            <thead>
              <tr class="heading">
                <th>Реферальный баланс</th>
              </tr>
            </thead>
            <tbody>
              {foreach from=$referal_profit.country item=item key=key}
                <tr>
                  <td><span class="flag flag-{$key}"></span>&nbsp;{$item}</td>
                </tr>
              {/foreach}
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
      <div class="portlet light">
        <div class="portlet-body">
          <div class="form-group">
            <label class="control-label">
              Реферальная ссылка на регистрацию:
            </label>
            <div class="input-group">
              <input type="text" class="form-control" id="referal-link1" value="{$ref_links.registration}" disabled="">
              <span class="input-group-btn">
                <button class="btn green" type="button" id="referal-link1-btn"><i class="fa fa-copy"></i></button>
             </span>
            </div>
          </div>

          <div class="form-group">
            <label class="control-label">
              Реферальная ссылка на главную:
            </label>
            <div class="input-group">
              <input type="text" class="form-control" id="referal-link2" value="{$ref_links.home}" disabled="">
              <span class="input-group-btn">
                <button class="btn green" type="button" id="referal-link2-btn"><i class="fa fa-copy"></i></button>
             </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
  </div>

  <div class="portlet light">
    <div class="portlet-title">
      <div class="caption">
        Список ваших рефералов
      </div>
    </div>
    <div class="portlet-body">
      {if !$referal_count}
        <div class="note note-info">
          У вас пока нет рефералов
        </div>
      {/if}

        <table id="referals_datatable" class="table table-hover datatable">
            <thead>
                <tr class="heading">
                    <th>#</th>
                    <th>Логин</th>
                    <th>Дата регистрации</th>
                    <th>Уровень</th>
                    <th>Ваш доход</th>
                </tr>
            </thead>
            <tbody> </tbody>
        </table>


    </div>
  </div>
</div>





