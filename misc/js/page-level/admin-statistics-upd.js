jQuery(document).ready(function(){
  var $ = jQuery;

  Statistics.init();
});

var Statistics = function () {

  var handleDatatable = function(){
    grid = new Datatable();

    grid.init({
      src: $("#datatable_stats"),
      loadingMessage: 'Загрузка...',
      dataTable: {
          "language": datatables_defaults.lang,
          "pageLength": 50, // default record count per page
          "lengthMenu": [
              [10, 25, 50, 100, 150, -1],
              [10, 25, 50, 100, 150, "Все"] // change per page values here
          ],
          "ajax": {
              "url": '/ajax/get-admin-statistics-upd/', // ajax source
              data: function (d) {
                var data = {
                  range: {
                    from : $('#datatable_stats_wrapper .input-daterange > input[name="from"]').val(),
                    to : $('#datatable_stats_wrapper .input-daterange > input[name="to"]').val()
                  },
                  filters : {
                    subid : {},
                    order_type : []
                  }
                };

                var sorting = $('#datatable_stats th.sorting-asc, #datatable_stats th.sorting-desc');
                data.sort_by = sorting.attr('data-sort-by');

                if (sorting.hasClass('sorting-asc')) {
                  data.sort_order = "asc";
                } else {
                  data.sort_order = "desc";
                }

                var temp;

                var group = $('#datatable_stats_wrapper .grouping-item.active > button');
                data.group_by = group.data('group-by');

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

                $('#datatable_stats_wrapper :checkbox[name="order_type"]:checked').each(function(){
                  data.filters.order_type.push($(this).val());
                })

                var uid = $('#datatable_stats').data('user');
                if (uid) {
                  data.uid = uid;
                }

                d.filter_data = data;

                console.log(data);
              },
          },
          preDrawCallback: function( settings ) {
            $('#datatable_stats tbody').css('opacity', '.5');
          },
          drawCallback: function(oSettings) { // run some code on table redraw
            $('#datatable_stats tbody').css('opacity', '1');
            $('#datatable_stats .heading > th:first-child').text($('.grouping-item.active .btn').attr('data-text'));
            $('#datatable_stats .f').mask('# ### ### ### ###', {reverse: true});
          },
          "bSort" : false
      },
    });
  }

  var handleSource = function(){
    $(document).on("change", '#datatable_stats_wrapper :checkbox[name="order_type"]', function(){
      var value = $(this).val();
      if ($('#datatable_stats_wrapper :checkbox[name="order_type"]:checked').length == 0) {
        $('#datatable_stats_wrapper :checkbox[name="order_type"][value!='+value+']').prop("checked", true).uniform();
      }
    })
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
      .on('click', '.close-popdown', function(){
        $(this).closest('.popover').addClass('hide');
      })
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

      .on('click', '.popdown .clear', function(){
        var l = 0, t;
        var p = $(this).closest('.popdown');

        p.find('select').each(function(){
          $(this).select2("val", "");
        })

        p.find('.toggler .count').hide();
      })
  }

  var handleGrouping = function(){
    $(document).on('click', '.grouping-item button', function(){
      $('.grouping-item').removeClass('active');
      $('.grouping-item button').removeClass('green-meadow').addClass('default');
      $(this).parent().addClass('active');
      $(this).removeClass('default').addClass('green-meadow');
    })

    $(document).on('click', '.fixed-range-item button', function(){
      grid.getDataTable().ajax.reload();
    })
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

    $('#datatable_stats_wrapper .input-daterange > input').datepicker({
      format: 'dd-mm-yyyy'
    }) .on("changeDate", function(e) {
      grid.getDataTable().ajax.reload();
    });
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
      $('#datatable_stats_wrapper .grouping-item').removeClass('active');
      $('#datatable_stats_wrapper .grouping-item button').removeClass('green-meadow');
      $('#datatable_stats_wrapper .grouping-item button.date').addClass('green-meadow');
      $('#datatable_stats_wrapper .grouping-item button.date').parent().addClass('active');

      grid.getDataTable().ajax.reload();
    })
  }

  var handleChooseWebmaster = function(){
    $('#choose-webmaster').select2();

    $(document).on('change', '#choose-webmaster', function(){
      var id = $(this).select2("val");
      if (id) {
        $('#show-webmaster-stat')
          .attr('href', '/admin/admin_statistics/webmaster/'+id)
          .attr('disabled', false);
      } else {
        $('#show-webmaster-stat').attr('disabled', true);
      }

      return false;
    })
  }

  var handleColumnsSorting = function(){
    $(document)
      .on('click', '#datatable_stats th.sorting', function(){
        $('#datatable_stats th.sorting-asc, #datatable_stats th.sorting-desc').removeClass('sorting-asc sorting-desc').addClass('sorting');
        $(this).removeClass('sorting').addClass('sorting-asc');
        grid.getDataTable().ajax.reload();
      })

      .on('click', '#datatable_stats th.sorting-asc', function(){
        $('#datatable_stats th.sorting-asc, #datatable_stats th.sorting-desc').removeClass('sorting-asc sorting-desc').addClass('sorting');
        $(this).removeClass('sorting').addClass('sorting-desc');
        grid.getDataTable().ajax.reload();
      })

      .on('click', '#datatable_stats th.sorting-desc', function(){
        $('#datatable_stats th.sorting-asc, #datatable_stats th.sorting-desc').removeClass('sorting-asc sorting-desc').addClass('sorting');
        $(this).removeClass('sorting').addClass('sorting-asc');
        grid.getDataTable().ajax.reload();
      })
  }

  return {
      init: function () {
        handlePickers();
        handlePopover();
        handleGrouping();
        handleFilters();
        handleChooseWebmaster();
        handleColumnsSorting();
        handleDatatable();
        handleSource();
      }
  };
}();