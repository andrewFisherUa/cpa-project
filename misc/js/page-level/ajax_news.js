// ajax load news
$(document).ready(function(){
	$("#imgLoad").hide();
});

var num = 10;

$(function() {
	$("#load div").click(function(){

		$("#imgLoad").show();

		$.ajax({
			url: "/ajax/action",
			type: "GET",
			data: {"num": num},
			cache: false,
			success: function(response){

				if(response == 0 ){
					$('#load').css('display', 'none');
					$("#imgLoad").hide();
				}
				else{
					$("#content").append(response);
					num = num + 10;
					$("#imgLoad").hide();
				}
      }
		});
	});
});
