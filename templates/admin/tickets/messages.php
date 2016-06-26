<h2 class="page-title">
    Тикет # <?php echo $ticket->getId(); ?> `<?php echo $ticket->getSubject();?>`
    <?php if ($ticket->isClosed()) : ?>
        <span class="label label-warning">Закрыт</span>
    <?php endif; ?>
</h2>

<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <a href="/admin"><i class="fa fa-home"></i></a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="/admin/tickets/">Тикеты</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
        <a href="#"><?php echo $ticket->getSubject();?></a>
    </li>
  </ul>
</div>

<?php if (!empty($success)) : ?>
<div class="alert alert-success"><?php echo $success;?></div>
<?php endif; ?>

<?php if ($ticket->isOpened()): ?>
<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
        Новое сообщение
    </div>
    <div class="actions"></div>
  </div>
  <div class="portlet-body">
    <form action="/admin/tickets/<?php echo $ticket->getId();?>" method="POST" id="reply-ticket-form" enctype="multipart/form-data">

        <div class="alert alert-danger form-alert" style="display:none"></div>

        <input type="hidden" name="ticket[ticket_id]" value="<?php echo $ticket->getId();?>">
        <input type="hidden" name="ticket[from_uid]" value="<?php echo $from_uid;?>">
        <input type="hidden" name="ticket[to_uid]" value="<?php echo $to_uid;?>">

        <div class="row">
            <div class="col-md-10 col-xs-9">
                <div class="row">
                    <div class="col-md-12">
                        <textarea name="ticket[message]" class="input-block-level form-control" placeholder="Написать сообщение" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-xs-3">
                <div class="row">
                    <div class="margin-bottom-5">
                        <button type="submit" name="add_message" class="btn btn-info">Отправить</button>
                    </div>
                    <input type="hidden" name="close_ticket" value="1">
                    <input type="hidden" name="ticket_id" value="<?php echo $ticket->getId();?>">
                    <button type="submit" name="close_ticket_btn" class="btn default">Закрыть тикет</button>
                </div>
            </div>
        </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php if ($ticket->isClosed() && !User::isAdmin()) : ?>
<div class="portlet light">
  <div class="portlet-body">
        <form action="/admin/tickets/<?php echo $ticket->getId();?>" method="POST" id="reply-ticket-form" enctype="multipart/form-data" novalidate="novalidate">
        <div class="row">
            <div class="col-md-12">
                <input type="hidden" name="open_ticket" value="1">
                <input type="hidden" name="ticket_id" value="<?php echo $ticket->getId();?>">
                <button type="submit" name="open_ticket_btn" class="btn btn-info">Открыть тикет</button>
            </div>
        </div>
    </form>
  </div>
</div>
<?php endif; ?>

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
        Сообщения
    </div>
    <div class="actions"></div>
  </div>
  <div class="portlet-body">
    <div class="tickets-messages">
        <?php foreach ($ticket->getMessages() as $item) : ?>
        <div class="general-item-list">
            <div class="item <?php echo ($item->sentBySupport()) ? "item-support" : "item-user";?>">
                <div class="item-head">
                    <div class="item-details">
                        <span class="item-label pull-right">
                            <?php if ($item->hasAttachments()) : ?>
                                <i class="glyphicon glyphicon-paperclip"></i>
                            <?php endif;?>
                            <?php echo $item->getCreated(true); ?>
                        </span>
                        <span class="item-name"><?php echo $item->getSenderName();?></span>
                    </div>
                </div>
                <div class="item-body">
                    <?php echo $item->getMessage(); ?>
                </div>

                <?php if ($item->hasAttachments()) : ?>
                    <div class="item-attachments">

                        <?php foreach ($item->getAttachments() as $a) : ?>
                        <div>
                            <div class="a-image">
                            <?php if ((end(explode(".", $a))) == "zip") : ?>
                                <img src="/misc/uploads/zip-icon.png" alt="<?php echo $a;?>">
                            <?php else : ?>
                                <a class="fancybox" rel="gallery-<?php echo $item->getId();?>" href="<?php echo $ticket->getAttachmentsFolder() . $a;?>">
                                    <img src="<?php echo $ticket->getAttachmentsFolder() . $a;?>" alt="<?php echo $a;?>">
                                </a>
                            <?php endif;?>
                            </div>
                            <div class="a-caption">
                                <?php echo $a;?>
                                <?php if ((end(explode(".", $a))) == "zip") : ?>
                                    <a class="pull-right" alt="Download" href="<?php echo $ticket->getAttachmentsFolder() . $a;?>"><i class="glyphicon glyphicon-download-alt"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach;?>
                    </div>
                <?php endif;?>
            </div>
        </div>
        <?php endforeach;?>
    </div>
  </div>
</div>




