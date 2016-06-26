<form id="edit-content-form" action="#" method="POST">
    <input type="hidden" name="content[type]" value="{$content.type}" id="content_type">
    <input type="hidden" name="content[id]" value="{$content.c_id}" id="content_id">

    <div class="form-group">
      <label class="control-label">Название</label>
      <div>
        <input type="text" class="form-control" value="{$content.name}" id="content_name" name="content[name]">
      </div>
    </div>

    <div class="form-group">
      <label class="control-label">Группа</label>
      <div>
        {if $content.type == "blog"}
            <select class="form-control select2me" data-placeholder="Select..." name="content[group]" id="content_group" data-load="ajax">
              <option value=""></option>
               {foreach from=$groups item=group}
                <option value="{$group.g_id}">{$group.name}</option>
              {/foreach}
            </select>         
        {else}
          {foreach from=$groups item=group}
            <div><input type="checkbox" class="groups" name="content[groups][]" value="{$group.g_id}" {if $group.checked}checked{/if}> {$group.name}</div>
          {/foreach}
        {/if}
      </div>
    </div>

    {if $content.type == "blog"}
      <div class="form-group">
        <label class="control-label">Лендинг</label>
        <div>
          <div class="input-group">
            <select class="form-control select2me" data-placeholder="Select..." id="content_landing" name="content[landing]">
              <option value=""></option>
              {foreach from=$landings item=lp}
                <option value="{$lp.c_id}">{$lp.name}</option>
              {/foreach}
            </select>
            <span class="input-group-btn">
              <button id="add-landing" class="btn btn-success disabled" type="button">Добавить</button>
            </span>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="control-label">Выбраны</label>
        <div>
          <div class="select2-container select2-container-multi">
            <ul id="selected_landings" class="select2-choices">
              {foreach from=$content.landings item=lp}
                <option value="{$lp.c_id}">{$lp.name}</option>
              {/foreach}
            </ul>
          </div>
        </div>
      </div>
    {/if}

    <div class="form-group">
      <label class="control-label">Ссылка</label>
      <div>
        <div class="input-group">
            <span class="input-group-addon">{$type_link}</span>
            <input type="text" class="form-control" value="{$content.link}" name="content[link]" id="content_link">
        </div>
      </div>
    </div>

    <div class="form-group">
      <button type="submit" name="save_content" class="btn default">Сохранить</button>
    </div>
</form>

