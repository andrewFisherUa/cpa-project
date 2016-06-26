{if $prices}
	<table class="table table-hover table-bordered" id="table_prices">
		<thead>
			<tr>
				<th width="20%">Страна</th>
				<th width="30%">Цель</th>
				<th width="20%">Рекомендуемая цена</th>
				<th width="15%">Цена</th>
				<th width="15%">Заработок</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$prices key=country_code item=item}
				<tr data-code="{$country_code}" data-currency="{$item.currency}">
					<td><span class="flag flag-{$country_code}"></span> {$item.country_name}</td>
					<td>
						<select class="target form-control" name="target">
							{foreach from=$item.targets item=t}
								<option value="{$t.id}" 
									data-price="{$item.price}"
									data-profit="{$t.profit}" 
									data-max="{$t.max}"
									data-editable="{$editable}" {if $t.selected} selected{/if}>
									{$t.name}
								</option>
							{/foreach}
						</select>
					</td>
					<td><span class="recommended">{$item.recommended}</span> {$item.currency}</td>
					<td><span class="price">{$item.price}</span> {$item.currency}</td>
					<td>
						<div class="input-inline input-medium">
							<input id="profit-{$country_code}" type="text" value="{$item.profit}" name="profit" class="form-control profit" disabled>
						</div>
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{else}
	<div class="note note-info">
		<p>У этого оффера нет цен</p>
	</div>
{/if}