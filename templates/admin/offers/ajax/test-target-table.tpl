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
        <th>Комиссия UM</th>
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
    {foreach from=$prices item=price}
      <tr>
        {assign var=code value=$price->getCountryCode()}
        {assign var=targetsCount value=$price->getTargetsCount()}
        {assign var=rowspan value=$targetsCount+1}
        <td rowspan="{if $targetsCount == 1}1{else}{$rowspan}{/if}"><span class="flag flag-{$price->getCountryCode()}"></span>&nbsp;{$countries.$code.name}</td>
        {if $view != 'extra-short'}
        <td rowspan="{if $targetsCount == 1}1{else}{$rowspan}{/if}" class="text-right">
          {$price->getValue()}&nbsp;{$price->getCurrency()}
        </td>
        {/if}
      {if $targetsCount == 0} <td colspan="3"></td> {/if}
      {if $targetsCount > 1} </tr> {/if}


      {foreach from=$price->getTargets() item=target}
        {if $targetsCount > 1}<tr>{/if}
          <td>{$target->getName()}</td>
          <td class="text-right"> <span class="highlight">{$target->getWebmasterCommission()}&nbsp;{$target->getCurrency()}</span> </td>
          {if $admin}
            <td class="text-right"><span class="highlight">{$target->getCommission()}&nbsp;{$target->getCurrency()}</span></td>
          {/if}
        {if $targetsCount > 1}</tr>{/if}
      {/foreach}
      {if $targetsCount == 1}</tr>{/if}
    {/foreach}
    {if !$targetsCount } </tr> {/if}
    </tbody>
  </table>
{/if}