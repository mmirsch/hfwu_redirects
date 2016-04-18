/*
(function($) {
    $.fn.bindWithDelay = function( type, data, fn, timeout, throttle ) {
        if ( $.isFunction( data ) ) {
            throttle = timeout;
            timeout = fn;
            fn = data;
            data = undefined;
        }
        fn.guid = fn.guid || ($.guid && $.guid++);

        return this.each(function() {
            var wait = null;
            function cb() {
                var e = $.extend(true, { }, arguments[0]);
                var ctx = this;
                var throttler = function() {
                    wait = null;
                    fn.apply(ctx, [e]);
                };

                if (!throttle) { clearTimeout(wait); wait = null; }
                if (!wait) { wait = setTimeout(throttler, timeout); }
            }

            cb.guid = fn.guid;

            $(this).bind(type, data, cb);
        });
    };
})(jQuery);


function executeAjax(url, dataType){
    var result=''
    $.ajax({
        url: url,
        dataType: dataType,
        async: false,
        beforeSend : function(){
            processingAnimation('start','bitte warten');
        },
        success: function(data) {
            result = data;
        }
    }).always(function() {
        processingAnimation('stop');
    });
    return result;
}

function processingAnimation(mode,message) {
    var aHeight = $(window).height();
    var aWidth = $(window).width();

    if (mode=='start') {
        if ($('#spinOverlay').size()==0) {
            $('body').append('<div id='spinOverlay'></div>');
            $('#spinOverlay').css('height', aHeight).css('width', aWidth);
            if (message) {
                $('#spinOverlay').append('<div id='spinOverlayMessage'>' + message + '</div>');
                var left = Math.ceil((aWidth - $('#spinOverlayMessage').width()) / 2);
                var top = Math.ceil((aHeight - $('#spinOverlayMessage').height()) / 2)+30;
                $('#spinOverlayMessage').css('left', left).css('top', top);
            }
        }
        $('#spinOverlay').show();
    } else if (mode=='stop') {
        if ($('#spinOverlay')) {
            $('#spinOverlay').remove();
        }
        if ($('#spinOverlayMessage')) {
            $('#spinOverlayMessage').remove();
        }
    }
}

*/


function aliasListAjax(filter, isQrCode, listId) {
    var ajaxUrl = TYPO3.settings.ajaxUrls['HfwuRedirects::aliasList'];
    TYPO3.jQuery.ajax({
        url: ajaxUrl,
        type: 'GET',
        dataType: 'html',
        data: {
            filter: filter,
            is_qr_code: isQrCode
        },
        success: function (result) {
            TYPO3.jQuery(listId).html(result);
        },
        error: function (error) {
            TYPO3.jQuery(listId).html(error);
        }
    });
}

function aliasDeleteAjax(id, filter, isQrCode, listId) {
    var ajaxUrl = TYPO3.settings.ajaxUrls['HfwuRedirects::deleteRedirectEntry'];
    TYPO3.jQuery.ajax({
        url:       ajaxUrl,
        type:      'GET',
        dataType:  'html',
        data: {
            id: id,
            is_qr_code: isQrCode
        },
        success: function (result, isQrCode, listId) {
            aliasListAjax(filter);
        },
        error: function (error, isQrCode, listId) {
            aliasListAjax(filter);
        }
    });
}


TYPO3.jQuery(document).ready(function() {
    TYPO3.jQuery('.ajaxFilter').on('keyup', function (event) {
        var filter = TYPO3.jQuery(this).val();
        if (filter.length>=3) {
            aliasListAjax(filter,false,'#redirect_list');
        }
    });
    TYPO3.jQuery('.ajaxFilterReset').on('click', function (event) {
        TYPO3.jQuery('.ajaxFilter').val('');
        aliasListAjax('',false,'#redirect_list');
    });
    TYPO3.jQuery('#redirect_list').on('click', '.deleteEntry', function (event) {
        var confirmationMessage = TYPO3.jQuery('#deleteConfirmationMessage').val();
        if (confirm(confirmationMessage)) {
            var id = TYPO3.jQuery(this).attr('data-uid');
            var filter = TYPO3.jQuery('.ajaxFilter').val();
            aliasDeleteAjax(id,filter,false,'#redirect_list');
        }
    });
    TYPO3.jQuery('.qrFilter').on('keyup', function (event) {
        var filter = TYPO3.jQuery(this).val();
        if (filter.length>=3) {
            aliasListAjax(filter,true,'#qr_list');
        }
    });
    TYPO3.jQuery('.qrFilterReset').on('click', function (event) {
        TYPO3.jQuery('.qrFilter').val('');
        aliasListAjax('',true,'#qr_list');
    });
    TYPO3.jQuery('#qr_list').on('click', '.deleteEntry', function (event) {
        var confirmationMessage = TYPO3.jQuery('#deleteConfirmationMessage').val();
        if (confirm(confirmationMessage)) {
            var id = TYPO3.jQuery(this).attr('data-uid');
            var filter = TYPO3.jQuery('.qrFilter').val();
            aliasDeleteAjax(id,filter,true,'#qr_list');
        }
    });

});