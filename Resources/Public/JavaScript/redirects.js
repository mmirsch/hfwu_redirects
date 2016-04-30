
TYPO3.jQuery.fn.extend({
    bindWithDelay: function( type, data, fn, timeout, throttle ) {

        if ( TYPO3.jQuery.isFunction( data ) ) {
            throttle = timeout;
            timeout = fn;
            fn = data;
            data = undefined;
        }

        // Allow delayed function to be removed with fn in unbind function
        fn.guid = fn.guid || (TYPO3.jQuery.guid && TYPO3.jQuery.guid++);

        // Bind each separately so that each element has its own delay
        return this.each(function() {

            var wait = null;

            function cb() {
                var e = TYPO3.jQuery.extend(true, { }, arguments[0]);
                var ctx = this;
                var throttler = function() {
                    wait = null;
                    fn.apply(ctx, [e]);
                };

                if (!throttle) { clearTimeout(wait); wait = null; }
                if (!wait) { wait = setTimeout(throttler, timeout); }
            }

            cb.guid = fn.guid;

            TYPO3.jQuery(this).bind(type, data, cb);
        });
    }
});

function processingAnimation(mode,message) {
    var aHeight = TYPO3.jQuery(window).height();
    var aWidth = TYPO3.jQuery(window).width();

    if (mode=='start') {
        if (TYPO3.jQuery('#spinOverlay').size()==0) {
            TYPO3.jQuery('body').append('<div id="spinOverlay"></div>');
            TYPO3.jQuery('#spinOverlay').css('height', aHeight).css('width', aWidth);
            if (message) {
                TYPO3.jQuery('#spinOverlay').append('<div id="spinOverlayMessage">' + message + '</div>');
                var left = Math.ceil((aWidth - TYPO3.jQuery('#spinOverlayMessage').width()) / 2);
                var top = Math.ceil((aHeight - TYPO3.jQuery('#spinOverlayMessage').height()) / 2)+30;
                TYPO3.jQuery('#spinOverlayMessage').css('left', left).css('top', top);
            }
        }
        TYPO3.jQuery('#spinOverlay').show();
    } else if (mode=='stop') {
        if (TYPO3.jQuery('#spinOverlay')) {
            TYPO3.jQuery('#spinOverlay').remove();
        }
        if (TYPO3.jQuery('#spinOverlayMessage')) {
            TYPO3.jQuery('#spinOverlayMessage').remove();
        }
    }
}

function aliasListAjax() {
    var ajaxUrl = TYPO3.settings.ajaxUrls['HfwuRedirects::aliasList'];
    var filter = TYPO3.jQuery('#search_filter').val();
    var pid = TYPO3.jQuery('#pid').val();
    var limit = TYPO3.jQuery('#limit').val();
    var site_url = TYPO3.jQuery('#site_url').val();
    var filter_types = TYPO3.jQuery('#filter_types').val();
    TYPO3.jQuery.ajax({
        url: ajaxUrl,
        type: 'GET',
        dataType: 'html',
        data: {
            filter: filter,
            pid: pid,
            limit: limit,
            site_url: site_url,
            filter_types: filter_types

        },
        beforeSend : function(){
            processingAnimation('start','bitte warten');
        },
        success: function (result) {
            TYPO3.jQuery('#redirect_list').html(result);
        },
        error: function (error) {
            TYPO3.jQuery('#redirect_list').html(error);
        }
    }).always(function() {
        processingAnimation('stop');
    });
}

function aliasDeleteAjax(uid) {
    var ajaxUrl = TYPO3.settings.ajaxUrls['HfwuRedirects::deleteRedirectEntry'];
    TYPO3.jQuery.ajax({
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

TYPO3.jQuery(document).ready(function() {
    TYPO3.jQuery('#search_filter').bindWithDelay('keyup', function (event) {
         aliasListAjax();
    },300);
    TYPO3.jQuery('.ajaxFilterReset').on('click', function (event) {
        TYPO3.jQuery('.ajaxFilter').val('');
        TYPO3.jQuery('#filter_types').val('all');
        aliasListAjax();
    });
    TYPO3.jQuery('#redirect_list').on('click', '.deleteEntry', function (event) {
        var confirmationMessage = TYPO3.jQuery('#deleteConfirmationMessage').val();
        if (confirm(confirmationMessage)) {
            var uid = TYPO3.jQuery(this).attr('data-uid');
            aliasDeleteAjax(uid);
        }
    });
    TYPO3.jQuery('#redirect_list').on('click', '.showQrCode', function (event) {
        var uid = TYPO3.jQuery(this).attr('data-uid');
        showQrCodeAjax(uid);
        return false;
    });
    TYPO3.jQuery('#filter_types').on('change', function (event) {
        aliasListAjax();
    });
    TYPO3.jQuery('#limit').on('change', function (event) {
        aliasListAjax();
    });

});