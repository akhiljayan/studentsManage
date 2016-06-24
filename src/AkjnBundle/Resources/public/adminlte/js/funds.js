/**
 * 
 */

function setTableTheme() {
    $(".jtable th").each(function() {
        $(this).addClass("ui-state-default ubuntu");
    });

    $(".jtable td").each(function() {
        $(this).addClass("ui-widget-content ubuntu");
    });

    $(".jtable tr").hover(function() {
        $(this).children("td").addClass("ui-state-hover");
    }, function() {
        $(this).children("td").removeClass("ui-state-hover");
    });

    $(".jtable tr").click(function() {
        $(this).children("td").toggleClass("ui-state-highlight");
    });

}
