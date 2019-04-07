; /*! dx call */
window.dx = window.dx || {};
(function (dx) {
    // 全局严格模式
    "use strict";
    var call_id = 0;
    var thatDom = function () {
        var scripts = document.getElementsByTagName("script");
        return scripts[scripts.length - 1];
    }();

    function call(url, method, params, thatParent) {
        var that = thatParent || this;
        var ajax = new XMLHttpRequest;

        ajax.addEventListener("readystatechange", function () {
            if (ajax.readyState == 4) {
                if (ajax.status == 200) {
                    var json = JSON.parse(ajax.responseText);
                    if (params.finish) {
                        params.finish.call(that, json);
                    }
                    if (typeof json.result != 'undefined') {
                        if (params.success) {
                            params.success.call(that, json.result);
                        }
                    } else {
                        if (params.error) {
                            params.error.call(that, json.error);
                        } else {
                            console.error(json.error.name + ":" + json.error.message, json);
                        }
                    }
                } else {
                    if (params.fail) {
                        if (ajax.getResponseHeader('Content-Type').match('json')) {
                            try {
                                var json = JSON.parse(ajax.responseText);
                                params.fail.call(that, json.error);
                            } catch (e) {
                                params.fail.call(that, ajax);
                            }
                        } else {
                            params.fail.call(that, ajax);
                        }
                    } else {
                        console.error('server return ' + ajax.status);
                    }
                }
            }
        });

        function objectHasFile(args) {
            for (var index in args) {
                if (args[index] instanceof File || args[index] instanceof Blob) {
                    return true;
                }
            }
            return false;
        }

        var param = null;
        if (typeof params.args == 'undefined') {
            param = [];
        } else if (params.args instanceof Array) {
            param = params.args;
        } else {
            if (!(params.args instanceof FormData) && objectHasFile(params.args)) {
                var form = new FormData();
                for (var name in params.args) {
                    form.append(name, params.args[name]);
                }
                param = form;
            } else {
                param = params.args;
            }
        }

        if (param instanceof FormData) {
            ajax.open("POST", url + '?_method=' + method);
            ajax.send(param);
        } else {
            ajax.open("POST", url);
            ajax.setRequestHeader("Content-Type", "application/json");
            ajax.send(JSON.stringify({
                method: method,
                params: param,
                id: call_id++,
            }));
        }
    }
    dx.xcall = call;

    dx.acall = function (method, args) {
        return dx.call(thatDom.dataset.api, method, args);
    }

    dx.call = function (name, method, args) {
        return new Promise((resolve, reject) => {
            if (typeof args == 'undefined') {
                if (typeof method == 'string') {
                    dx.xcall(name, method, {
                        args: [],
                        success: resolve,
                        error: reject,
                        fail: reject,
                    });
                } else {
                    args = method;
                    method = name;
                    dx.xcall(method, {
                        args: args || [],
                        success: resolve,
                        error: reject,
                        fail: reject,
                    });
                }

            } else {
                dx.xcall(name, method, {
                    args: args || [],
                    success: resolve,
                    error: reject,
                    fail: reject,
                });
            }
        });
    }
})(window.dx);