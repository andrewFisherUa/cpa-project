{foreach from=$blogs item=bl name=b}
   <div>#{$smarty.foreach.b.iteration}
    <a href="{$bl.url}" target="_blank" class="page" data-id="{$bl.b_id}">{$bl.name}</a>
    <a data-page-type="blogs" class="btn btn-circle btn-icon-only btn-default btn-edit" href="javascript:;">
        <i class="icon-pencil"></i>
    </a>
    <a data-page-type="blogs" class="btn btn-circle btn-icon-only btn-default btn-remove" href="javascript:;">
        <i class="icon-trash"></i>
    </a>
   <input type="hidden" name="blog[{$smarty.foreach.b.iteration}][name]" value={$bl.name}>
   <input type="hidden" name="blog[{$smarty.foreach.b.iteration}][url]" value={$bl.url}>
   </div>
{/foreach}