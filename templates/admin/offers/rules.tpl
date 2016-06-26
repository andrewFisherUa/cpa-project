<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Условия подключения офферов</h3>
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
      <a href="#">Условия подключения</a>
    </li>
  </ul>
</div>
<!-- END PAGE HEADER-->

<div class="portlet light">
  <div class="portlet-body">
    <div id="rules-frm">
      <div class="alert" style="display:none"></div>
      <div class="form-group">
        <select id="offer" class="select2me">
          <option value="0">Общие правила</option>
          {foreach from=$offers item=offer}
            <option value="{$offer.id}">{$offer.id}: {$offer.name}</option>
          {/foreach}
        </select>
      </div>

      <div class="form-group">
        <textarea id="rules" class="ckeditor" cols="30" rows="10">{$rules}</textarea>
      </div>

      <div class="form-group text-right">

        <div class="checkbox pull-left" id="switch-wrap" style="display:none">
          <label>
            <input type="checkbox" id="switch" name="switch"> Включить
          </label>
        </div>

        <button class="btn green" id="reset">Сохранить для всех</button>
        <button class="btn green" id="recovery-rules" style="display:none">Восстановить</button>
        <button class="btn blue" id="save-rules" data-action="save">Сохранить</button>
        <div class="clearfix"></div>
      </div>
    </div>
  </div>
</div>