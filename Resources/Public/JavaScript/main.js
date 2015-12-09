'use strict';

/**
 * Created by seltmann on 03.12.15.
 */

jQuery(document).ready(function($) {
    var collection = $('.PMIToggle.truncate');

    for (var i=0; collection.length > i; i++) {
        $(collection[i]).css('cursor', 'pointer');
        $(collection[i]).on('click', function() {
            if ($(this).hasClass('expand')) {
                $(this).removeClass('expand');
                $(this).addClass('truncate');
            } else if ($(this).hasClass('truncate')) {
                $(this).removeClass('truncate');
                $(this).addClass('expand');
            }
        });
    }
});