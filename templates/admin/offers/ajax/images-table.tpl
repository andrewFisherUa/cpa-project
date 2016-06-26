{if $imagesCount > 0}
    {foreach from=$offerImages.images item=im name=i key=key}
        {if $im.name}
        <tr>
            <td>
                <a href="/misc/images/goods/{$im.name}" class="thumbnail fancybox" rel="offer-images" style="max-width:160px; max-height:160px">
                    <img src="/misc/images/goods/{$im.name}" />
                </a>
            </td>
            <td>
                <label class="radio">
                    <div>
                        <input type="radio" name="offer[mainimg]" class="image{$key}" {if $im.main || $offerImages.main.id == $im.id } checked{/if} onclick="changeMainImg('{$im.name}', {$key})" value="{$im.name}">
                    </div>
                </label>
            </td>
            <td>
                <span class="btn red btn-remove" alt="Удалить" data-toggle="confirmation" data-image="{$key}"><i class="fa fa-times"></i> Удалить</span>
            </td>
        </tr>
        {/if}
    {/foreach}
{else}
    <tr>
        <td colspan="3">Добавьте изображения</td>
    </tr>
{/if}