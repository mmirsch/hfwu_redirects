
$.fn.extend({
    bindWithDelay: function( type, data, fn, timeout, throttle ) {

        if ( $.isFunction( data ) ) {
            throttle = timeout;
            timeout = fn;
            fn = data;
            data = undefined;
        }

        // Allow delayed function to be removed with fn in unbind function
        fn.guid = fn.guid || ($.guid && $.guid++);

        // Bind each separately so that each element has its own delay
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
    }
});

function processingAnimation(mode,message) {
    var aHeight = $(window).height();
    var aWidth = $(window).width();

    if (mode=='start') {
        if ($('#spinOverlay').size()==0) {
            $('body').append('<div id="spinOverlay"></div>');
            $('#spinOverlay').css('height', aHeight).css('width', aWidth);
            if (message) {
                $('#spinOverlay').append('<div id="spinOverlayMessage">' + message + '</div>');
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

function aliasListAjax() {
    var ajaxUrl = TYPO3.settings.ajaxUrls['HfwuRedirects::aliasList'];
    var filter = $('#search_filter').val();
    var pid = $('#pid').val();
    var limit = $('#limit').val();
    var site_url = $('#site_url').val();
    var return_url = $('#return_url').val();
    var filter_types = $('#filter_types').val();

    $.ajax({
        url: ajaxUrl,
        type: 'GET',
        dataType: 'html',
        data: {
            filter: filter,
            pid: pid,
            limit: limit,
            site_url: site_url,
            return_url: return_url,
            filter_types: filter_types

        },
        beforeSend : function(){
            processingAnimation('start','bitte warten');
        },
        success: function (result) {
            $('#redirect_list').html(result);
        },
        error: function (error) {
            $('#redirect_list').html(error);
        }
    }).always(function() {
        processingAnimation('stop');
    });
}

function aliasDeleteAjax(uid) {
    var ajaxUrl = TYPO3.settings.ajaxUrls['HfwuRedirects::deleteRedirectEntry'];
    $.ajax({
        url:       ajaxUrl,
        type:      'GET',
        dataType:  'html',
        data: {
            uid: uid
        },
        success: function (result) {
            aliasListAjax();
        },
        error: function (error) {
            aliasListAjax();
        }
    });
}

function showQrCodeAjax(uid) {
    var ajaxUrl = TYPO3.settings.ajaxUrls['HfwuRedirects::showQrCodeAjax'] + '&uid=' + uid;
    document.location.href = ajaxUrl;
}

$(document).ready(function() {
    $('#search_filter').bindWithDelay('keyup', function (event) {
         aliasListAjax();
    },300);
    $('.ajaxFilterReset').on('click', function (event) {
        $('.ajaxFilter').val('');
        $('#filter_types').val('all');
        aliasListAjax();
    });
    $('#redirect_list').on('click', '.deleteEntry', function (event) {
        var confirmationMessage = $('#deleteConfirmationMessage').val();
        if (confirm(confirmationMessage)) {
            var uid = $(this).attr('data-uid');
            aliasDeleteAjax(uid);
        }
    });
    $('#redirect_list').on('click', '.showQrCode', function (event) {
        var uid = $(this).attr('data-uid');
        showQrCodeAjax(uid);
        return false;
    });
    $('#filter_types').on('change', function (event) {
        aliasListAjax();
    });
    $('#limit').on('change', function (event) {
        aliasListAjax();
    });

});