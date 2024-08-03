/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){"use strict";function b(b){var c=[{re:/[\xC0-\xC6]/g,ch:"A"},{re:/[\xE0-\xE6]/g,ch:"a"},{re:/[\xC8-\xCB]/g,ch:"E"},{re:/[\xE8-\xEB]/g,ch:"e"},{re:/[\xCC-\xCF]/g,ch:"I"},{re:/[\xEC-\xEF]/g,ch:"i"},{re:/[\xD2-\xD6]/g,ch:"O"},{re:/[\xF2-\xF6]/g,ch:"o"},{re:/[\xD9-\xDC]/g,ch:"U"},{re:/[\xF9-\xFC]/g,ch:"u"},{re:/[\xC7-\xE7]/g,ch:"c"},{re:/[\xD1]/g,ch:"N"},{re:/[\xF1]/g,ch:"n"}];return a.each(c,function(){b=b.replace(this.re,this.ch)}),b}function c(a){var b={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#x27;","`":"&#x60;"},c="(?:"+Object.keys(b).join("|")+")",d=new RegExp(c),e=new RegExp(c,"g"),f=null==a?"":""+a;return d.test(f)?f.replace(e,function(a){return b[a]}):f}function d(b,c){var d=arguments,f=b,g=c;[].shift.apply(d);var h,i=this.each(function(){var b=a(this);if(b.is("select")){var c=b.data("selectpicker"),i="object"==typeof f&&f;if(c){if(i)for(var j in i)i.hasOwnProperty(j)&&(c.options[j]=i[j])}else{var k=a.extend({},e.DEFAULTS,a.fn.selectpicker.defaults||{},b.data(),i);b.data("selectpicker",c=new e(this,k,g))}"string"==typeof f&&(h=c[f]instanceof Function?c[f].apply(c,d):c.options[f])}});return"undefined"!=typeof h?h:i}String.prototype.includes||!function(){var a={}.toString,b=function(){try{var a={},b=Object.defineProperty,c=b(a,a,a)&&b}catch(d){}return c}(),c="".indexOf,d=function(b){if(null==this)throw TypeError();var d=String(this);if(b&&"[object RegExp]"==a.call(b))throw TypeError();var e=d.length,f=String(b),g=f.length,h=arguments.length>1?arguments[1]:void 0,i=h?Number(h):0;i!=i&&(i=0);var j=Math.min(Math.max(i,0),e);return g+j>e?!1:-1!=c.call(d,f,i)};b?b(String.prototype,"includes",{value:d,configurable:!0,writable:!0}):String.prototype.includes=d}(),String.prototype.startsWith||!function(){var a=function(){try{var a={},b=Object.defineProperty,c=b(a,a,a)&&b}catch(d){}return c}(),b={}.toString,c=function(a){if(null==this)throw TypeError();var c=String(this);if(a&&"[object RegExp]"==b.call(a))throw TypeError();var d=c.length,e=String(a),f=e.length,g=arguments.length>1?arguments[1]:void 0,h=g?Number(g):0;h!=h&&(h=0);var i=Math.min(Math.max(h,0),d);if(f+i>d)return!1;for(var j=-1;++j<f;)if(c.charCodeAt(i+j)!=e.charCodeAt(j))return!1;return!0};a?a(String.prototype,"startsWith",{value:c,configurable:!0,writable:!0}):String.prototype.startsWith=c}(),Object.keys||(Object.keys=function(a,b,c){c=[];for(b in a)c.hasOwnProperty.call(a,b)&&c.push(b);return c}),a.expr[":"].icontains=function(b,c,d){var e=a(b),f=(e.data("tokens")||e.text()).toUpperCase();return f.includes(d[3].toUpperCase())},a.expr[":"].ibegins=function(b,c,d){var e=a(b),f=(e.data("tokens")||e.text()).toUpperCase();return f.startsWith(d[3].toUpperCase())},a.expr[":"].aicontains=function(b,c,d){var e=a(b),f=(e.data("tokens")||e.data("normalizedText")||e.text()).toUpperCase();return f.includes(d[3].toUpperCase())},a.expr[":"].aibegins=function(b,c,d){var e=a(b),f=(e.data("tokens")||e.data("normalizedText")||e.text()).toUpperCase();return f.startsWith(d[3].toUpperCase())};var e=function(b,c,d){d&&(d.stopPropagation(),d.preventDefault()),this.$element=a(b),this.$newElement=null,this.$button=null,this.$menu=null,this.$lis=null,this.options=c,null===this.options.title&&(this.options.title=this.$element.attr("title")),this.val=e.prototype.val,this.render=e.prototype.render,this.refresh=e.prototype.refresh,this.setStyle=e.prototype.setStyle,this.selectAll=e.prototype.selectAll,this.deselectAll=e.prototype.deselectAll,this.destroy=e.prototype.remove,this.remove=e.prototype.remove,this.show=e.prototype.show,this.hide=e.prototype.hide,this.init()};e.VERSION="1.7.2",e.DEFAULTS={noneSelectedText:"Nothing selected",noneResultsText:"No results matched {0}",countSelectedText:function(a,b){return 1==a?"{0} item selected":"{0} items selected"},maxOptionsText:function(a,b){return[1==a?"Limit reached ({n} item max)":"Limit reached ({n} items max)",1==b?"Group limit reached ({n} item max)":"Group limit reached ({n} items max)"]},selectAllText:"Select All",deselectAllText:"Deselect All",doneButton:!1,doneButtonText:"Close",multipleSeparator:", ",styleBase:"btn",style:"btn-default",size:"auto",title:null,selectedTextFormat:"values",width:!1,container:!1,hideDisabled:!1,showSubtext:!1,showIcon:!0,showContent:!0,dropupAuto:!0,header:!1,liveSearch:!1,liveSearchPlaceholder:null,liveSearchNormalize:!1,liveSearchStyle:"contains",actionsBox:!1,iconBase:"glyphicon",tickIcon:"glyphicon-ok",maxOptions:!1,mobile:!1,selectOnTab:!1,dropdownAlignRight:!1},e.prototype={constructor:e,init:function(){var b=this,c=this.$element.attr("id");this.$element.addClass("bs-select-hidden"),this.liObj={},this.multiple=this.$element.prop("multiple"),this.autofocus=this.$element.prop("autofocus"),this.$newElement=this.createView(),this.$element.after(this.$newElement),this.$button=this.$newElement.children("button"),this.$menu=this.$newElement.children(".dropdown-menu"),this.$menuInner=this.$menu.children(".inner"),this.$searchbox=this.$menu.find("input"),this.options.dropdownAlignRight&&this.$menu.addClass("dropdown-menu-right"),"undefined"!=typeof c&&(this.$button.attr("data-id",c),a('label[for="'+c+'"]').click(function(a){a.preventDefault(),b.$button.focus()})),this.checkDisabled(),this.clickListener(),this.options.liveSearch&&this.liveSearchListener(),this.render(),this.setStyle(),this.setWidth(),this.options.container&&this.selectPosition(),this.$menu.data("this",this),this.$newElement.data("this",this),this.options.mobile&&this.mobile(),this.$newElement.on("hide.bs.dropdown",function(a){b.$element.trigger("hide.bs.select",a)}),this.$newElement.on("hidden.bs.dropdown",function(a){b.$element.trigger("hidden.bs.select",a)}),this.$newElement.on("show.bs.dropdown",function(a){b.$element.trigger("show.bs.select",a)}),this.$newElement.on("shown.bs.dropdown",function(a){b.$element.trigger("shown.bs.select",a)}),setTimeout(function(){b.$element.trigger("loaded.bs.select")})},createDropdown:function(){var b=this.multiple?" show-tick":"",d=this.$element.parent().hasClass("input-group")?" input-group-btn":"",e=this.autofocus?" autofocus":"",f=this.options.header?'<div class="popover-title"><button type="button" class="close" aria-hidden="true">&times;</button>'+this.options.header+"</div>":"",g=this.options.liveSearch?'<div class="bs-searchbox"><input type="text" class="form-control" autocomplete="off"'+(null===this.options.liveSearchPlaceholder?"":' placeholder="'+c(this.options.liveSearchPlaceholder)+'"')+"></div>":"",h=this.multiple&&this.options.actionsBox?'<div class="bs-actionsbox"><div class="btn-group btn-group-sm btn-block"><button type="button" class="actions-btn bs-select-all btn btn-default">'+this.options.selectAllText+'</button><button type="button" class="actions-btn bs-deselect-all btn btn-default">'+this.options.deselectAllText+"</button></div></div>":"",i=this.multiple&&this.options.doneButton?'<div class="bs-donebutton"><div class="btn-group btn-block"><button type="button" class="btn btn-sm btn-default">'+this.options.doneButtonText+"</button></div></div>":"",j='<div class="btn-group bootstrap-select'+b+d+'"><button type="button" class="'+this.options.styleBase+' dropdown-toggle" data-toggle="dropdown"'+e+'><span class="filter-option pull-left"></span>&nbsp;<span class="caret"></span></button><div class="dropdown-menu open">'+f+g+h+'<ul class="dropdown-menu inner" role="menu"></ul>'+i+"</div></div>";return a(j)},createView:function(){var a=this.createDropdown(),b=this.createLi();return a.find("ul")[0].innerHTML=b,a},reloadLi:function(){this.destroyLi();var a=this.createLi();this.$menuInner[0].innerHTML=a},destroyLi:function(){this.$menu.find("li").remove()},createLi:function(){var d=this,e=[],f=0,g=document.createElement("option"),h=-1,i=function(a,b,c,d){return"<li"+("undefined"!=typeof c&""!==c?' class="'+c+'"':"")+("undefined"!=typeof b&null!==b?' data-original-index="'+b+'"':"")+("undefined"!=typeof d&null!==d?'data-optgroup="'+d+'"':"")+">"+a+"</li>"},j=function(a,e,f,g){return'<a tabindex="0"'+("undefined"!=typeof e?' class="'+e+'"':"")+("undefined"!=typeof f?' style="'+f+'"':"")+(d.options.liveSearchNormalize?' data-normalized-text="'+b(c(a))+'"':"")+("undefined"!=typeof g||null!==g?' data-tokens="'+g+'"':"")+">"+a+'<span class="'+d.options.iconBase+" "+d.options.tickIcon+' check-mark"></span></a>'};if(this.options.title&&!this.multiple&&(h--,!this.$element.find(".bs-title-option").length)){var k=this.$element[0];g.className="bs-title-option",g.appendChild(document.createTextNode(this.options.title)),g.value="",k.insertBefore(g,k.firstChild),null===k.options[k.selectedIndex].getAttribute("selected")&&(g.selected=!0)}return this.$element.find("option").each(function(b){var c=a(this);if(h++,!c.hasClass("bs-title-option")){var g=this.className||"",k=this.style.cssText,l=c.data("content")?c.data("content"):c.html(),m=c.data("tokens")?c.data("tokens"):null,n="undefined"!=typeof c.data("subtext")?'<small class="text-muted">'+c.data("subtext")+"</small>":"",o="undefined"!=typeof c.data("icon")?'<span class="'+d.options.iconBase+" "+c.data("icon")+'"></span> ':"",p=this.disabled||"OPTGROUP"===this.parentElement.tagName&&this.parentElement.disabled;if(""!==o&&p&&(o="<span>"+o+"</span>"),d.options.hideDisabled&&p)return void h--;if(c.data("content")||(l=o+'<span class="text">'+l+n+"</span>"),"OPTGROUP"===this.parentElement.tagName&&c.data("divider")!==!0){if(0===c.index()){f+=1;var q=this.parentElement.label,r="undefined"!=typeof c.parent().data("subtext")?'<small class="text-muted">'+c.parent().data("subtext")+"</small>":"",s=c.parent().data("icon")?'<span class="'+d.options.iconBase+" "+c.parent().data("icon")+'"></span> ':"",t=" "+this.parentElement.className||"";q=s+'<span class="text">'+q+r+"</span>",0!==b&&e.length>0&&(h++,e.push(i("",null,"divider",f+"div"))),h++,e.push(i(q,null,"dropdown-header"+t,f))}e.push(i(j(l,"opt "+g+t,k,m),b,"",f))}else c.data("divider")===!0?e.push(i("",b,"divider")):c.data("hidden")===!0?e.push(i(j(l,g,k,m),b,"hidden is-hidden")):(this.previousElementSibling&&"OPTGROUP"===this.previousElementSibling.tagName&&(h++,e.push(i("",null,"divider",f+"div"))),e.push(i(j(l,g,k,m),b)));d.liObj[b]=h}}),this.multiple||0!==this.$element.find("option:selected").length||this.options.title||this.$element.find("option").eq(0).prop("selected",!0).attr("selected","selected"),e.join("")},findLis:function(){return null==this.$lis&&(this.$lis=this.$menu.find("li")),this.$lis},render:function(b){var c,d=this;b!==!1&&this.$element.find("option").each(function(a){var b=d.findLis().eq(d.liObj[a]);d.setDisabled(a,this.disabled||"OPTGROUP"===this.parentElement.tagName&&this.parentElement.disabled,b),d.setSelected(a,this.selected,b)}),this.tabIndex();var e=this.$element.find("option").map(function(){if(this.selected){if(d.options.hideDisabled&&(this.disabled||"OPTGROUP"===this.parentElement.tagName&&this.parentElement.disabled))return!1;var b,c=a(this),e=c.data("icon")&&d.options.showIcon?'<i class="'+d.options.iconBase+" "+c.data("icon")+'"></i> ':"";return b=d.options.showSubtext&&c.data("subtext")&&!d.multiple?' <small class="text-muted">'+c.data("subtext")+"</small>":"","undefined"!=typeof c.attr("title")?c.attr("title"):c.data("content")&&d.options.showContent?c.data("content"):e+c.html()+b}}).toArray(),f=this.multiple?e.join(this.options.multipleSeparator):e[0];if(this.multiple&&this.options.selectedTextFormat.indexOf("count")>-1){var g=this.options.selectedTextFormat.split(">");if(g.length>1&&e.length>g[1]||1==g.length&&e.length>=2){c=this.options.hideDisabled?", [disabled]":"";var h=this.$element.find("option").not('[data-divider="true"], [data-hidden="true"]'+c).length,i="function"==typeof this.options.countSelectedText?this.options.countSelectedText(e.length,h):this.options.countSelectedText;f=i.replace("{0}",e.length.toString()).replace("{1}",h.toString())}}void 0==this.options.title&&(this.options.title=this.$element.attr("title")),"static"==this.options.selectedTextFormat&&(f=this.options.title),f||(f="undefined"!=typeof this.options.title?this.options.title:this.options.noneSelectedText),this.$button.attr("title",a.trim(f.replace(/<[^>]*>?/g,""))),this.$button.children(".filter-option").html(f),this.$element.trigger("rendered.bs.select")},setStyle:function(a,b){this.$element.attr("class")&&this.$newElement.addClass(this.$element.attr("class").replace(/selectpicker|mobile-device|bs-select-hidden|validate\[.*\]/gi,""));var c=a?a:this.options.style;"add"==b?this.$button.addClass(c):"remove"==b?this.$button.removeClass(c):(this.$button.removeClass(this.options.style),this.$button.addClass(c))},liHeight:function(b){if(b||this.options.size!==!1&&!this.sizeInfo){var c=document.createElement("div"),d=document.createElement("div"),e=document.createElement("ul"),f=document.createElement("li"),g=document.createElement("li"),h=document.createElement("a"),i=document.createElement("span"),j=this.options.header?this.$menu.find(".popover-title")[0].cloneNode(!0):null,k=this.options.liveSearch?document.createElement("div"):null,l=this.options.actionsBox&&this.multiple?this.$menu.find(".bs-actionsbox")[0].cloneNode(!0):null,m=this.options.doneButton&&this.multiple?this.$menu.find(".bs-donebutton")[0].cloneNode(!0):null;if(i.className="text",c.className=this.$menu[0].parentNode.className+" open",d.className="dropdown-menu open",e.className="dropdown-menu inner",f.className="divider",i.appendChild(document.createTextNode("Inner text")),h.appendChild(i),g.appendChild(h),e.appendChild(g),e.appendChild(f),j&&d.appendChild(j),k){var n=document.createElement("span");k.className="bs-searchbox",n.className="form-control",k.appendChild(n),d.appendChild(k)}l&&d.appendChild(l),d.appendChild(e),m&&d.appendChild(m),c.appendChild(d),document.body.appendChild(c);var o=h.offsetHeight,p=j?j.offsetHeight:0,q=k?k.offsetHeight:0,r=l?l.offsetHeight:0,s=m?m.offsetHeight:0,t=a(f).outerHeight(!0),u=getComputedStyle?getComputedStyle(d):!1,v=u?a(d):null,w=parseInt(u?u.paddingTop:v.css("paddingTop"))+parseInt(u?u.paddingBottom:v.css("paddingBottom"))+parseInt(u?u.borderTopWidth:v.css("borderTopWidth"))+parseInt(u?u.borderBottomWidth:v.css("borderBottomWidth")),x=w+parseInt(u?u.marginTop:v.css("marginTop"))+parseInt(u?u.marginBottom:v.css("marginBottom"))+2;document.body.removeChild(c),this.sizeInfo={liHeight:o,headerHeight:p,searchHeight:q,actionsHeight:r,doneButtonHeight:s,dividerHeight:t,menuPadding:w,menuExtras:x}}},setSize:function(){this.findLis(),this.liHeight();var b,c,d,e,f=this,g=this.$menu,h=this.$menuInner,i=a(window),j=this.$newElement[0].offsetHeight,k=this.sizeInfo.liHeight,l=this.sizeInfo.headerHeight,m=this.sizeInfo.searchHeight,n=this.sizeInfo.actionsHeight,o=this.sizeInfo.doneButtonHeight,p=this.sizeInfo.dividerHeight,q=this.sizeInfo.menuPadding,r=this.sizeInfo.menuExtras,s=this.options.hideDisabled?".disabled":"",t=function(){d=f.$newElement.offset().top-i.scrollTop(),e=i.height()-d-j};if(t(),this.options.header&&g.css("padding-top",0),"auto"===this.options.size){var u=function(){var i,j=function(b,c){return function(d){return c?d.classList?d.classList.contains(b):a(d).hasClass(b):!(d.classList?d.classList.contains(b):a(d).hasClass(b))}},p=f.$menuInner[0].getElementsByTagName("li"),s=Array.prototype.filter?Array.prototype.filter.call(p,j("hidden",!1)):f.$lis.not(".hidden"),u=Array.prototype.filter?Array.prototype.filter.call(s,j("dropdown-header",!0)):s.filter(".dropdown-header");t(),b=e-r,f.options.container?(g.data("height")||g.data("height",g.height()),c=g.data("height")):c=g.height(),f.options.dropupAuto&&f.$newElement.toggleClass("dropup",d>e&&c>b-r),f.$newElement.hasClass("dropup")&&(b=d-r),i=s.length+u.length>3?3*k+r-2:0,g.css({"max-height":b+"px",overflow:"hidden","min-height":i+l+m+n+o+"px"}),h.css({"max-height":b-l-m-n-o-q+"px","overflow-y":"auto","min-height":Math.max(i-q,0)+"px"})};u(),this.$searchbox.off("input.getSize propertychange.getSize").on("input.getSize propertychange.getSize",u),i.off("resize.getSize scroll.getSize").on("resize.getSize scroll.getSize",u)}else if(this.options.size&&"auto"!=this.options.size&&this.$lis.not(s).length>this.options.size){var v=this.$lis.not(".divider").not(s).children().slice(0,this.options.size).last().parent().index(),w=this.$lis.slice(0,v+1).filter(".divider").length;b=k*this.options.size+w*p+q,f.options.container?(g.data("height")||g.data("height",g.height()),c=g.data("height")):c=g.height(),f.options.dropupAuto&&this.$newElement.toggleClass("dropup",d>e&&c>b-r),g.css({"max-height":b+l+m+n+o+"px",overflow:"hidden","min-height":""}),h.css({"max-height":b-q+"px","overflow-y":"auto","min-height":""})}},setWidth:function(){if("auto"===this.options.width){this.$menu.css("min-width","0");var a=this.$menu.parent().clone().appendTo("body"),b=this.options.container?this.$newElement.clone().appendTo("body"):a,c=a.children(".dropdown-menu").outerWidth(),d=b.css("width","auto").children("button").outerWidth();a.remove(),b.remove(),this.$newElement.css("width",Math.max(c,d)+"px")}else"fit"===this.options.width?(this.$menu.css("min-width",""),this.$newElement.css("width","").addClass("fit-width")):this.options.width?(this.$menu.css("min-width",""),this.$newElement.css("width",this.options.width)):(this.$menu.css("min-width",""),this.$newElement.css("width",""));this.$newElement.hasClass("fit-width")&&"fit"!==this.options.width&&this.$newElement.removeClass("fit-width")},selectPosition:function(){var b,c,d=this,e="<div />",f=a(e),g=function(a){f.addClass(a.attr("class").replace(/form-control|fit-width/gi,"")).toggleClass("dropup",a.hasClass("dropup")),b=a.offset(),c=a.hasClass("dropup")?0:a[0].offsetHeight,f.css({top:b.top+c,left:b.left,width:a[0].offsetWidth,position:"absolute"})};this.$newElement.on("click",function(){d.isDisabled()||(g(a(this)),f.appendTo(d.options.container),f.toggleClass("open",!a(this).hasClass("open")),f.append(d.$menu))}),a(window).on("resize scroll",function(){g(d.$newElement)}),this.$element.on("hide.bs.select",function(){d.$menu.data("height",d.$menu.height()),f.detach()})},setSelected:function(a,b,c){if(!c)var c=this.findLis().eq(this.liObj[a]);c.toggleClass("selected",b)},setDisabled:function(a,b,c){if(!c)var c=this.findLis().eq(this.liObj[a]);b?c.addClass("disabled").children("a").attr("href","#").attr("tabindex",-1):c.removeClass("disabled").children("a").removeAttr("href").attr("tabindex",0)},isDisabled:function(){return this.$element[0].disabled},checkDisabled:function(){var a=this;this.isDisabled()?(this.$newElement.addClass("disabled"),this.$button.addClass("disabled").attr("tabindex",-1)):(this.$button.hasClass("disabled")&&(this.$newElement.removeClass("disabled"),this.$button.removeClass("disabled")),-1!=this.$button.attr("tabindex")||this.$element.data("tabindex")||this.$button.removeAttr("tabindex")),this.$button.click(function(){return!a.isDisabled()})},tabIndex:function(){this.$element.is("[tabindex]")&&(this.$element.data("tabindex",this.$element.attr("tabindex")),this.$button.attr("tabindex",this.$element.data("tabindex")))},clickListener:function(){var b=this,c=a(document);this.$newElement.on("touchstart.dropdown",".dropdown-menu",function(a){a.stopPropagation()}),c.data("spaceSelect",!1),this.$button.on("keyup",function(a){/(32)/.test(a.keyCode.toString(10))&&c.data("spaceSelect")&&(a.preventDefault(),c.data("spaceSelect",!1))}),this.$newElement.on("click",function(){b.setSize(),b.$element.on("shown.bs.select",function(){if(b.options.liveSearch||b.multiple){if(!b.multiple){var a=b.liObj[b.$element[0].selectedIndex];if("number"!=typeof a)return;var c=b.$lis.eq(a)[0].offsetTop-b.$menuInner[0].offsetTop;c=c-b.$menuInner[0].offsetHeight/2+b.sizeInfo.liHeight/2,b.$menuInner[0].scrollTop=c}}else b.$menu.find(".selected a").focus()})}),this.$menu.on("click","li a",function(c){var d=a(this),e=d.parent().data("originalIndex"),f=b.$element.val(),g=b.$element.prop("selectedIndex");if(b.multiple&&c.stopPropagation(),c.preventDefault(),!b.isDisabled()&&!d.parent().hasClass("disabled")){var h=b.$element.find("option"),i=h.eq(e),j=i.prop("selected"),k=i.parent("optgroup"),l=b.options.maxOptions,m=k.data("maxOptions")||!1;if(b.multiple){if(i.prop("selected",!j),b.setSelected(e,!j),d.blur(),l!==!1||m!==!1){var n=l<h.filter(":selected").length,o=m<k.find("option:selected").length;if(l&&n||m&&o)if(l&&1==l)h.prop("selected",!1),i.prop("selected",!0),b.$menu.find(".selected").removeClass("selected"),b.setSelected(e,!0);else if(m&&1==m){k.find("option:selected").prop("selected",!1),i.prop("selected",!0);var p=d.parent().data("optgroup");b.$menu.find('[data-optgroup="'+p+'"]').removeClass("selected"),b.setSelected(e,!0)}else{var q="function"==typeof b.options.maxOptionsText?b.options.maxOptionsText(l,m):b.options.maxOptionsText,r=q[0].replace("{n}",l),s=q[1].replace("{n}",m),t=a('<div class="notify"></div>');q[2]&&(r=r.replace("{var}",q[2][l>1?0:1]),s=s.replace("{var}",q[2][m>1?0:1])),i.prop("selected",!1),b.$menu.append(t),l&&n&&(t.append(a("<div>"+r+"</div>")),b.$element.trigger("maxReached.bs.select")),m&&o&&(t.append(a("<div>"+s+"</div>")),b.$element.trigger("maxReachedGrp.bs.select")),setTimeout(function(){b.setSelected(e,!1)},10),t.delay(750).fadeOut(300,function(){a(this).remove()})}}}else h.prop("selected",!1),i.prop("selected",!0),b.$menu.find(".selected").removeClass("selected"),b.setSelected(e,!0);b.multiple?b.options.liveSearch&&b.$searchbox.focus():b.$button.focus(),(f!=b.$element.val()&&b.multiple||g!=b.$element.prop("selectedIndex")&&!b.multiple)&&(b.$element.change(),b.$element.trigger("changed.bs.select",[e,i.prop("selected"),j]))}}),this.$menu.on("click","li.disabled a, .popover-title, .popover-title :not(.close)",function(c){c.currentTarget==this&&(c.preventDefault(),c.stopPropagation(),b.options.liveSearch&&!a(c.target).hasClass("close")?b.$searchbox.focus():b.$button.focus())}),this.$menu.on("click","li.divider, li.dropdown-header",function(a){a.preventDefault(),a.stopPropagation(),b.options.liveSearch?b.$searchbox.focus():b.$button.focus()}),this.$menu.on("click",".popover-title .close",function(){b.$button.click()}),this.$searchbox.on("click",function(a){a.stopPropagation()}),this.$menu.on("click",".actions-btn",function(c){b.options.liveSearch?b.$searchbox.focus():b.$button.focus(),c.preventDefault(),c.stopPropagation(),a(this).hasClass("bs-select-all")?b.selectAll():b.deselectAll(),b.$element.change()}),this.$element.change(function(){b.render(!1)})},liveSearchListener:function(){var d=this,e=a('<li class="no-results"></li>');this.$newElement.on("click.dropdown.data-api touchstart.dropdown.data-api",function(){d.$menuInner.find(".active").removeClass("active"),d.$searchbox.val()&&(d.$searchbox.val(""),d.$lis.not(".is-hidden").removeClass("hidden"),e.parent().length&&e.remove()),d.multiple||d.$menuInner.find(".selected").addClass("active"),setTimeout(function(){d.$searchbox.focus()},10)}),this.$searchbox.on("click.dropdown.data-api focus.dropdown.data-api touchend.dropdown.data-api",function(a){a.stopPropagation()}),this.$searchbox.on("input propertychange",function(){if(d.$searchbox.val()){var f=d.$lis.not(".is-hidden").removeClass("hidden").children("a");f=d.options.liveSearchNormalize?f.not(":a"+d._searchStyle()+"("+b(d.$searchbox.val())+")"):f.not(":"+d._searchStyle()+"("+d.$searchbox.val()+")"),f.parent().addClass("hidden"),d.$lis.filter(".dropdown-header").each(function(){var b=a(this),c=b.data("optgroup");0===d.$lis.filter("[data-optgroup="+c+"]").not(b).not(".hidden").length&&(b.addClass("hidden"),d.$lis.filter("[data-optgroup="+c+"div]").addClass("hidden"))});var g=d.$lis.not(".hidden");g.each(function(b){var c=a(this);c.hasClass("divider")&&(c.index()===g.eq(0).index()||c.index()===g.last().index()||g.eq(b+1).hasClass("divider"))&&c.addClass("hidden")}),d.$lis.not(".hidden, .no-results").length?e.parent().length&&e.remove():(e.parent().length&&e.remove(),e.html(d.options.noneResultsText.replace("{0}",'"'+c(d.$searchbox.val())+'"')).show(),d.$menuInner.append(e))}else d.$lis.not(".is-hidden").removeClass("hidden"),e.parent().length&&e.remove();d.$lis.filter(".active").removeClass("active"),d.$lis.not(".hidden, .divider, .dropdown-header").eq(0).addClass("active").children("a").focus(),a(this).focus()})},_searchStyle:function(){var a="icontains";switch(this.options.liveSearchStyle){case"begins":case"startsWith":a="ibegins";break;case"contains":}return a},val:function(a){return"undefined"!=typeof a?(this.$element.val(a),this.render(),this.$element):this.$element.val()},selectAll:function(){this.findLis(),this.$element.find("option:enabled").not("[data-divider], [data-hidden]").prop("selected",!0),this.$lis.not(".divider, .dropdown-header, .disabled, .hidden").addClass("selected"),this.render(!1)},deselectAll:function(){this.findLis(),this.$element.find("option:enabled").not("[data-divider], [data-hidden]").prop("selected",!1),this.$lis.not(".divider, .dropdown-header, .disabled, .hidden").removeClass("selected"),this.render(!1)},keydown:function(c){var d,e,f,g,h,i,j,k,l,m=a(this),n=m.is("input")?m.parent().parent():m.parent(),o=n.data("this"),p=":not(.disabled, .hidden, .dropdown-header, .divider)",q={32:" ",48:"0",49:"1",50:"2",51:"3",52:"4",53:"5",54:"6",55:"7",56:"8",57:"9",59:";",65:"a",66:"b",67:"c",68:"d",69:"e",70:"f",71:"g",72:"h",73:"i",74:"j",75:"k",76:"l",77:"m",78:"n",79:"o",80:"p",81:"q",82:"r",83:"s",84:"t",85:"u",86:"v",87:"w",88:"x",89:"y",90:"z",96:"0",97:"1",98:"2",99:"3",100:"4",101:"5",102:"6",103:"7",104:"8",105:"9"};if(o.options.liveSearch&&(n=m.parent().parent()),o.options.container&&(n=o.$menu),d=a("[role=menu] li a",n),l=o.$menu.parent().hasClass("open"),!l&&(c.keyCode>=48&&c.keyCode<=57||event.keyCode>=65&&event.keyCode<=90)&&(o.options.container?o.$newElement.trigger("click"):(o.setSize(),o.$menu.parent().addClass("open"),l=!0),o.$searchbox.focus()),o.options.liveSearch&&(/(^9$|27)/.test(c.keyCode.toString(10))&&l&&0===o.$menu.find(".active").length&&(c.preventDefault(),o.$menu.parent().removeClass("open"),o.options.container&&o.$newElement.removeClass("open"),o.$button.focus()),d=a("[role=menu] li:not(.disabled, .hidden, .dropdown-header, .divider)",n),m.val()||/(38|40)/.test(c.keyCode.toString(10))||0===d.filter(".active").length&&(d=o.$newElement.find("li"),d=o.options.liveSearchNormalize?d.filter(":a"+o._searchStyle()+"("+b(q[c.keyCode])+")"):d.filter(":"+o._searchStyle()+"("+q[c.keyCode]+")"))),d.length){if(/(38|40)/.test(c.keyCode.toString(10)))e=d.index(d.filter(":focus")),g=d.parent(p).first().data("originalIndex"),h=d.parent(p).last().data("originalIndex"),f=d.eq(e).parent().nextAll(p).eq(0).data("originalIndex"),i=d.eq(e).parent().prevAll(p).eq(0).data("originalIndex"),j=d.eq(f).parent().prevAll(p).eq(0).data("originalIndex"),o.options.liveSearch&&(d.each(function(b){a(this).hasClass("disabled")||a(this).data("index",b)}),e=d.index(d.filter(".active")),g=d.first().data("index"),h=d.last().data("index"),f=d.eq(e).nextAll().eq(0).data("index"),i=d.eq(e).prevAll().eq(0).data("index"),j=d.eq(f).prevAll().eq(0).data("index")),k=m.data("prevIndex"),38==c.keyCode?(o.options.liveSearch&&(e-=1),e!=j&&e>i&&(e=i),g>e&&(e=g),e==k&&(e=h)):40==c.keyCode&&(o.options.liveSearch&&(e+=1),-1==e&&(e=0),e!=j&&f>e&&(e=f),e>h&&(e=h),e==k&&(e=g)),m.data("prevIndex",e),o.options.liveSearch?(c.preventDefault(),m.hasClass("dropdown-toggle")||(d.removeClass("active").eq(e).addClass("active").children("a").focus(),m.focus())):d.eq(e).focus();else if(!m.is("input")){var r,s,t=[];d.each(function(){a(this).parent().hasClass("disabled")||a.trim(a(this).text().toLowerCase()).substring(0,1)==q[c.keyCode]&&t.push(a(this).parent().index())}),r=a(document).data("keycount"),r++,a(document).data("keycount",r),s=a.trim(a(":focus").text().toLowerCase()).substring(0,1),s!=q[c.keyCode]?(r=1,a(document).data("keycount",r)):r>=t.length&&(a(document).data("keycount",0),r>t.length&&(r=1)),d.eq(t[r-1]).focus()}if((/(13|32)/.test(c.keyCode.toString(10))||/(^9$)/.test(c.keyCode.toString(10))&&o.options.selectOnTab)&&l){if(/(32)/.test(c.keyCode.toString(10))||c.preventDefault(),o.options.liveSearch)/(32)/.test(c.keyCode.toString(10))||(o.$menu.find(".active a").click(),m.focus());else{var u=a(":focus");u.click(),u.focus(),c.preventDefault(),a(document).data("spaceSelect",!0)}a(document).data("keycount",0)}(/(^9$|27)/.test(c.keyCode.toString(10))&&l&&(o.multiple||o.options.liveSearch)||/(27)/.test(c.keyCode.toString(10))&&!l)&&(o.$menu.parent().removeClass("open"),o.options.container&&o.$newElement.removeClass("open"),o.$button.focus())}},mobile:function(){this.$element.addClass("mobile-device").appendTo(this.$newElement),this.options.container&&this.$menu.hide()},refresh:function(){this.$lis=null,this.reloadLi(),this.render(),this.checkDisabled(),this.liHeight(!0),this.setStyle(),this.setWidth(),this.$lis&&this.$searchbox.trigger("propertychange"),this.$element.trigger("refreshed.bs.select")},hide:function(){this.$newElement.hide()},show:function(){this.$newElement.show()},remove:function(){this.$newElement.remove(),this.$element.remove()}};var f=a.fn.selectpicker;a.fn.selectpicker=d,a.fn.selectpicker.Constructor=e,a.fn.selectpicker.noConflict=function(){return a.fn.selectpicker=f,this},a(document).data("keycount",0).on("keydown",'.bootstrap-select [data-toggle=dropdown], .bootstrap-select [role="menu"], .bs-searchbox input',e.prototype.keydown).on("focusin.modal",'.bootstrap-select [data-toggle=dropdown], .bootstrap-select [role="menu"], .bs-searchbox input',function(a){a.stopPropagation()}),a(window).on("load.bs.select.data-api",function(){a(".selectpicker").each(function(){var b=a(this);d.call(b,b.data())})})}(jQuery)});
//# sourceMappingURL=bootstrap-select.js.map
{"version":3,"file":"bootstrap-select.min.js","sources":["bootstrap-select.js"],"names":["root","factory","define","amd","a0","exports","module","require","jQuery","this","$","normalizeToBase","text","rExps","re","ch","each","replace","htmlEscape","html","escapeMap","&","<",">","\"","'","`","source","Object","keys","join","testRegexp","RegExp","replaceRegexp","string","test","match","Plugin","option","event","args","arguments","_option","_event","shift","apply","value","chain","$this","is","data","options","i","hasOwnProperty","config","extend","Selectpicker","DEFAULTS","fn","selectpicker","defaults","Function","String","prototype","includes","toString","defineProperty","object","$defineProperty","result","error","indexOf","search","TypeError","call","stringLength","length","searchString","searchLength","position","undefined","pos","Number","start","Math","min","max","configurable","writable","startsWith","index","charCodeAt","o","k","r","push","expr","icontains","obj","meta","$obj","haystack","toUpperCase","ibegins","aicontains","aibegins","element","e","stopPropagation","preventDefault","$element","$newElement","$button","$menu","$lis","title","attr","val","render","refresh","setStyle","selectAll","deselectAll","destroy","remove","show","hide","init","VERSION","noneSelectedText","noneResultsText","countSelectedText","numSelected","numTotal","maxOptionsText","numAll","numGroup","selectAllText","deselectAllText","doneButton","doneButtonText","multipleSeparator","styleBase","style","size","selectedTextFormat","width","container","hideDisabled","showSubtext","showIcon","showContent","dropupAuto","header","liveSearch","liveSearchPlaceholder","liveSearchNormalize","liveSearchStyle","actionsBox","iconBase","tickIcon","maxOptions","mobile","selectOnTab","dropdownAlignRight","constructor","that","id","addClass","liObj","multiple","prop","autofocus","createView","after","children","$menuInner","$searchbox","find","click","focus","checkDisabled","clickListener","liveSearchListener","setWidth","selectPosition","on","trigger","setTimeout","createDropdown","inputGroup","parent","hasClass","searchbox","actionsbox","donebutton","drop","$drop","li","createLi","innerHTML","reloadLi","destroyLi","_li","optID","titleOption","document","createElement","liIndex","generateLI","content","classes","optgroup","generateA","inline","tokens","className","appendChild","createTextNode","insertBefore","firstChild","selectedIndex","getAttribute","selected","optionClass","cssText","subtext","icon","isDisabled","disabled","parentElement","tagName","label","labelSubtext","labelIcon","optGroupClass","previousElementSibling","eq","findLis","updateLi","notDisabled","setDisabled","setSelected","tabIndex","selectedItems","map","toArray","split","totalCount","not","tr8nText","trim","status","buttonClass","removeClass","liHeight","sizeInfo","newElement","menu","menuInner","divider","a","cloneNode","actions","parentNode","input","body","offsetHeight","headerHeight","searchHeight","actionsHeight","doneButtonHeight","dividerHeight","outerHeight","menuStyle","getComputedStyle","menuPadding","parseInt","paddingTop","css","paddingBottom","borderTopWidth","borderBottomWidth","menuExtras","marginTop","marginBottom","removeChild","setSize","menuHeight","getHeight","selectOffsetTop","selectOffsetBot","$window","window","selectHeight","divHeight","posVert","offset","top","scrollTop","height","getSize","minHeight","include","classList","contains","lis","getElementsByTagName","lisVisible","Array","filter","optGroup","toggleClass","max-height","overflow","min-height","overflow-y","off","optIndex","slice","last","divLength","$selectClone","clone","appendTo","$selectClone2","ulWidth","outerWidth","btnWidth","actualHeight","getPlacement","left","offsetWidth","append","detach","removeAttr","$document","keyCode","offsetTop","clickedIndex","prevValue","prevIndex","$options","$option","state","$optgroup","maxOptionsGrp","blur","maxReached","maxReachedGrp","optgroupID","maxOptionsArr","maxTxt","maxTxtGrp","$notify","delay","fadeOut","change","currentTarget","target","$no_results","$searchBase","_searchStyle","$lisVisible","keydown","$items","next","first","prev","nextPrev","isActive","$parent","selector","keyCodeMap",32,48,49,50,51,52,53,54,55,56,57,59,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,96,97,98,99,100,101,102,103,104,105,"nextAll","prevAll","count","prevKey","keyIndex","toLowerCase","substring","elem","old","Constructor","noConflict","$selectpicker"],"mappings":"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"}
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Нищо избрано',
    noneResultsText: 'Няма резултат за {0}',
    countSelectedText: function (numSelected, numTotal) {
      return (numSelected == 1) ? "{0} избран елемент" : "{0} избрани елемента";
    },
    maxOptionsText: function (numAll, numGroup) {
      return [
        (numAll == 1) ? 'Лимита е достигнат ({n} елемент максимум)' : 'Лимита е достигнат ({n} елемента максимум)',
        (numGroup == 1) ? 'Груповия лимит е достигнат ({n} елемент максимум)' : 'Груповия лимит е достигнат ({n} елемента максимум)'
      ];
    },
    selectAllText: 'Избери всички',
    deselectAllText: 'Размаркирай всички',
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Нищо избрано",noneResultsText:"Няма резултат за {0}",countSelectedText:function(a,b){return 1==a?"{0} избран елемент":"{0} избрани елемента"},maxOptionsText:function(a,b){return[1==a?"Лимита е достигнат ({n} елемент максимум)":"Лимита е достигнат ({n} елемента максимум)",1==b?"Груповия лимит е достигнат ({n} елемент максимум)":"Груповия лимит е достигнат ({n} елемента максимум)"]},selectAllText:"Избери всички",deselectAllText:"Размаркирай всички",multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Nic není vybráno',
    noneResultsText: 'Žádné výsledky {0}',
    countSelectedText: 'Označeno {0} z {1}',
    maxOptionsText: ['Limit překročen ({n} {var} max)', 'Limit skupiny překročen ({n} {var} max)', ['položek', 'položka']],
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Nic není vybráno",noneResultsText:"Žádné výsledky {0}",countSelectedText:"Označeno {0} z {1}",maxOptionsText:["Limit překročen ({n} {var} max)","Limit skupiny překročen ({n} {var} max)",["položek","položka"]],multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Intet valgt',
    noneResultsText: 'Ingen resultater fundet {0}',
    countSelectedText: function (numSelected, numTotal) {
      return (numSelected == 1) ? "{0} valgt" : "{0} valgt";
    },
    maxOptionsText: function (numAll, numGroup) {
      return [
        (numAll == 1) ? 'Begrænsning nået (max {n} valgt)' : 'Begrænsning nået (max {n} valgte)',
        (numGroup == 1) ? 'Gruppe-begrænsning nået (max {n} valgt)' : 'Gruppe-begrænsning nået (max {n} valgte)'
      ];
    },
    selectAllText: 'Markér alle',
    deselectAllText: 'Afmarkér alle',
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Intet valgt",noneResultsText:"Ingen resultater fundet {0}",countSelectedText:function(a,b){return"{0} valgt"},maxOptionsText:function(a,b){return[1==a?"Begrænsning nået (max {n} valgt)":"Begrænsning nået (max {n} valgte)",1==b?"Gruppe-begrænsning nået (max {n} valgt)":"Gruppe-begrænsning nået (max {n} valgte)"]},selectAllText:"Markér alle",deselectAllText:"Afmarkér alle",multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Bitte wählen...',
    noneResultsText: 'Keine Ergebnisse für {0}',
    countSelectedText: '{0} von {1} ausgewählt',
    maxOptionsText: ['Limit erreicht ({n} {var} max.)', 'Gruppen-Limit erreicht ({n} {var} max.)', ['Eintrag', 'Einträge']],
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Bitte wählen...",noneResultsText:"Keine Ergebnisse für {0}",countSelectedText:"{0} von {1} ausgewählt",maxOptionsText:["Limit erreicht ({n} {var} max.)","Gruppen-Limit erreicht ({n} {var} max.)",["Eintrag","Einträge"]],multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Nothing selected',
    noneResultsText: 'No results match {0}',
    countSelectedText: function (numSelected, numTotal) {
      return (numSelected == 1) ? "{0} item selected" : "{0} items selected";
    },
    maxOptionsText: function (numAll, numGroup) {
      return [
        (numAll == 1) ? 'Limit reached ({n} item max)' : 'Limit reached ({n} items max)',
        (numGroup == 1) ? 'Group limit reached ({n} item max)' : 'Group limit reached ({n} items max)'
      ];
    },
    selectAllText: 'Select All',
    deselectAllText: 'Deselect All',
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Nothing selected",noneResultsText:"No results match {0}",countSelectedText:function(a,b){return 1==a?"{0} item selected":"{0} items selected"},maxOptionsText:function(a,b){return[1==a?"Limit reached ({n} item max)":"Limit reached ({n} items max)",1==b?"Group limit reached ({n} item max)":"Group limit reached ({n} items max)"]},selectAllText:"Select All",deselectAllText:"Deselect All",multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'No hay selección',
    noneResultsText: 'No hay resultados {0}',
    countSelectedText: 'Seleccionados {0} de {1}',
    maxOptionsText: ['Límite alcanzado ({n} {var} max)', 'Límite del grupo alcanzado({n} {var} max)', ['elementos', 'element']],
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"No hay selección",noneResultsText:"No hay resultados {0}",countSelectedText:"Seleccionados {0} de {1}",maxOptionsText:["Límite alcanzado ({n} {var} max)","Límite del grupo alcanzado({n} {var} max)",["elementos","element"]],multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Hautapenik ez',
    noneResultsText: 'Emaitzarik ez {0}',
    countSelectedText: '{1}(e)tik {0} hautatuta',
    maxOptionsText: ['Mugara iritsita ({n} {var} gehienez)', 'Taldearen mugara iritsita ({n} {var} gehienez)', ['elementu', 'elementu']],
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Hautapenik ez",noneResultsText:"Emaitzarik ez {0}",countSelectedText:"{1}(e)tik {0} hautatuta",maxOptionsText:["Mugara iritsita ({n} {var} gehienez)","Taldearen mugara iritsita ({n} {var} gehienez)",["elementu","elementu"]],multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
    $.fn.selectpicker.defaults = {
        noneSelectedText: 'چیزی انتخاب نشده است',
        noneResultsText: 'هیج مشابهی برای {0} پیدا نشد',
        countSelectedText: "{0} از {1} مورد انتخاب شده",
        maxOptionsText: ['بیشتر ممکن نیست {حداکثر {n} عدد}', 'بیشتر ممکن نیست {حداکثر {n} عدد}'],
        selectAllText: 'انتخاب همه',
        deselectAllText: 'انتخاب هیچ کدام',
        multipleSeparator: ', '
    };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"چیزی انتخاب نشده است",noneResultsText:"هیج مشابهی برای {0} پیدا نشد",countSelectedText:"{0} از {1} مورد انتخاب شده",maxOptionsText:["بیشتر ممکن نیست {حداکثر {n} عدد}","بیشتر ممکن نیست {حداکثر {n} عدد}"],selectAllText:"انتخاب همه",deselectAllText:"انتخاب هیچ کدام",multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Aucune s&eacute;lection',
    noneResultsText: 'Aucun r&eacute;sultat pour {0}',
    countSelectedText: function (numSelected, numTotal) {
      return (numSelected > 1) ? "{0} &eacute;l&eacute;ments s&eacute;lectionn&eacute;s" : "{0} &eacute;l&eacute;ment s&eacute;lectionn&eacute;";
    },
    maxOptionsText: function (numAll, numGroup) {
      return [
        (numAll > 1) ? 'Limite atteinte ({n} &eacute;l&eacute;ments max)' : 'Limite atteinte ({n} &eacute;l&eacute;ment max)',
        (numGroup > 1) ? 'Limite du groupe atteinte ({n} &eacute;l&eacute;ments max)' : 'Limite du groupe atteinte ({n} &eacute;l&eacute;ment max)'
      ];
    },
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Aucune s&eacute;lection",noneResultsText:"Aucun r&eacute;sultat pour {0}",countSelectedText:function(a,b){return a>1?"{0} &eacute;l&eacute;ments s&eacute;lectionn&eacute;s":"{0} &eacute;l&eacute;ment s&eacute;lectionn&eacute;"},maxOptionsText:function(a,b){return[a>1?"Limite atteinte ({n} &eacute;l&eacute;ments max)":"Limite atteinte ({n} &eacute;l&eacute;ment max)",b>1?"Limite du groupe atteinte ({n} &eacute;l&eacute;ments max)":"Limite du groupe atteinte ({n} &eacute;l&eacute;ment max)"]},multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Válasszon!',
    noneResultsText: 'Nincs találat {0}',
    countSelectedText: function (numSelected, numTotal) {
      return '{n} elem kiválasztva';
    },
    maxOptionsText: function (numAll, numGroup) {
      return [
        'Legfeljebb {n} elem választható',
        'A csoportban legfeljebb {n} elem választható'
      ];
    },
    selectAllText: 'Mind',
    deselectAllText: 'Egyik sem',
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Válasszon!",noneResultsText:"Nincs találat {0}",countSelectedText:function(a,b){return"{n} elem kiválasztva"},maxOptionsText:function(a,b){return["Legfeljebb {n} elem választható","A csoportban legfeljebb {n} elem választható"]},selectAllText:"Mind",deselectAllText:"Egyik sem",multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Nessuna selezione',
    noneResultsText: 'Nessun risultato per {0}',
    countSelectedText: 'Selezionati {0} di {1}',
    maxOptionsText: ['Limite raggiunto ({n} {var} max)', 'Limite del gruppo raggiunto ({n} {var} max)', ['elementi', 'elemento']],
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Nessuna selezione",noneResultsText:"Nessun risultato per {0}",countSelectedText:"Selezionati {0} di {1}",maxOptionsText:["Limite raggiunto ({n} {var} max)","Limite del gruppo raggiunto ({n} {var} max)",["elementi","elemento"]],multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: '항목을 선택해주세요',
    noneResultsText: '{0} 검색 결과가 없습니다',
    countSelectedText: function (numSelected, numTotal) {
      return "{0}개를 선택하였습니다";
    },
    maxOptionsText: function (numAll, numGroup) {
      return [
        '{n}개까지 선택 가능합니다',
        '해당 그룹은 {n}개까지 선택 가능합니다'
      ];
    },
    selectAllText: '전체선택',
    deselectAllText: '전체해제',
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"항목을 선택해주세요",noneResultsText:"{0} 검색 결과가 없습니다",countSelectedText:function(a,b){return"{0}개를 선택하였습니다"},maxOptionsText:function(a,b){return["{n}개까지 선택 가능합니다","해당 그룹은 {n}개까지 선택 가능합니다"]},selectAllText:"전체선택",deselectAllText:"전체해제",multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Niets geselecteerd',
    noneResultsText: 'Geen resultaten gevonden voor {0}',
    countSelectedText: '{0} van {1} geselecteerd',
    maxOptionsText: ['Limiet bereikt ({n} {var} max)', 'Groep limiet bereikt ({n} {var} max)', ['items', 'item']],
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Niets geselecteerd",noneResultsText:"Geen resultaten gevonden voor {0}",countSelectedText:"{0} van {1} geselecteerd",maxOptionsText:["Limiet bereikt ({n} {var} max)","Groep limiet bereikt ({n} {var} max)",["items","item"]],multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Nic nie zaznaczono',
    noneResultsText: 'Brak wyników wyszukiwania {0}',
    countSelectedText: 'Zaznaczono {0} z {1}',
    maxOptionsText: ['Osiągnięto limit ({n} {var} max)', 'Limit grupy osiągnięty ({n} {var} max)', ['elementy', 'element']],
    selectAll: 'Zaznacz wszystkie',
    deselectAll: 'Odznacz wszystkie',
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Nic nie zaznaczono",noneResultsText:"Brak wyników wyszukiwania {0}",countSelectedText:"Zaznaczono {0} z {1}",maxOptionsText:["Osiągnięto limit ({n} {var} max)","Limit grupy osiągnięty ({n} {var} max)",["elementy","element"]],selectAll:"Zaznacz wszystkie",deselectAll:"Odznacz wszystkie",multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Nada selecionado',
    noneResultsText: 'Nada encontrado contendo {0}',
    countSelectedText: 'Selecionado {0} de {1}',
    maxOptionsText: ['Limite excedido (máx. {n} {var})', 'Limite do grupo excedido (máx. {n} {var})', ['itens', 'item']],
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Nada selecionado",noneResultsText:"Nada encontrado contendo {0}",countSelectedText:"Selecionado {0} de {1}",maxOptionsText:["Limite excedido (máx. {n} {var})","Limite do grupo excedido (máx. {n} {var})",["itens","item"]],multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
$.fn.selectpicker.defaults = {
noneSelectedText: 'Nenhum seleccionado',
noneResultsText: 'Sem resultados contendo {0}',
countSelectedText: 'Selecionado {0} de {1}',
maxOptionsText: ['Limite ultrapassado (máx. {n} {var})', 'Limite de seleções ultrapassado (máx. {n} {var})', ['itens', 'item']],
multipleSeparator: ', '
};
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Nenhum seleccionado",noneResultsText:"Sem resultados contendo {0}",countSelectedText:"Selecionado {0} de {1}",maxOptionsText:["Limite ultrapassado (máx. {n} {var})","Limite de seleções ultrapassado (máx. {n} {var})",["itens","item"]],multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Nu a fost selectat nimic',
    noneResultsText: 'Nu exista niciun rezultat {0}',
    countSelectedText: '{0} din {1} selectat(e)',
    maxOptionsText: ['Limita a fost atinsa ({n} {var} max)', 'Limita de grup a fost atinsa ({n} {var} max)', ['iteme', 'item']],
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Nu a fost selectat nimic",noneResultsText:"Nu exista niciun rezultat {0}",countSelectedText:"{0} din {1} selectat(e)",maxOptionsText:["Limita a fost atinsa ({n} {var} max)","Limita de grup a fost atinsa ({n} {var} max)",["iteme","item"]],multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Ничего не выбрано',
    noneResultsText: 'Совпадений не найдено {0}',
    countSelectedText: 'Выбрано {0} из {1}',
    maxOptionsText: ['Достигнут предел ({n} {var} максимум)', 'Достигнут предел в группе ({n} {var} максимум)', ['items', 'item']],
    doneButtonText: 'Закрыть',
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Ничего не выбрано",noneResultsText:"Совпадений не найдено {0}",countSelectedText:"Выбрано {0} из {1}",maxOptionsText:["Достигнут предел ({n} {var} максимум)","Достигнут предел в группе ({n} {var} максимум)",["items","item"]],doneButtonText:"Закрыть",multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Vyberte zo zoznamu',
    noneResultsText: 'Pre výraz {0} neboli nájdené žiadne výsledky',
    countSelectedText: 'Vybrané {0} z {1}',
    maxOptionsText: ['Limit prekročený ({n} {var} max)', 'Limit skupiny prekročený ({n} {var} max)', ['položiek', 'položka']],
    selectAllText: 'Vybrať všetky',
    deselectAllText: 'Zrušiť výber',
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Vyberte zo zoznamu",noneResultsText:"Pre výraz {0} neboli nájdené žiadne výsledky",countSelectedText:"Vybrané {0} z {1}",maxOptionsText:["Limit prekročený ({n} {var} max)","Limit skupiny prekročený ({n} {var} max)",["položiek","položka"]],selectAllText:"Vybrať všetky",deselectAllText:"Zrušiť výber",multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Nič izbranega',
    noneResultsText: 'Ni zadetkov za {0}',
    countSelectedText: function (numSelected, numTotal) {
      "Število izbranih: {0}";
    },
    maxOptionsText: function (numAll, numGroup) {
      return [
        'Omejitev dosežena (max. izbranih: {n})',
        'Omejitev skupine dosežena (max. izbranih: {n})'
      ];
    },
    selectAllText: 'Izberi vse',
    deselectAllText: 'Počisti izbor',
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Nič izbranega",noneResultsText:"Ni zadetkov za {0}",countSelectedText:function(a,b){"Število izbranih: {0}"},maxOptionsText:function(a,b){return["Omejitev dosežena (max. izbranih: {n})","Omejitev skupine dosežena (max. izbranih: {n})"]},selectAllText:"Izberi vse",deselectAllText:"Počisti izbor",multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Inget valt',
    noneResultsText: 'Inget sökresultat matchar {0}',
    countSelectedText: function (numSelected, numTotal) {
      return (numSelected === 1) ? "{0} alternativ valt" : "{0} alternativ valda";
    },
    maxOptionsText: function (numAll, numGroup) {
      return [
        'Gräns uppnåd (max {n} alternativ)',
        'Gräns uppnåd (max {n} gruppalternativ)'
      ];
    },
    selectAllText: 'Markera alla',
    deselectAllText: 'Avmarkera alla',
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Inget valt",noneResultsText:"Inget sökresultat matchar {0}",countSelectedText:function(a,b){return 1===a?"{0} alternativ valt":"{0} alternativ valda"},maxOptionsText:function(a,b){return["Gräns uppnåd (max {n} alternativ)","Gräns uppnåd (max {n} gruppalternativ)"]},selectAllText:"Markera alla",deselectAllText:"Avmarkera alla",multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Hiçbiri seçilmedi',
    noneResultsText: 'Hiçbir sonuç bulunamadı {0}',
    countSelectedText: function (numSelected, numTotal) {
      return (numSelected == 1) ? "{0} öğe seçildi" : "{0} öğe seçildi";
    },
    maxOptionsText: function (numAll, numGroup) {
      return [
        (numAll == 1) ? 'Limit aşıldı (maksimum {n} sayıda öğe )' : 'Limit aşıldı (maksimum {n} sayıda öğe)',
        (numGroup == 1) ? 'Grup limiti aşıldı (maksimum {n} sayıda öğe)' : 'Grup limiti aşıldı (maksimum {n} sayıda öğe)'
      ];
    },
    selectAllText: 'Tümünü Seç',
    deselectAllText: 'Seçiniz',
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Hiçbiri seçilmedi",noneResultsText:"Hiçbir sonuç bulunamadı {0}",countSelectedText:function(a,b){return"{0} öğe seçildi"},maxOptionsText:function(a,b){return[1==a?"Limit aşıldı (maksimum {n} sayıda öğe )":"Limit aşıldı (maksimum {n} sayıda öğe)","Grup limiti aşıldı (maksimum {n} sayıda öğe)"]},selectAllText:"Tümünü Seç",deselectAllText:"Seçiniz",multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Нічого не вибрано',
    noneResultsText: 'Збігів не знайдено {0}',
    countSelectedText: 'Вибрано {0} із {1}',
    maxOptionsText: ['Досягнута межа ({n} {var} максимум)', 'Досягнута межа в групі ({n} {var} максимум)', ['items', 'item']],
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"Нічого не вибрано",noneResultsText:"Збігів не знайдено {0}",countSelectedText:"Вибрано {0} із {1}",maxOptionsText:["Досягнута межа ({n} {var} максимум)","Досягнута межа в групі ({n} {var} максимум)",["items","item"]],multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: '没有选中任何项',
    noneResultsText: '没有找到匹配项',
    countSelectedText: '选中{1}中的{0}项',
    maxOptionsText: ['超出限制 (最多选择{n}项)', '组选择超出限制(最多选择{n}组)'],
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"没有选中任何项",noneResultsText:"没有找到匹配项",countSelectedText:"选中{1}中的{0}项",maxOptionsText:["超出限制 (最多选择{n}项)","组选择超出限制(最多选择{n}组)"],multipleSeparator:", "}}(jQuery)});
/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */

(function (root, factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module unless amdModuleId is set
    define(["jquery"], function (a0) {
      return (factory(a0));
    });
  } else if (typeof exports === 'object') {
    // Node. Does not work with strict CommonJS, but
    // only CommonJS-like environments that support module.exports,
    // like Node.
    module.exports = factory(require("jquery"));
  } else {
    factory(jQuery);
  }
}(this, function () {

(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: '沒有選取任何項目',
    noneResultsText: '沒有找到符合的結果',
    countSelectedText: '已經選取{0}個項目',
    maxOptionsText: ['超過限制 (最多選擇{n}項)', '超過限制(最多選擇{n}組)'],
    selectAllText: '選取全部',
    deselectAllText: '全部取消',
    multipleSeparator: ', '
  };
})(jQuery);


}));

/*!
 * Bootstrap-select v1.7.2 (http://silviomoreto.github.io/bootstrap-select)
 *
 * Copyright 2013-2015 bootstrap-select
 * Licensed under MIT (https://github.com/silviomoreto/bootstrap-select/blob/master/LICENSE)
 */
!function(a,b){"function"==typeof define&&define.amd?define(["jquery"],function(a){return b(a)}):"object"==typeof exports?module.exports=b(require("jquery")):b(jQuery)}(this,function(){!function(a){a.fn.selectpicker.defaults={noneSelectedText:"沒有選取任何項目",noneResultsText:"沒有找到符合的結果",countSelectedText:"已經選取{0}個項目",maxOptionsText:["超過限制 (最多選擇{n}項)","超過限制(最多選擇{n}組)"],selectAllText:"選取全部",deselectAllText:"全部取消",multipleSeparator:", "}}(jQuery)});