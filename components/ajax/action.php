<?php

header("Content-type: text/html; charset=utf-8");

 if(isset($_GET['num'])){
  	 $num = $_GET['num'] ;
     $news = News::getAll($GLOBALS['DB'], $params, $num , 10);
?>
        <?php if (count($news)) : ?>
          <?php
          $icons_name_jpg = array(
                      1 => 'new_offer',     2 => 'offera_suspension',
                      3 => 'offer_change',  4 => 'new_landing',
                      5 => 'system_news',   6 => 'important'
                    );
          ?>
          <?php foreach ($news as $v) : ?>

            <?php $url_for_icons = $icons_name_jpg[$v->getTypeId()]; ?>

                <div class="note timeline-item header_news_<?php echo $v->getId();?>" id="<?php echo $v->getId();?>">
                    <div class="timeline-badge custom_basic basic_<?php echo $v->getId();?>">
                        <div id="timeline" class="timeline_basic timeline_<?php echo $url_for_icons; ?>"></div>
                    </div>
                    <div id="new_custom_<?php echo $v->getId();?>" class="timeline-body custom_bg bg_border_<?php echo $url_for_icons; ?> new_custom_<?php echo $v->getId();?>">
                        <div class="timeline-body-arrow custom_arrow_<?php echo $url_for_icons; ?>"> </div>
                        <div class="timeline-body-head">
                            <div class="timeline-body-head-caption">
                                <a href="javascript:;" class="timeline-body-title font-blue-madison"><?php echo $v->getTitle(); ?></a>
                                <span class="timeline-body-time font-grey-cascade"><?php echo date("d.m.Y H:i", $v->getActivateTime()); ?></span>
                            </div>
                        </div>
                        <div class="timeline-body-content">
                            <span class="font-grey-cascade my_cascade"> <?php echo $v->getContent();?> </span>
                        </div>
                    </div>
                </div>
                <hr />
          <?php endforeach; ?>
        <?php endif; ?>
<?php
}
