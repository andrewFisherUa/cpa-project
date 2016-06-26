<h2 class="page-title">
  {if $data.id}Редактирование источника `{$data.name}`{else}Добавление нового источника - Сайт, форум, блог{/if}
</h2>
<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="/admin">Главная</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">{if $data.id}Редактирование источника `{$data.name}`{else}Добавление нового источника - Сайт, форум, блог{/if}</a>
    </li>
  </ul>
</div>

<div class="portlet light">
  <div class="portlet-body">
    <p>После добавления сайта, его необходимо будет подтвердить установив специальный код подтверждения на него, после успешного подтверждения, сайт будет отправлен на модерацию на соответствие условиям системы</p>
    <p>Проверка модератором может занять до 2-х рабочих дней. После того как сайт будет проверен, вы получите уведомление на свой Email и в случае положительного решения, вы сможете начать c нами работать.</p>

    <form action="{if $data.id}/admin/spaces{else}/admin/spaces/validate{/if}" method="post" id="space-form">

      <div class="form-horizontal">
        <input type="hidden" name="space[type]" value="site" id="space-form">
        <input type="hidden" name="space[id]" value="{$data.id}">
        <div class="form-group">
          <label class="control-label col-sm-2">Название: <span class="required">*</span></label>
          <div class="col-sm-10">
            <input type="text" name="space[name]" class="form-control" value="{$data.name}">
            <p class="help-block">Максимальное количество символов - 30</p>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-sm-2">URL: <span class="required">*</span></label>
          <div class="col-sm-10">
            <input type="text" name="space[url]" class="form-control" placeholder="http://example.com" value="{$data.url}" {if $data.id}readonly{/if}>
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
          <label class="control-label col-sm-2">Язык сайта: </label>
          <div class="col-sm-10">
            <div class="checkbox">
              <label>
                <input type="checkbox" name="space[meta][lang][]" value="ru" {if $data.lang.ru}checked{/if}> Русский
              </label>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="space[meta][lang][]" value="ua"{if $data.lang.ua}checked{/if}> Украинский
              </label>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="space[meta][lang][]" value="en"{if $data.lang.en}checked{/if}> Английский
              </label>
            </div>
            <div class="checkbox">
              <label>
                <input type="checkbox" name="space[meta][lang][]" value="other" {if $data.lang.other}checked{/if}> Другой
              </label>
            </div>
          </div>
        </div>

        <p>Обращаем ваше внимание что минимальными требованиями для приёма сайта является не менее 300 хостов в сутки (Уникальных посетителей). Предоставление статистики является обязательным условием для приёма сайта. Если у вашего сайта открытая статистика, поле пароль можно оставить пустым.</p>

        <div class="form-group">
          <label class="control-label col-sm-2">Ссылка на статистику: <span class="required">*</span></label>
          <div class="col-sm-10">
            <input type="text" name="space[meta][stat_url]" class="form-control" value="{$data.stat_url}" required/>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-sm-2">Логин от статистики: <span class="required">*</span></label>
          <div class="col-sm-10">
            <input type="text" name="space[meta][stat_login]" class="form-control" value="{$data.stat_login}" required/>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-sm-2">Пароль от статистики:</label>
          <div class="col-sm-10">
            <input type="text" name="space[meta][stat_pass]" class="form-control" value="{$data.stat_pass}"/>
          </div>
        </div>

        <div class="form-group">
          <label class="control-label col-sm-2">Комментарий для администрации:</label>
          <div class="col-sm-10">
            <textarea type="text" name="space[comment]" class="form-control" rows="10" cols="30">{$data.comment}</textarea>
          </div>
        </div>

        <div class="text-right">
          <button type="submit" name="submit" class="btn blue">{if $data.id}Сохранить{else}Отправить на модерацию{/if}</button>
          <a href="/admin/spaces" class="btn btn-default">Назад</a>
        </div>

      </div>
    </form>
  </div>
</div>