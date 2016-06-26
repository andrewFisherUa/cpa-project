<h3 class="page-title">{if $cat->getId()}Редактирование категории `{$cat->getName()}`{else}Создание категории{/if}</h3>
<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <a href="/admin"><i class="fa fa-home"></i></a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="/admin/cats">Категории</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">{if $cat->getId()}Редактирование категории `{$cat->getName()}`{else}Создание категории{/if}</a>
    </li>
  </ul>
</div>

<form id="cat-form" action="/admin/cats/{if $cat->getId()}edit/{$cat->getId()}/{else}new/{/if}" method="post">
  <div class="portlet light">
    <div class="portlet-title">
      <div class="caption">
        {if $cat->getId()}Редактирование категории `{$cat->getName()}`{else}Создание категории{/if}
      </div>
      <div class="actions">
        <button id="save-cat" type="submit" class="btn blue btn-circle" name="save">Сохранить</button>
      </div>
    </div>
    <div class="portlet-body">
      <div class="alert alert-danger" style="display:none;"></div>
      <div class="form-horizontal">
        <input type="hidden" name="id" value="{$cat->getId()}">
        <input type="hidden" name="sub" value="7">
        <input type="hidden" name="sub_order" value="{$cat->getSubOrder()}">
        <div class="form-group">
          <label class="col-sm-2 control-label">Название: <span class="required">*</span></label>
          <div class="col-sm-10">
            <input type="text" name="name" class="form-control" placeholder="Название" value="{$cat->getName()}" required>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label">Ссылка: <span class="required">*</span></label>
          <div class="col-sm-10">
            <input type="text" name="link" class="form-control" placeholder="Ссылка" value="{$cat->getAlias()}" required>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-2 control-label">Тип: <span class="required">*</span></label>
          <div class="col-sm-10">
            <select name="type" class="form-control">
              <option value="shop_category" {if $cat->getType() == "shop_category"}selected{/if}>Магазин</option>
              <option value="offer_category" {if $cat->getType() == "offer_category"}selected{/if}>Офферы</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-10 col-sm-offset-2">
            <div class="checkbox">
              <label>
                <input type="checkbox" name="hidden" value="1" {if $cat->isHidden()}checked{/if}> Скрыть категорию
              </label>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">Css класс: </label>
          <div class="col-sm-10">
            <input type="text" name="css" class="form-control" placeholder="Css класс" value="{$cat->getCss()}">
            <p class="help-block">
              Класс css для иконки
              <ul class="icons-list">
              {foreach from=$icons item=i}
                <li>
                  <i class="ci ci-{$i}"></i>
                  <span>ci ci-{$i}</span>
                </li>
              {/foreach}
              </ul>
            </p>
          </div>
        </div>
        <div class="form-group">
          <label for="cat-heading" class="col-sm-2 control-label">Заголовок (H1): </label>
          <div class="col-sm-10">
            <textarea class="form-control" name="heading" placeholder="Заголовок" rows="5">{$cat->getHeading()}</textarea>
          </div>
        </div>
        <div class="form-group">
          <label for="cat-text" class="col-sm-2 control-label">Описание: </label>
          <div class="col-sm-10">
            <textarea class="form-control ckeditor" id="cattext" name="cattext" placeholder="Описание" rows="5">{$cat->getCattext()}</textarea>
            {literal}
            <script type="text/javascript">
              var editor1 = CKEDITOR.replace('cattext');
              AjexFileManager.init({
                returnTo: 'ckeditor',
                editor: editor1
              });
            </script>
            {/literal}
          </div>
        </div>
        <div class="form-group">
          <label for="cat-seo" class="col-sm-2 control-label">SEO текст: </label>
          <div class="col-sm-10">
            <textarea class="form-control ckeditor" id="seo" name="seo" placeholder="SEO текст" rows="5">{$cat->getSeo()}</textarea>
            {literal}
            <script type="text/javascript">
              var editor2 = CKEDITOR.replace('seo');
              AjexFileManager.init({
                returnTo: 'ckeditor',
                editor: editor2
              });
            </script>
            {/literal}
          </div>
        </div>
        <div class="form-group">
          <label for="cat-title" class="col-sm-2 control-label">Title: </label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="title" placeholder="Title" value="{$cat->getTitle()}">
          </div>
        </div>
        <div class="form-group">
          <label for="cat-description" class="col-sm-2 control-label">Description: </label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="description" placeholder="Description"  value="{$cat->getDescription()}">
          </div>
        </div>
        <div class="form-group">
          <label for="cat-keywords" class="col-sm-2 control-label">Keywords: </label>
          <div class="col-sm-10">
            <input type="text" class="form-control" name="keywords" placeholder="Keywords"  value="{$cat->getKeywords()}">
          </div>
        </div>

      <div class="row">
        <div class="col-sm-10 col-sm-offset-2">
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label">Основное изображение: </label>
                <div>
                    <input type="hidden" id="mainimg" name="mainimg" value="{$cat->getMainImg()}">
                    <div id="mainimg-progress-wrap" class="fileinput-new" style="width: 240px">
                      <div id="mainimgbox" class="thumbnail" {if !$cat->getMainImg()} style="display:none" {/if} >
                          {if $cat->getMainImg()}<img src="/misc/images/cats/{$cat->getMainImg()}" alt="">{/if}
                      </div>
                    </div>

                    <input type="button" id="upload-mainimg-btn" class="btn clearfix green" value="Выбрать файл">
                    <div id="mainimg-errormsg" class="clearfix redtext" style="padding-top: 5px;"></div>
                    <div id="mainimg-progressOuter" class="progress progress-striped active" style="display:none;">
                      <div id="mainimg-progressBar" class="progress-bar progress-bar-success"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                      </div>
                    </div>
                </div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="form-group">
                <label class="control-label">Изображение для TOP блока: </label>
                <p class="help-block">Рекомендуемое соотношение сторон 270x140</p>
                <div>
                    <input type="hidden" id="topimg" name="topimg" value="{$cat->getTopimg()}">
                    <div id="topimg-progress-wrap" class="fileinput-new" style="width: 240px">
                      <div id="topimgbox" class="thumbnail" {if !$cat->getTopimg()} style="display:none" {/if} >
                          {if $cat->getTopimg()}<img src="/misc/images/cats/{$cat->getTopimg()}" alt="">{/if}
                      </div>
                    </div>

                    <input type="button" id="upload-topimg-btn" class="btn clearfix green" value="Выбрать файл">
                    <div id="topimg-errormsg" class="clearfix redtext" style="padding-top: 5px;"></div>
                    <div id="topimg-progressOuter" class="progress progress-striped active" style="display:none;">
                      <div id="topimg-progressBar" class="progress-bar progress-bar-success"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                      </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      </div>
    </div>
  </div>
</form>