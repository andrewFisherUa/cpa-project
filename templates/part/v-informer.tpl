<div class="widget-2 widget ya_best_seller_product-2 ya_best_seller_product">
    <div class="widget-inner">
        <h3>Hot Price</h3>
        <div id="ya_best_seller_product-2" class="sw-best-seller-product">
            <ul class="list-unstyled">
                {foreach from=$products item=product}
                <li class="clearfix">
                    <div class="item-img">
                        <a href="{$shopUrl}/product/{$product->getId()}" title="{$product->getName()}">
                            <img width="90" height="90" src="/misc/images/goods/{$product->getMainImage()}" class="attachment-shop_thumbnail wp-post-image" alt="">
                        </a>
                    </div>
                    <div class="item-content">
                        <h4>
                            <a href="{$shopUrl}/product/{$product->getId()}" title="{$product->getName()}">{$product->getName()}</a>
                        </h4>
                        <div class="price"><span class="amount">{$product->getPriceHTML()}</span></div>
                        {if $showScores}
                            {$product->getRatingHTML()}
                        {/if}
                        <div class="review">
                            <span>{$product->getCommentsCount()} отзывов </span>
                        </div>
                    </div>
                </li>
                {/foreach}
            </ul>
        </div>
    </div>
</div>