jQuery(function($) {
    // Configure/customize these variables.
    var showChar = 300;  // How many characters are shown by default
    var ellipsestext = "...";
    var moretext = "Read More";
    var lesstext = "Collapse";


    $('.more').each(function() {
        var content = $(this).html();

        if(content.length > showChar) {

            var c = content.substr(0, showChar);
            var h = content.substr(showChar, content.length - showChar);

            var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span><a href="" class="morelink">' + lesstext + '</a></span>';

            $(this).html(html);
            $(this).slideDown();
        }

    });

    $(".morelink").click(function(){
        if($(this).hasClass("less")) {
            $(this).removeClass("less");
            $(this).html(lesstext);
        } else {
            $(this).addClass("less");
            $(this).html(moretext);
        }
        $(this).parent().prev().toggle();
        $(this).prev().toggle();
        return false;
    });
});