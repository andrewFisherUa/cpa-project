<h2 class="page-title">
  {if $data.id}Редактирование источника `{$data.name}`{else}Добавление источника - Другое{/if}
</h2>

<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="/admin">Главная</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">{if $data.id}Редактирование источника `{$data.name}`{else}Добавление источника - Другое{/if}</a>
    </li>
  </ul>
</div>

<div class="portlet light">
  <div class="portlet-body">
    <form action="/admin/spaces" method="post" id="space-form">

      <div class="form-horizontal">
        <input type="hidden" name="space[type]" value="other">
        <input type="hidden" name="space[id]" value="{$data.id}">
        <div class="form-group">
          <label class="control-label col-sm-2">Название: <span class="required">*</span></label>
          <div class="col-sm-10">
            <input type="text" name="space[name]" class="form-control" value="{$data.name}" required>
            <p class="help-block">Максимальное количество символов - 30</p>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-sm-2">URL: <span class="required">*</span></label>
          <div class="col-sm-10">
            <input type="text" name="space[url]" class="form-control" placeholder="http://example.com" value="{$data.url}" required>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-sm-2">Источники трафика: <span class="required">*</span></label>
          <div class="col-sm-10">
            <select name="space[source]" class="form-control select2me" required>
              {foreach from=$data.sources key=k item=a}
                <option value="{$k}" {if $k == $data.source}selected{/if}>{$a}</option>
              {/foreach}
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-sm-2">Описание:</label>
          <div class="col-sm-10">
            <textarea type="text" name="space[desc]" class="form-control" rows="10" cols="30" id="desc">{$data.desc}</textarea>
            <!--<p class="help-block">Минимальное количество символов - 70</p>-->
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-sm-2">Комментарий для администрации:</label>
          <div class="col-sm-10">
            <textarea type="text" name="space[comment]" class="form-control" rows="10" cols="30">{$data.comment}</textarea>
          </div>
        </div>

        <div class="text-right">
          <button type="submit" name="submit" class="btn blue">{if $data.id}Сохранить{else}Добавить{/if}</button>
          <a href="/admin/spaces" class="btn btn-default">Назад</a>
        </div>

      </div>
    </form>
  </div>
</div>