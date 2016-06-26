$(document).ready(function(){
    countryChange();
    filter();
});


function countryChange() {

    var country = $('body').attr('lang');
    $(".country-change").hide();

    if ($("body").attr('lang', country).length) {
        $("." + country).fadeIn();
    }

    if ( $("select[name=country]").length) {

        $("select[name=country] option").each(function() {
            if ($(this).val() == $("body").attr('lang')) {
                $("select[name=country]").val($(this).val());
            }
        });

        $("select[name=country]").change(function() {
            $(".country-change").hide();
            $("select[name=country]").val($(this).val());
            $("body").removeAttr('lang').attr('lang', $(this).val());
            $("." + $(this).val()).fadeIn();
            return false;
        });

    } else {
        $("body").removeAttr('lang').attr('lang', $("input[name=country]").val());
    }
}

 function filter() {
    $('input[name="phone"]').on('keyup keypress', function(e) {
       if (e.keyCode == 8 || e.keyCode == 46) {}
       else
         {
           var letters=' 1234567890()-+';
                return (letters.indexOf(String.fromCharCode(e.which))!=-1);
         }
    });
}