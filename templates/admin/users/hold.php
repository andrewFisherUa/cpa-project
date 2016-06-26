<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Настройки холда</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="/admin/users">Пользователи</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Холд</a>
        </li>
      </ul>
</div>
<!-- END PAGE HEADER-->

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      Настройки холда по умолчанию
    </div>
  </div>
  <div class="portlet-body">
    <div class="row">
      <div class="col-md-6">
        <div id="defaults-table-wrap">
          <?=$hold->getTable();?>
        </div>
      </div>
      <div class="col-md-6 form-horizontal">
        <div class="form-group">
          <label class="control-label col-sm-3">Страна</label>
          <div class="col-sm-9">
            <select id="select_country_1" class="form-control">
              <?php foreach ($countries as $code=>$item) : ?>
                <option value="<?=$code;?>"><?=$item["name"];?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-sm-3">Цель</label>
          <div class="col-sm-9">
            <select id="select_target_1" class="form-control">
              <?php foreach ($targets as $target) : ?>
                <option value="<?=$target['id'];?>"><?=$target["name"];?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-sm-3">Значение холда</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" id="hold_1">
          </div>
        </div>
        <div class="col-sm-12 text-right">
          <button id="save_default" class="btn btn-default">Сохранить</button>
          <button id="save_to_all" class="btn btn-default">Сохранить для всех</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
      Настройки холда по вебмастерам
    </div>
  </div>
  <div class="portlet-body">
    <div class="row">
      <div class="col-md-6">
        <div id="webmaster-table-wrap">
          <?=$hold->getTable();?>
        </div>
      </div>
      <div class="col-md-6 form-horizontal" id="webmaster-hold-form">
        <div class="alert alert-danger" style="display:none"></div>
        <div class="form-group">
          <label class="control-label col-sm-3">Вебмастер</label>
          <div class="col-sm-9">
            <select id="select_webmaster" class="form-control select2me">
              <option value="-1">Выбор вебмастера</option>
              <?php foreach ($webmasters as $user) :?>
                <option value="<?=$user['user_id'];?>"><?=$user['user_id'];?>: <?=$user['login'];?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-sm-3">Страна</label>
          <div class="col-sm-9">
            <select id="select_country_2" class="form-control">
              <?php foreach ($countries as $code=>$item) : ?>
                <option value="<?=$code;?>"><?=$item["name"];?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-sm-3">Цель</label>
          <div class="col-sm-9">
            <select id="select_target_2" class="form-control">
              <?php foreach ($targets as $target) : ?>
                <option value="<?=$target['id'];?>"><?=$target["name"];?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-sm-3">Значение холда</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" id="hold_2">
          </div>
        </div>
        <div class="col-sm-12 text-right">
          <button id="save_webmaster_hold" class="btn btn-default">Сохранить</button>
        </div>
      </div>
    </div>
  </div>
</div>