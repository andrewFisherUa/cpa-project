<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Новости и уведомления</h3>

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <i class="fa fa-home"></i>

            <a href="/admin">Главная</a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="#">Новости</a>
        </li>
    </ul>
</div>
<!-- END PAGE HEADER-->

<!-- START NEWS -->
<div class="row" id="news-wrapper">
    <div class="col-md-12">
        <!-- START VIEWED NEWS -->
        <div class="clapboard" id="news-main">
          <?php if($search_id_news) :?>
            <?php $url_for_icons = $icons_name_jpg[$news_top_url->getTypeId()]; ?>
               <div class="header_news_<?php echo $news_top_url->getId();?> top_header" >

                <div id="new_custom_<?php echo $news_top_url->getId();?>" class="timeline-body custom_bg bg_border_<?php echo $url_for_icons; ?> top_bg">

                  <a href="/admin/offers/view/<?php echo $news_top_url->getGoodId();?>" class="timeline-body-title font-blue-madison"><?php echo $news_top_url->getTitle(); ?></a>
                  <span class="timeline-body-time font-grey-cascade top_time"><?php echo date("d.m.Y H:i", $news_top_url->getActivateTime()); ?></span>

                  <div class="timeline-body-content top_content">
                      <span class="font-grey-cascade my_cascade top_content"><?php echo $news_top_url->getContent();?> </span>
                  </div>
                </div>

              </div>
          <?php endif;?>
        <!-- END VIEWED NEWS -->
<div class="timeline">
  <div id="content">

    <?php if (count($news)) : ?>

      <?php foreach ($news as $v) : ?>

        <?php $url_for_icons = $icons_name_jpg[$v->getTypeId()]; ?>

        <?php if($search_id_news ) :?>

           <div class="note timeline-item header_news_<?php echo $v->getId();?>" id="<?php echo $v->getId();?>">

                <div class="timeline-badge custom_basic basic_<?php echo $v->getId();?>">
                    <div id="timeline" class="timeline_basic timeline_<?php echo $url_for_icons; ?>"></div>
                </div>

                <div id="new_custom_<?php echo $v->getId();?>" class="timeline-body custom_bg bg_border_<?php echo $url_for_icons; ?> new_custom_<?php echo $v->getId();?>">

                    <div class="timeline-body-arrow custom_arrow_<?php echo $url_for_icons; ?>"> </div>
                    <div class="timeline-body-head">

                        <div class="timeline-body-head-caption">
                            <a href="/admin/offers/view/<?php echo $v->getGoodId();?>" class="timeline-body-title font-blue-madison"><?php echo $v->getTitle(); ?></a>
                            <span class="timeline-body-time font-grey-cascade"><?php echo date("d.m.Y H:i", $v->getActivateTime()); ?></span>
                        </div>

                    </div>

                    <div class="timeline-body-content">
                        <span class="font-grey-cascade my_cascade"> <?php echo $v->getContent();?> </span>
                    </div>
                </div>
            </div>

        <?php else: ?>

          <div class="note timeline-item header_news_<?php echo $v->getId();?>" id="<?php echo $v->getId();?>">

               <div class="timeline-badge custom_basic basic_<?php echo $v->getId();?>">
                   <div id="timeline" class="timeline_basic timeline_<?php echo $url_for_icons; ?>"></div>
               </div>

               <div id="new_custom_<?php echo $v->getId();?>" class="timeline-body custom_bg bg_border_<?php echo $url_for_icons; ?> new_custom_<?php echo $v->getId();?>">

                   <div class="timeline-body-arrow custom_arrow_<?php echo $url_for_icons; ?>"> </div>
                   <div class="timeline-body-head">

                       <div class="timeline-body-head-caption">
                           <a href="/admin/offers/view/<?php echo $v->getGoodId();?>" class="timeline-body-title font-blue-madison"><?php echo $v->getTitle(); ?></a>
                           <span class="timeline-body-time font-grey-cascade"><?php echo date("d.m.Y H:i", $v->getActivateTime()); ?></span>
                       </div>

                   </div>

                   <div class="timeline-body-content">
                       <span class="font-grey-cascade my_cascade"> <?php echo $v->getContent();?> </span>
                   </div>
               </div>
           </div>

          <?php endif;?>
      <?php endforeach; ?>
  </div>
</div>

    <div id="load">
      <div class="link_get_news">Еще</div>
      <img src="/misc/images/icons_type_news/loading.gif" id="imgLoad">
    </div>
      <?php else : ?>

          <?php if (!isset($main)) : ?>
            <div class="note note-info">Нет новостей</div>
          <?php endif;?>

      <?php endif;?>
    </div>
</div>

<div>
  <?php echo get_pagination("/admin/news/page", $page, $total); ?>
</div>
<!-- END NEWS -->
