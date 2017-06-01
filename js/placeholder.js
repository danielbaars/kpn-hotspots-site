if(typeof Neo !== "undefined") {

    var _arguments = { history: false };

    (function($) {

		if(/MSIE\s/.test(navigator.userAgent) && parseFloat(navigator.appVersion.split("MSIE")[1]) < 10) {

            $.select("input", "tag").filter(function(node) {
                return node.hasAttribute("placeholder");
            }).each(function(node) {

                var placeholder = node.attribute("placeholder"),
                    value = node.value();

                if(value.length == 0) {
                    node.value(placeholder);
                }

                node.bind("focus", function(e) {
                    if(node.value() == placeholder) {
                        node.value("");
                    }
                });

                node.bind("blur", function(e) {
                    if(node.value().length == 0) {
                        node.value(placeholder);
                    }
                });

            });

        }

    })(new Neo(_arguments));

}