<!-- BEGIN PAGE HEADER-->
<h3 class="page-title">Балансовые операции</h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
          <i class="fa fa-home"></i>
          <a href="/admin">Главная</a>
          <i class="fa fa-angle-right"></i>
        </li>
        <li>
          <a href="#">Взаиморасчеты</a>
		  <i class="fa fa-angle-right"></i>
        </li>
		<li>
          <a href="#">Балансовые операции</a>
        </li>
    </ul>
</div>
<!-- END PAGE HEADER-->


<div class="row">
	<div class="col-md-6">
		<div class="portlet light">
		  <div class="portlet-title">
			<div class="caption">
			  Пополнение баланса
			</div>
			<div class="actions">
			</div>
		  </div>
		  <div class="portlet-body">
			<?php if ($success) : ?>
				<div class="alert alert-success">Баланс пополнен</div>
			<?php endif; ?>
			<!--Форма зачисления на баланс-->
			<form action="/admin/balance_operations/" method="post" id="make_replenishmen_frm">
				<div class="alert alert-danger" style="display:none"></div>
				<div class="form-group">
					<label class="control-label">Пользователь:</label>
					<div>
						<select class="form-control select2me" name="user_id">
							<option value="-1">Выбор пользователя</option>
							<?php foreach($users as $user) : ?>
								<option value="<?=$user['user_id'];?>"><?=$user['user_id'];?>: <?=$user['login'];?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label">Валюта:</label>
					<div>
						<select class="form-control" name="country_code">
							<option value="-1">Выбор валюты</option>
							<?php foreach($countries as $item): ?>
								<option value="<?=$item['code'];?>"><?=$item['currency_code'];?></option>
							<?php endforeach;?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label">Сумма:</label>
					<div>
						<input type="text" class="form-control" name="amount" value="0">
					</div>
				</div>
				<input type="hidden" name="make_replenishment" value="1">
				<input type="submit" name="submit" class="btn blue" value="Зачислить на баланс">
			</form>
		  </div>
		</div>
	</div>
	</div>
	<div class="col-md-6">

	</div>
</div>
