!function(a,b){"use strict";var c=function(d,e){var f=d.split("."),g=c.instantiate(),h=a,i=f.length,j=0;for(j;j<i;j++)h[f[j]]=i-1===j?g:h[f[j]]||{},h=h[f[j]];return"function"==typeof e&&e.call(null,h,b,c.utils),h};c.instantiate=function(){var a=function(){},b=function(){var b;return b=new a,b.start.apply(b,arguments),b};return b.fn=b.prototype,a.prototype=b.fn,b.fn.start=function(){},b},c.utils={getAjaxUrl:function(){return(window.WPUSBVars||{}).ajaxUrl},getContext:function(){return(window.WPUSBVars||{}).context},getLocale:function(){return(window.WPUSBVars||{}).WPLANG},getPathUrl:function(a){var b=decodeURIComponent(a);return b.split(/[?#]/)[0]}},a.WPUSB=c}(window,jQuery),WPUSB("WPUSB.BuildComponents",function(a,b){a.create=function(a){a.findComponent("[data-component]",b.proxy(this,"_start"))},a._start=function(a){return"undefined"==typeof WPUSB.Components?void console.warn("Component not defined in its structure."):void this._iterator(a)},a._iterator=function(a){var c;a.each(function(d,e){e=b(e),c=a.ucfirst(e.data("component")),this._callback(c,e)}.bind(this))},a._callback=function(a,b){var c=WPUSB.Components[a];"function"==typeof c&&c.call(null,b)}},{}),function(a){a.fn.byElement=function(a){return this.find('[data-element="'+a+'"]')},a.fn.byAction=function(a){return this.find('[data-action="'+a+'"]')},a.fn.byReferrer=function(a){return this.find('[data-referrer="'+a+'"]')},a.fn.byComponent=function(a){return this.find('[data-component="'+a+'"]')},a.fn.findComponent=function(b,c){var d=a(this).find(b);return d.length&&"function"==typeof c&&c.call(null,d,a(this)),d.length},a.fn.ucfirst=function(a){return a=a.replace(/(?:^|-)\w/g,function(a){return a.toUpperCase()}),a.replace(/-/g,"")},a.fn.addEvent=function(b,c,d){var e=a.fn.ucfirst(["_on",b,c].join("-"));this.byAction(c).on(b,a.proxy(d,e))},a.fn.getTime=function(){return(new Date).getTime()},a.fn.hashStr=function(a){var b,c=0,d=0;if(!a.length)return c;for(d;d<a.length;d++)b=a.charCodeAt(d),c=(c<<10)-c+b,c&=c;return Math.abs(c)}}(jQuery),WPUSB("WPUSB.Application",function(a,b){a.init=function(a){WPUSB.BuildComponents.create(a),WPUSB.FixedTop.create(a)}}),WPUSB("WPUSB.Components.CounterSocialShare",function(a,b,c){a.fn.start=function(a){this.$el=a,this.prefix=WPUSB.vars.prefix+"-",this.data=a.data(),this.facebook=this.$el.byElement("facebook"),this.twitter=this.$el.byElement("twitter"),this.google=this.$el.byElement("google-plus"),this.pinterest=this.$el.byElement("pinterest"),this.linkedin=this.$el.byElement("linkedin"),this.totalShare=this.$el.byElement("total-share"),this.totalCounter=0,this.facebookCounter=0,this.twitterCounter=0,this.googleCounter=0,this.linkedinCounter=0,this.pinterestCounter=0,this.max=5,this.items=[],this.init()},a.fn.init=function(){WPUSB.FeaturedReferrer.create(this.$el),WPUSB.OpenPopup.create(this.$el),WPUSB.FixedContext.create(this.$el),this.addEventListeners(),this.request()},a.fn.addEventListeners=function(){this.$el.addEvent("click","open-popup",this),WPUSB.ToggleButtons.create(this.$el.data("element"),this.$el)},a.fn.request=function(){this.items=[{reference:"facebookCounter",element:"facebook",url:"https://graph.facebook.com/?id="+this.data.elementUrl},{reference:"twitterCounter",element:"twitter",url:"https://public.newsharecounts.com/count.json?url="+this.data.elementUrl},{reference:"googleCounter",element:"google",url:c.getAjaxUrl(),data:this.getParamsGoogle()},{reference:"linkedinCounter",element:"linkedin",url:"https://www.linkedin.com/countserv/count/share?url="+this.data.elementUrl},{reference:"pinterestCounter",element:"pinterest",url:"https://api.pinterest.com/v1/urls/count.json?url="+this.data.elementUrl}],this._eachAjaxSocial()},a.fn._eachAjaxSocial=function(){this.items.forEach(this._iterateItems.bind(this))},a.fn._iterateItems=function(a,b){var c=0;return this.totalShare.text(c),this[a.element].text(c),this._verifyTimeCache()?void this._getJSON(a):void this.setCounterCache(c,a)},a.fn.setCounterCache=function(a,b){a=parseFloat(this._getItem(b.element)),this.totalCounter+=a,this[b.reference]=a,this[b.element].text(a),this.totalShare.text(this.totalCounter)},a.fn._getJSON=function(a){var c=b.extend({dataType:"jsonp"},a),d=b.ajax(c);d.done(b.proxy(this,"_done",a)),d.fail(b.proxy(this,"_fail",a))},a.fn._done=function(a,b){var c=this.getNumberByData(b);this[a.reference]=c,this.max-=1,this.totalCounter+=parseFloat(c),this[a.element].text(c),this._setItem(a.element,c),this.max||(this._setItem("cache",this._timerCache(900)),this.totalShare.text(this.totalCounter))},a.fn._fail=function(a,b,c){this[a.reference]=0,this[a.element].text(0)},a.fn.getNumberByData=function(a){var b=parseFloat(a.shares)+parseFloat(a.comments),c=parseFloat(a.shares),d=parseFloat(a.count);return b||c||d||0},a.fn.getParamsGoogle=function(){return{action:"share_google_plus",url:this.data.elementUrl}},a.fn._onClickOpenPopup=function(a){this.items.forEach(this.clearStorage.bind(this));var d={action:"counts_social_share",reference:this.data.attrReference,count_facebook:this.facebookCounter,count_twitter:this.twitterCounter,count_google:this.googleCounter,count_linkedin:this.linkedinCounter,count_pinterest:this.pinterestCounter,nonce:this.data.attrNonce};b.ajax({method:"POST",url:c.getAjaxUrl(),data:d})},a.fn.clearStorage=function(a,b,c){this._removeItem(a.element),b+1==c.length&&this._removeItem("cache")},a.fn._removeItem=function(a){localStorage.removeItem(b.fn.hashStr(this.data.elementUrl+a))},a.fn._getItem=function(a){var c=localStorage.getItem(b.fn.hashStr(this.data.elementUrl+a));return null===c?0:c},a.fn._setItem=function(a,c){localStorage.setItem(b.fn.hashStr(this.data.elementUrl+a),c)},a.fn._timerCache=function(a){return b.fn.getTime()+1e3*a},a.fn._verifyTimeCache=function(){return this._getItem("cache")<b.fn.getTime()}}),WPUSB("WPUSB.Components.SocialPopup",function(a,b){a.fn.start=function(a){this.prefix="."+WPUSB.vars.prefix+"-",this.container=a,this.$el=a.find(this.prefix+"networks"),this.close=this.$el.byAction("close-popup"),this.open=WPUSB.vars.body.byAction("open-modal-networks"),this.init()},a.fn.init=function(){WPUSB.OpenPopup.create(this.$el),this.addEventListener(),this.container.show(),this.setSizes(),this.setPosition(),this.container.hide()},a.fn.addEventListener=function(){this.close.on("click",this._onClickClose.bind(this)),WPUSB.vars.body.on("click",this._onClickBody.bind(this)),this.open.on("click",this._onClickOpen.bind(this))},a.fn._onClickClose=function(a){a.preventDefault(),this.closeModal()},a.fn._onClickBody=function(a){var c=b(a.target).is(this.prefix+"popup-content");c&&this.closeModal()},a.fn._onClickOpen=function(a){a.preventDefault(),this.container.fadeTo("slow",1)},a.fn.setSizes=function(){this.setTop(),this.setLeft()},a.fn.closeModal=function(){this.container.fadeOut("slow")},a.fn.setTop=function(){var a=.5*window.innerHeight,b=.5*this.$el.height();this.top=a-b+"px"},a.fn.setLeft=function(){var a=.5*window.innerWidth,b=.5*this.$el.width();this.left=a-b+"px"},a.fn.setPosition=function(){this.$el.css({top:this.top,left:this.left})}}),WPUSB("WPUSB.FeaturedReferrer",function(a,b){a.create=function(a){this.prefix=WPUSB.vars.prefix+"-",this.$el=a,this.init()},a.init=function(){this.$el.attr("class").match("-fixed")||this.setReferrer()},a.setReferrer=function(){(this.isMatch("twitter")||this.isMatch("t"))&&this.showReferrer("twitter"),this.isMatch("google")&&this.showReferrer("google-plus"),this.isMatch("facebook")&&this.showReferrer("facebook"),this.isMatch("linkedin")&&this.showReferrer("linkedin")},a.showReferrer=function(a){var b=this.prefix+"referrer";this.$el.find("."+this.prefix+"count").hide(),this.$el.byReferrer(a).addClass(b),this.$el.byReferrer(a).addClass(b+"-"+a)},a.isMatch=function(a){var b=document.referrer,c=new RegExp("^https?://([^/]+\\.)?"+a+"\\.com?(/|$)","i");return b.match(c)}},{}),WPUSB("WPUSB.FixedContext",function(a,b,c){a.create=function(a){this.$el=a,this.isLayoutFixed()&&this.issetContext()&&this.init()},a.isLayoutFixed=function(){return this.$el.attr("class").match("-fixed-")},a.issetContext=function(){return this.id=c.getContext(),this.getContext(!0)},a.init=function(){this.setContext(),this.alignButtons()},a.setContext=function(){this.context=this.getContext(),this.setRect()},a.setRect=function(){this.rect=this.context.getBoundingClientRect(),this.top=this.rect.top,this.setLeft(this.rect.left)},a.setLeft=function(a){this.left=a-this.$el.width()},a.alignButtons=function(){this.$el.byAction("close-buttons").remove(),this.changeClass(),this.$el.css({left:this.left}),this.setLeftMobile()},a.setLeftMobile=function(){window.innerWidth>769||this.$el.css({left:"initial"})},a.changeClass=function(){var a=WPUSB.vars.prefix,b=this.$el.attr("class");b.match("-fixed-left")||(this.$el.removeClass(a+"-fixed-right"),this.$el.addClass(a+"-fixed-left"))},a.getContext=function(a){var b=this.id.replace(/[^A-Z0-9a-z-_]/g,""),c=document.getElementById(b);return a?this.sendNotice(b,c):"",c},a.sendNotice=function(a,b){a&&!b&&console.warn("WPUSB: Context not found.")}},{}),WPUSB("WPUSB.FixedTop",function(a,b){a.create=function(a){this["class"]=WPUSB.vars.prefix+"-fixed-top",this.$el=a.byElement(this["class"]),this.$el.length&&(this.$el=b(this.$el.get(0)),this.init())},a.init=function(){this.scroll=this.$el.get(0).getBoundingClientRect(),this.isInvalidScroll()&&(this.scroll["static"]=300),this.context=window,this.addEventListener()},a.addEventListener=function(){b(this.context).scroll(this._setPositionFixed.bind(this))},a._setPositionFixed=function(){var a=this.scroll["static"]||this.scroll.top;return b(this.context).scrollTop()>a?void this.$el.addClass(this["class"]):void this.$el.removeClass(this["class"])},a.isInvalidScroll=function(){return 1>this.scroll.top}},{}),WPUSB("WPUSB.OpenPopup",function(a,b){a.create=function(a){this.$el=a,this.addEventListener()},a.addEventListener=function(){this.$el.addEvent("click","open-popup",this)},a._onClickOpenPopup=function(a){a.preventDefault();var b=jQuery(a.currentTarget),c="685",d="500";this.popupCenter(b.attr("href"),"Compartilhar",c,d)},a.popupCenter=function(a,b,c,d){var e,f;return c=c||screen.width,d=d||screen.height,e=.5*screen.width-.5*c,f=.5*screen.height-.5*d,window.open(a,b,"menubar=no,toolbar=no,status=no,width="+c+",height="+d+",toolbar=no,left="+e+",top="+f)}},{}),WPUSB("WPUSB.ShortUrl",function(a,b){a.create=function(a){this.$el=a,this.data=this.$el.data(),this.shareUrls=this.$el.find("a[href]"),this.checkToken()},a.checkToken=function(){this.data.token.trim()&&this.request()},a.request=function(){var a=b.proxy(this,"_done"),c=b.proxy(this,"_fail"),d={access_token:this.data.token,longUrl:this.longUrl()},e=b.ajax({url:"https://api-ssl.bitly.com/v3/shorten",data:d,dataType:"jsonp"});e.then(a,c)},a.longUrl=function(){return this.data.elementUrl+this.data.tracking},a._done=function(a){return 500===a.status_code?void console.warn("Bitly: "+a.status_txt):void this.shareUrls.each(b.proxy(this,"_each",a))},a._fail=function(a,b){console.warn(a)},a._each=function(a,c,d){var e=b(d),f=e.attr("href"),g=this.replaceUrl(a,f);e.attr("href",g)},a.replaceUrl=function(a,b){var c=encodeURIComponent(a.data.url),d=b.replace(/\[.*\]/,c);return d}},{}),WPUSB("WPUSB.ToggleButtons",function(a,b){a.create=function(a,b){"fixed"===a&&(this.$el=b,this.prefix=WPUSB.vars.prefix+"-",this.closeButtons=WPUSB.vars.body.byAction("close-buttons"),this.buttons=b.byElement("buttons"),this.init())},a.init=function(){this.addEventListener()},a.addEventListener=function(){this.closeButtons.on("click",this._onCloseButtons.bind(this))},a._onCloseButtons=function(a){a.preventDefault();var b=this.prefix+"icon-right",c=this.prefix+"toggle-active";this.buttons.toggleClass(this.prefix+"buttons"),this.closeButtons.toggleClass(b+" "+c)}}),jQuery(function(a){var b=a("body");WPUSB.vars={body:b,prefix:"wpusb"},WPUSB.Application.init.apply(null,[b])});