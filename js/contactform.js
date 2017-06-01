var callback_function = function() {
    $("#captchaModal").modal("hide");
};

var captcha_callback = function(response) {

    document.querySelector("body").setAttribute("data-captcha", response);

    var nodes = document.querySelectorAll("input[name=\"g-recaptcha-response\"]");

    for(var i = 0; i < nodes.length; i++) {
        nodes[i].value = response;
    }

    callback_function(response);

};

if(typeof Neo !== "undefined") {

    var _arguments = { history: false };

    (function($) {

        var forms = $.select("form", "tag"),
            address_inputs = $.select("input", "tag"),
            factuur = $.select("factuur_check", "id"),
            factuur_form = $.select("factuur_form", "id"),
            bekijk = $.select("bekijk", "id"),
            body = $.select("body"),
            order_form = $.select("order_form", "id"),
            functions = this;

        functions.return_call = function(request, node) {

            var submit = node.select("[type=\"submit\"]");

            if(request.status == 200) {

                var parsed = JSON.parse(request.responseText);
                
                if(parsed.text == "Verzonden") {

                    var parent = node.parent(".bel-mij-terug").addClass("bel-mij-terug--bedankt");

                    parent.select(".bel-mij-terug__header").inner("Bedankt voor uw interesse in KPN WiFi HotSpots");
                    parent.select(".bel-mij-terug__body").inner("Binnen twee werkdagen nemen we contact met u op. We hebben een bevestiging van uw informatieaanvraag naar uw e&#8209;mailadres gestuurd.");

                    node.addClass("hidden");

                    //if($.matchMedia("(max-width: 767px)")) {
                        $.scrollTop(parent.position().y - 20, 400, "easeOutQuad");
                    //}

                    if($.isDefined(dataLayer) && !(window.location.pathname.indexOf("recreatie-lp.html") > -1)) {
                        dataLayer.push({
                           event: (window.location.pathname.indexOf("sportvereniging-lp.html") > -1 ? "bel-mij-terug-sp-lp" : (window.location.pathname.indexOf("recreatie-lp.html") > -1 ? "bel-mij-terug-re-lp" : "bel-mij-terug"))
                        });
                    }

                    if($.isDefined(_gaq)) {
                        _gaq.push([ "_trackEvent", (window.location.pathname.indexOf("sportvereniging-lp.html") > -1 ? "bel-mij-terug-sp-lp" : (window.location.pathname.indexOf("recreatie-lp.html") > -1 ? "bel-mij-terug-re-lp" : "bel-mij-terug")), "submission", null, 0, true ]);
                    }

                }

                if(submit.tagName() == "button") {
                    submit.inner(parsed.text);
                } else {
                    submit.value(parsed.text);
                }

                return;

            }

            console.error("error while sending contact form");

        };

        functions.contact = function(request, node) {

            if(request.status == 200) {

                var parsed = JSON.parse(request.responseText);

                node.select("input", "tag").filter(function(node) {
                    return node.attribute("type") == "text" || node.attribute("type") == "email" || node.attribute("type") == "tel";
                }).call("value", [ "" ]);

                node.select("textarea", "tag").call("value", [ "" ]);

                $.select("contact_text_body", "id").removeClass("hidden").inner("Binnen twee werkdagen nemen we contact met u op. We hebben een bevestiging van uw vraag naar uw e&#8209;mailadres gestuurd.");

                if($.matchMedia("(max-width: 767px)")) {
                    $.scrollTop($.select("contact_text_body", "id").position().y - 10, 400, "easeOutQuad");
                }

                return;

            }

            console.error("error while sending contact form");

        };

        function query(data) {
            return Object.keys(data).map(function(key) {
                return [key, data[key]].map(encodeURIComponent).join("=");
            }).join("&");
        }

        forms.filter(function(node) {
            return node.hasAttribute("data-tip");
        }).each(function(node) {

            var properties = node.data("tip");
            var parsed = JSON.parse(properties.replace(/'/g, "\""));
            var fields = parsed.fields;
            var url = parsed.url;
            var _function = parsed.function;

            if($.isArray(fields) && fields.length > 0) {

                node.bind("submit", function(e) {

                    e.preventDefault();

                    var _query;
                    var _pairs = { };

                    fields.forEach(function(field) {

                        var _field = node.select("[name=\""+field+"\"]");

                        if($.isNode(_field)) {
                            _pairs[field] = encodeURIComponent(_field.value());
                        }

                    });

                    _query = query(_pairs);

                    var _function2 = function() {

                        _pairs['g-recaptcha-response'] = body.data("captcha");
                        _query = query(_pairs);

                        $.ajax().call(url+"?"+_query, function(request) {
                            functions[_function](request, e.node);
                        }, false);

                    };

                    $.ajax().call("captcha/validate.php?"+query({ 'form': node.data("form"), email: _pairs['email'] }), function(request) {

                        if(request.status == 200) {

                            var parsed = JSON.parse(request.responseText),
                                response = parsed.response;

                            if(response == "show") {

                                callback_function = function() {
                                    setTimeout(function() {
                                        jQuery("#captchaModal").modal("hide");
                                        _function2();
                                    }, 1000);
                                };

                                grecaptcha.reset();
                                jQuery("#captchaModal").modal("show");

                            } else {
                                _function2();
                            }

                        }


                    }, false);

                });

            }

        });

        address_inputs.each(function(node) {

            if(node.hasAttribute("data-error")) {

                node.bind([ "change", "input" ], function() {
                    node.origin.setCustomValidity("");
                });

                node.bind("invalid", function() {
                    node.origin.setCustomValidity(node.data("error"));
                });

            }

        });

        address_inputs.filter(function(node) {
            return node.attribute("type") == "text" || node.attribute("type") == "email";
        }).each(function(node) {
            if(!$.isPositiveLength(node.value())) {
                node.addClass("empty-control");
            } else {
                node.removeClass("empty-control");
            }
        }).call("bind", [ "keyup", function(e) {
            if(!$.isPositiveLength(e.node.value())) {
                e.node.addClass("empty-control");
            } else {
                e.node.removeClass("empty-control");
            }
        } ]);

        address_inputs.filter(function(node) {
            return $.isDefined($.select("#"+node.data("address")));
        }).each(function(node) {

            var parsed,
                house_number = $.select("#"+node.data("house"));

            node.bind("keyup", function(e) {

                e.preventDefault();

                var target = $.select("#"+node.data("address")),
                    tip = JSON.parse(target.data("tip").replace(/'/g, "\"")),
                    street = target.select("input[name=\""+tip.street+"\"]"),
                    city = target.select("input[name=\""+tip.city+"\"]"),
                    pattern = new RegExp(/^[1-9][0-9]{3} ?(?!sa|sd|ss)[a-z]{2}$/i);

                if(pattern.test(node.value())) {

                    node.addClass("show-loading").removeClass("show-error");

                    $.ajax().call("zipcode/validate.php?postcode="+encodeURIComponent(node.value()), function(request) {

                        parsed = JSON.parse(request.responseText);

                        if(parsed == null) {
                            node.removeClass("show-loading").removeClass("show-check").addClass("show-error");
                            return;
                        }

                        street.value(parsed.street);
                        city.value(parsed.city);

                        house_number.events["keyup"].forEach(function(event) {
                            event(e);
                        });

                        node.removeClass("show-loading").addClass("show-check");

                    }, false);

                } else if(node.hasClass("show-check")) {
                    node.removeClass("show-loading").removeClass("show-check").addClass("show-error");
                    target.addClass("hidden");
                    parsed = {"id":"","postcode":"","postcode_id":"","pnum":"","pchar":"","minnumber":"0","maxnumber":"0","numbertype":"","street":"","city":"","city_id":"","municipality":"","municipality_id":"","province":"","province_code":"","lat":"","lon":"","rd_x":"","rd_y":"","location_detail":"","changed_date":""};
                    house_number.events["keyup"].forEach(function(event) {
                        event(e);
                    });
                }

            });

            house_number.bind("keyup", function(e) {

                e.preventDefault();

                var target = $.select("#"+node.data("address")),
                    input = house_number.value();

                if($.isDefined(target)) {

                    if(!isNaN(input) && input.length > 0) {
                        input = parseInt(input);
                    } else {
                        input = 0;
                    }

                    var warning = house_number.parent(".form-group").select(".warning-message");

                    if(input == 0) {
                        target.addClass("hidden");
                        house_number.removeClass("show-check").removeClass("show-warning").addClass("show-error");
                        if($.isDefined(warning)) {
                            warning.addClass("hidden");
                        }
                    } else if($.isDefined(parsed) && input >= parsed.minnumber && input <= parsed.maxnumber) {
                        target.removeClass("hidden");
                        house_number.removeClass("show-warning").addClass("show-check").removeClass("show-error");
                        if($.isDefined(warning)) {
                            warning.addClass("hidden");
                        }
                    } else {
                        target.addClass("hidden");
                        house_number.removeClass("show-check").addClass("show-warning").removeClass("show-error");
                        if($.isDefined(warning)) {
                            warning.removeClass("hidden");
                        }
                    }

                }

            });

        });

        $.isDefined(factuur) ? factuur.bind("change", function(e) {

            e.preventDefault();

            factuur_form.css("display", e.node.checked() ? "block" : "none").select("input", "tag").filter(function(node) {
                return node.hasAttribute("data-required");
            }).each(function(node) {
                if(e.node.checked()) {
                    node.attribute("required", "required");
                } else {
                    node.removeAttribute("required");
                }
            });

        }) : "";

        $.isDefined(bekijk) ? bekijk.select(".bekijk-uw-bestelling").bind("click", function(e) {
            e.node.addClass("hidden");
            bekijk.select(".keuze").css("display", "block");
        }) : "";

        $.isDefined(order_form) ? order_form.bind("submit", function(e) {

            if(order_form.data("submit") != "true") {

                e.preventDefault();

                var email = order_form.select("[name=\"email\"]").value();

                $.ajax().call("captcha/validate.php?"+query({ 'form': 2, email: email }), function(request) {

                    if(request.status == 200) {

                        var parsed = JSON.parse(request.responseText),
                            response = parsed.response;

                        if(response == "show") {

                            callback_function = function(response) {
                                setTimeout(function() {
                                    jQuery("#captchaModal").modal("hide");
                                    order_form.select("[name=\"g-recaptcha-response\"]").value(response);
                                    order_form.data("submit", "true").origin.submit();
                                }, 1000);

                            };

                            grecaptcha.reset();
                            jQuery("#captchaModal").modal("show");

                        } else {
                            order_form.data("submit", "true").origin.submit();
                        }

                    }

                }, false);

            }

        }) : "";

    })(new Neo(_arguments));

}