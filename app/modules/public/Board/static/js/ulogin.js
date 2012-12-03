if (typeof uLogin == "undefined" || !uLogin.uLogin) {
    var uLogin = {protocol:location.href.match(/^https/i) ? "https" : "http", host:encodeURIComponent(location.host), uLogin:true, ids:[], lang:(navigator.language ? navigator.language : navigator.userLanguage).substr(0, 2), supportedLanguages:["en", "ru", "uk"], dialog:"", close:"", lightbox:"", dialogSocket:"", scrollTimer:false, providerNames:["vkontakte", "odnoklassniki", "mailru", "facebook", "twitter", "google", "yandex", "livejournal", "openid", "lastfm", "linkedin", "liveid", "soundcloud", "steam", "flickr", "youtube", "vimeo", "webmoney"], states:["ready", "receive", "open", "close"], get:function (a) {
        return document.getElementById(a)
    }, exists:function (a) {
        return(typeof a != "undefined")
    }, add:function (a, c, b) {
        if (a.addEventListener) {
            a.addEventListener(c, function (d) {
                b(a, d)
            }, false)
        } else {
            if (a.attachEvent) {
                a.attachEvent("on" + c, function (d) {
                    b(a, d)
                })
            } else {
                a["on" + c] = function (d) {
                    b(a, d)
                }
            }
        }
    }, is_encoded:function (a) {
        return decodeURIComponent(a) != a
    }, genID:function () {
        var b = "ul_";
        var c = new Date();
        var a = c.getTime() + Math.floor(Math.random() * 100000);
        while (uLogin.get(b + a)) {
            a = new Date();
            a = c.getTime() + Math.floor(Math.random() * 100000)
        }
        return b + a
    }, show:function (a) {
        if (this.exists(a)) {
            a.style.visibility = "visible"
        }
    }, hide:function (a) {
        if (this.exists(a)) {
            a.style.visibility = "hidden"
        }
    }, parse:function (c) {
        var e = new Object();
        if (!c) {
            return e
        }
        var d = c.split("&");
        d = d.length > 1 ? d : c.split(";");
        for (var a = 0; a < d.length; a++) {
            var b = d[a].split("=");
            if (b[0]) {
                b[0] = b[0].trim()
            }
            if (b[1]) {
                b[1] = b[1].trim()
            }
            e[b[0]] = b[1]
        }
        return e
    }, def:function (a, b, c) {
        return(this.exists(a[b]) ? a[b] : c)
    }, scrollTop:function () {
        return window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop
    }, scrollLeft:function () {
        return window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft
    }, clientWidth:function () {
        var a = 0;
        if (Object.prototype.toString.call(window.opera) == "[object Opera]" && (window.parseFloat(window.opera.version()) < 9.5)) {
            a = document.body.clientWidth
        } else {
            if (window.innerWidth) {
                a = window.innerWidth
            }
        }
        if (uLogin.isIE()) {
            a = document.documentElement.clientWidth
        }
        return a
    }, clientHeight:function () {
        var a = 0;
        if (Object.prototype.toString.call(window.opera) == "[object Opera]" && (window.parseFloat(window.opera.version()) < 9.5)) {
            a = document.body.clientHeight
        } else {
            if (window.innerHeight) {
                a = window.innerHeight
            }
        }
        if (uLogin.isIE()) {
            a = document.documentElement.clientHeight
        }
        return a
    }, hideAll:function () {
        if (this.lightbox) {
            this.hide(this.lightbox);
            this.hide(this.dialog);
            this.hide(this.close)
        }
        for (var a = 0; a < this.ids.length; a++) {
            this.ids[a].showed = false;
            this.hide(this.ids[a].hiddenW);
            this.hide(this.ids[a].hiddenA)
        }
    }, isIE:function () {
        if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) {
            var a = new Number(RegExp.$1);
            if (a < 9) {
                return a
            }
        }
        return false
    }, extraction:function () {
        var a = 0;
        var c = new Array();
        var e = new Array();
        var b = document.getElementsByTagName("div");
        var d = document.getElementsByTagName("a");
        while (d[a]) {
            if (d[a]) {
                c[a] = d[a]
            }
            a++
        }
        a = 0;
        while (b[a]) {
            if (b[a]) {
                e[a] = b[a]
            }
            a++
        }
        a = 0;
        while (e[a] || c[a]) {
            if (e[a]) {
                uLogin.addWidget(e[a])
            }
            if (c[a]) {
                uLogin.addWidget(c[a])
            }
            a++
        }
    }, addWidget:function (c) {
        var g = "";
        var e = "";
        if (c.id) {
            g = c.id;
            e = c.getAttribute("x-ulogin-params");
            e = e ? e : c.getAttribute("data-ulogin")
        }
        if (g && e) {
            var d = this.parse(e);
            var a = true;
            for (var b = 0; b < this.ids.length; b++) {
                if (g == this.ids[b].id) {
                    a = false;
                    break
                }
            }
            if (a) {
                var f = this.ids.length;
                this.setWidgetProperties(g, f, d)
            }
        }
    }, initWidget:function (e) {
        if (!e) {
            return
        }
        var b = uLogin.get(e);
        if (!b) {
            return
        }
        var f = b.getAttribute("x-ulogin-params");
        f = f ? f : b.getAttribute("data-ulogin");
        if (f) {
            var d = uLogin.parse(f);
            var c = false;
            var a;
            for (a = 0; a < uLogin.ids.length; a++) {
                if (e == uLogin.ids[a].id) {
                    c = true;
                    break
                }
            }
            if (!c) {
                a = uLogin.ids.length;
                uLogin.setWidgetProperties(e, a, d)
            } else {
                if (!uLogin.ids[a].initCheck) {
                    uLogin.ids[a].initCheck = window.setInterval(function () {
                        if (uLogin.ids[a].done) {
                            window.clearInterval(uLogin.ids[a].initCheck);
                            uLogin.setWidgetProperties(e, a, d)
                        }
                    }, 100)
                }
            }
        }
    }, setWidgetProperties:function (e, d, b) {
        this.ids[d] = {id:e, dropTimer:false, initCheck:false, done:false, type:this.def(b, "display", ""), providers:this.def(b, "providers", ""), hidden:this.def(b, "hidden", ""), redirect_uri:this.def(b, "redirect_uri", ""), callback:this.def(b, "callback", ""), fields:this.def(b, "fields", "first_name,last_name"), optional:this.def(b, "optional", ""), color:this.def(b, "color", "fff"), opacity:this.def(b, "opacity", "75"), verify:this.def(b, "verify", ""), lang:this.def(b, "lang", uLogin.lang), state:"", ready_func:new Array(), receive_func:new Array(), open_func:new Array(), close_func:new Array()};
        this.ids[d].redirect_uri = uLogin.is_encoded(this.ids[d].redirect_uri) ? this.ids[d].redirect_uri.replace(/\//g, "%2F") : encodeURIComponent(this.ids[d].redirect_uri);
        if (uLogin.supportedLanguages.indexOf(this.ids[d].lang) == -1) {
            this.ids[d].lang = uLogin.lang
        }
        if (typeof easyXDM == "undefined" && this.ids.length <= 1) {
            var a = document.createElement("script");
            a.src = "//ulogin.ru/js/easyXDM.min.js";
            document.body.appendChild(a)
        }
        var c = window.setInterval(function () {
            if (typeof easyXDM != "undefined") {
                if (typeof easyXDM.Socket != "undefined") {
                    window.clearInterval(c);
                    switch (b.display) {
                        case"small":
                        case"panel":
                            uLogin.ids[d].listener_id = false;
                            uLogin.initPanel(d);
                            break;
                        case"window":
                            uLogin.initWindow(d);
                            break;
                        case"buttons":
                            uLogin.initButtons(d, uLogin.def(b, "receiver", uLogin.ids[d].redirect_uri));
                            break;
                        default:
                            uLogin.ids.splice(d, d);
                            break
                    }
                }
            }
        }, 100)
    }, init:function (c) {
        if (c == "") {
            uLogin.add(window, "load", function (g, f) {
                uLogin.extraction()
            });
            c = document.getElementsByTagName("script");
            c = c[c.length - 1].src;
            if (c.indexOf("?") == -1) {
                c = c + "?"
            }
            c = c.substr(c.indexOf("?") + 1)
        }
        if (c != "") {
            var b = this.parse(c);
            if (b.display) {
                var e = this.def(b, "id", "uLogin");
                if (this.get(e)) {
                    cont = true;
                    for (var a = 0; a < this.ids.length; a++) {
                        if (e == this.ids[a].id) {
                            cont = false
                        }
                    }
                    if (cont) {
                        var d = this.ids.length;
                        this.setWidgetProperties(this.def(b, "id", "uLogin"), d, b)
                    }
                } else {
                    window.setTimeout('uLogin.init("' + c + '")', 1000)
                }
            }
        }
    }, initSocket:function (e, a, d, f, c) {
        var b = new easyXDM.Socket({remote:e, swf:uLogin.isIE() ? "https://ulogin.ru/js/easyxdm.swf" : "", props:d, container:a, onMessage:function (h, g) {
            if (uLogin.states.indexOf(h) > -1) {
                uLogin._changeState(f, h)
            } else {
                if (typeof window[uLogin.ids[f].callback] != "undefined") {
                    window[uLogin.ids[f].callback](h);
                    if (uLogin.dialog) {
                        uLogin.lightbox.style.display = "none";
                        uLogin.dialog.style.display = "none";
                        uLogin.hide(uLogin.close)
                    }
                }
            }
        }});
        return b
    }, getWidgetNumber:function (b) {
        for (var a = 0; a < uLogin.ids.length; a++) {
            if (b == uLogin.ids[a].id) {
                return a
            }
        }
        return NaN
    }, initWindow:function (g) {
        var a = document.createElement("div");
        if ((this.lightbox == "" || this.close == "" || this.dialog == "")) {
            a.innerHTML = '<div style="position:absolute;z-index:9999;left:0;top:0;margin:0;padding:0;width:100%;height:100%;background:#' + this.ids[g].color + ";opacity:0." + this.ids[g].opacity + ";filter:progid:DXImageTransform.Microsoft.Alpha(opacity=" + this.ids[g].opacity + ');display:none;"></div>';
            this.lightbox = a.firstChild;
            a.innerHTML = '<div id = "' + uLogin.genID() + '" style="position:absolute;z-index:9999;left:0;top:0;margin:0;padding:0;width:564px;height:358px;border:10px solid #666;border-radius:8px;display:none;"></div>';
            this.dialog = a.firstChild;
            a.innerHTML = '<img style="width:30px;height:30px;position:absolute;z-index:9999;border:0px;left:0;top:0;margin:0;padding:0;background:url(https://ulogin.ru/img/x.png);cursor:pointer;visibility:hidden" src="https://ulogin.ru/img/blank.gif"/>';
            this.close = a.firstChild;
            this.add(this.close, "click", function (i, h) {
                uLogin.lightbox.style.display = "none";
                uLogin.dialog.style.display = "none";
                uLogin.hide(uLogin.close)
            });
            this.add(this.lightbox, "click", function (i, h) {
                uLogin.lightbox.style.display = "none";
                uLogin.dialog.style.display = "none";
                uLogin.hide(uLogin.close)
            });
            this.add(this.close, "mouseover", function (i, h) {
                i.style.background = "url(https://ulogin.ru/img/x_.png)"
            });
            this.add(this.close, "mouseout", function (i, h) {
                i.style.background = "url(https://ulogin.ru/img/x.png)"
            });
            document.body.appendChild(this.lightbox);
            document.body.appendChild(this.dialog);
            document.body.appendChild(this.close);
            var b = this.get(this.ids[g].id);
            var c = b.getElementsByTagName("img");
            var e = c[0];
            var f = (this.ids[g].lang == "ru" ? "https://ulogin.ru/img/button.png" : "https://ulogin.ru/img/" + this.ids[g].lang + "/button.png");
            var d = (this.ids[g].lang == "ru" ? "https://ulogin.ru/img/button_.png" : "https://ulogin.ru/img/" + this.ids[g].lang + "/button_.png");
            if (e) {
                e.src = f;
                e.style.border = "none";
                this.add(e, "mouseover", function (h, i) {
                    if (h.src != d) {
                        h.src = d
                    }
                });
                this.add(e, "mouseout", function (h, i) {
                    if (h.src != f) {
                        h.src = f
                    }
                })
            }
        }
        if (!this.ids[g].done) {
            this.add(this.get(this.ids[g].id), "click", function (h, j) {
                if (j.preventDefault) {
                    j.preventDefault()
                } else {
                    j.returnValue = false
                }
                var i = h.id ? h : j.srcElement;
                if (i) {
                    uLogin.showWindow(i.id)
                }
                return false
            });
            uLogin.add(window, "scroll", function (i, h) {
                uLogin.onMoveWindow()
            });
            uLogin.add(window, "resize", function (i, h) {
                uLogin.onMoveWindow()
            });
            this.ids[g].done = true
        }
    }, onMoveWindow:function () {
        uLogin.lightbox.style.left = uLogin.scrollLeft() + "px";
        uLogin.lightbox.style.top = uLogin.scrollTop() + "px";
        if (uLogin.scrollTimer) {
            window.clearTimeout(uLogin.scrollTimer)
        }
        uLogin.scrollTimer = window.setTimeout(uLogin.moveWindow, 200)
    }, showWindow:function (c) {
        var b = uLogin.getWidgetNumber(c);
        var a = "https://ulogin.ru/window.html?id=" + b + "&redirect_uri=" + uLogin.ids[b].redirect_uri + "&callback=" + uLogin.ids[b].callback + "&fields=" + uLogin.ids[b].fields + "&optional=" + uLogin.ids[b].optional;
        a += "&protocol=" + uLogin.protocol;
        a += "&host=" + uLogin.host;
        a += "&lang=" + this.ids[b].lang;
        a += "&verify=" + this.ids[b].verify;
        if (uLogin.dialogSocket != "") {
            uLogin.dialogSocket.destroy()
        }
        uLogin.dialogSocket = uLogin.initSocket(a, uLogin.dialog.getAttribute("id"), {style:{margin:"0", padding:"0", background:"#fff", width:"564px", height:"358px", border:"0", overflow:"hidden"}, frameBorder:"0"}, b);
        uLogin.lightbox.style.left = uLogin.scrollLeft() + "px";
        uLogin.lightbox.style.top = uLogin.scrollTop() + "px";
        uLogin.dialog.style.left = Math.floor(uLogin.scrollLeft() + (uLogin.clientWidth() - 564) / 2) + "px";
        uLogin.dialog.style.top = Math.floor(uLogin.scrollTop() + (uLogin.clientHeight() - 358) / 2) + "px";
        uLogin.close.style.left = Math.floor(uLogin.scrollLeft() + (uLogin.clientWidth() + 562) / 2) + "px";
        uLogin.close.style.top = Math.floor(uLogin.scrollTop() + (uLogin.clientHeight() - 374) / 2) + "px";
        uLogin.lightbox.style.display = "block";
        uLogin.dialog.style.display = "block";
        uLogin.lightbox.style.visibility = "";
        uLogin.dialog.style.visibility = "";
        uLogin.show(uLogin.close)
    }, moveWindow:function () {
        var e = (Math.floor(uLogin.scrollLeft() + (uLogin.clientWidth() - 564) / 2) - new Number(uLogin.dialog.style.left.slice(0, -2))) / 10;
        var c = (Math.floor(uLogin.scrollTop() + (uLogin.clientHeight() - 358) / 2) - new Number(uLogin.dialog.style.top.slice(0, -2))) / 10;
        var b = (Math.floor(uLogin.scrollLeft() + (uLogin.clientWidth() + 562) / 2) - new Number(uLogin.close.style.left.slice(0, -2))) / 10;
        var d = (Math.floor(uLogin.scrollTop() + (uLogin.clientHeight() - 374) / 2) - new Number(uLogin.close.style.top.slice(0, -2))) / 10;
        for (var a = 0; a < 10; a++) {
            uLogin.dialog.style.left = e + new Number(uLogin.dialog.style.left.slice(0, -2)) + "px";
            uLogin.dialog.style.top = c + new Number(uLogin.dialog.style.top.slice(0, -2)) + "px";
            uLogin.close.style.left = b + new Number(uLogin.close.style.left.slice(0, -2)) + "px";
            uLogin.close.style.top = d + new Number(uLogin.close.style.top.slice(0, -2)) + "px"
        }
    }, initPanel:function (b) {
        var c = uLogin.get(uLogin.ids[b].id);
        c.innerHTML = "";
        var i = true;
        var l = uLogin.ids[b].type == "small" ? 21 : 42;
        var k = uLogin.ids[b].type == "small" ? 16 : 32;
        var d = uLogin.ids[b].type == "small" ? "0 5px 0 0" : "0 10px 0 0";
        var j = uLogin.ids[b].type == "small" ? "url(https://ulogin.ru/img/small4.png) 0 0" : "url(https://ulogin.ru/img/panel4.png) 0 -3px";
        var g = uLogin.ids[b].type == "small" ? 1 : 2;
        if (this.ids[b].providers) {
            var m = document.createElement("div");
            var a = "https://ulogin.ru/panel.html?id=" + b + "&display=" + g + "&redirect_uri=" + this.ids[b].redirect_uri + "&callback=" + this.ids[b].callback + "&providers=" + this.ids[b].providers + "&fields=" + this.ids[b].fields + "&optional=" + this.ids[b].optional;
            a += "&protocol=" + uLogin.protocol;
            a += "&host=" + uLogin.host;
            a += "&lang=" + this.ids[b].lang;
            a += "&verify=" + this.ids[b].verify;
            uLogin.initSocket(a, uLogin.ids[b].id, {style:{display:"inline-block", margin:"0", padding:"0", width:(this.ids[b].providers.split(",").length * l) + "px", height:k + "px", border:"0", overflow:"hidden"}, frameBorder:"0", allowTransparency:"true"}, b);
            if (this.ids[b].hidden) {
                var e = this.ids[b].providers.split(",");
                for (var h in this.providerNames) {
                    if (!e[h]) {
                        i = false;
                        break
                    }
                }
            } else {
                i = false
            }
        } else {
            i = false
        }
        function f() {
            if (uLogin.ids[b].listener_id) {
                uLogin.removeStateListener(uLogin.ids[b].id, uLogin.ids[b].listener_id, "ready")
            }
            if (!i && uLogin.ids[b].hidden != "" && !uLogin.ids[b].done) {
                var n = document.createElement("div");
                var o = uLogin.ids[b].opacity;
                n.innerHTML = '<img src="https://ulogin.ru/img/blank.gif" style="position:relative;width:' + k + "px;height:" + k + "px;margin:" + d + ";cursor:pointer;background:" + j + ';vertical-align:none;border:0px;"/>';
                uLogin.add(n.firstChild, "mouseover", function (q, p) {
                    uLogin.ids[b].showed = false;
                    uLogin.dropdownDelayed(b, g);
                    q.style.filter = "alpha(opacity=" + o + ") progid:DXImageTransform.Microsoft.AlphaImageLoader(src=transparent.png, sizingMethod='crop')";
                    q.style.opacity = parseFloat(o) / 100
                });
                uLogin.add(n.firstChild, "mouseout", function (q, p) {
                    uLogin.ids[b].showed = true;
                    uLogin.dropdownDelayed(b, g);
                    q.style.filter = "";
                    q.style.opacity = ""
                });
                uLogin.add(n.firstChild, "click", function (q, p) {
                    uLogin.dropdown(b, g)
                });
                uLogin.ids[b].drop = n.firstChild;
                uLogin.get(uLogin.ids[b].id).appendChild(uLogin.ids[b].drop);
                uLogin.initDrop(b);
                uLogin.ids[b].listener_id = uLogin.setStateListener(uLogin.ids[b].id, "ready", function () {
                    uLogin.ids[b].done = true;
                    uLogin.removeStateListener(uLogin.ids[b].id, uLogin.ids[b].listener_id, "ready")
                })
            } else {
                if (uLogin.ids[b].hidden == "" || i) {
                    uLogin.ids[b].done = true
                }
            }
        }

        if (this.ids[b].providers) {
            uLogin.ids[b].listener_id = uLogin.setStateListener(uLogin.ids[b].id, "ready", f)
        } else {
            f()
        }
    }, initDrop:function (c) {
        if (this.ids[c].hidden != "") {
            var h = document.createElement("div");
            var b = "128";
            var i = -2;
            var e = this.get(this.ids[c].id);
            var j = uLogin.genID();
            if (this.ids[c].hidden == "other") {
                var f = this.providerNames.slice(0);
                var d = this.ids[c].providers.split(",");
                for (var g in d) {
                    f.splice(f.indexOf(d[g]), 1)
                }
                this.ids[c].hidden = f.toString()
            }
            h.innerHTML = '<div id = "' + j + '" style="position:absolute;z-index:9999;left:100px;top:200px;margin:0;padding:0;width:' + b + "px;height:" + (this.ids[c].hidden.split(",").length * 23 + i) + 'px;border:5px solid #666;border-radius:4px;visibility:hidden"></div>';
            this.ids[c].hiddenW = h.firstChild;
            e.appendChild(this.ids[c].hiddenW);
            var a = "https://ulogin.ru/drop.html?id=" + c + "&redirect_uri=" + this.ids[c].redirect_uri + "&callback=" + this.ids[c].callback + "&providers=" + this.ids[c].hidden + "&fields=" + this.ids[c].fields + "&optional=" + uLogin.ids[c].optional;
            a += "&protocol=" + uLogin.protocol;
            a += "&host=" + uLogin.host;
            a += "&lang=" + this.ids[c].lang;
            a += "&verify=" + this.ids[c].verify;
            uLogin.initSocket(a, j, {style:{position:"relative", margin:"0", padding:"0", background:"#fff", width:"128px", height:(this.ids[c].hidden.split(",").length * 23 - 2) + "px", border:"0", overflow:"hidden"}, frameBorder:"0"}, c);
            h.innerHTML = '<div style="position:absolute;background:#000;left:82px;top:' + (this.ids[c].hidden.split(",").length * 23 - 7) + 'px;margin:0;padding:0;width:41px;height:13px;border:5px solid #666;border-radius:0px;text-align:center"><a href="https://ulogin.ru/" target="_blank" style="display:block;margin:0px;width:41px;height:13px;background:url(https://ulogin.ru/img/text.png) no-repeat;"></a></div>';
            this.ids[c].hiddenW.appendChild(h.firstChild);
            h.innerHTML = '<img src="https://ulogin.ru/img/link.png" style="width:8px;height:4px;position:absolute;z-index:9999;margin:0;padding:0;visibility:hidden"/>';
            this.ids[c].hiddenA = h.firstChild;
            e.appendChild(this.ids[c].hiddenA);
            this.ids[c].showed = false;
            this.add(document.body, "click", function (k, m) {
                if (!m.target) {
                    m.target = m.srcElement
                }
                for (var l = 0; l < uLogin.ids.length; l++) {
                    if (m.target != uLogin.ids[l].drop) {
                        uLogin.hide(uLogin.ids[l].hiddenW);
                        uLogin.hide(uLogin.ids[l].hiddenA)
                    }
                }
            });
            if (uLogin.ids[c].hiddenW && uLogin.ids[c].hiddenA) {
                this.add(uLogin.ids[c].hiddenW, "mouseout", function (k, l) {
                    uLogin.dropdownDelayed(c, 0)
                });
                this.add(uLogin.ids[c].hiddenA, "mouseout", function (k, l) {
                    uLogin.dropdownDelayed(c, 0)
                });
                this.add(uLogin.ids[c].hiddenW, "mouseover", function (k, l) {
                    uLogin.clearDropTimer(c)
                });
                this.add(uLogin.ids[c].hiddenA, "mouseover", function (k, l) {
                    uLogin.clearDropTimer(c)
                })
            }
        }
    }, showDrop:function (e, b) {
        if (!uLogin.ids[e].hiddenW && !uLogin.ids[e].hiddenA) {
            return
        }
        if (uLogin.ids[e].showed || b == 0) {
            uLogin.ids[e].showed = false;
            uLogin.hide(uLogin.ids[e].hiddenW);
            uLogin.hide(uLogin.ids[e].hiddenA)
        } else {
            uLogin.ids[e].showed = true;
            var d = 0, c = 0, a = uLogin.ids[e].drop;
            d += a.offsetLeft;
            c += a.offsetTop;
            d -= a.scrollLeft;
            c -= a.scrollTop;
            uLogin.ids[e].hiddenW.style.left = (d - (b == 1 ? 100 : 106)) + "px";
            uLogin.ids[e].hiddenW.style.top = (c + (b == 1 ? 21 : 37)) + "px";
            uLogin.ids[e].hiddenA.style.left = (d + (b == 1 ? 4 : 12)) + "px";
            uLogin.ids[e].hiddenA.style.top = (c + (b == 1 ? 17 : 33)) + "px";
            uLogin.show(uLogin.ids[e].hiddenA);
            uLogin.show(uLogin.ids[e].hiddenW)
        }
    }, clearDropTimer:function (a) {
        if (uLogin.ids[a].dropTimer) {
            window.clearTimeout(uLogin.ids[a].dropTimer)
        }
    }, dropdown:function (b, a) {
        uLogin.clearDropTimer(b);
        uLogin.showDrop(b, a)
    }, dropdownDelayed:function (b, a) {
        uLogin.clearDropTimer(b);
        uLogin.ids[b].dropTimer = window.setTimeout(function () {
            uLogin.showDrop(b, a)
        }, 600)
    }, initButtons:function (c, a) {
        var b = uLogin.get(uLogin.ids[c].id);
        a = uLogin.is_encoded(a) ? a.replace(/\//g, "%2F") : encodeURIComponent(a);
        uLogin._proceedChildren(b, uLogin._initButton, c, a);
        uLogin._changeState(c, uLogin.states[0]);
        uLogin.ids[c].done = true
    }, _proceedChildren:function (e, c, g, d) {
        var b = e.childNodes;
        var a = 0;
        for (a = 0; a < b.length; a++) {
            var f = b[a];
            if (f.getAttribute) {
                c(f, g, d)
            }
            uLogin._proceedChildren(f, c, g, d)
        }
    }, _initButton:function (c, g, e) {
        var f = c.getAttribute("x-ulogin-button");
        if (f) {
            if (uLogin.providerNames.indexOf(f) > -1) {
                var b = e.match(/^https/i) ? "https" : "http";
                if (b != uLogin.protocol) {
                    d = ":";
                    var a = e.split(d);
                    if (a.length == 1) {
                        var d = "%3A";
                        a = e.split(d)
                    }
                    a.splice(0, 1);
                    e = uLogin.protocol + d + a.join(d)
                }
                uLogin.add(c, "mouseover", function (i) {
                    var h = uLogin.ids[g].opacity;
                    i.style.filter = "alpha(opacity=" + h + ") progid:DXImageTransform.Microsoft.AlphaImageLoader(src=transparent.png, sizingMethod='crop')";
                    i.style.opacity = parseFloat(h) / 100
                });
                uLogin.add(c, "mouseout", function (i, h) {
                    i.style.filter = "";
                    i.style.opacity = ""
                });
                uLogin.add(c, "click", function (k, j) {
                    var m = k.getAttribute("x-ulogin-button");
                    var l = "https://ulogin.ru/auth.php?name=" + m + "&window=3&lang=" + uLogin.lang + "&fields=" + uLogin.ids[g].fields + "&optional=" + uLogin.ids[g].optional + "&redirect_uri=" + uLogin.ids[g].redirect_uri + "&verify=" + uLogin.ids[g].verify + "&callback=" + uLogin.ids[g].callback + "&screen=" + screen.width + "x" + screen.height + "&q=" + e;
                    uLogin._changeState(g, uLogin.states[1]);
                    var i = window.open(l, "uLogin", "width=800,height=600,left=" + ((screen.width - 800) / 2) + ",top=" + ((screen.height - 600) / 2));
                    var h = window.setInterval(function () {
                        if (i) {
                            if (i.closed) {
                                window.clearInterval(h);
                                uLogin._changeState(g, uLogin.states[0])
                            }
                        }
                    }, 50)
                })
            }
        }
    }, checkCurrentWidgets:function () {
        var a = 0;
        while (uLogin.ids[a]) {
            if (uLogin.ids[a].type != "window") {
                var b = uLogin.get(uLogin.ids[a].id);
                if (b) {
                    var c = b.getElementsByTagName("iframe");
                    if (!c.length && uLogin.ids[a].done) {
                        uLogin.initWidget(uLogin.ids[a].id)
                    }
                }
            } else {
                uLogin.initWindow(a)
            }
            a++
        }
    }, setStateListener:function (e, c, b) {
        var d = false;
        var a = uLogin.getWidgetNumber(e);
        if (a != NaN && uLogin.ids[a]) {
            switch (c) {
                case"ready":
                    d = uLogin.ids[a].ready_func.push(b);
                    break;
                case"receive":
                    d = uLogin.ids[a].receive_func.push(b);
                    break;
                case"open":
                    d = uLogin.ids[a].open_func.push(b);
                    break;
                case"close":
                    d = uLogin.ids[a].close_func.push(b);
                    break;
                default:
                    break
            }
        }
        return d - 1
    }, removeStateListener:function (b, d, c) {
        var a = uLogin.getWidgetNumber(b);
        if (a != NaN && uLogin.states.indexOf(c) > -1) {
            switch (c) {
                case"ready":
                    if (uLogin.ids[a].ready_func.length >= d) {
                        uLogin.ids[a].ready_func.splice(d, 1)
                    }
                    break;
                case"receive":
                    if (uLogin.ids[a].receive_func.length >= d) {
                        uLogin.ids[a].receive_func.splice(d, 1)
                    }
                    break;
                case"open":
                    if (uLogin.ids[a].open_func.length >= d) {
                        uLogin.ids[a].open_func.splice(d, 1)
                    }
                    break;
                case"close":
                    if (uLogin.ids[a].close_func.length > d) {
                        uLogin.ids[a].close_func.splice(d, 1)
                    }
                    break;
                default:
                    break
            }
        }
    }, _changeState:function (b, c) {
        if (uLogin.ids[b]) {
            uLogin.ids[b].state = c;
            var a = 0;
            switch (c) {
                case"ready":
                    while (uLogin.ids[b].ready_func[a]) {
                        uLogin.ids[b].ready_func[a]();
                        a++
                    }
                    break;
                case"receive":
                    while (uLogin.ids[b].receive_func[a]) {
                        uLogin.ids[b].receive_func[a]();
                        a++
                    }
                    break;
                case"open":
                    while (uLogin.ids[b].open_func[a]) {
                        uLogin.ids[b].open_func[a]();
                        a++
                    }
                    break;
                case"close":
                    while (uLogin.ids[b].close_func[a]) {
                        uLogin.ids[b].close_func[a]();
                        a++
                    }
                    break;
                default:
                    break
            }
        }
    }};
    if (!Array.indexOf) {
        Array.prototype.indexOf = function (b) {
            for (var a = 0; a < this.length; a++) {
                if (this[a] == b) {
                    return a
                }
            }
            return -1
        }
    }
    if (!String.prototype.trim) {
        String.prototype.trim = function () {
            return this.replace(/^\s+|\s+$/g, "")
        }
    }
    if (uLogin.supportedLanguages.indexOf(uLogin.lang) == -1) {
        uLogin.lang = uLogin.supportedLanguages[0]
    }
    uLogin.init(typeof uLogin_query != "undefined" ? uLogin_query : "");
    setInterval(function () {
        uLogin.checkCurrentWidgets()
    }, 500)
}
function receiver(a, b) {
    window[b](a)
}
function redirect(c, b) {
    var d = document.createElement("form");
    d.action = decodeURIComponent(b);
    d.method = "post";
    d.target = "_top";
    d.style.display = "none";
    var a = document.createElement("input");
    a.type = "hidden";
    a.name = "token";
    a.value = c;
    d.appendChild(a);
    document.body.appendChild(d);
    d.submit()
};