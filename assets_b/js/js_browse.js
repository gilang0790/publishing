/**
 * Created by EeyO on 11/28/2015.
 */
$(document).on('keydown', function(e) {
    // Escape
    if (e.keyCode == 27) {
        window.close();
    }
});

var currRow;
var __gridSelector;
$(document).on('pjax:complete', function () {
    initBrowse(__gridSelector);
});

$(document).ready(function() {
    initBrowse(__gridSelector);

    $(document).on('keydown', __gridSelector + ' .table input', function(e) {
        if (e.which == 40) {
            // Down Arrow
            currRow.focus();
        }
    });

    $(document).on('keydown', __gridSelector + ' .table tbody tr', function(e) {
        var c = "";
        if (e.which == 38) {
            // Up Arrow
            c = currRow.closest('tr').prev();
        } else if (e.which == 40) {
            // Down Arrow
            c = currRow.closest('tr').next();
        } else if (e.keyCode == 13) {
            // Enter
            var btn = currRow.children('td:last');
            btn.click();
        }

        if (c.length > 0) {
            currRow = c;
            currRow.focus();
        }
    });
});

function initBrowse(gridSelector) {
//    var filterCol = $('.table input:first');
//    filterCol.focus();
    
    if (gridSelector === undefined) {
        gridSelector = "";
    }
    
    __gridSelector = gridSelector;

    //Add tabindex to all tr
    
    var i = 1;
    $(__gridSelector + ' .table tbody tr').each(function() {
        $(this).attr('tabindex', i);
        i += 1;
    });

    currRow = $(__gridSelector + ' .table tbody tr:first');
}