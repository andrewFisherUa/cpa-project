(function( $ ) {
    var methods = {
        init : function( options ) {
            var settings = $.extend( {
                'max_files': 5,
                'input_name': 'attachments',
                ext: ['jpg', 'png','zip']
            }, options);

            return this.each(function() {
                var $this = $(this),
                    data = $this.data('multiInputFile');

                if ( !data ) {
                    $(this).data('multiInputFile', {
                        target: $this,
                        settings: settings,
                        inputs_count: 0
                    });
                }

                methods.genInputFile.apply(this);

            });
        },
        'genInputFile': function() {
            var $this = $(this),
                data = $this.data('multiInputFile');

            if( data.inputs_count < data.settings.max_files ) {
                var input =
                    $('<div class="'+((data.inputs_count > 0)?'innerT ':'')+'btn-file-group">'+
                        '<span class="btn-group margin-bottom-5">'+
                            '<span class="btn btn-success btn-file margin-right-none">'+
                                '<i class="icon-paper-clip"> </i><span> Прикрепить</span>'+
                                '<input type="file" name="'+data.settings.input_name+(data.settings.max_files ? '[]' : '')+'" />'+
                            '</span>'+
                            '<span class="btn btn-danger hide btn-file-remove">'+
                                '<i class="fa fa-times"></i>'+
                            '</span>'+
                        '</span>'+
                    '</div>');

                $('input[type=file]', input).change(function(event) {
                    var bg = $( '.btn-group', input);
                    bg.next().remove();
                    var value = $(this).val();
                    var fileName = value.replace(/\\/g, "/");
                    fileName = fileName.substring(fileName.lastIndexOf('/')+1);
                    var fileExt = fileName.split('.').pop().toLowerCase();

                    if( data.settings.ext.indexOf(fileExt) == -1 ) {
                        $(this).val('');
                        var ext = [];
                        for ( var i=0; i<data.settings.ext.length; i++ ) {
                            console.log(data.settings.ext[i]);
                            ext.push( data.settings.ext[i].toUpperCase() );
                        }
                        ext = ext.join(", ");
                        $('<span class="text-danger"> <i class="icon-ban-circle"> </i>  Файл должен быть в одном из форматов: '+ext+' </span>').insertAfter(bg);
                        return false;
                    }
                    $( '.btn-file-remove', bg).removeClass('hide');
                    $('<span> <i class="icon-file icon-' + fileExt + '"> </i> ' + fileName + '</span>').insertAfter(bg);
                    if(!this.input_set) {
                        this.input_set = true;
                        $( 'span', $(this).parent()).html(' Изменить');
                        data.inputs_count++;
                        methods.genInputFile.apply($this);
                    }
                });

                $(".btn-file-remove" ,input).click(function(event){
                    data.inputs_count--;
                    input.remove();
                    if(data.inputs_count == (data.settings.max_files - 1)) {
                        methods.genInputFile.apply($this);
                    }
                });
            }
            $this.append(input);
        }
    };

    $.fn.multiInputFile = function(method) {
        if ( methods[method] ) {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' not found in jQuery.multiInputFile' );
        }
    };
})(jQuery);