<?php /* Smarty version 2.6.22, created on 2016-06-09 15:46:39
         compiled from admin%5Cflows%5Cajax%5Cprices.tpl */ ?>
<?php if ($this->_tpl_vars['prices']): ?>
	<table class="table table-hover table-bordered" id="table_prices">
		<thead>
			<tr>
				<th width="20%">Страна</th>
				<th width="30%">Цель</th>
				<th width="20%">Рекомендуемая цена</th>
				<th width="15%">Цена</th>
				<th width="15%">Заработок</th>
			</tr>
		</thead>
		<tbody>
			<?php $_from = $this->_tpl_vars['prices']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['country_code'] => $this->_tpl_vars['item']):
?>
				<tr>
					<td><span class="flag flag-<?php echo $this->_tpl_vars['country_code']; ?>
"></span> <?php echo $this->_tpl_vars['item']['country_name']; ?>
</td>
					<td>
						<select class="target form-control" data-code="<?php echo $this->_tpl_vars['country_code']; ?>
">
							<?php $_from = $this->_tpl_vars['item']['targets']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['t'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['t']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['target']):
        $this->_foreach['t']['iteration']++;
?>
								<option value="<?php echo $this->_tpl_vars['target']->getId(); ?>
"
									data-webmaster_commission="<?php echo $this->_tpl_vars['target']->getWebmasterCommission(); ?>
"
									data-price="<?php echo $this->_tpl_vars['item']['recommended']; ?>
"
									data-editable="<?php echo $this->_tpl_vars['canEdit']; ?>
"
									data-max_profit="<?php echo $this->_tpl_vars['target']->getMaxProfit(); ?>
"
									<?php if ($this->_tpl_vars['target']->getId() == $this->_tpl_vars['item']['target_id']): ?> selected<?php endif; ?>
									><?php echo $this->_tpl_vars['target']->getName(); ?>

								</option>
							<?php endforeach; endif; unset($_from); ?>
						</select>
					</td>
					<td><span class="recommended" data-code="<?php echo $this->_tpl_vars['country_code']; ?>
"><?php echo $this->_tpl_vars['item']['recommended']; ?>
</span> <?php echo $this->_tpl_vars['item']['currency']; ?>
</td>
					<td><span class="price" data-code="<?php echo $this->_tpl_vars['country_code']; ?>
"><?php echo $this->_tpl_vars['item']['price']; ?>
</span> <?php echo $this->_tpl_vars['item']['currency']; ?>
</td>
					<td>
						<div class="input-inline input-medium">
							<input id="profit-<?php echo $this->_tpl_vars['country_code']; ?>
" type="text" value="<?php echo $this->_tpl_vars['item']['profit']; ?>
" name="profit" class="form-control profit" data-code="<?php echo $this->_tpl_vars['country_code']; ?>
" data-currency="<?php echo $this->_tpl_vars['item']['currency']; ?>
" data-max="<?php echo $this->_tpl_vars['item']['max_profit']; ?>
" <?php if ($this->_tpl_vars['item']['target_id'] == 1 && ! $this->_tpl_vars['canEdit']): ?>disabled<?php endif; ?>>
						</div>
					</td>
				</tr>
			<?php endforeach; endif; unset($_from); ?>
		</tbody>
	</table>
<?php else: ?>
	<div class="note note-info">
		<p>У этого оффера нет цен</p>
	</div>
<?php endif; ?>