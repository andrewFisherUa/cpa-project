{if $prices}
  <table class="table table-bordered table-hover table-condensed offers-table">

    <thead>
    {if $view == 'full'}
      <tr role="row" class="heading">
        <th width="22%">Страна</th>
        <th>Базовая цена</th>
        <th width="28%">Цель</th>
        <th>Комиссия вебмастера</th>
        {if $admin}
        <th>Комиссия univermag</th>
        {/if}
      </tr>
    {/if}
    {if $view == 'short'}
      <tr role="row" class="heading">
        <th width="22%"></th>
        <th></th>
        <th {if $admin}width="28%"{else}width="35%"{/if}></th>
        <th></th>
        {if $admin}
        <th></th>
        {/if}
      </tr>
    {/if}
    {if $view == 'extra-short'}
      <tr role="row" class="heading">
        <th width="30%"></th>
        <th width="40%"></th>
        <th></th>
      </tr>
    {/if}
    </thead>


    <tbody>
    {foreach from=$prices item=list}
      <tr>
        <td rowspan="{$list.targetsCount}"><span class="flag flag-{$list.country_code}"></span>&nbsp;{$list.name}</td>
        {if $view != 'extra-short'}<td rowspan="{$list.targetsCount}" class="text-right">{$list.price} {$list.currency}</td>{/if}
      {if $list.targetsCount > 1} </tr> {/if}

      {if $list.targetsCount == 1 }
        <td colspan="3"></td>
      {/if}
      {foreach from=$list.targets item=t}
        {if $list.targetsCount > 1}<tr>{/if}
          <td>{$t.label}</td>
          <td class="text-right"> <span class="highlight">{$t.comission_webmaster} {$list.currency}</span> </td>
          {if $admin}
          <td class="text-right"><span class="highlight">{$t.comission} {$list.currency}</span></td>
          {/if}
        {if $list.targetsCount > 1}</tr>{/if}
      {/foreach}
      {if $list.targetsCount == 1}</tr>{/if}
    {/foreach}
    {if !$list.targetsCount } </tr> {/if}
    </tbody>
  </table>
{/if}