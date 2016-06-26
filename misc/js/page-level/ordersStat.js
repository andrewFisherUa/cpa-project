jQuery(document).ready(function(){
  OrdersStat.init();
});

var OrdersStat = function () {

  var grid;

  // Управление таблицей потоков
  var handleDatatable = function(){
    grid = new Datatable();

    grid.init({
      src: $("#datatable_order_stat"),
      loadingMessage: 'Загрузка...',
      dataTable: {
          "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
          "language": datatables_defaults.lang,
          "pageLength": 50, // default record count per page
          "lengthMenu": [
              [10, 25, 50, 100, 150, -1],
              [10, 25, 50, 100, 150, "Все"] // change per page values here
          ],
          "ajax": {
              "url": '/ajax/get-orders-stat/', // ajax source
              data: function (d) {
                var data = {
                  range: {
                    from : $('.input-daterange > input[name="from"]').val(),
                    to : $('.input-daterange > input[name="to"]').val()
                  },
                  filters : {
                    subid : {}
                  }
                };

                var temp;

                $('#datatable_stats_wrapper select.filter-select').each(function(){
                  temp = $(this).select2('val');

                  if (temp) {
                    if ($(this).hasClass('subid')) {
                      data.filters.subid[$(this).attr('name')] = temp;
                    } else {
                      data.filters[$(this).attr('name')] = temp;
                    }
                  }
                })

                var status = $('#status-filter option:selected').val();

                if (status != "-1") {
                  data.filters.status = status;
                }

                var status2_name = $('#status2-filter option:selected').val();

                if (status2_name != "-1") {
                  data.filters.status2_name = status2_name;
                }

                console.log(data);

                d.filter_data = data;
              },
          },
          preDrawCallback: function( settings ) {
            $('#datatable_order_stat tbody').css('opacity', '.5');
          },
          drawCallback: function(oSettings) { // run some code on table redraw
            $('#datatable_order_stat tbody').css('opacity', '1');
          },
          "bSort" : false
      },
    });
  }

  var handlePickers = function(){

    $(document).on('click', '.fixed-range-item button', function(){
      var span = $(this).attr('data-shortcut');

       $.ajax({
        url: '/ajax/get-range/',
        type: "POST",
        dataType: 'json',
        data: "range="+span,
        success: function(result){
          $('#datatable_stats_wrapper input[name="from"]').val(result.range.from);
          $('#datatable_stats_wrapper input[name="to"]').val(result.range.to);
          grid.getDataTable().ajax.reload();
        }
      });

    })

    $('.input-daterange > input').datepicker({
      format: 'dd-mm-yyyy'
    }) .on("changeDate", function(e) {
      grid.getDataTable().ajax.reload();
    });
  }

  var handlePopover = function(){

    // init select2
    $("#datatable_stats_wrapper .filter-select").each(function(){
      $(this).select2({
        tags: true
      })
    })

    $(document)
      .on('click', '.popdown .toggler', function(){
        var p = $(this).siblings('.popover');
        var li = $(this).closest('.filter-item').attr('id');

        p.toggleClass('hide');

        $('.filter-item:not(#'+li+') .popover').each(function(){
            $(this).addClass('hide');
        })

      })
      // Закрытие попапа
      .on('click', '.close-popdown', function(){
        $(this).closest('.popover').addClass('hide');
      })
      // Применение настроек
      .on('click', '.popdown .apply', function(){
        var l = 0, t;
        var p = $(this).closest('.popdown');

        p.find('select').each(function(){
          t = $(this).select2("val");
          if (t) {
            l += t.length;
          }
        })

        if (l == 0) {
          p.find('.toggler .count').hide();
        } else {
          p.find('.toggler .count').text("("+l+")").show();
        }

        $(this).closest('.popover').addClass('hide');
      })

      // Применение настроек
      .on('click', '.popdown .clear', function(){
        var l = 0, t;
        var p = $(this).closest('.popdown');

        p.find('select').each(function(){
          $(this).select2("val", "");
        })

        p.find('.toggler .count').hide();
      })
  }

  var handleFilters = function(){
    $(document).on('click', '#datatable_stats_wrapper #filter-submit', function(){
      grid.getDataTable().ajax.reload();
      return false;
    })

    $(document).on('click', '#datatable_stats_wrapper #filter-reset', function(){
      $('#datatable_stats_wrapper select.filter-select').each(function(){
        $(this).select2('val', {});
      })

      $('#datatable_stats_wrapper .form-item .preview > .count').hide();

      grid.getDataTable().ajax.reload();
    })

    $(document).on('change', '#datatable_order_stat .filter', function(){
      grid.getDataTable().ajax.reload();
      return false;
    })
  }


  return {
      init: function () {
        handleFilters();
        handlePopover();
        handlePickers();
        handleDatatable();
      }
  };
}();