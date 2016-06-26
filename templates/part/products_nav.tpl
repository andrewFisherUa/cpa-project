<div class="products-nav">
    {if !$only_pages}
    <ul class="respl-view respl-option" data-option-key="layoutMode">
        <li class="view-grid sel">
            <a data-rl_value="fitRows" href="#content"><i class="icon-th-large"></i></a>
        </li>
        <li class="view-list">
            <a data-rl_value="straightDown" href="#content"><i class="icon-list-ul"></i></a>
        </li>
    </ul>
    <div class="catalog-ordering clearfix">
        <div class="orderby-order-container">
        <div class="orderby-sort-name">
            <span class="lb_sort">Сортировка</span>
            <ul class="orderby order-dropdown">
                <li>
                    <span class="current-li">
                        <span class="current-li-content"><a>По умолчанию</a></span></span>
                        <ul style="display: none;">
                            <li {if $orderby=="name"}class="current"{/if}>
                                <a href="?orderby=name&amp;order={$order}&amp;page={$pages.current}&amp;length={$pages.length}{$query}">По умолчанию</a>
                            </li>
                            <li {if $orderby=="name"}class="current"{/if}>
                                <a href="?orderby=name&amp;order={$order}&amp;page={$pages.current}&amp;length={$pages.length}{$query}">Имя</a>
                            </li>
                            <li {if $orderby=="price"}class="current"{/if}>
                                <a href="?orderby=price&amp;order={$order}&amp;page={$pages.current}&amp;length={$pages.length}{$query}">Цена</a>
                            </li>
                            <!--
                            <li {if $orderby=="date"}class="current"{/if}>
                                <a href="?orderby=date&amp;order={$order}&amp;page={$pages.current}&amp;length={$pages.length}{$query}">Дата</a>
                            </li>
                            -->
                        </ul>
                    </li>
                </ul>
        </div>
        <div class="orderby-sc">
            <ul class="order">
                <li class="asc" {if $order=="asc"}style="display:none"{/if}>
                    <a href="?orderby={$orderby}&amp;order=asc&amp;page={$pages.current}&amp;length={$pages.length}{$query}"><i class="icon-ar-down"></i></a>
                </li>
                <li class="desc" {if $order=="desc"}style="display:none"{/if}>
                    <a href="?orderby={$orderby}&amp;order=desc&amp;page={$pages.current}&amp;length={$pages.length}{$query}"><i class="icon-ar-up"></i></a>
                </li>
            </ul>
        </div>

        <div class="orderby-sc">
            <span class="lb_show_col">Показывать</span>
                <ul class="sort-count order-dropdown">
                    <li>
                        <span class="current-li"><a>{$pages.length}</a></span>
                        <ul style="display: none; opacity: 1;">
                            <li {if $pages.length == 9}class="current"{/if}>
                                <a href="?orderby={$orderby}&amp;order={$order}&amp;page={$pages.current}&amp;length=9{$query}">9</a>
                            </li>
                            <li {if $pages.length == 18}class="current"{/if}>
                                <a href="?orderby={$orderby}&amp;order={$order}&amp;page={$pages.current}&amp;length=18{$query}">18</a>
                            </li>
                            <li {if $pages.length == 27}class="current"{/if}>
                                <a href="?orderby={$orderby}&amp;order={$order}&amp;page={$pages.current}&amp;length=27{$query}">27</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    {/if}
    {if $pages.last > 1}
    <div class="pag_product clearfix">
        <nav class="woocommerce-pagination clearfix">
            <ul class="page-numbers">
                {if $pages.current > 1}
                    <li>
                        <a class="prev page-numbers" href="?orderby={$orderby}&amp;order={$order}&amp;page={$pages.prev}&amp;length={$pages.length}{$query}">
                            <i class="icon-caret-left"></i>
                        </a>
                    </li>
                {/if}
                {foreach from=$pages.links item=page}
                    <li>
                        {if $page == $pages.current}
                            <span class="page-numbers current">{$page}</span>
                        {else}
                            <a class="page-numbers" href="?orderby={$orderby}&amp;order={$order}&amp;page={$page}&amp;length={$pages.length}{$query}">{$page}</a>
                        {/if}
                    </li>
                {/foreach}
                {if $pages.current != $pages.last}
                    <li>
                        <a class="next page-numbers" href="?orderby={$orderby}&amp;order={$order}&amp;page={$pages.next}&amp;length={$pages.length}{$query}">
                            <i class="icon-caret-right"></i>
                        </a>
                    </li>
                {/if}
            </ul>
        </nav>
    </div>
    {/if}
</div>