<div class="alert alert-danger" style="display:none"></div>

<div class="form" id="article-form">
    <input type="hidden" name="article-id" id="article-id" value="{$article.article_id}">
    <div class="form-group">
        <label class="control-label">Название статьи:
            <span class="required">*</span>
        </label>
        <input id="article-title" type="text" class="form-control" value="{$article.title}">
    </div>
    <div class="form-group">
        <label class="control-label">Описание:
            <span class="required">*</span>
        </label>
        <div class="summernote" name="summernote" id="article-content">{$article.content}</div>
    </div>

    <div class="form-group">
        <label class="control-label">Рубрика
            <span class="required">*</span>
        </label>
        <select id="article-rubric" class="form-control select2me">
            <option value="0">Без рубрики</option>
            {foreach from=$rubrics item=v}
                <option value="{$v.rubric_id}" {if $v.rubric_id == $article.rubric_id}selected{/if}>{$v.name}</option>
            {/foreach}
        </select>
    </div>

    <div class="form-group">
        <label for="article-rubric" class="control-label">Статус:
            <span class="required">*</span>
        </label>
        <select id="article-status" class="form-control">
            {foreach from=$status key=k item=v}
                <option value="{$k}" {if $article.status == $k}selected{/if}>{$v}</option>
            {/foreach}
        </select>
    </div>

    <div class="form-group">
        <label class="control-label">Вес
            <span class="required">*</span>
        </label>
        <select id="article-weight" class="form-control select2me">
            {foreach from=$weight item=w}
                <option value="{$w}" {if $w==$article.weight}selected{/if}>{$w}</option>
            {/foreach}
        </select>
    </div>
</div>