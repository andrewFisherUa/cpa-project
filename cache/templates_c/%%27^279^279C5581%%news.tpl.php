<?php /* Smarty version 2.6.22, created on 2016-06-09 15:45:00
         compiled from email_templates%5Cnews.tpl */ ?>
<p>Здравствуйте, уважаемый партнер!</p>
<p><?php echo $this->_tpl_vars['content']; ?>
</p>
<?php if ($this->_tpl_vars['good_id']): ?>
	<a href='<?php echo $this->_tpl_vars['site_url']; ?>
/admin/offers/view/<?php echo $this->_tpl_vars['good_id']; ?>
'>Перейти к офферу</a>
<?php endif; ?>
<p>Отписаться от уведомлений Вы можете в своем кабинете!</p>
<p>С уважением,<br>Команда Univer-Mag</p>