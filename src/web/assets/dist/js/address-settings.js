(()=>{var t={757:(t,e,o)=>{t.exports=o(666)},624:(t,e,o)=>{"use strict";o.d(e,{Z:()=>r});var n=o(519),a=o.n(n)()((function(t){return t[1]}));a.push([t.id,".disabled[data-v-3fde90ed]{background-color:#e4eaf4;opacity:.6}",""]);const r=a},519:t=>{"use strict";t.exports=function(t){var e=[];return e.toString=function(){return this.map((function(e){var o=t(e);return e[2]?"@media ".concat(e[2]," {").concat(o,"}"):o})).join("")},e.i=function(t,o,n){"string"==typeof t&&(t=[[null,t,""]]);var a={};if(n)for(var r=0;r<this.length;r++){var i=this[r][0];null!=i&&(a[i]=!0)}for(var s=0;s<t.length;s++){var l=[].concat(t[s]);n&&a[l[0]]||(o&&(l[2]?l[2]="".concat(o," and ").concat(l[2]):l[2]=o),e.push(l))}},e}},666:t=>{var e=function(t){"use strict";var e,o=Object.prototype,n=o.hasOwnProperty,a="function"==typeof Symbol?Symbol:{},r=a.iterator||"@@iterator",i=a.asyncIterator||"@@asyncIterator",s=a.toStringTag||"@@toStringTag";function l(t,e,o){return Object.defineProperty(t,e,{value:o,enumerable:!0,configurable:!0,writable:!0}),t[e]}try{l({},"")}catch(t){l=function(t,e,o){return t[e]=o}}function d(t,e,o,n){var a=e&&e.prototype instanceof g?e:g,r=Object.create(a.prototype),i=new M(n||[]);return r._invoke=function(t,e,o){var n=u;return function(a,r){if(n===p)throw new Error("Generator is already running");if(n===h){if("throw"===a)throw r;return L()}for(o.method=a,o.arg=r;;){var i=o.delegate;if(i){var s=C(i,o);if(s){if(s===m)continue;return s}}if("next"===o.method)o.sent=o._sent=o.arg;else if("throw"===o.method){if(n===u)throw n=h,o.arg;o.dispatchException(o.arg)}else"return"===o.method&&o.abrupt("return",o.arg);n=p;var l=c(t,e,o);if("normal"===l.type){if(n=o.done?h:f,l.arg===m)continue;return{value:l.arg,done:o.done}}"throw"===l.type&&(n=h,o.method="throw",o.arg=l.arg)}}}(t,o,i),r}function c(t,e,o){try{return{type:"normal",arg:t.call(e,o)}}catch(t){return{type:"throw",arg:t}}}t.wrap=d;var u="suspendedStart",f="suspendedYield",p="executing",h="completed",m={};function g(){}function v(){}function y(){}var $={};l($,r,(function(){return this}));var b=Object.getPrototypeOf,w=b&&b(b(E([])));w&&w!==o&&n.call(w,r)&&($=w);var _=y.prototype=g.prototype=Object.create($);function k(t){["next","throw","return"].forEach((function(e){l(t,e,(function(t){return this._invoke(e,t)}))}))}function x(t,e){function o(a,r,i,s){var l=c(t[a],t,r);if("throw"!==l.type){var d=l.arg,u=d.value;return u&&"object"==typeof u&&n.call(u,"__await")?e.resolve(u.__await).then((function(t){o("next",t,i,s)}),(function(t){o("throw",t,i,s)})):e.resolve(u).then((function(t){d.value=t,i(d)}),(function(t){return o("throw",t,i,s)}))}s(l.arg)}var a;this._invoke=function(t,n){function r(){return new e((function(e,a){o(t,n,e,a)}))}return a=a?a.then(r,r):r()}}function C(t,o){var n=t.iterator[o.method];if(n===e){if(o.delegate=null,"throw"===o.method){if(t.iterator.return&&(o.method="return",o.arg=e,C(t,o),"throw"===o.method))return m;o.method="throw",o.arg=new TypeError("The iterator does not provide a 'throw' method")}return m}var a=c(n,t.iterator,o.arg);if("throw"===a.type)return o.method="throw",o.arg=a.arg,o.delegate=null,m;var r=a.arg;return r?r.done?(o[t.resultName]=r.value,o.next=t.nextLoc,"return"!==o.method&&(o.method="next",o.arg=e),o.delegate=null,m):r:(o.method="throw",o.arg=new TypeError("iterator result is not an object"),o.delegate=null,m)}function N(t){var e={tryLoc:t[0]};1 in t&&(e.catchLoc=t[1]),2 in t&&(e.finallyLoc=t[2],e.afterLoc=t[3]),this.tryEntries.push(e)}function S(t){var e=t.completion||{};e.type="normal",delete e.arg,t.completion=e}function M(t){this.tryEntries=[{tryLoc:"root"}],t.forEach(N,this),this.reset(!0)}function E(t){if(t){var o=t[r];if(o)return o.call(t);if("function"==typeof t.next)return t;if(!isNaN(t.length)){var a=-1,i=function o(){for(;++a<t.length;)if(n.call(t,a))return o.value=t[a],o.done=!1,o;return o.value=e,o.done=!0,o};return i.next=i}}return{next:L}}function L(){return{value:e,done:!0}}return v.prototype=y,l(_,"constructor",y),l(y,"constructor",v),v.displayName=l(y,s,"GeneratorFunction"),t.isGeneratorFunction=function(t){var e="function"==typeof t&&t.constructor;return!!e&&(e===v||"GeneratorFunction"===(e.displayName||e.name))},t.mark=function(t){return Object.setPrototypeOf?Object.setPrototypeOf(t,y):(t.__proto__=y,l(t,s,"GeneratorFunction")),t.prototype=Object.create(_),t},t.awrap=function(t){return{__await:t}},k(x.prototype),l(x.prototype,i,(function(){return this})),t.AsyncIterator=x,t.async=function(e,o,n,a,r){void 0===r&&(r=Promise);var i=new x(d(e,o,n,a),r);return t.isGeneratorFunction(o)?i:i.next().then((function(t){return t.done?t.value:i.next()}))},k(_),l(_,s,"Generator"),l(_,r,(function(){return this})),l(_,"toString",(function(){return"[object Generator]"})),t.keys=function(t){var e=[];for(var o in t)e.push(o);return e.reverse(),function o(){for(;e.length;){var n=e.pop();if(n in t)return o.value=n,o.done=!1,o}return o.done=!0,o}},t.values=E,M.prototype={constructor:M,reset:function(t){if(this.prev=0,this.next=0,this.sent=this._sent=e,this.done=!1,this.delegate=null,this.method="next",this.arg=e,this.tryEntries.forEach(S),!t)for(var o in this)"t"===o.charAt(0)&&n.call(this,o)&&!isNaN(+o.slice(1))&&(this[o]=e)},stop:function(){this.done=!0;var t=this.tryEntries[0].completion;if("throw"===t.type)throw t.arg;return this.rval},dispatchException:function(t){if(this.done)throw t;var o=this;function a(n,a){return s.type="throw",s.arg=t,o.next=n,a&&(o.method="next",o.arg=e),!!a}for(var r=this.tryEntries.length-1;r>=0;--r){var i=this.tryEntries[r],s=i.completion;if("root"===i.tryLoc)return a("end");if(i.tryLoc<=this.prev){var l=n.call(i,"catchLoc"),d=n.call(i,"finallyLoc");if(l&&d){if(this.prev<i.catchLoc)return a(i.catchLoc,!0);if(this.prev<i.finallyLoc)return a(i.finallyLoc)}else if(l){if(this.prev<i.catchLoc)return a(i.catchLoc,!0)}else{if(!d)throw new Error("try statement without catch or finally");if(this.prev<i.finallyLoc)return a(i.finallyLoc)}}}},abrupt:function(t,e){for(var o=this.tryEntries.length-1;o>=0;--o){var a=this.tryEntries[o];if(a.tryLoc<=this.prev&&n.call(a,"finallyLoc")&&this.prev<a.finallyLoc){var r=a;break}}r&&("break"===t||"continue"===t)&&r.tryLoc<=e&&e<=r.finallyLoc&&(r=null);var i=r?r.completion:{};return i.type=t,i.arg=e,r?(this.method="next",this.next=r.finallyLoc,m):this.complete(i)},complete:function(t,e){if("throw"===t.type)throw t.arg;return"break"===t.type||"continue"===t.type?this.next=t.arg:"return"===t.type?(this.rval=this.arg=t.arg,this.method="return",this.next="end"):"normal"===t.type&&e&&(this.next=e),m},finish:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var o=this.tryEntries[e];if(o.finallyLoc===t)return this.complete(o.completion,o.afterLoc),S(o),m}},catch:function(t){for(var e=this.tryEntries.length-1;e>=0;--e){var o=this.tryEntries[e];if(o.tryLoc===t){var n=o.completion;if("throw"===n.type){var a=n.arg;S(o)}return a}}throw new Error("illegal catch attempt")},delegateYield:function(t,o,n){return this.delegate={iterator:E(t),resultName:o,nextLoc:n},"next"===this.method&&(this.arg=e),m}},t}(t.exports);try{regeneratorRuntime=e}catch(t){"object"==typeof globalThis?globalThis.regeneratorRuntime=e:Function("r","regeneratorRuntime = r")(e)}},379:(t,e,o)=>{"use strict";var n,a=function(){return void 0===n&&(n=Boolean(window&&document&&document.all&&!window.atob)),n},r=function(){var t={};return function(e){if(void 0===t[e]){var o=document.querySelector(e);if(window.HTMLIFrameElement&&o instanceof window.HTMLIFrameElement)try{o=o.contentDocument.head}catch(t){o=null}t[e]=o}return t[e]}}(),i=[];function s(t){for(var e=-1,o=0;o<i.length;o++)if(i[o].identifier===t){e=o;break}return e}function l(t,e){for(var o={},n=[],a=0;a<t.length;a++){var r=t[a],l=e.base?r[0]+e.base:r[0],d=o[l]||0,c="".concat(l," ").concat(d);o[l]=d+1;var u=s(c),f={css:r[1],media:r[2],sourceMap:r[3]};-1!==u?(i[u].references++,i[u].updater(f)):i.push({identifier:c,updater:g(f,e),references:1}),n.push(c)}return n}function d(t){var e=document.createElement("style"),n=t.attributes||{};if(void 0===n.nonce){var a=o.nc;a&&(n.nonce=a)}if(Object.keys(n).forEach((function(t){e.setAttribute(t,n[t])})),"function"==typeof t.insert)t.insert(e);else{var i=r(t.insert||"head");if(!i)throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");i.appendChild(e)}return e}var c,u=(c=[],function(t,e){return c[t]=e,c.filter(Boolean).join("\n")});function f(t,e,o,n){var a=o?"":n.media?"@media ".concat(n.media," {").concat(n.css,"}"):n.css;if(t.styleSheet)t.styleSheet.cssText=u(e,a);else{var r=document.createTextNode(a),i=t.childNodes;i[e]&&t.removeChild(i[e]),i.length?t.insertBefore(r,i[e]):t.appendChild(r)}}function p(t,e,o){var n=o.css,a=o.media,r=o.sourceMap;if(a?t.setAttribute("media",a):t.removeAttribute("media"),r&&"undefined"!=typeof btoa&&(n+="\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(r))))," */")),t.styleSheet)t.styleSheet.cssText=n;else{for(;t.firstChild;)t.removeChild(t.firstChild);t.appendChild(document.createTextNode(n))}}var h=null,m=0;function g(t,e){var o,n,a;if(e.singleton){var r=m++;o=h||(h=d(e)),n=f.bind(null,o,r,!1),a=f.bind(null,o,r,!0)}else o=d(e),n=p.bind(null,o,e),a=function(){!function(t){if(null===t.parentNode)return!1;t.parentNode.removeChild(t)}(o)};return n(t),function(e){if(e){if(e.css===t.css&&e.media===t.media&&e.sourceMap===t.sourceMap)return;n(t=e)}else a()}}t.exports=function(t,e){(e=e||{}).singleton||"boolean"==typeof e.singleton||(e.singleton=a());var o=l(t=t||[],e);return function(t){if(t=t||[],"[object Array]"===Object.prototype.toString.call(t)){for(var n=0;n<o.length;n++){var a=s(o[n]);i[a].references--}for(var r=l(t,e),d=0;d<o.length;d++){var c=s(o[d]);0===i[c].references&&(i[c].updater(),i.splice(c,1))}o=r}}}}},e={};function o(n){var a=e[n];if(void 0!==a)return a.exports;var r=e[n]={id:n,exports:{}};return t[n](r,r.exports,o),r.exports}o.n=t=>{var e=t&&t.__esModule?()=>t.default:()=>t;return o.d(e,{a:e}),e},o.d=(t,e)=>{for(var n in e)o.o(e,n)&&!o.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:e[n]})},o.o=(t,e)=>Object.prototype.hasOwnProperty.call(t,e),(()=>{"use strict";function t(t,e,o,n,a,r,i,s){var l,d="function"==typeof t?t.options:t;if(e&&(d.render=e,d.staticRenderFns=o,d._compiled=!0),n&&(d.functional=!0),r&&(d._scopeId="data-v-"+r),i?(l=function(t){(t=t||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext)||"undefined"==typeof __VUE_SSR_CONTEXT__||(t=__VUE_SSR_CONTEXT__),a&&a.call(this,t),t&&t._registeredComponents&&t._registeredComponents.add(i)},d._ssrRegister=l):a&&(l=s?function(){a.call(this,(d.functional?this.parent:this).$root.$options.shadowRoot)}:a),l)if(d.functional){d._injectStyles=l;var c=d.render;d.render=function(t,e){return l.call(e),c(t,e)}}else{var u=d.beforeCreate;d.beforeCreate=u?[].concat(u,l):[l]}return{exports:t,options:d}}const e=t({data:function(){return{toggleOffset:-25}},computed:{marginTop:function(){return"".concat(this.toggleOffset,"px")},toggleMode:function(){return this.$root.$data.settings.visibilityToggle},toggleText:function(){return this.showMap?"Hide Map":"Show Map"},showMap:function(){return this.$root.$data.settings.showMap},markerIcon:function(){var t=this.$root.$data.icons;return this.showMap?t.markerHollow:t.marker}},mounted:function(){this.adjustTogglePosition()},methods:{adjustTogglePosition:function(){var t=this.$el.closest(".field"),e=t.getElementsByClassName("copytextbtn"),o=t.getElementsByClassName("instructions");if(e.length){var n="gm-toggle-".concat(this.toggleMode);e[0].classList.add(n)}if(o.length){var a=o[0].clientHeight;this.toggleOffset-=a}},toggle:function(){var t=this.$root.$data.settings.showMap;this.$root.$data.settings.showMap=!t}}},(function(){var t=this,e=t.$createElement,o=t._self._c||e;return"hidden"!==t.toggleMode?o("span",{style:{float:"right","margin-top":t.marginTop,"margin-right":"8px",cursor:"pointer"},on:{click:function(e){return t.toggle()}}},["icon"!==t.toggleMode?o("span",[t._v(t._s(t.toggleText))]):t._e(),t._v(" "),"text"!==t.toggleMode?o("img",{style:{height:"14px","margin-left":"2px","margin-bottom":"-2px"},attrs:{alt:"Marker icon",title:"icon"===t.toggleMode&&t.toggleText,src:t.markerIcon}}):t._e()]):t._e()}),[],!1,null,null,null).exports;var n=["Australia","Canada","France","Hong Kong","India","Ireland","Malaysia","New Zealand","Pakistan","Singapore","Sri Lanka","Taiwan","Thailand","United Kingdom","United States"],a=["Italy"];function r(t,e){var o,r,i,s,l,d={};if(t.forEach((function(t){var e=t.types[0];switch(e){case"locality":case"country":d[e]=t.long_name;break;default:d[e]=t.short_name}})),e.street1=(r=(o=d).street_number||"",i=o.route||"",s=o.country||"",l="".concat(i," ").concat(r),n.includes(s)?l="".concat(r," ").concat(i):a.includes(s)&&(l="".concat(i,", ").concat(r)),l.trim().replace(/,*$/,"")),e.street2=null,e.city=d.locality,e.state=d.administrative_area_level_1,e.zip=d.postal_code,e.county=d.administrative_area_level_2,e.country=d.country,"United Kingdom"===d.country)e.city=d.postal_town,e.state=d.administrative_area_level_2}function i(t){return function(t){var e=[];for(var o in t){var n=t[o],a={};if(n.enabled){var r=n.width;100<r&&(r=100),a.width="".concat(--r,"%")}else a.display="none";e.push({key:n.key,label:n.label,enabled:n.enabled,styles:a})}return e}(t=function(t){var e=100,o=[];for(var n in t){var a=t[n];a.key=n,o[parseInt(a.position||e++)]=a}return Object.values(o)}(t))}const s=t({data:function(){return{handle:this.$root.$data.handle,namespacedName:this.$root.$data.namespacedName,inputClasses:["text","fullwidth"]}},mounted:function(){try{if(!window.google)return void console.error("[GM] The `google` object has not yet been loaded.");var t,e,o,n=this.$root.$data.settings,a=!1,r=[];for(var i in n.subfieldConfig){var s,l;t=n.subfieldConfig[i],e=1===parseInt(null!==(s=t.enabled)&&void 0!==s?s:0),o=1===parseInt(null!==(l=t.autocomplete)&&void 0!==l?l:0),!a&&e&&(a=i),o&&r.push(this.$refs[i][0])}for(var d in r.length||r.push(this.$refs[a][0]),r)this.setAutocomplete(r[d])}catch(t){console.error(t)}},methods:{setAutocomplete:function(t){var e=this,o=new window.google.maps.places.Autocomplete(t,{fields:["address_components","formatted_address","geometry.location","name","place_id"]});o.addListener("place_changed",(function(){var t=o.getPlace();e.setAddressData(t);var n=e.$root.$data.settings;"noChange"!==n.mapOnSearch&&(e.$root.$data.settings.showMap="open"===n.mapOnSearch)})),window.google.maps.event.addDomListener(t,"keydown",(function(t){13===t.keyCode&&t.preventDefault()}))},setAddressData:function(t){var e=this.$root.$data.data,o=t.address_components,n=t.geometry.location;r(o,e.address),e.address.name=t.name,e.address.placeId=t.place_id,e.address.formatted=t.formatted_address,e.address.raw=JSON.stringify(t),e.coords.lat=parseFloat(n.lat().toFixed(7)),e.coords.lng=parseFloat(n.lng().toFixed(7)),e.coords.lat&&e.coords.lng||(e.address.placeId=null,e.address.formatted=null,e.address.raw=null)},subfieldDisplay:function(){return i(this.$root.$data.settings.subfieldConfig)}}},(function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",t._l(t.subfieldDisplay(),(function(e){return o("input",{directives:[{name:"model",rawName:"v-model",value:t.$root.$data.data.address[e.key],expression:"$root.$data.data.address[subfield.key]"}],ref:e.key,refInFor:!0,class:t.inputClasses,style:e.styles,attrs:{placeholder:e.label,autocomplete:"chrome-off",name:t.namespacedName+"["+e.key+"]"},domProps:{value:t.$root.$data.data.address[e.key]},on:{input:function(o){o.target.composing||t.$set(t.$root.$data.data.address,e.key,o.target.value)}}})})),0)}),[],!1,null,null,null).exports;const l={data:function(){return{handle:this.$root.$data.handle,namespacedName:this.$root.$data.namespacedName}},computed:{getType:function(){return"hidden"===this.$root.$data.settings.coordinatesMode?"hidden":"number"},getReadOnly:function(){return!["editable","hidden"].includes(this.$root.$data.settings.coordinatesMode)},getInputClasses:function(){var t=this.$root.$data.settings.coordinatesMode;return"hidden"===t?[]:["text","code","fullwidth","editable"!==t?"disabled":null]}},methods:{coordinatesDisplay:function(){return[{key:"lat",label:"Latitude",styles:{width:"43%"}},{key:"lng",label:"Longitude",styles:{width:"43%"}},{key:"zoom",label:"Zoom",styles:{width:"11%"}}]}}};var d=o(379),c=o.n(d),u=o(624),f={insert:"head",singleton:!1};c()(u.Z,f);u.Z.locals;const p=t(l,(function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",t._l(t.coordinatesDisplay(),(function(e){return o("input",{directives:[{name:"model",rawName:"v-model.number",value:t.$root.$data.data.coords[e.key],expression:"$root.$data.data.coords[coord.key]",modifiers:{number:!0}}],class:t.getInputClasses,style:e.styles,attrs:{placeholder:e.label,type:t.getType,readonly:t.getReadOnly,autocomplete:"chrome-off",name:t.namespacedName+"["+e.key+"]"},domProps:{value:t.$root.$data.data.coords[e.key]},on:{input:function(o){o.target.composing||t.$set(t.$root.$data.data.coords,e.key,t._n(o.target.value))},blur:function(e){return t.$forceUpdate()}}})})),0)}),[],!1,null,"3fde90ed",null).exports;const h=t({data:function(){return{namespacedName:this.$root.$data.namespacedName}},watch:{"$parent.lat":function(){this.validateMeta()},"$parent.lng":function(){this.validateMeta()}},methods:{validateMeta:function(){this.$parent.validCoords()||(this.$root.$data.data.address.formatted=null,this.$root.$data.data.address.raw=null)}}},(function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",[o("input",{directives:[{name:"model",rawName:"v-model",value:t.$root.$data.data.address.formatted,expression:"$root.$data.data.address['formatted']"}],attrs:{type:"hidden",name:t.namespacedName+"[formatted]"},domProps:{value:t.$root.$data.data.address.formatted},on:{input:function(e){e.target.composing||t.$set(t.$root.$data.data.address,"formatted",e.target.value)}}}),t._v(" "),o("input",{directives:[{name:"model",rawName:"v-model",value:t.$root.$data.data.address.raw,expression:"$root.$data.data.address['raw']"}],attrs:{type:"hidden",name:t.namespacedName+"[raw]"},domProps:{value:t.$root.$data.data.address.raw},on:{input:function(e){e.target.composing||t.$set(t.$root.$data.data.address,"raw",e.target.value)}}})])}),[],!1,null,null,null).exports;var m=o(757),g=o.n(m);function v(t,e,o,n,a,r,i){try{var s=t[r](i),l=s.value}catch(t){return void o(t)}s.done?e(l):Promise.resolve(l).then(n,a)}var y=t({data:function(){return{map:null,marker:null,settings:this.$root.$data.settings}},computed:{zoom:function(){return this.$root.$data.data.coords.zoom}},watch:{"$parent.lat":function(){this.updateMarkerPosition(),this.$root.$data.data.coords.zoom=this.map.getZoom()},"$parent.lng":function(){this.updateMarkerPosition(),this.$root.$data.data.coords.zoom=this.map.getZoom()},zoom:function(){this.updateZoomLevel()}},mounted:function(){var t,e=this;return(t=g().mark((function t(){var o;return g().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:if(!(o=e.fromField(e.$root.$data))){t.next=4;break}return e.initMap(o),t.abrupt("return");case 4:return t.next=6,new Promise((function(t,e){console.log("Attempting geolocation..."),navigator.geolocation.getCurrentPosition(t,e,{timeout:5e3})})).then((function(t){console.log("Success!"),t.coords&&e.initMap({lat:t.coords.latitude,lng:t.coords.longitude,zoom:10})}),(function(t){console.log("Unable to perform HTML5 geolocation."),e.initMap(e.fromFallback())}));case 6:t.sent;case 7:case"end":return t.stop()}}),t)})),function(){var e=this,o=arguments;return new Promise((function(n,a){var r=t.apply(e,o);function i(t){v(r,n,a,i,s,"next",t)}function s(t){v(r,n,a,i,s,"throw",t)}i(void 0)}))})()},methods:{updateMarkerPosition:function(){if(this.$parent.validCoords()){var t=this.$root.$data.data.coords;this.marker.setPosition({lat:parseFloat(t.lat.toFixed(7)),lng:parseFloat(t.lng.toFixed(7))}),this.centerMap()}},_getCoords:function(t){return!!this.$parent.validCoords(t)&&t},fromField:function(t){var e=this._getCoords(t.data.coords);if(e)return e;var o=this._getCoords(t.settings.coordinatesDefault);return o||!1},fromFallback:function(){return{lat:32.3113966,lng:-64.7527469,zoom:6}},updateZoomLevel:function(){var t=parseInt(this.$root.$data.data.coords.zoom);0===t||t<0?t=0:t&&!isNaN(t)||(t=11),this.map.setZoom(t)},centerMap:function(){var t=JSON.parse(JSON.stringify(this.$root.$data.data.coords));t.lat&&t.lng&&this.map.panTo(t)},initMap:function(t){var e=this;try{var o=window.google;if(!o)return void console.error("[GM] The `google` object has not yet been loaded.");var n={lat:parseFloat(t.lat),lng:parseFloat(t.lng)};this.map=new o.maps.Map(this.$el,{streetViewControl:!1,fullscreenControl:!1,center:n,zoom:parseInt(t.zoom),controlSize:this.settings.controlSize}),this.marker=new o.maps.Marker({position:n,map:this.map,draggable:!0}),o.maps.event.addListener(this.marker,"dragend",(function(){var t=e.marker.getPosition();e.$root.$data.data.coords={lat:parseFloat(t.lat().toFixed(7)),lng:parseFloat(t.lng().toFixed(7)),zoom:e.map.getZoom()},e.centerMap()})),o.maps.event.addListener(this.map,"zoom_changed",(function(){e.$root.$data.data.coords.zoom=e.map.getZoom()}))}catch(t){console.error(t)}}}},(function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{directives:[{name:"show",rawName:"v-show",value:t.settings.showMap,expression:"settings.showMap"}],staticClass:"gm-map"},[o("div",[t._v("Loading map...")])])}),[],!1,null,null,null);function $(t){return $="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},$(t)}const b=t({name:"AddressField",components:{"address-toggle":e,"address-subfields":s,"address-coords":p,"address-meta":h,"address-map":y.exports},props:["settings","data"],computed:{lat:function(){return this.data.coords.lat},lng:function(){return this.data.coords.lng}},methods:{validCoords:function(t){for(var e in t||(t={lat:this.data.coords.lat,lng:this.data.coords.lng}),t)if("zoom"!==e){var o=t[e];if(!["number","string"].includes($(o)))return!1;if(isNaN(o))return!1;if(""===o)return!1}return!0}}},(function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{staticClass:"address-field"},[o("address-toggle"),t._v(" "),o("address-subfields"),t._v(" "),o("address-coords"),t._v(" "),o("address-meta"),t._v(" "),o("div",{staticStyle:{clear:"both"}}),t._v(" "),o("address-map")],1)}),[],!1,null,null,null).exports;function w(t){return function(t){var e=100,o=[];for(var n in t){var a=t[n];a.key=n,o[parseInt(a.position||e++)]=a}return Object.values(o)}(t)}const _=t({props:["settings","namespacedName"],data:function(){return{subfieldConfig:[]}},mounted:function(){new Sortable(this.$refs.sortable,{handle:".move",animation:150,ghostClass:"sortable-ghost",onUpdate:this.updatePositions});var t=this.$root.$data.settings.subfieldConfig;this.subfieldConfig=w(t)},methods:{fieldName:function(t,e){return"".concat(this.namespacedName,"[").concat(t,"][").concat(e,"]")},updatePositions:function(){var t=this;Array.from(this.$refs.sortable.children).forEach((function(e,o){var n=o+1,a=e.dataset.handle;t.$root.$data.settings.subfieldConfig[a].position=n}))}}},(function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("table",{staticClass:"editable fullwidth"},[t._m(0),t._v(" "),o("tbody",{ref:"sortable"},t._l(t.subfieldConfig,(function(e){return o("tr",{class:{disabled:!t.$root.$data.settings.subfieldConfig[e.key].enabled},attrs:{"data-handle":e.key}},[o("td",{staticClass:"singleline-cell textual"},[o("textarea",{directives:[{name:"model",rawName:"v-model",value:t.$root.$data.settings.subfieldConfig[e.key].label,expression:"$root.$data.settings.subfieldConfig[subfield.key].label"}],staticStyle:{"min-height":"34px"},attrs:{name:t.fieldName(e.key,"label"),rows:"1",placeholder:e.key},domProps:{value:t.$root.$data.settings.subfieldConfig[e.key].label},on:{input:function(o){o.target.composing||t.$set(t.$root.$data.settings.subfieldConfig[e.key],"label",o.target.value)}}})]),t._v(" "),o("td",{staticClass:"textual code",staticStyle:{width:"15%","text-align":"right"}},[o("input",{directives:[{name:"model",rawName:"v-model.number",value:t.$root.$data.settings.subfieldConfig[e.key].width,expression:"$root.$data.settings.subfieldConfig[subfield.key].width",modifiers:{number:!0}}],staticStyle:{"min-height":"34px","max-width":"60px","text-align":"right",border:"none"},attrs:{type:"number",name:t.fieldName(e.key,"width")},domProps:{value:t.$root.$data.settings.subfieldConfig[e.key].width},on:{input:function(o){o.target.composing||t.$set(t.$root.$data.settings.subfieldConfig[e.key],"width",t._n(o.target.value))},blur:function(e){return t.$forceUpdate()}}})]),t._v(" "),o("td",{staticClass:"checkbox-cell",staticStyle:{width:"15%","text-align":"center"}},[o("div",{staticClass:"checkbox-wrapper"},[o("input",{directives:[{name:"model",rawName:"v-model",value:t.$root.$data.settings.subfieldConfig[e.key].enabled,expression:"$root.$data.settings.subfieldConfig[subfield.key].enabled"}],staticClass:"checkbox",attrs:{type:"checkbox",name:t.fieldName(e.key,"enabled"),id:"enabled-"+e.key,value:"1"},domProps:{checked:Array.isArray(t.$root.$data.settings.subfieldConfig[e.key].enabled)?t._i(t.$root.$data.settings.subfieldConfig[e.key].enabled,"1")>-1:t.$root.$data.settings.subfieldConfig[e.key].enabled},on:{change:function(o){var n=t.$root.$data.settings.subfieldConfig[e.key].enabled,a=o.target,r=!!a.checked;if(Array.isArray(n)){var i=t._i(n,"1");a.checked?i<0&&t.$set(t.$root.$data.settings.subfieldConfig[e.key],"enabled",n.concat(["1"])):i>-1&&t.$set(t.$root.$data.settings.subfieldConfig[e.key],"enabled",n.slice(0,i).concat(n.slice(i+1)))}else t.$set(t.$root.$data.settings.subfieldConfig[e.key],"enabled",r)}}}),o("label",{attrs:{for:"enabled-"+e.key}})])]),t._v(" "),o("td",{staticClass:"checkbox-cell",staticStyle:{width:"15%","text-align":"center"}},[o("div",{staticClass:"checkbox-wrapper"},[o("input",{directives:[{name:"model",rawName:"v-model",value:t.$root.$data.settings.subfieldConfig[e.key].autocomplete,expression:"$root.$data.settings.subfieldConfig[subfield.key].autocomplete"}],staticClass:"checkbox",attrs:{type:"checkbox",name:t.fieldName(e.key,"autocomplete"),id:"autocomplete-"+e.key,value:"1"},domProps:{checked:Array.isArray(t.$root.$data.settings.subfieldConfig[e.key].autocomplete)?t._i(t.$root.$data.settings.subfieldConfig[e.key].autocomplete,"1")>-1:t.$root.$data.settings.subfieldConfig[e.key].autocomplete},on:{change:function(o){var n=t.$root.$data.settings.subfieldConfig[e.key].autocomplete,a=o.target,r=!!a.checked;if(Array.isArray(n)){var i=t._i(n,"1");a.checked?i<0&&t.$set(t.$root.$data.settings.subfieldConfig[e.key],"autocomplete",n.concat(["1"])):i>-1&&t.$set(t.$root.$data.settings.subfieldConfig[e.key],"autocomplete",n.slice(0,i).concat(n.slice(i+1)))}else t.$set(t.$root.$data.settings.subfieldConfig[e.key],"autocomplete",r)}}}),o("label",{attrs:{for:"autocomplete-"+e.key}})])]),t._v(" "),o("td",{staticClass:"thin action"},[o("a",{staticClass:"move icon",attrs:{title:"Reorder"}}),t._v(" "),o("input",{directives:[{name:"model",rawName:"v-model",value:t.$root.$data.settings.subfieldConfig[e.key].position,expression:"$root.$data.settings.subfieldConfig[subfield.key].position"}],attrs:{type:"hidden",name:t.fieldName(e.key,"position")},domProps:{value:t.$root.$data.settings.subfieldConfig[e.key].position},on:{input:function(o){o.target.composing||t.$set(t.$root.$data.settings.subfieldConfig[e.key],"position",o.target.value)}}})])])})),0)])}),[function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("thead",[o("tr",[o("th",{staticClass:"singleline-cell textual",attrs:{scope:"col"}},[t._v("Label")]),t._v(" "),o("th",{staticClass:"number-cell textual",staticStyle:{"text-align":"right"},attrs:{scope:"col"}},[t._v("Width")]),t._v(" "),o("th",{staticClass:"checkbox-cell",staticStyle:{"text-align":"center"},attrs:{scope:"col",title:"Include a subfield in the visible layout."}},[t._v("Show")]),t._v(" "),o("th",{staticClass:"checkbox-cell",staticStyle:{"text-align":"center"},attrs:{scope:"col",title:"Show autocomplete matches when typing."}},[t._v("Auto")]),t._v(" "),o("th",[t._v(" ")])])])}],!1,null,null,null).exports;const k=t({props:["namespacedName"],data:function(){return{}},computed:{coordinatesDefault:function(){var t=this.$root.$data.settings.coordinatesDefault,e=this.$root.$data.data.coords;return e.lat&&e.lng?e:t.lat&&t.lng?t:{lat:null,lng:null,zoom:null}}},watch:{coordsWatcher:function(t){this.updateCoords(t)}},methods:{fieldName:function(t){return"".concat(this.namespacedName,"[").concat(t,"]")},updateCoords:function(t){this.coordinatesDefault={lat:t.lat,lng:t.lng,zoom:t.zoom}}}},(function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{staticClass:"default-coords"},[o("input",{directives:[{name:"model",rawName:"v-model",value:t.coordinatesDefault.lat,expression:"coordinatesDefault['lat']"}],attrs:{type:"hidden",name:t.fieldName("lat")},domProps:{value:t.coordinatesDefault.lat},on:{input:function(e){e.target.composing||t.$set(t.coordinatesDefault,"lat",e.target.value)}}}),t._v(" "),o("input",{directives:[{name:"model",rawName:"v-model",value:t.coordinatesDefault.lng,expression:"coordinatesDefault['lng']"}],attrs:{type:"hidden",name:t.fieldName("lng")},domProps:{value:t.coordinatesDefault.lng},on:{input:function(e){e.target.composing||t.$set(t.coordinatesDefault,"lng",e.target.value)}}}),t._v(" "),o("input",{directives:[{name:"model",rawName:"v-model",value:t.coordinatesDefault.zoom,expression:"coordinatesDefault['zoom']"}],attrs:{type:"hidden",name:t.fieldName("zoom")},domProps:{value:t.coordinatesDefault.zoom},on:{input:function(e){e.target.composing||t.$set(t.coordinatesDefault,"zoom",e.target.value)}}})])}),[],!1,null,null,null).exports;Vue.config.productionTip=!1,window.initAddressFieldSettings=function(){var t=document.querySelectorAll(".address-settings"),e="vue-mounted";t.forEach((function(t){t.classList.contains(e)||new Vue({el:t,components:{"address-field":b,"subfield-manager":_,"default-coords":k},mounted:function(){document.getElementById(t.id).classList.add(e)},data:{settings,data,icons}})}))}})()})();