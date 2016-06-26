{foreach from=$targets key=key item=target}
    <div class="clearfix margin-bottom-5">
        {$target.name}
        <a {if !$target.selected}style="display:none"{/if} data-toggle="tooltip" data-placement="left" title="Удалить цель" class="btn btn-sm btn-circle btn-icon-only btn-default btn-remove pull-right" data-target="{$key}" href="javascript:;">
            <i class="icon-trash"></i>
        </a>
        <a {if !$target.selected}style="display:none"{/if} data-toggle="tooltip" data-placement="left" title="Редактировать цель" class="btn btn-sm btn-circle btn-icon-only btn-default btn-edit pull-right" data-target="{$key}" href="javascript:;">
            <i class="icon-pencil"></i>
        </a>
        <a {if $target.selected}style="display:none"{/if} data-toggle="tooltip" data-placement="left" title="Добавить цель" class="btn btn-sm btn-circle btn-icon-only btn-default btn-add pull-right" data-target="{$key}" href="javascript:;">
            <i class="fa fa-plus"></i>
        </a>
    </div>
{/foreach}