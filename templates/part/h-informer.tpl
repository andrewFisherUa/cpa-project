<div class="related-upsell">
    <div class="widget-1 widget-first widget  sw_woo_slider_content-7 sw_woo_slider_content">
        <div class="widget-inner">
            <div class="box-recommend-title">
                <h2>{$slider.title}</h2>
            </div>
            <div id="sw_woo_slider_{$slider.id}" class="sw-woo-container-slider">
                <div class="page-button">
                    <ul class="control-button">
                        <li class="preview"></li>
                        <li class="next"></li>
                    </ul>
                </div>
                <div class="slider cols-6 preset01-4 preset02-3 preset03-2 preset04-1 preset05-1 js-loaded">
                    <div class="vpo-wrap">
                        <div class="vp" style="height: 410px;">
                            <div class="vpi-wrap" style="left: 0px;">
                                {foreach from=$products item=product}
                                 <div class="item">
                                    <div class="item-wrap">
                                        <div class="item-img item-height">
                                            <div class="item-img-info products-thumb">
                                               <a href="{$shopUrl}/product/{$product.item->getId()}" title="{$product.item->getName()}">
                                                    {if $product.item->getDiscount()}
                                                        <span class="onsale onsale-o">-{$product.item->getDiscount()}%</span>
                                                    {/if}
                                                    <div {if $product.secondImage}class="product-thumb-hover"{/if}>
                                                        <img width="270" height="270" src="/misc/images/goods/{$product.item->getMainImage()}" class="attachment-shop_catalog wp-post-image" alt="">
                                                        {if $product.secondImage}
                                                        <img width="270" height="270" src="/misc/images/goods/{$product.secondImage}" class="hover-image back" alt="">
                                                        {/if}
                                                    </div>
                                                </a>
                                                {if $showOverview}
                                                 <a href="javascript:;" data-product="{$product.item->getId()}" data-shop="{$product.item->getShopId()}" class="quick-overview fancybox" title="Быстрый просмотр товара"> Подробнее </a>
                                                {/if}
                                            </div>
                                        </div>
                                        <div class="item-content">
                                            <h4>
                                               <a href="{$shopUrl}/product/{$product.item->getId()}" title="{$product.item->getName()}"> {$product.item->getName()} </a>
                                            </h4>
                                            {if $showScores}
                                                <div class="reviews-content">
                                                    <div class="star"></div>
                                                    <div class="item-number-rating">0 отзывов</div>
                                                </div>
                                            {/if}

                                            {$product.item->getPriceHTML()}

                                            {if $product.item->inStock()}
                                            <div class="item-bottom clearfix">
                                                <a href="javascript:;" rel="nofollow" data-product_name="{$product.item->getName()}" data-product="{$product.item->getId()}" title="Добавить в корзину" class="button add_to_cart_button {if $product.item->inCart()}added{/if}">{if $product.item->inCart()}В корзине{else}Купить{/if}</a>
                                                <div class="yith-wcwl-add-to-wishlist" data-product="{$product.item->getId()}">
                                                    <div class="yith-wcwl-add-button" {if !$product.inWishlist}style="display:block"{else}style="display:none"{/if}>
                                                       <a href="javascript:;" data-product="{$product.item->getId()}" class="add_to_wishlist"></a>
                                                    </div>
                                                    <div class="yith-wcwl-wishlistaddedbrowse" {if $product.inWishlist}style="display:block"{else}style="display:none"{/if}>
                                                        <span class="feedback">Товар добавлен!</span>
                                                        <a href="{$shopUrl}/wishlist">Просмотреть список желаний</a>
                                                    </div>
                                                    <div style="clear:both"></div>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                            {else}
                                                {$product.item->getStockHTML()}
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

