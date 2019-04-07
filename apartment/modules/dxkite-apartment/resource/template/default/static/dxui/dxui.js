/**
 * DxUI 芒刺项目衍生界面库
 * Author: DXkite
 * Git:  https://github.com/DXkite/dxui.git
 * Lisence: MIT
 */
// 全局严格模式
"use strict";
// 初始化
var dxui = dxui || {
    version: '1.0.0'
};
/** DOM 辅助 */
;!(function (dxui) {
    var DxDOM = function (selecter, context) {
        return new DxDOM.constructor(selecter, context);
    }

    DxDOM.constructor = function (selecter, context) {
        if (typeof selecter === 'string') {
            this.elements = (context || document).querySelectorAll(selecter);
        } else {
            this.elements = [selecter];
        }
        this.context = context;
        this.length = this.elements.length;
        for (var i = 0; i < this.length; i++) {
            this[i] = this.elements[i];
        }
        return this;
    };

    DxDOM.extend = function (methods) {
        for (var name in methods) {
            this[name] = methods[name];
        }
    };

    DxDOM.extend({
        element: function (tag, attr, css) {
            var element = document.createElement(tag);
            DxDOM(element).attr(attr).css(css);
            return element;
        }
    });
    

    DxDOM.method = DxDOM.constructor.prototype;
    DxDOM.method.extend = DxDOM.extend;
    // 属性方法
    DxDOM.method.extend({
        attr: function (attrs) {
            this.each(function () {
                if (attrs) {
                    for (var name in attrs) {
                        this.setAttribute(name, attrs[name]);
                    }
                }
            });
            return this;
        },
        css: function (cssObj) {
            this.each(function () {
                if (cssObj) {
                    for (var name in cssObj) {
                        this.style[dxui.cssfix(name)] = cssObj[name];
                    }
                }
            });
            return this;
        },
        addClass: function (add) {
            this.each(function () {
                this.class += ' ' + add;
            });
            return this;
        },
        removeClass: function (remove) {
            this.each(function () {
                var reg = new RegExp('/\\s+?' + remove + '/');
                this.class.replace(reg, '');
            });
            return this;
        },
        each: function (callback) {
            for (var i = 0; i < this.length; i++) {
                callback.call(this[i], this[i], i);
            }
            return this;
        },
        on: function (type, listener, useCaptrue) {
            var captrue = typeof useCaptrue === undefined ? true : useCaptrue;
            this.each(function () {
                this.addEventListener(type, listener, useCaptrue);
            });
            return this;
        }
    });

    dxui.dom = DxDOM;
})(dxui)
;!(function (dxui) {
    /* --------------- 全局函数 ------------------ */
    dxui.is_function = function (obj) {
        return Object.prototype.toString.call(obj) === '[object Function]';
    }
    dxui.is_array = function (obj) {
        return Object.prototype.toString.call(obj) === '[object Array]';
    }
    dxui.is_object = function (obj) {
        return Object.prototype.toString.call(obj) === '[object Object]';
    }
    dxui.is_string = function (obj) {
        return typeof obj === 'string';
    }
    dxui.get_root_path = function () {
            var scripts = document.getElementsByTagName("script");
            var _self_path = scripts[scripts.length - 1].getAttribute("src");
            return _self_path.substring(0, _self_path.lastIndexOf("/"));
        }
        // 分发事件
    dxui.dipatch_event = function (obj, name, value, canBubbleArg, cancelAbleArg) {
        var event = document.createEvent(str_cache[0]);
        var canBubble = typeof canBubbleArg === undefined ? true : canBubbleArg;
        var cancelAbl = typeof cancelAbleArg === undefined ? true : cancelAbleArg;
        event.initCustomEvent(name, canBubble, cancelAbl, value);
        obj.dispatchEvent(event);
        if (obj['on' + name] && is_function(obj['on' + name])) {
            obj['on' + name].call(obj, event);
        }
        return event;
    }

    /**
     * 复制合并对象
     * 
     * @param {Object|string} arrays
     * @returns
     */
    dxui.object_copy = function (arrays) {
        var object = {};
        for (var i = 0; i < arguments.length; i++) {
            for (var index in arguments[i]) {
                object[index] = arguments[i][index];
            }
        }
        return object;
    }


    // 前缀支持
    dxui.get_css_perfix = function () {
        var styles = window.getComputedStyle(document.documentElement, '');
        var core = (
            Array.prototype.slice
            .call(styles)
            .join('')
            .match(/-(moz|webkit|ms|)-/) || (styles.OLink === '' && ['', 'o'])
        )[1];
        return '-' + core + '-';
    }
    dxui.css_perfix = dxui.get_css_perfix();

    /**
     * 添加CSS前缀（如果存在前缀）
     * 
     * @param {string} name
     * @returns 
     */
    function add_css_prefix(name) {
        name = name.trim();
        name = typeof document.documentElement.style[name] === 'undefined' ?  dxui.css_perfix + name : name;
        return name;
    }

    /**
     * 将驼峰式CSS转化成CSS文件用的CSS命名
     * 
     * @param {string} name
     * @returns
     */
    dxui.cssname = function (name) {
        name = add_css_prefix(name);
        name = name.replace(/[A-Z]/, function (name) {
            return '-' + name.toLowerCase();
        });
        return name;
    }
    dxui.cssfix = add_css_prefix;
    window.dxui = dxui;
})(dxui)
// TODO : 控制NavBar支持长型导航菜单
;
!(function (window) {

    //  缓存查找节点可能会耗时较多 
    var default_config = {
        cache: true, // 是否开启缓存
        tagstart: '{',
        tagend: '}', //控制标签
        compress: true,
        strict: true,
    };

    // 关键字
    var KEYWORD = 'if,else,each,include,while,for';
    var KEYWORD_PREG = '^\\s*((?:\/)?(?:' + KEYWORD.split(',').join('|') + '))(.*)';

    // @artTemplate:https://github.com/aui/artTemplate
    var ENGINE = ''.trim ? ["$_tpl_=''", "$_tpl_+=", ";", "$_tpl_"] : ["$_tpl_=[]", "$_tpl_.push(", ");", "$_tpl_.join('')"];

    var escape = {
        "<": "&#60;",
        ">": "&#62;",
        '"': "&#34;",
        "'": "&#39;",
        "&": "&#38;"
    };


    /*  --------------------  静态内部函数 protected ------------------------*/


    /**
     * 测试模板语句的可行性
     * 
     * @param {any} test
     * @param {any} code
     * @returns
     */
    function statmentTest(test, code) {
        try {
            new Function(test);
        } catch (e) {
            return 'throw ' + e.name + '(' + _string(e.message) + ');{';
        }
        return code;
    }


    /**
     * 处理HTML部分
     * 
     * @param {any} html
     * @param {any} compress 是否压缩
     * @returns
     */
    function parserHTML(html, compress) {
        // console.log('HTML:', html);
        var out = '';
        if (html.match(/(?!^)\n/)) {
            _each(html.split('\n'), function (html) {
                if (html) {
                    // 压缩多余空白与注释
                    if (compress) {
                        html = html.replace(/\s+/g, ' ').replace(/<!--.*?-->/g, '');
                    }
                    if (html) {
                        out += ENGINE[1] + _string(html) + ENGINE[2];
                        out += '\n';
                    }
                }
            });
        } else if (html) {
            out += ENGINE[1] + _string(html) + ENGINE[2];
        }
        return out;
    }


    /**
     * 处理代码
     * 
     * @param {any} code
     * @returns
     */
    function parserCode(code) {
        var match;
        // console.log(new RegExp(KEYWORD_PREG));
        if (match = code.match(new RegExp(KEYWORD_PREG))) {
            // console.log(code,':',match);
            var command = match[1];
            var param = match[2];

            switch (command) {
                case 'include': // 编译时包含
                    param = param.trim().split(' ');
                    if (param.length === 1) {
                        param.push("$_unit.value");
                    }
                    param = param.join(',');
                    return ENGINE[1] + '$_unit._include(' + param + ')' + ENGINE[2];
                case 'if':
                    return statmentTest('if(' + param + '){}', 'if (' + param + ') {');
                case 'else':
                    // console.log(param,param.match(/^\s*if\s+(.*)/));
                    if (match = param.match(/^\s*if\s+(.*)/)) {
                        return '} else if (' + match[1] + '){';
                    }
                    return '}else{';
                case '/if':
                case '/while':
                case '/for':
                    return '}';
                case 'while':
                    return statmentTest('while(' + param + '){}', 'while (' + param + ') {');
                case 'for':
                    return statmentTest('for(' + param + '){}', 'for (' + param + ') {');
                case 'each':
                    var match = param.match(/\s*(.+?)\s+(?:(?:as(?:\s+(\w+)))?(?:\s*:\s*(\w+))?)?/);
                    if (match) {
                        var value = match[1];
                        var each_param;
                        if (match[2]) {
                            if (match[3]) {
                                each_param = match[3] + ',' + match[2];
                            } else {
                                each_param = match[2];
                            }
                        } else {
                            each_param = 'value,index';
                        }
                        return '$_unit._each(' + value + ',function(' + each_param + '){';
                    }
                    return 'throw SyntaxError("Null Each Value");$_unit._each(null,function(){';
                case '/each':
                    return '});';
            }
        }
        // 非转义
        else if (match = code.match(/^!.*$/)) {
            return ENGINE[1] + '$_unit._echo(' + match[1] + ')' + ENGINE[2];
        }
        // 转义输出
        else {
            return ENGINE[1] + '$_unit._escape(' + code + ')' + ENGINE[2];
        }
    }



    var _echo = function (value) {
        return new String(value);
    }

    var _escape = function (content) {
        return _echo(content).replace(/&(?![\w#]+;)|[<>"']/g, function (s) {
            return escape[s];
        });
    };

    var _each = function (value, callback) {
        if (is_array(value)) {
            _arrayEach(value, callback);
        } else {
            for (var index in value) {
                callback.call(value[index], value[index], index);
            }
        }
    }
    var _arrayEach = function (value, callback) {
        for (var index = 0; index < value.length; ++index) {
            callback.call(value[index], value[index], index);
        }
    }
    var _objectCopy = function (arrays) {
        var object = {};
        for (var i = 0; i < arguments.length; i++) {
            for (var index in arguments[i]) {
                object[index] = arguments[i][index];
            }
        }
        return object;
    }

    var _include = function (id, value) {
        return new Template(id).render(value);
    }

    /**
     * 生成可显示字符串
     * 
     * @param {any} code
     * @returns
     */
    function _string(code) {
        return "'" + code
            // 单引号与反斜杠转义
            .replace(/('|\\)/g, '\\$1')
            .replace(/\r/g, '\\r')
            .replace(/\n/g, '\\n') + "'";
    }


    /**
     * 判断是否是数组
     * 
     * @param {any} obj
     * @returns
     */
    function is_array(obj) {
        return Object.prototype.toString.call(obj) === '[object Array]';
    }



    /**
     * 提示代码错误
     * 
     * @param {any} name
     * @param {any} content
     * @param {any} line
     * @param {any} e
     */
    function reportError(name, content, line, e) {
        var name = name || 'anonymous';
        var report = 'DxTPL Error:';
        console.group(report);
        if (content) {

            var codes = content.replace(/^\n/, '').split('\n');
            var start = line - 5 > 0 ? line - 5 : 1;
            var end = line + 5 > codes.length ? codes.length : line + 5;
            console.error(e);
            // console.log(codes);
            for (var i = start; i < end; i++) {
                if (i == line) {
                    console.log(i + '|%c' + codes[line - 1] + '\t\t%c->\t\t%c' + e.name + ':' + e.message, 'color:red;', 'color:green;', 'color:red;');
                } else {
                    console.log(i + '|' + codes[i - 1]);
                }
            }

        } else {
            console.log(content);
            console.log('%c' + report + e.message + '\t\t@' + name + ':' + line, 'color:red;');
        }
        console.groupEnd(report);
    }



    /**
     * 编译模板
     * 
     * @param {any} text
     * @param {any} config
     * @returns
     */
    function compileTemplate(text, config) {
        var tpl = '';
        // console.log('code',text);
        text = text.replace(/^\n/, '');
        // console.log(tagstart);
        _each(text.split(config.tagstart), function (value) {
            // console.log('split',value);
            var split = value.split(config.tagend);
            if (split.length === 1) {
                tpl += parserHTML(split[0], config.compress);
            } else {
                tpl += parserCode(split[0]);
                tpl += parserHTML(split[1]);
            }
        });
        return tpl;
    }


    /**
     * 给模板压入变量
     * 
     * @param {any} source
     * @param {any} value
     * @param {any} strict
     * @returns
     */
    function linkValue(source, value, strict) {
        var use_strict = strict === undefined ? true : strict;
        var ext = [];
        ext.push('var $_unit=this,' + ENGINE[0]);
        for (var index in value) {
            ext.push(index + '=this.value.' + index);
        }
        var link_str = '';
        if (use_strict) {
            link_str = '"use strict";';
        }
        link_str += ext.join(',');
        link_str += ';';
        link_str += source + 'return new String(' + ENGINE[3] + ');';
        return link_str;
    }


    function renderTpl(selector, glovalue) {
        var nodes = document.querySelectorAll(selector);
        // console.log(nodes);
        _arrayEach(nodes, function (node, index) {
            var source = node.innerHTML;
            var value;
            var config = default_config;

            if (node.dataset.init) {
                try {
                    var json = new Function('return ' + node.dataset.init + ';');
                    value = json();
                } catch (e) {
                    reportError(selector + '[' + index + ']', null, 0, new Error('Unsupport json'));
                }
            }
            if (node.dataset.config) {
                try {
                    var json = new Function('return ' + node.dataset.config + ';');
                    var conf = json();
                    config = _objectCopy(config, conf);
                } catch (e) {
                    reportError(selector + '[' + index + ']', null, 0, new Error('Unsupport json'));
                }
            }

            value = _objectCopy(value, glovalue);
            var code = compileTemplate(source, config);
            node.innerHTML = render(selector, source, code, value, config.strict);
        });
    };

    /**
     * 渲染模板代码
     * 
     * @param {any} name
     * @param {any} source
     * @param {any} compiled_code
     * @param {any} value
     * @returns
     */
    function render(name, source, compiled_code, value, strict) {
        // console.time('render ' + name);
        var runcode = linkValue(compiled_code, value, strict);
        // console.log(runcode);
        var caller = {
            _each: _each,
            _echo: _echo,
            _escape: _escape,
            _include: _include,
            value: value
        };

        var html;
        try {
            var render = new Function(runcode);
            html = render.call(caller);
        } catch (e) {
            // For Chrome
            var match = new String(e.stack).match(/<anonymous>:(\d+):\d+/);
            // console.log(source);
            // console.log(e);
            if (match) {
                var line = match[1] - 1;
                reportError(name, source, line, e);
            } else {
                var name = name || 'anonymous';
                // For Edge
                var match = new String(e.stack).match(/Function code:(\d+):\d+/);
                if (match) {
                    console.error('DxTPL:Compile Error@' + name + ' Line ' + match[1]);
                } else {
                    console.error('DxTPL:Compile Error@' + name);
                }
            }

        }
        // console.timeEnd('render ' + name);
        return html;
    }

    function getDOMcache(name, config) {
        // console.time('getcache:' + name);
        var cache_parent = document.getElementById('template_caches');
        if (!cache_parent) {
            cache_parent = document.createElement('div');
            cache_parent.id = 'template_caches';
            cache_parent.style.display = 'none';
            document.body.appendChild(cache_parent);
        }
        var cache_name = 'template_cache_' + name;

        var tpl_cache = document.getElementById('template_cache_' + name);
        if (!tpl_cache) {
            tpl_cache = document.createElement('div');
            tpl_cache.id = cache_name;
            tpl_cache.innerText = compileTemplate(document.getElementById(name).innerHTML, config || default_config);
            cache_parent.appendChild(tpl_cache);
        }
        // console.timeEnd('getcache:' + name);
        return tpl_cache.innerText;
    }


    /* ----  编译DOM对象 ----*/
    function compile(id, config) {
        var tplId = id || config.id;
        var anonymous = false;
        if (typeof tplId !== 'string') throw Error('Unsupport Template ID');
        var tpl = document.getElementById(tplId);
        if (tpl) {
            // 获取源码
            config.source = tpl.innerHTML;
        } else {
            // 无法获取，将ID作为源码解析
            config.source = tplId;
            config.id = 'anonymous';
            anonymous = true;
        }
        if (config.code) {
            // 代码已经编译
        } else if (config.cache && !anonymous) {
            config.code = getDOMcache(tplId, config);
        } else {
            config.code = compileTemplate(config.source, config);
        }
        return config;
    }

    /* -----------------  外部函数 public ---------------------------*/

    var Template = function (name, config) {
        this.version='1.0.43';
        var conf = default_config;
        if (typeof name === 'string') {
            // 适配对象
            conf = _objectCopy(conf, config);
            conf.id = name;
        } else {
            // 适配对象
            conf = _objectCopy(conf, name);
        }
        this.config(conf);
    }


    Template.prototype.config = function (config) {
        for (var index in config) {
            this[index] = config[index];
        }
        return this;
    }


    Template.prototype.assign = function (name, value) {
        this.value[name] = _objectCopy(this.value[name], value);
        return this;
    }

    Template.prototype.value = function (value) {
        this.value = _objectCopy(this.value, value);
        return this;
    }

    Template.prototype.compile = function (id) {
        var config = _objectCopy(this, compile(id, this));
        return new Template(config);
    }

    Template.prototype.render = function (value) {
        // 未编译
        if (!(this.source && this.code)) {
            var val = compile(this.id,this);
            this.config(val);
        }
        return render(this.id, this.source, this.code, value, this.strict);
    }

    window.dxtpl = new Template();
    window.Template = Template;
    window.renderTpl = renderTpl;
})(window);
;!(function (dxui) {
    var $ = dxui.dom;
    var Editor = function (node) {
        this.m_node = node;
        var self = this;
        this.buildRichUI();
        // 丢失焦点获取最后编辑的光标位置
        $(this.m_content).on('blur', function () {
            self.m_selection = window.getSelection();
            self.setRange(self.m_selection.getRangeAt(0))
        });
    }

    Editor.prototype = {
        getRange: function () {
            if (this.m_range) {
                return this.m_range;
            }
            var range = document.createRange();
            var node = null;
            if (this.m_content.firstChild) {
                node = this.m_content.firstChild;
            } else {
                node = $.element('p');
                this.m_content.appendChild(node);
            }
            range.selectNode(node);
            return range;
        },
        setRange: function (range) {
            this.m_range = range.cloneRange();
        },
        insertNode: function (element) {
            var range = this.getRange();
            range.insertNode(element);
        },
        buildRichUI: function () {
            var self = this;
            this.m_controls = $.element('div', {
                class: 'editor-controls'
            });
            this.m_content = $.element('div', {
                contenteditable: 'true',
                class: 'editor-content'
            });

            this.m_node.appendChild(this.m_controls);
            this.m_node.appendChild(this.m_content);
            

            var insertHTML = $.element('a', {
                href: '#'
            }, {
                cursor: 'pointer'
            });

            insertHTML.innerHTML = 'Html';
            this.m_controls.appendChild(insertHTML);

            $(insertHTML).on('click', function () {
                var value = prompt('url:');
                var newNode = $.element('div');
                newNode.innerHTML = value;
                self.insertNode(newNode);
            });
        }
    };


    dxui.Editor = Editor;
})(dxui)
;!(function (dxui) {
    /**
     * 创建可移动层
     * 
     * @param {Element} layer 移动层
     * @param {Element} controller 控制移动的层
     * @returns
     */
    dxui.moveable=function moveable(layer, controller) {
        var _controller = controller || layer;
        var _self = layer;
        // 调整层可以移动
        _self.style.position = 'fixed';
        var _move_layer = function (event) {
                // 阻止拖动页面（手机端）
                event.preventDefault();
                var eventMove = 'mousemove',
                    eventEnd = 'mouseup';
                // 手机触屏事件会成多点触控
                if (event.touches) {
                    event = event.touches[0];
                    eventMove = 'touchmove';
                    eventEnd = 'touchend';
                }
                var rect = _controller.getBoundingClientRect();
                var x = event.clientX - rect.left;
                var y = event.clientY - rect.top;
                // 拖拽
                var doc = document;
                if (_self.setCapture) {
                    _self.setCapture();
                } else if (window.captureEvents) {
                    window.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP);
                }

                // 移动
                var winmove = function (e) {
                    if (e.touches) {
                        e = e.touches[0];
                    }
                    var px = e.pageX || (e.clientX + document.body.scrollLeft - document.body.clientLeft);
                    var py = e.pageY || (e.clientY + document.body.scrollTop - document.body.clientTop);

                    var dx = px - x;
                    var dy = py - y;
                    _self.style.left = dx + 'px';
                    _self.style.top = dy + 'px';
                };
                // 停止
                var winend = function (e) {
                    if (_self.releaseCapture) {
                        _self.releaseCapture();
                    } else if (window.releaseEvents) {
                        window.releaseEvents(Event.MOUSEMOVE | Event.MOUSEUP);
                    }
                    doc.removeEventListener(eventMove, winmove);
                    doc.removeEventListener(eventEnd, winend);
                };
                doc.addEventListener(eventMove, winmove);
                doc.addEventListener(eventEnd, winend);
            }
            // 监听起始事件
        _controller.addEventListener('mousedown', _move_layer);
        _controller.addEventListener('touchstart', _move_layer);
        return _self;
    }
})(dxui)
/** Toast 弹出提示 */ ;
!(function (dxui) {
    // 常量
    var TOAST_PARENT_ID = 'Toast-Parent';
    var TOAST_SHOW_ID = 'Toast-Show';
    var TOAST_DEFAULT_STYLE = 'toast';
    var TOAST_POP_LEVEL = 10000;

    var Toast = function (text, time, style) {
        return new Toast.create(text, time, style);
    }

    // Toast队列
    Toast.Queue = new Array();
    // 构造函数
    Toast.create = function (message, time, style) {
        Toast.Parent = document.getElementById(TOAST_PARENT_ID);

        if (!Toast.Parent) {
            Toast.Parent = document.createElement('div');
            Toast.Parent.id = TOAST_PARENT_ID;
            document.body.appendChild(Toast.Parent);
        }
        Toast.Queue.push({
            message: message,
            timeout: time,
            style: style ? TOAST_DEFAULT_STYLE + '-' + style : TOAST_DEFAULT_STYLE,
        });
        Toast.show();
    };


    Toast.show = function () {
        // 一个时刻只能显示一个Toast
        if (document.getElementById(TOAST_SHOW_ID)) return;
        var show = Toast.Queue.shift();
        var toastdiv = dxui.dom.element('div', {
            id: TOAST_SHOW_ID,
            class: show.style
        });
        toastdiv.innerHTML = show.message;
        Toast.Parent.appendChild(toastdiv);

        var margin = window.innerWidth / 2 - toastdiv.scrollWidth / 2;
        var bottom = window.innerHeight - toastdiv.scrollHeight * 2;
        toastdiv.style.marginLeft = margin + 'px';
        toastdiv.style.top = bottom + 'px';
        var timeout = show.timeout || 2000;

        var close = function () {
            dxui.dom(toastdiv).css({
                'transition': 'opacity 0.3s ease-out',
                opacity: 0
            });

            setTimeout(function () {
                Toast.Parent.removeChild(toastdiv);
                if (Toast.Queue.length) {
                     Toast.show();
                }
            }, 300);
        };

        dxui.dom(toastdiv).css({
            position: 'fixed',
            opacity: 1,
            'z-index': TOAST_POP_LEVEL,
            transition: 'opacity 0.1s ease-in'
        });
        setTimeout(close, timeout);
    }
    dxui.Toast = Toast;
})(dxui)
/* HTML5 视频播放器 */
// TODO
;!(function(dxui){
    function VideoPlayer(url,type) {

    }
    dxui.video_player=function(url,type){
        return new VideoPlayer(url,type);
    }

    dxui.VideoPlayer=VideoPlayer;

})(dxui)
;!(function (dxui) {
    
    var Window=function(title,content,config){
        
    }

    Window.prototype={

    };
}(dxui))