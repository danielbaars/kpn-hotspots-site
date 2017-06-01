if(typeof Neo !== "undefined") {

    var _arguments = { history: false };

    (function($) {

        var sliders = $.select("citaat__slider", "class");

        if($.matchMedia("(max-width: 767px)")) {
            return;
        }

        var hidden;

        if(typeof document.hidden !== "undefined") {
            hidden = "hidden";
        } else if(typeof document.mozHidden !== "undefined") {
            hidden = "mozHidden";
        } else if(typeof document.msHidden !== "undefined") {
            hidden = "msHidden";
        } else if(typeof document.webkitHidden !== "undefined") {
            hidden = "webkitHidden";
        }

        function insertAfter(newElement, targetElement) {

            var parent = targetElement.parentNode;

            if(parent.lastchild == targetElement) {
                parent.appendChild(newElement);
            } else {
                parent.insertBefore(newElement, targetElement.nextSibling);
            }

        }

        sliders.each(function(slider) {

            var quotes = slider.select("citaat", "class"),
                time = 10000,
                interval = setInterval(_interval, time),
                initial = quotes.size(),
                offset = 1;

            function _interval() {

                if($.isString(hidden) && document[hidden]) {
                    return;
                }

                slider.css("text-indent", "-"+(offset * 100)+"%");

                if(++offset >= quotes.size()) {
                    quotes = slider.select("citaat", "class");
                    insertAfter(quotes.get(offset % 4).origin.cloneNode(true), quotes.last().origin);
                }

                if(offset == (initial * 2) + 1) {
                    setTimeout(function() {
                        quotes.each(function(node, i) {
                            if(i > initial) {
                                node.remove();
                            }
                            slider.addClass("no-transition");
                            slider.css("text-indent", "0%");
                            setTimeout(function() {
                                slider.removeClass("no-transition");
                            }, 800);
                            offset = 1;
                        });
                        quotes = slider.select("citaat", "class");
                    }, time / 2);
                }

            }

        });

    })(new Neo(_arguments));

}