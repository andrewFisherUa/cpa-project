{foreach from=$countries item=c}
<div class="clearfix margin-bottom-5">
    <span class="flag flag-{$c.country_code}"></span> {$c.name}
    <input type="hidden" class="hidden-code" value="{$c.country_code}" />
    <input type="hidden" class="hidden-price" value="{$c.price}" />
    <input type="hidden" class="hidden-price_id" value="{$c.price_id}" />
    <input type="hidden" class="hidden-qty" value="{$c.qty}" />
    <a data-toggle="tooltip" data-placement="left" title="Удалить цены по стране" class="btn btn-sm btn-circle btn-icon-only btn-default btn-remove pull-right" href="javascript:;">
        <i class="icon-trash"></i>
    </a>
    <a data-toggle="tooltip" data-placement="left" title="Редактировать базовую цену" class="btn btn-sm btn-circle btn-icon-only btn-default btn-edit pull-right" href="javascript:;">
        <i class="icon-pencil"></i>
    </a>
</div>
{/foreach}