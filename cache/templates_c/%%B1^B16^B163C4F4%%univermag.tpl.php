<?php /* Smarty version 2.6.22, created on 2016-06-09 16:59:31
         compiled from admin%5Cbalance%5Cunivermag.tpl */ ?>
<div id="page-balance">

<h2 class="page-title">Баланс UM</h2>

<div class="page-bar">
  <ul class="page-breadcrumb">
    <li>
      <i class="fa fa-home"></i>
      <a href="/admin">Главная</a>
      <i class="fa fa-angle-right"></i>
    </li>
    <li>
      <a href="#">Баланс</a>
    </li>
  </ul>
</div>

<div class="portlet light">
  <div class="portlet-title">
    <div class="caption">
        Баланс
    </div>
    <div class="actions">
    </div>
  </div>
  <div class="portlet-body">
    <div class="row">
      <div class="col-md-5">
        <?php if ($this->_tpl_vars['success']): ?>
            <div class="alert alert-success">Баланс пополнен</div>
        <?php endif; ?>
        <!--Форма зачисления на баланс-->
        <form action="/admin/balance/" method="post" id="make_replenishmen_frm">
            <div class="alert alert-danger" style="display:none"></div>
            <input type="hidden" name="user_id" value="0">
            <div class="form-group">
                <label class="control-label">Валюта:</label>
                <div>
                    <select class="form-control" name="country_code">
                        <option value="-1">Выбор валюты</option>
                        <?php $_from = $this->_tpl_vars['countries']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['country']):
?>
                            <option value="<?php echo $this->_tpl_vars['country']['code']; ?>
"><?php echo $this->_tpl_vars['country']['currency_code']; ?>
</option>
                        <?php endforeach; endif; unset($_from); ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">Сумма:</label>
                <div>
                    <input type="text" class="form-control" name="amount" value="0">
                </div>
            </div>
            <input type="hidden" name="make_replenishment" value = "1">
            <input type="submit" name="submit" class="btn blue" value="Зачислить на баланс">
        </form>
      </div>
      <div class="col-md-7">
        <table class="table table-hover table-striped">
          <thead>
            <tr>
              <th></th>
              <th>Баланс</th>
              <th>Пополнение</th>
              <th>Заработок</th>
              <th>Холд</th>
              <th>Баланс (отклонено)</th>
              <th>Расход</th>
            </tr>
          </thead>
          <tbody>
            <?php $_from = $this->_tpl_vars['balance']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
              <tr>
                <td><i class="flag flag-<?php echo $this->_tpl_vars['item']->getCountryCode(); ?>
"></i></td>
                <td><?php echo $this->_tpl_vars['item']->getCurrent(); ?>
&nbsp;<?php echo $this->_tpl_vars['item']->getCurrencyCode(); ?>
</td>
                <td><?php echo $this->_tpl_vars['item']->getAccountBalance(); ?>
&nbsp;<?php echo $this->_tpl_vars['item']->getCurrencyCode(); ?>
</td>
                <td><?php echo $this->_tpl_vars['item']->getProfit(); ?>
&nbsp;<?php echo $this->_tpl_vars['item']->getCurrencyCode(); ?>
</td>
                <td><?php echo $this->_tpl_vars['item']->getHold(); ?>
&nbsp;<?php echo $this->_tpl_vars['item']->getCurrencyCode(); ?>
</td>
                <td><?php echo $this->_tpl_vars['item']->getCanceled(); ?>
&nbsp;<?php echo $this->_tpl_vars['item']->getCurrencyCode(); ?>
</td>
                <td><?php echo $this->_tpl_vars['item']->getExpense(); ?>
&nbsp;<?php echo $this->_tpl_vars['item']->getCurrencyCode(); ?>
</td>
              </tr>
          <?php endforeach; endif; unset($_from); ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  </div>