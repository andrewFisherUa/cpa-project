<div class="comments-nav">
    <div class="orderby-order-container clearfix">
        <div class="orderby-sc">
            <span class="lb_show_col">Показывать</span>
            <ul class="sort-count order-dropdown">
                <li>
                    <span class="current-li"><a>{$pages.length}</a></span>
                    <ul style="display: none; opacity: 1;">
                        <li {if $pages.length == 5}class="current"{/if}>
                            <a href="javascript:;" data-length="5" data-page="{$pages.current}">5</a>
                        </li>
                        <li {if $pages.length == 10}class="current"{/if}>
                            <a href="javascript:;" data-length="10" data-page="{$pages.current}">10</a>
                        </li>
                        <li {if $pages.length == 15}class="current"{/if}>
                            <a href="javascript:;" data-length="15" data-page="{$pages.current}">15</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    {if $pages.last != 1}
    <div class="pag_comment clearfix">
        <nav class="woocommerce-pagination clearfix">
            <ul class="page-numbers">
                {if $pages.current > 1}
                    <li>
                        <a class="prev page-numbers" href="javascript:;" data-page="{$pages.prev}" data-length="{$pages.length}">
                            <i class="icon-caret-left"></i>
                        </a>
                    </li>
                {/if}
                {foreach from=$pages.links item=page}
                    <li>
                        {if $page == $pages.current}
                            <span class="page-numbers current">{$page}</span>
                        {else}
                            <a class="page-numbers" href="javascript:;" data-page="{$page}" data-length="{$pages.length}">{$page}</a>
                        {/if}
                    </li>
                {/foreach}
                {if $pages.current != $pages.last}
                    <li>
                        <a class="next page-numbers" data-page="{$pages.next}" data-length="{$pages.length}">
                            <i class="icon-caret-right"></i>
                        </a>
                    </li>
                {/if}
            </ul>
        </nav>
    </div>
    {/if}
</div>