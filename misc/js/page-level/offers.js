jQuery(document).ready(function(){
  Offers.init();
});

var Offers = function () {

    var grid;

    var applyFilters = function(){
      $('.table-group-action-input').each(function(){
        var actionValue = $(this).find('option:selected').val();
        if ( actionValue != '-1' ) {
          var actionName = $(this).attr('name');
          grid.setAjaxParam(actionName, actionValue);
        }
      })
    }

    var addUserGood = function(id, modal) {
      var data = {
        "action" : "add",
        "g_id" : id
      }

      $.ajax({
        url: '/ajax/add-user-good/',
        dataType: 'json',
        type: 'POST',
        data: $.param(data),
        success: function(result){
          grid.getDataTable().ajax.reload();
          if (modal) {
            $('#rulesModal').modal('hide');
          }
        }
      })
    }

    var handleOffers = function () {

      if ( $("#datatable_offers").data("records") == "user" ) {
        url = "/ajax/get-user-offers/";
      } else {
        url = "/ajax/get-offers/";
      }

      grid = new Datatable();

      grid.init({
          src: $("#datatable_offers"),
          loadingMessage: 'Загрузка...',
          dataTable: {
              "dom": "<'row'<'col-md-12'<'table-group-actions'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
              "language": datatables_defaults.lang,
              "pageLength": 50, // default record count per page
              "columnDefs": [ { "targets": 0, "orderable": false },
                              { "targets": 4, "orderable": false },
                              { "targets": 5, "orderable": false } ],
              "ajax": {
                  "url": url, // ajax source
              },
              fnDrawCallback: function( oSettings ) {
                handleOfferStatus();
                $('select[name="id"]').select2();
                console.log($('.disabled-offer').length);
                $('.disabled-offer').closest("tr").find("td").addClass("disabled");
              }
          },
      });

      grid.getDataTable().on('page.dt', function () {
          applyFilters()
      });

      // handle group actionsubmit button click
      grid.getTableWrapper()
        // Обработка изменения значения фильтров
        .on('change', '.table-group-action-input', function () {
          $('.table-group-action-input').each(function(){
            applyFilters();
            grid.getDataTable().ajax.reload();
            grid.clearAjaxParams();
          })
        })
        // Сброс фильтров
        .on('click', '.reset-filters', function () {
          $('.table-group-action-input').each(function(){
            $(this).find('option:first-child').prop('selected', true);
          })
          grid.getDataTable().ajax.reload();
          grid.clearAjaxParams();
        })

        // Включение оффера
        .on('click', '.add-user-good', function(){
          if ($(this).attr('data-rules') == "0") {
            addUserGood($(this).attr('data-g_id'), false);
            return false;
          }

          var data = {
            action : "get-rules",
            id : $(this).attr('data-g_id')
          };

           $.ajax({
            url: '/ajax/manage-offer-rules/',
            type: 'POST',
            dataType: 'json',
            data: $.param(data),
            success: function(response){
              $('#rulesModal #add-offer').attr('data-g_id', data.id);
              $('#rulesModal #rules-wrap').html(response.text);
              $('#rulesModal').modal('show');
            }
          })
        })

        // Включение / отключение оффера
        .on('click', '.remove-user-good', function(){
          if ($(this).hasClass('remove-user-good') && confirm("Вы действительно хотите отключить оффер?")) {
              handleUserOffers( $(this) );
              $('.table-group-action-input').each(function(){
                var actionValue = $(this).find('option:selected').val();
                if ( actionValue != '-1' ) {
                  var actionName = $(this).attr('name');
                  grid.setAjaxParam(actionName, actionValue);
                }
              })

              grid.getDataTable().ajax.reload();
              grid.clearAjaxParams();
          }
        })

        $(document).on('click', '#rulesModal #add-offer', function(){
          addUserGood($(this).attr('data-g_id'), true);
          return false;
        })
      }

    var handleOfferStatus = function(){
      $('.status').each(function(){
        var id = $(this).data('subject');

        $('#status'+id).editable({
          inputclass: 'form-control',
          source: [ {value: "moderation", text: 'На модерации'},
                    {value: "active", text: 'Активирован'},
                    {value: "disabled", text: 'Отключен'},
                    {value: "archive", text: 'Архив'}],
          autotext: 'always',
          params: { 'id': id },
          url: '/ajax/save-offer-status/',
          success: function(result){
            $('#status'+id).parent().removeAttr('class').addClass('label label-sm label-'+result);
          }
      });

      })
    }

    return {
        //main function to initiate the module
        init: function () {
            handleOffers();
        }
    };
}();