var loadedTagManager = false;

function tagManager() {
    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-5ZM8DJ');
    loadedTagManager = true;
}

if(typeof Neo !== "undefined") {

    var _arguments = { history: false };

    (function($) {

        var warning = $.select("cookie_warning", "id"),
            accept = $.select("cookie-compliance-submit", "id"),
            decline = $.select("cookie-compliance-cancel", "id");

        if($.isDefined(warning)) {

            var enabled = $.cookies().get("cookies_enabled") != null,
                button = warning.select("#KPN_cookie_text_bottom .KPN_cookie_button");

            if(!enabled) {
                warning.removeClass("hidden");
            }

            button.bind("click", function() {
                warning.addClass("hidden");
                $.cookies().set("cookies_enabled", "true", 365, "/", window.location.hostname, false);
                tagManager();
            });

            if($.cookies().get("cookies_enabled") == "true") {
                tagManager();
            }

        } else {
            tagManager();
        }

        $.isDefined(accept) ? accept.bind("click", function(e) {

            e.preventDefault();

            $.cookies().set("cookies_enabled", "true", 365, "/", window.location.hostname, false);
            tagManager();
            window.location = window.location.origin;

        }) : null;

        $.isDefined(decline) ? decline.bind("click", function(e) {

            e.preventDefault();

            $.cookies().set("cookies_enabled", "false", 365, "/", window.location.hostname, false);
            window.location = window.location.origin;

        }) : null;

    })(new Neo(_arguments));

}