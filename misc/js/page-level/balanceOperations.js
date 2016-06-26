jQuery(document).ready(function(){
  BalanceOperations.init();
});

var BalanceOperations = function () {
	
	var handleForm = function(){
		$(document).on('click', 'input[name="make_replenishment"]', function(){
			var form = $('#make_replenishmen_frm'),
				userIdField = form.find('select[name="user_id"]'),
				currencyField = form.find('select[name="country_code"]'),
				amountField = form.find('input[name="amount"]'),
				errors = [];
			
			form.find('.alert-danger').fadeOut();
			form.find('.form-group').removeClass('has-error');
			
			if (userIdField.find("option:selected").val() == -1) {
				errors.push("Необходимо выбрать пользователя");
				userIdField.closest('.form-group').addClass('has-error');
			}
			
			if (currencyField.find("option:selected").val() == -1) {
				errors.push("Необходимо выбрать валюту");
				currencyField.closest('.form-group').addClass('has-error');
			}
			
			if (parseInt(amountField.val()) <= 0) {
				errors.push("Сумма пополнения должна быть больше 0");
				amountField.closest('.form-group').addClass('has-error');
			}
			
			if (errors.length){
				form.find('.alert-danger').html(errors.join("<br />")).fadeIn();
				return false;
			}
		})
	}

  return {
      init: function () {
        handleForm();
      }
  };
}();