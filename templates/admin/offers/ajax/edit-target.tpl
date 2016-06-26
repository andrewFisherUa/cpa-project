<table class="table table-bordered table-hover" id="offer-price-table">
    <thead>
        <tr>
            <th width="5%"></th>
            <th width="15%">Страна</th>
            <th width="20%">Цена на лендинге</th>
            <th width="20%">Максимальная цена</th>
            <th width="20%">Комиссия вебмастера</th>
            <th width="20%">Комиссия Univermag</th>
        </tr>
    </thead>
    <tbody>
      {foreach from=$data key=code item=item}
        <tr data-code='{$code}' {if !$item.target}style="opacity:.5;"{/if}>
          <input type='hidden' name='target' value='{$item.target_id}'>
          <td>
              <div class='checkbox text-center'>
                <label><input type='checkbox' class="select-row" {if $item.target}checked{/if}></input></label>
              </div>
          </td>
          <td><strong>{$item.country_name}</strong></td>
          <td><div class='input-group margin-top-10'>
            <input type='text' class='form-control' value='{$item.price}' name='price' disabled>
            <span class='input-group-addon'>{$item.currency}</span>
          </div></td>
          <td><div class='input-group margin-top-10'>
            <input type='text' class='form-control' value='{if $item.max_price}{$item.max_price}{else}{$item.price}{/if}' name='max_price'>
            <span class='input-group-addon'>{$item.currency}</span>
          </div></td>
          <td><div class='input-group margin-top-10'>
            <input type='text' class='form-control numbers-only' value='{$item.target.webmaster_commission}' name='webmaster_commission'>
            <span class='input-group-addon'>{$item.currency}</span>
          </div></td>
          <td><div class='input-group margin-top-10'>
            <input type='text' class='form-control  numbers-only' value='{$item.target.commission}' name='commission'>
            <span class='input-group-addon'>{$item.currency}</span>
          </div></td>
        </tr>
      {/foreach}
    </tbody>
</table>

{if $item.target_id == 1}
<h5>Разрешить изменение цены вебмастерам:</h5>
<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <div class="input-group">
        <select id="add-webmaster" style="width:100%">
          {foreach from=$webmasters item=item}
           <option value="{$item.id}" data-login="{$item.login}">{$item.id}: {$item.login}</option>
          {/foreach}
        </select>
        <div class="input-group-btn">
            <button type="button" class="btn green" id="add-webmaster-btn">Добавить</button>
        </div>
        <!-- /btn-group -->
      </div>
    </div>
  </div>
</div>

<div class="bootstrap-tagsinput" id="selected-webmasters">
  {foreach from=$selected_webmasters item=user}
  <span class="tag label label-info" data-id="{$user.id}" data-login="{$user.login}">{$user.id}: <span class='login'>{$user.login}</span>
    <span data-role="remove"></span>
  </span>
  {/foreach}
</div>

{/if}



