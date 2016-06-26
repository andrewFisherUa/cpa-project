{foreach from=$landings item=lp name=l}
 <div>#{$smarty.foreach.l.iteration}
  <a href="{$lp.url}" target="_blank" class="page" data-id="{$lp.l_id}">{$lp.name}</a>
  <a class="btn btn-circle btn-icon-only btn-default btn-edit" data-page-type="landings" href="javascript:;">
      <i class="icon-pencil"></i>
  </a>
  <a class="btn btn-circle btn-icon-only btn-default btn-remove" data-page-type="landings" href="javascript:;">
      <i class="icon-trash"></i>
  </a>
 <input type="hidden" name="landing[{$smarty.foreach.l.iteration}][name]" value={$lp.name}>
 <input type="hidden" name="landing[{$smarty.foreach.l.iteration}][url]" value={$lp.url}>
 </div>
{/foreach}