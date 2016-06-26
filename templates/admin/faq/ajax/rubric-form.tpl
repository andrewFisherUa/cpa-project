<div class="alert alert-danger" style="display:none"></div>

<div class="form" id="rubric-form">
    <input type="hidden" name="rubric-id" id="rubric-id" value="{$rubric.rubric_id}">
    <div class="form-group">
        <label class="control-label">Название:
            <span class="required">*</span>
        </label>
        <input id="rubric-name" type="text" class="form-control" value="{$rubric.name}">
    </div>

    <div class="form-group">
        <label class="control-label">CSS класс для иконки: </label>
        <input id="rubric-css" type="text" class="form-control" value="{$rubric.css}">
    </div>

    <div class="form-group">
        <label class="control-label">Вес
            <span class="required">*</span
        </label>
        <select id="rubric-weight" class="form-control select2me">
            {foreach from=$weight item=w}
                <option value="{$w}" {if $w==$rubric.weight}selected{/if}>{$w}</option>
            {/foreach}
        </select>
    </div>

</div>