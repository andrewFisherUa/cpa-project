<div class="form">
	<div class="alert alert-danger" style="display:none"></div>
	<input type="hidden" name="user_id" value="{$data.user_id}">
	<div class="form-group">
		<label for="created" class="control-label">Регистрация</label>
		<p class="form-control-static">{$data.created}</p>
	</div>
	{if $isPartner}
	<div class="form-group">
		<label for="first_name" class="control-label">Имя</label>
		<input type="text" class="form-control" name="first_name" value="{$data.first_name}">
	</div>
	<div class="form-group">
		<label for="last_name" class="control-label">Фамилия</label>
		<input type="text" class="form-control" name="last_name" value="{$data.last_name}">
	</div>
	{/if}
	<div class="form-group">
		<label for="email" class="control-label">Email</label>
		<input type="text" class="form-control" name="email" value="{$data.email}">
	</div>
	{if $isPartner}
	<div class="form-group">
		<label for="email" class="control-label">Skype</label>
		<input type="text" class="form-control" name="skype" value="{$data.skype}">
	</div>
	<div class="form-group">
		<label for="phone" class="control-label">Телефон</label>
		<input type="text" class="form-control" name="phone" value="{$data.phone}">
	</div>
	{/if}
	<div role="separator" class="divider"></div>

	<div class="form-group">
		<label for="pass" class="control-label">Новый пароль</label>
		<input type="password" class="form-control" name="pass" value="">
	</div>
	<div class="form-group">
		<label for="passr" class="control-label">Подтверждение пароля</label>
		<input type="password" class="form-control" name="passr" value="">
	</div>
	{if $isPartner}
		<div role="separator" class="divider"></div>

		{foreach from=$data.options item=i key=k}
		<div class="checkbox">
		    <label>
		      <input type="checkbox" {if $i.value == 1}checked{/if} data-option="{$k}" value="{$i.value}"> {$i.desc}
		    </label>
		  </div>
		{/foreach}
	{/if}
</div>