<form class="form">
	<input type="hidden" id="menu-id" name="menu_id" value="{$link.m_id}">
  <div class="form-group">
    <label class="control-label">Название страницы:</label>
    <input type="text" class="form-control" name="menu_title" value="{$link.title}" id="menu-title">
  </div>
  <div class="form-group">
    <label class="control-label">Ссылка:</label>
    <div class="input-group">
        <span class="input-group-addon">{$admin_url}</span>
        <input type="text" class="form-control" name="menu_link" value="{$link.link}" id="menu-link">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label">Описание:</label>
    <p class="help-block">Показывается при наведении курсора на категорию</p>
    <textarea col="10" rows="2" class="form-control" name="menu_desc" id="menu-desc">{$link.description}</textarea>
  </div>
  <div class="form-group">
    <label class="control-label">Родительская страница:</label>
    <div>
  		<select class="form-control select2me" name="menu_parent" id="menu-parent" >
        <option value="0">Нет</option>
  			{foreach from=$parents item=p}
          {if $p.m_id == $link.parent }
            <option value="{$p.m_id}" selected>{$p.title}</option>
          {else}
            <option value="{$p.m_id}">{$p.title}</option>
          {/if}

  			{/foreach}
	    </select>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label">Вес:</label>
    <p class="help-block">Страницы с меньшим весом выводятся раньше страниц с большим весом</p>
    <div class="row">
    	<div class="col-md-4">
    		<select class="form-control" name="menu_weight" id="menu-weight">
    			{foreach from=$weight item=w}
            {if $w == $link.weight }
              <option value="{$w}" selected>{$w}</option>
            {else}
              <option value="{$w}">{$w}</option>
            {/if}
				  {/foreach}
		    </select>
    	</div>
    </div>
  </div>

  <div class="form-group">
    <label class="control-label">Класс CSS для иконки:</label>
    <input type="text" class="form-control" name="menu_css" value="{$link.css}" id="menu-css">
  </div>

</form>
