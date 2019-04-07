/*! dxui by dxkite 2016-12-16 */
"use strict";

var dxui = dxui || {
    version: "1.0.0"
};

!function(dxui) {
    var DxDOM = function(selecter, context) {
        return new DxDOM.constructor(selecter, context);
    };
    DxDOM.constructor = function(selecter, context) {
        "string" == typeof selecter ? this.elements = (context || document).querySelectorAll(selecter) : this.elements = [ selecter ], 
        this.context = context, this.length = this.elements.length;
        for (var i = 0; i < this.length; i++) this[i] = this.elements[i];
        return this;
    }, DxDOM.extend = function(methods) {
        for (var name in methods) this[name] = methods[name];
    }, DxDOM.extend({
        element: function(tag, attr, css) {
            var element = document.createElement(tag);
            return DxDOM(element).attr(attr).css(css), element;
        }
    }), DxDOM.method = DxDOM.constructor.prototype, DxDOM.method.extend = DxDOM.extend, 
    DxDOM.method.extend({
        attr: function(attrs) {
            return this.each(function() {
                if (attrs) for (var name in attrs) this.setAttribute(name, attrs[name]);
            }), this;
        },
        css: function(cssObj) {
            return this.each(function() {
                if (cssObj) for (var name in cssObj) this.style[dxui.cssfix(name)] = cssObj[name];
            }), this;
        },
        addClass: function(add) {
            return this.each(function() {
                this.class += " " + add;
            }), this;
        },
        removeClass: function(remove) {
            return this.each(function() {
                var reg = new RegExp("/\\s+?" + remove + "/");
                this.class.replace(reg, "");
            }), this;
        },
        each: function(callback) {
            for (var i = 0; i < this.length; i++) callback.call(this[i], this[i], i);
            return this;
        },
        on: function(type, listener, useCaptrue) {
            return this.each(function() {
                this.addEventListener(type, listener, useCaptrue);
            }), this;
        }
    }), dxui.dom = DxDOM;
}(dxui), !function(dxui) {
    function add_css_prefix(name) {
        return name = name.trim(), name = "undefined" == typeof document.documentElement.style[name] ? dxui.css_perfix + name : name;
    }
    dxui.is_function = function(obj) {
        return "[object Function]" === Object.prototype.toString.call(obj);
    }, dxui.is_array = function(obj) {
        return "[object Array]" === Object.prototype.toString.call(obj);
    }, dxui.is_object = function(obj) {
        return "[object Object]" === Object.prototype.toString.call(obj);
    }, dxui.is_string = function(obj) {
        return "string" == typeof obj;
    }, dxui.get_root_path = function() {
        var scripts = document.getElementsByTagName("script"), _self_path = scripts[scripts.length - 1].getAttribute("src");
        return _self_path.substring(0, _self_path.lastIndexOf("/"));
    }, dxui.dipatch_event = function(obj, name, value, canBubbleArg, cancelAbleArg) {
        var event = document.createEvent(str_cache[0]), canBubble = void 0 === typeof canBubbleArg || canBubbleArg, cancelAbl = void 0 === typeof cancelAbleArg || cancelAbleArg;
        return event.initCustomEvent(name, canBubble, cancelAbl, value), obj.dispatchEvent(event), 
        obj["on" + name] && is_function(obj["on" + name]) && obj["on" + name].call(obj, event), 
        event;
    }, dxui.object_copy = function(arrays) {
        for (var object = {}, i = 0; i < arguments.length; i++) for (var index in arguments[i]) object[index] = arguments[i][index];
        return object;
    }, dxui.get_css_perfix = function() {
        var styles = window.getComputedStyle(document.documentElement, ""), core = (Array.prototype.slice.call(styles).join("").match(/-(moz|webkit|ms|)-/) || "" === styles.OLink && [ "", "o" ])[1];
        return "-" + core + "-";
    }, dxui.css_perfix = dxui.get_css_perfix(), dxui.cssname = function(name) {
        return name = add_css_prefix(name), name = name.replace(/[A-Z]/, function(name) {
            return "-" + name.toLowerCase();
        });
    }, dxui.cssfix = add_css_prefix, window.dxui = dxui;
}(dxui), !function(window) {
    function statmentTest(test, code) {
        try {
            new Function(test);
        } catch (e) {
            return "throw " + e.name + "(" + _string(e.message) + ");{";
        }
        return code;
    }
    function parserHTML(html, compress) {
        var out = "";
        return html.match(/(?!^)\n/) ? _each(html.split("\n"), function(html) {
            html && (compress && (html = html.replace(/\s+/g, " ").replace(/<!--.*?-->/g, "")), 
            html && (out += ENGINE[1] + _string(html) + ENGINE[2], out += "\n"));
        }) : html && (out += ENGINE[1] + _string(html) + ENGINE[2]), out;
    }
    function parserCode(code) {
        var match;
        if (!(match = code.match(new RegExp(KEYWORD_PREG)))) return (match = code.match(/^!.*$/)) ? ENGINE[1] + "$_unit._echo(" + match[1] + ")" + ENGINE[2] : ENGINE[1] + "$_unit._escape(" + code + ")" + ENGINE[2];
        var command = match[1], param = match[2];
        switch (command) {
          case "include":
            return param = param.trim().split(" "), 1 === param.length && param.push("$_unit.value"), 
            param = param.join(","), ENGINE[1] + "$_unit._include(" + param + ")" + ENGINE[2];

          case "if":
            return statmentTest("if(" + param + "){}", "if (" + param + ") {");

          case "else":
            return (match = param.match(/^\s*if\s+(.*)/)) ? "} else if (" + match[1] + "){" : "}else{";

          case "/if":
          case "/while":
          case "/for":
            return "}";

          case "while":
            return statmentTest("while(" + param + "){}", "while (" + param + ") {");

          case "for":
            return statmentTest("for(" + param + "){}", "for (" + param + ") {");

          case "each":
            var match = param.match(/\s*(.+?)\s+(?:(?:as(?:\s+(\w+)))?(?:\s*:\s*(\w+))?)?/);
            if (match) {
                var each_param, value = match[1];
                return each_param = match[2] ? match[3] ? match[3] + "," + match[2] : match[2] : "value,index", 
                "$_unit._each(" + value + ",function(" + each_param + "){";
            }
            return 'throw SyntaxError("Null Each Value");$_unit._each(null,function(){';

          case "/each":
            return "});";
        }
    }
    function _string(code) {
        return "'" + code.replace(/('|\\)/g, "\\$1").replace(/\r/g, "\\r").replace(/\n/g, "\\n") + "'";
    }
    function is_array(obj) {
        return "[object Array]" === Object.prototype.toString.call(obj);
    }
    function reportError(name, content, line, e) {
        var name = name || "anonymous", report = "DxTPL Error:";
        if (console.group(report), content) {
            var codes = content.replace(/^\n/, "").split("\n"), start = line - 5 > 0 ? line - 5 : 1, end = line + 5 > codes.length ? codes.length : line + 5;
            console.error(e);
            for (var i = start; i < end; i++) i == line ? console.log(i + "|%c" + codes[line - 1] + "\t\t%c->\t\t%c" + e.name + ":" + e.message, "color:red;", "color:green;", "color:red;") : console.log(i + "|" + codes[i - 1]);
        } else console.log(content), console.log("%c" + report + e.message + "\t\t@" + name + ":" + line, "color:red;");
        console.groupEnd(report);
    }
    function compileTemplate(text, config) {
        var tpl = "";
        return text = text.replace(/^\n/, ""), _each(text.split(config.tagstart), function(value) {
            var split = value.split(config.tagend);
            1 === split.length ? tpl += parserHTML(split[0], config.compress) : (tpl += parserCode(split[0]), 
            tpl += parserHTML(split[1]));
        }), tpl;
    }
    function linkValue(source, value, strict) {
        var use_strict = void 0 === strict || strict, ext = [];
        ext.push("var $_unit=this," + ENGINE[0]);
        for (var index in value) ext.push(index + "=this.value." + index);
        var link_str = "";
        return use_strict && (link_str = '"use strict";'), link_str += ext.join(","), link_str += ";", 
        link_str += source + "return new String(" + ENGINE[3] + ");";
    }
    function renderTpl(selector, glovalue) {
        var nodes = document.querySelectorAll(selector);
        _arrayEach(nodes, function(node, index) {
            var value, source = node.innerHTML, config = default_config;
            if (node.dataset.init) try {
                var json = new Function("return " + node.dataset.init + ";");
                value = json();
            } catch (e) {
                reportError(selector + "[" + index + "]", null, 0, new Error("Unsupport json"));
            }
            if (node.dataset.config) try {
                var json = new Function("return " + node.dataset.config + ";"), conf = json();
                config = _objectCopy(config, conf);
            } catch (e) {
                reportError(selector + "[" + index + "]", null, 0, new Error("Unsupport json"));
            }
            value = _objectCopy(value, glovalue);
            var code = compileTemplate(source, config);
            node.innerHTML = render(selector, source, code, value, config.strict);
        });
    }
    function render(name, source, compiled_code, value, strict) {
        var html, runcode = linkValue(compiled_code, value, strict), caller = {
            _each: _each,
            _echo: _echo,
            _escape: _escape,
            _include: _include,
            value: value
        };
        try {
            var render = new Function(runcode);
            html = render.call(caller);
        } catch (e) {
            var match = new String(e.stack).match(/<anonymous>:(\d+):\d+/);
            if (match) {
                var line = match[1] - 1;
                reportError(name, source, line, e);
            } else {
                var name = name || "anonymous", match = new String(e.stack).match(/Function code:(\d+):\d+/);
                match ? console.error("DxTPL:Compile Error@" + name + " Line " + match[1]) : console.error("DxTPL:Compile Error@" + name);
            }
        }
        return html;
    }
    function getDOMcache(name, config) {
        var cache_parent = document.getElementById("template_caches");
        cache_parent || (cache_parent = document.createElement("div"), cache_parent.id = "template_caches", 
        cache_parent.style.display = "none", document.body.appendChild(cache_parent));
        var cache_name = "template_cache_" + name, tpl_cache = document.getElementById("template_cache_" + name);
        return tpl_cache || (tpl_cache = document.createElement("div"), tpl_cache.id = cache_name, 
        tpl_cache.innerText = compileTemplate(document.getElementById(name).innerHTML, config || default_config), 
        cache_parent.appendChild(tpl_cache)), tpl_cache.innerText;
    }
    function compile(id, config) {
        var tplId = id || config.id, anonymous = !1;
        if ("string" != typeof tplId) throw Error("Unsupport Template ID");
        var tpl = document.getElementById(tplId);
        return tpl ? config.source = tpl.innerHTML : (config.source = tplId, config.id = "anonymous", 
        anonymous = !0), config.code || (config.cache && !anonymous ? config.code = getDOMcache(tplId, config) : config.code = compileTemplate(config.source, config)), 
        config;
    }
    var default_config = {
        cache: !0,
        tagstart: "{",
        tagend: "}",
        compress: !0,
        strict: !0
    }, KEYWORD = "if,else,each,include,while,for", KEYWORD_PREG = "^\\s*((?:/)?(?:" + KEYWORD.split(",").join("|") + "))(.*)", ENGINE = "".trim ? [ "$_tpl_=''", "$_tpl_+=", ";", "$_tpl_" ] : [ "$_tpl_=[]", "$_tpl_.push(", ");", "$_tpl_.join('')" ], escape = {
        "<": "&#60;",
        ">": "&#62;",
        '"': "&#34;",
        "'": "&#39;",
        "&": "&#38;"
    }, _echo = function(value) {
        return new String(value);
    }, _escape = function(content) {
        return _echo(content).replace(/&(?![\w#]+;)|[<>"']/g, function(s) {
            return escape[s];
        });
    }, _each = function(value, callback) {
        if (is_array(value)) _arrayEach(value, callback); else for (var index in value) callback.call(value[index], value[index], index);
    }, _arrayEach = function(value, callback) {
        for (var index = 0; index < value.length; ++index) callback.call(value[index], value[index], index);
    }, _objectCopy = function(arrays) {
        for (var object = {}, i = 0; i < arguments.length; i++) for (var index in arguments[i]) object[index] = arguments[i][index];
        return object;
    }, _include = function(id, value) {
        return new Template(id).render(value);
    }, Template = function(name, config) {
        this.version = "1.0.43";
        var conf = default_config;
        "string" == typeof name ? (conf = _objectCopy(conf, config), conf.id = name) : conf = _objectCopy(conf, name), 
        this.config(conf);
    };
    Template.prototype.config = function(config) {
        for (var index in config) this[index] = config[index];
        return this;
    }, Template.prototype.assign = function(name, value) {
        return this.value[name] = _objectCopy(this.value[name], value), this;
    }, Template.prototype.value = function(value) {
        return this.value = _objectCopy(this.value, value), this;
    }, Template.prototype.compile = function(id) {
        var config = _objectCopy(this, compile(id, this));
        return new Template(config);
    }, Template.prototype.render = function(value) {
        if (!this.source || !this.code) {
            var val = compile(this.id, this);
            this.config(val);
        }
        return render(this.id, this.source, this.code, value, this.strict);
    }, window.dxtpl = new Template(), window.Template = Template, window.renderTpl = renderTpl;
}(window), !function(dxui) {
    var $ = dxui.dom, Editor = function(node) {
        this.m_node = node;
        var self = this;
        this.buildRichUI(), $(this.m_content).on("blur", function() {
            self.m_selection = window.getSelection(), self.setRange(self.m_selection.getRangeAt(0));
        });
    };
    Editor.prototype = {
        getRange: function() {
            if (this.m_range) return this.m_range;
            var range = document.createRange(), node = null;
            return this.m_content.firstChild ? node = this.m_content.firstChild : (node = $.element("p"), 
            this.m_content.appendChild(node)), range.selectNode(node), range;
        },
        setRange: function(range) {
            this.m_range = range.cloneRange();
        },
        insertNode: function(element) {
            var range = this.getRange();
            range.insertNode(element);
        },
        buildRichUI: function() {
            var self = this;
            this.m_controls = $.element("div", {
                class: "editor-controls"
            }), this.m_content = $.element("div", {
                contenteditable: "true",
                class: "editor-content"
            }), this.m_node.appendChild(this.m_controls), this.m_node.appendChild(this.m_content);
            var insertHTML = $.element("a", {
                href: "#"
            }, {
                cursor: "pointer"
            });
            insertHTML.innerHTML = "Html", this.m_controls.appendChild(insertHTML), $(insertHTML).on("click", function() {
                var value = prompt("url:"), newNode = $.element("div");
                newNode.innerHTML = value, self.insertNode(newNode);
            });
        }
    }, dxui.Editor = Editor;
}(dxui), !function(dxui) {
    dxui.moveable = function(layer, controller) {
        var _controller = controller || layer, _self = layer;
        _self.style.position = "fixed";
        var _move_layer = function(event) {
            event.preventDefault();
            var eventMove = "mousemove", eventEnd = "mouseup";
            event.touches && (event = event.touches[0], eventMove = "touchmove", eventEnd = "touchend");
            var rect = _controller.getBoundingClientRect(), x = event.clientX - rect.left, y = event.clientY - rect.top, doc = document;
            _self.setCapture ? _self.setCapture() : window.captureEvents && window.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP);
            var winmove = function(e) {
                e.touches && (e = e.touches[0]);
                var px = e.pageX || e.clientX + document.body.scrollLeft - document.body.clientLeft, py = e.pageY || e.clientY + document.body.scrollTop - document.body.clientTop, dx = px - x, dy = py - y;
                _self.style.left = dx + "px", _self.style.top = dy + "px";
            }, winend = function(e) {
                _self.releaseCapture ? _self.releaseCapture() : window.releaseEvents && window.releaseEvents(Event.MOUSEMOVE | Event.MOUSEUP), 
                doc.removeEventListener(eventMove, winmove), doc.removeEventListener(eventEnd, winend);
            };
            doc.addEventListener(eventMove, winmove), doc.addEventListener(eventEnd, winend);
        };
        return _controller.addEventListener("mousedown", _move_layer), _controller.addEventListener("touchstart", _move_layer), 
        _self;
    };
}(dxui), !function(dxui) {
    var TOAST_PARENT_ID = "Toast-Parent", TOAST_SHOW_ID = "Toast-Show", TOAST_DEFAULT_STYLE = "toast", TOAST_POP_LEVEL = 1e4, Toast = function(text, time, style) {
        return new Toast.create(text, time, style);
    };
    Toast.Queue = new Array(), Toast.create = function(message, time, style) {
        Toast.Parent = document.getElementById(TOAST_PARENT_ID), Toast.Parent || (Toast.Parent = document.createElement("div"), 
        Toast.Parent.id = TOAST_PARENT_ID, document.body.appendChild(Toast.Parent)), Toast.Queue.push({
            message: message,
            timeout: time,
            style: style ? TOAST_DEFAULT_STYLE + "-" + style : TOAST_DEFAULT_STYLE
        }), Toast.show();
    }, Toast.show = function() {
        if (!document.getElementById(TOAST_SHOW_ID)) {
            var show = Toast.Queue.shift(), toastdiv = dxui.dom.element("div", {
                id: TOAST_SHOW_ID,
                class: show.style
            });
            toastdiv.innerHTML = show.message, Toast.Parent.appendChild(toastdiv);
            var margin = window.innerWidth / 2 - toastdiv.scrollWidth / 2, bottom = window.innerHeight - 2 * toastdiv.scrollHeight;
            toastdiv.style.marginLeft = margin + "px", toastdiv.style.top = bottom + "px";
            var timeout = show.timeout || 2e3, close = function() {
                dxui.dom(toastdiv).css({
                    transition: "opacity 0.3s ease-out",
                    opacity: 0
                }), setTimeout(function() {
                    Toast.Parent.removeChild(toastdiv), Toast.Queue.length && Toast.show();
                }, 300);
            };
            dxui.dom(toastdiv).css({
                position: "fixed",
                opacity: 1,
                "z-index": TOAST_POP_LEVEL,
                transition: "opacity 0.1s ease-in"
            }), setTimeout(close, timeout);
        }
    }, dxui.Toast = Toast;
}(dxui), !function(dxui) {
    function VideoPlayer(url, type) {}
    dxui.video_player = function(url, type) {
        return new VideoPlayer(url, type);
    }, dxui.VideoPlayer = VideoPlayer;
}(dxui), !function(dxui) {
    var Window = function(title, content, config) {};
    Window.prototype = {};
}(dxui);