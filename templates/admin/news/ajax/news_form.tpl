<div class="error-news">
    <div class="alert alert-danger">

    </div>
</div>
<form method="post" class="form" id="news-form">
    <div class="form-group">
        <label for="title-news" class="control-label">Название новости:
            <span class="required">*</span>
        </label>
        <input id="title-news" type="text" class="form-control" name="title" value="{$news->getTitle()}">
    </div>
    <div class="form-group">
        <label for="content-news" class="control-label">Описание новости :
            <span class="required">*</span>
        </label>
        <div class="summernote" name="summernote" id="summernote_news">{$news->getContent()}</div>
    </div>

    <div class="form-group">
        <label for="type-news" class="type control-label">Тип новости:
            <span class="required">*</span>
        </label>
        <select {if $news->getType()}disabled{/if} id="type-news" class="form-control" name="type">
            {foreach from=$type key=k item=v}
                <option value="{$k+1}" {if $news->getType() eq $k+1}selected{/if}>{$v}</option>
            {/foreach}
        </select>
    </div>
    <div class="form-group" style="display: none" >
        <label for="goods-news" class="goods control-label">Товар:
            <span class="required">*</span>
        </label>
        <select {if $news->getId()}disabled{/if} id="goods-news" class="form-control select2me" name="goods">
            {foreach from=$goods key=k item=v}
                <option value="{$v.id}" {if $news->getGoodId() eq $v.id}selected="selected"{/if}>ID: {$v.id} - {$v.name}</option>
            {/foreach}
        </select>
    </div>

    <div class="form-group">
        <label for="status-news" class="control-label">Статус:
            <span class="required">*</span>
        </label>
        {if $news->getStatus() == 4}
            <span class="label label-um-green">Выполнено</label>
        {else}
        <select id="status-news" class="form-control" name="status">
            {foreach from=$status key=k item=v}
                <option value="{$k+1}" {if $news->getStatus() eq $k+1}selected{/if}>{$v}</option>
            {/foreach}
        </select>
        {/if}
    </div>

    <div class="form-group">
        {if $news->getStatus() == 4}
            <label for="" class="control-label">Время рассылки: {$activate_time}</label>
        {else}
            <label for="" class="control-label">Запланировать время рассылки:</label>
            <div class="input-group date datetime">
                <input id="activate_time" type="text" value="{$activate_time}" size="16" readonly="" class="form-control" placeholder="Время рассылки">
                <span class="input-group-btn">
                    <button class="btn default date-set" type="button">
                        <i class="fa fa-calendar"></i>
                    </button>
                </span>
            </div>
        {/if}
    </div>

    <div class="form-group">
        <a href="javascript:;" data-news="{$news->getId()}" class="btn green savenews" name="submit">
            Сохранить
        </a>
    </div>
</form>
