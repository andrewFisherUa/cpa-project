<?php

$page_name = ($stream->getId() == 0) ? "Создание потока" : "Редактирование потока `{$stream->getName()}`";

?>

<!-- BEGIN PAGE HEADER-->
<h3 class="page-title"><?php echo $page_name;?></h3>

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <i class="fa fa-home"></i>
            <a href="/admin">Главная</a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="/admin/api/">API</a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">
            <?php echo $page_name;?>
          </a>
        </li>
    </ul>
</div>
<!-- END PAGE HEADER-->

<div class="portlet light">
    <div class="portlet-title">
        <div class="caption">
          <?php echo $page_name;?>
        </div>
    </div>
    <div class="portlet-body">
      <div id="edit-stream-form">
        <div class="alert alert-danger" style="display:none;"></div>

        <input type="hidden" name="stream_id" class="form-field" value="<?php echo $stream->getId(); ?>">

        <div class="form-group">
          <label for="" class="control-label">Название потока: <span class="required">*</span></label>
          <div>
            <input type="text" class="form-control form-field" name="stream_name" value="<?php echo $stream->getName();?>">
          </div>
        </div>

          <div class="form-group">
            <label for="" class="control-label">Оффер: <span class="required">*</span></label>

            <?php if ($stream->getId() == 0) : ?>
            <select name="stream_oid" class="form-control select2me form-field">
              <option value="0">Выберите оффер</option>
              <?php foreach ($offers as $a) : ?>
                <option value="<?php echo $a["id"];?>">#<?php echo $a["id"];?>: <?php echo $a["name"];?></option>
              <?php endforeach; ?>
            </select>
            <?php else : ?>
              <input type="text" class="form-control" value="<?php echo $stream->getOfferId() . ': ' . $stream->getOfferName();?>">
            <?php endif; ?>
          </div>
        
        <div class="form-group" id="key_wrap" <?php if ($stream->getId() == 0) : ?>style="display:none"; <?php endif;?>>
          <label for="" class="control-label">Ключ: <span class="required">*</span></label>
          <div class="input-group">
            <input type="text" class="form-control" id="stream_key" value="<?php echo $stream->getKey();?>" disabled="">
            <span class="input-group-btn">
              <button class="btn green" type="button" id="copy_key_btn"><i class="fa fa-copy"></i></button>
           </span>
          </div>
        </div>

        <div id="prices-wrap">
          <?php if ($stream->getId() > 0) : ?>
            <?php echo $prices_html;?>
          <?php endif; ?>
        </div>

        <div class="form-group clearfix">
          <button id="save-stream" class="btn green">Сохранить поток</button>
        </div>
    </div>
  </div>
</div>