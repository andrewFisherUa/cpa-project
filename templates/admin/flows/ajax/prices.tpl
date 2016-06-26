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
				<tr>
					<td><span class="flag flag-{$country_code}"></span> {$item.country_name}</td>
					<td>
						<select class="target form-control" data-code="{$country_code}">
							{foreach from=$item.targets item=target name=t}
								<option value="{$target->getId()}"
									data-webmaster_commission="{$target->getWebmasterCommission()}"
									data-price="{$item.recommended}"
									data-editable="{$canEdit}"
									data-max_profit="{$target->getMaxProfit()}"
									{if $target->getId() == $item.target_id} selected{/if}
									>{$target->getName()}
								</option>
							{/foreach}
						</select>
					</td>
					<td><span class="recommended" data-code="{$country_code}">{$item.recommended}</span> {$item.currency}</td>
					<td><span class="price" data-code="{$country_code}">{$item.price}</span> {$item.currency}</td>
					<td>
						<div class="input-inline input-medium">
							<input id="profit-{$country_code}" type="text" value="{$item.profit}" name="profit" class="form-control profit" data-code="{$country_code}" data-currency="{$item.currency}" data-max="{$item.max_profit}" {if $item.target_id == 1 && !$canEdit}disabled{/if}>
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