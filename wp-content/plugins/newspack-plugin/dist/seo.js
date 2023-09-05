(()=>{var t,e={6577:(t,e,r)=>{"use strict";var n,o=r(5618),i=r(5536),s=r(1516),a=r(8005),c=r(5516),u=r(1465),l=Function.prototype.bind,p=Object.defineProperty,f=Object.prototype.hasOwnProperty;n=function(t,e,r){var n,o=i(e)&&s(e.value);return delete(n=a(e)).writable,delete n.value,n.get=function(){return!r.overwriteDefinition&&f.call(this,t)?o:(e.value=l.call(o,r.resolveContext?r.resolveContext(this):this),p(this,t,e),this[t])},n},t.exports=function(t){var e=c(arguments[1]);return o(e.resolveContext)&&s(e.resolveContext),u(t,(function(t,r){return n(r,t,e)}))}},1804:(t,e,r)=>{"use strict";var n=r(5618),o=r(7205),i=r(7191),s=r(5516),a=r(9981);(t.exports=function(t,e){var r,o,c,u,l;return arguments.length<2||"string"!=typeof t?(u=e,e=t,t=null):u=arguments[2],n(t)?(r=a.call(t,"c"),o=a.call(t,"e"),c=a.call(t,"w")):(r=c=!0,o=!1),l={value:e,configurable:r,enumerable:o,writable:c},u?i(s(u),l):l}).gs=function(t,e,r){var c,u,l,p;return"string"!=typeof t?(l=r,r=e,e=t,t=null):l=arguments[3],n(e)?o(e)?n(r)?o(r)||(l=r,r=void 0):r=void 0:(l=e,e=r=void 0):e=void 0,n(t)?(c=a.call(t,"c"),u=a.call(t,"e")):(c=!0,u=!1),p={get:e,set:r,configurable:c,enumerable:u},l?i(s(l),p):p}},3843:(t,e,r)=>{"use strict";Object.defineProperty(e,"__esModule",{value:!0});var n=r(7845),o=r(2819),i=function(){function t(t,e){this.mapFn=t,this.opts=e,this.cache=new n}return t.prototype.map=function(t){return o.isArray(t)?this.mapArray(t):o.isObject(t)?this.mapObject(t):t},t.prototype.mapArray=function(t){if(this.cache.has(t))return this.cache.get(t);var e=t.length,r=[];this.cache.set(t,r);for(var n=0;n<e;n++)r.push(this.map(t[n]));return r},t.prototype.mapObject=function(t){if(this.cache.has(t))return this.cache.get(t);var e=this.mapFn,r=this.opts.thisArg,n={};for(var o in this.cache.set(t,n),t)t.hasOwnProperty(o)&&(n[e.call(r,o,t[o])]=this.map(t[o]));return n},t}();e.DeepMapKeys=i},4237:(t,e,r)=>{"use strict";var n=r(2819),o=r(3843);t.exports=function(t,e,r){if(r=n.isNil(r)?{}:r,!e)throw new Error("mapFn is required");if(!n.isFunction(e))throw new TypeError("mapFn must be a function");if(!n.isObject(r))throw new TypeError("options must be an object");return new o.DeepMapKeys(e,r).map(t)}},1126:(t,e,r)=>{"use strict";var n=r(2745);t.exports=function(){return n(this).length=0,this}},4616:(t,e,r)=>{"use strict";t.exports=r(7379)()?Array.from:r(2669)},7379:t=>{"use strict";t.exports=function(){var t,e,r=Array.from;return"function"==typeof r&&(e=r(t=["raz","dwa"]),Boolean(e&&e!==t&&"dwa"===e[1]))}},2669:(t,e,r)=>{"use strict";var n=r(8641).iterator,o=r(6766),i=r(1216),s=r(3902),a=r(1290),c=r(2745),u=r(6914),l=r(4349),p=Array.isArray,f=Function.prototype.call,_={configurable:!0,enumerable:!0,writable:!0,value:null},h=Object.defineProperty;t.exports=function(t){var e,r,y,d,v,w,b,g,x,m,O=arguments[1],j=arguments[2];if(t=Object(c(t)),u(O)&&a(O),this&&this!==Array&&i(this))e=this;else{if(!O){if(o(t))return 1!==(v=t.length)?Array.apply(null,t):((d=new Array(1))[0]=t[0],d);if(p(t)){for(d=new Array(v=t.length),r=0;r<v;++r)d[r]=t[r];return d}}d=[]}if(!p(t))if(void 0!==(x=t[n])){for(b=a(x).call(t),e&&(d=new e),g=b.next(),r=0;!g.done;)m=O?f.call(O,j,g.value,r):g.value,e?(_.value=m,h(d,r,_)):d[r]=m,g=b.next(),++r;v=r}else if(l(t)){for(v=t.length,e&&(d=new e),r=0,y=0;r<v;++r)m=t[r],r+1<v&&(w=m.charCodeAt(0))>=55296&&w<=56319&&(m+=t[++r]),m=O?f.call(O,j,m,y):m,e?(_.value=m,h(d,y,_)):d[y]=m,++y;v=y}if(void 0===v)for(v=s(t.length),e&&(d=new e(v)),r=0;r<v;++r)m=O?f.call(O,j,t[r],r):t[r],e?(_.value=m,h(d,r,_)):d[r]=m;return e&&(_.value=null,d.length=v),d}},6766:t=>{"use strict";var e=Object.prototype.toString,r=e.call(function(){return arguments}());t.exports=function(t){return e.call(t)===r}},1216:t=>{"use strict";var e=Object.prototype.toString,r=RegExp.prototype.test.bind(/^[object [A-Za-z0-9]*Function]$/);t.exports=function(t){return"function"==typeof t&&r(e.call(t))}},430:t=>{"use strict";t.exports=function(){}},5875:(t,e,r)=>{"use strict";t.exports=r(5448)()?Math.sign:r(4592)},5448:t=>{"use strict";t.exports=function(){var t=Math.sign;return"function"==typeof t&&1===t(10)&&-1===t(-20)}},4592:t=>{"use strict";t.exports=function(t){return t=Number(t),isNaN(t)||0===t?t:t>0?1:-1}},3478:(t,e,r)=>{"use strict";var n=r(5875),o=Math.abs,i=Math.floor;t.exports=function(t){return isNaN(t)?0:0!==(t=Number(t))&&isFinite(t)?n(t)*i(o(t)):t}},3902:(t,e,r)=>{"use strict";var n=r(3478),o=Math.max;t.exports=function(t){return o(0,n(t))}},4214:(t,e,r)=>{"use strict";var n=r(1290),o=r(2745),i=Function.prototype.bind,s=Function.prototype.call,a=Object.keys,c=Object.prototype.propertyIsEnumerable;t.exports=function(t,e){return function(r,u){var l,p=arguments[2],f=arguments[3];return r=Object(o(r)),n(u),l=a(r),f&&l.sort("function"==typeof f?i.call(f,r):void 0),"function"!=typeof t&&(t=l[t]),s.call(t,l,(function(t,n){return c.call(r,t)?s.call(u,p,r[t],t,r,n):e}))}}},7191:(t,e,r)=>{"use strict";t.exports=r(2429)()?Object.assign:r(7346)},2429:t=>{"use strict";t.exports=function(){var t,e=Object.assign;return"function"==typeof e&&(e(t={foo:"raz"},{bar:"dwa"},{trzy:"trzy"}),t.foo+t.bar+t.trzy==="razdwatrzy")}},7346:(t,e,r)=>{"use strict";var n=r(5103),o=r(2745),i=Math.max;t.exports=function(t,e){var r,s,a,c=i(arguments.length,2);for(t=Object(o(t)),a=function(n){try{t[n]=e[n]}catch(t){r||(r=t)}},s=1;s<c;++s)n(e=arguments[s]).forEach(a);if(void 0!==r)throw r;return t}},8005:(t,e,r)=>{"use strict";var n=r(4616),o=r(7191),i=r(2745);t.exports=function(t){var e=Object(i(t)),r=arguments[1],s=Object(arguments[2]);if(e!==t&&!r)return e;var a={};return r?n(r,(function(e){(s.ensure||e in t)&&(a[e]=t[e])})):o(a,t),a}},634:(t,e,r)=>{"use strict";var n,o,i,s,a=Object.create;r(4589)()||(n=r(9221)),t.exports=n?1!==n.level?a:(o={},i={},s={configurable:!1,enumerable:!1,writable:!0,value:void 0},Object.getOwnPropertyNames(Object.prototype).forEach((function(t){i[t]="__proto__"!==t?s:{configurable:!0,enumerable:!1,writable:!0,value:void 0}})),Object.defineProperties(o,i),Object.defineProperty(n,"nullPolyfill",{configurable:!1,enumerable:!1,writable:!1,value:o}),function(t,e){return a(null===t?o:t,e)}):a},7031:(t,e,r)=>{"use strict";t.exports=r(4214)("forEach")},6191:(t,e,r)=>{"use strict";var n=r(6914),o={function:!0,object:!0};t.exports=function(t){return n(t)&&o[typeof t]||!1}},6914:(t,e,r)=>{"use strict";var n=r(430)();t.exports=function(t){return t!==n&&null!==t}},5103:(t,e,r)=>{"use strict";t.exports=r(7446)()?Object.keys:r(6137)},7446:t=>{"use strict";t.exports=function(){try{return Object.keys("primitive"),!0}catch(t){return!1}}},6137:(t,e,r)=>{"use strict";var n=r(6914),o=Object.keys;t.exports=function(t){return o(n(t)?Object(t):t)}},1465:(t,e,r)=>{"use strict";var n=r(1290),o=r(7031),i=Function.prototype.call;t.exports=function(t,e){var r={},s=arguments[2];return n(e),o(t,(function(t,n,o,a){r[n]=i.call(e,s,t,n,o,a)})),r}},5516:(t,e,r)=>{"use strict";var n=r(6914),o=Array.prototype.forEach,i=Object.create,s=function(t,e){var r;for(r in t)e[r]=t[r]};t.exports=function(t){var e=i(null);return o.call(arguments,(function(t){n(t)&&s(Object(t),e)})),e}},1386:(t,e,r)=>{"use strict";t.exports=r(4589)()?Object.setPrototypeOf:r(9221)},4589:t=>{"use strict";var e=Object.create,r=Object.getPrototypeOf,n={};t.exports=function(){var t=Object.setPrototypeOf,o=arguments[0]||e;return"function"==typeof t&&r(t(o(null),n))===n}},9221:(t,e,r)=>{"use strict";var n,o,i,s,a=r(6191),c=r(2745),u=Object.prototype.isPrototypeOf,l=Object.defineProperty,p={configurable:!0,enumerable:!1,writable:!0,value:void 0};n=function(t,e){if(c(t),null===e||a(e))return t;throw new TypeError("Prototype must be null or an object")},t.exports=(o=function(){var t,e=Object.create(null),r={},n=Object.getOwnPropertyDescriptor(Object.prototype,"__proto__");if(n){try{(t=n.set).call(e,r)}catch(t){}if(Object.getPrototypeOf(e)===r)return{set:t,level:2}}return e.__proto__=r,Object.getPrototypeOf(e)===r?{level:2}:((e={}).__proto__=r,Object.getPrototypeOf(e)===r&&{level:1})}(),o?(2===o.level?o.set?(s=o.set,i=function(t,e){return s.call(n(t,e),e),t}):i=function(t,e){return n(t,e).__proto__=e,t}:i=function t(e,r){var o;return n(e,r),(o=u.call(t.nullPolyfill,e))&&delete t.nullPolyfill.__proto__,null===r&&(r=t.nullPolyfill),e.__proto__=r,o&&l(t.nullPolyfill,"__proto__",p),e},Object.defineProperty(i,"level",{configurable:!1,enumerable:!1,writable:!1,value:o.level})):null),r(634)},1290:t=>{"use strict";t.exports=function(t){if("function"!=typeof t)throw new TypeError(t+" is not a function");return t}},2959:(t,e,r)=>{"use strict";var n=r(6191);t.exports=function(t){if(!n(t))throw new TypeError(t+" is not an Object");return t}},2745:(t,e,r)=>{"use strict";var n=r(6914);t.exports=function(t){if(!n(t))throw new TypeError("Cannot use null or undefined");return t}},9981:(t,e,r)=>{"use strict";t.exports=r(3591)()?String.prototype.contains:r(6042)},3591:t=>{"use strict";var e="razdwatrzy";t.exports=function(){return"function"==typeof e.contains&&!0===e.contains("dwa")&&!1===e.contains("foo")}},6042:t=>{"use strict";var e=String.prototype.indexOf;t.exports=function(t){return e.call(this,t,arguments[1])>-1}},4349:t=>{"use strict";var e=Object.prototype.toString,r=e.call("");t.exports=function(t){return"string"==typeof t||t&&"object"==typeof t&&(t instanceof String||e.call(t)===r)||!1}},4701:t=>{"use strict";var e=Object.create(null),r=Math.random;t.exports=function(){var t;do{t=r().toString(36).slice(2)}while(e[t]);return t}},2270:(t,e,r)=>{"use strict";var n,o=r(1386),i=r(9981),s=r(1804),a=r(8641),c=r(2764),u=Object.defineProperty;n=t.exports=function(t,e){if(!(this instanceof n))throw new TypeError("Constructor requires 'new'");c.call(this,t),e=e?i.call(e,"key+value")?"key+value":i.call(e,"key")?"key":"value":"value",u(this,"__kind__",s("",e))},o&&o(n,c),delete n.prototype.constructor,n.prototype=Object.create(c.prototype,{_resolve:s((function(t){return"value"===this.__kind__?this.__list__[t]:"key+value"===this.__kind__?[t,this.__list__[t]]:t}))}),u(n.prototype,a.toStringTag,s("c","Array Iterator"))},9334:(t,e,r)=>{"use strict";var n=r(6766),o=r(1290),i=r(4349),s=r(3226),a=Array.isArray,c=Function.prototype.call,u=Array.prototype.some;t.exports=function(t,e){var r,l,p,f,_,h,y,d,v=arguments[2];if(a(t)||n(t)?r="array":i(t)?r="string":t=s(t),o(e),p=function(){f=!0},"array"!==r)if("string"!==r)for(l=t.next();!l.done;){if(c.call(e,v,l.value,p),f)return;l=t.next()}else for(h=t.length,_=0;_<h&&(y=t[_],_+1<h&&(d=y.charCodeAt(0))>=55296&&d<=56319&&(y+=t[++_]),c.call(e,v,y,p),!f);++_);else u.call(t,(function(t){return c.call(e,v,t,p),f}))}},3226:(t,e,r)=>{"use strict";var n=r(6766),o=r(4349),i=r(2270),s=r(1746),a=r(2233),c=r(8641).iterator;t.exports=function(t){return"function"==typeof a(t)[c]?t[c]():n(t)?new i(t):o(t)?new s(t):new i(t)}},2764:(t,e,r)=>{"use strict";var n,o=r(1126),i=r(7191),s=r(1290),a=r(2745),c=r(1804),u=r(6577),l=r(8641),p=Object.defineProperty,f=Object.defineProperties;t.exports=n=function(t,e){if(!(this instanceof n))throw new TypeError("Constructor requires 'new'");f(this,{__list__:c("w",a(t)),__context__:c("w",e),__nextIndex__:c("w",0)}),e&&(s(e.on),e.on("_add",this._onAdd),e.on("_delete",this._onDelete),e.on("_clear",this._onClear))},delete n.prototype.constructor,f(n.prototype,i({_next:c((function(){var t;if(this.__list__)return this.__redo__&&void 0!==(t=this.__redo__.shift())?t:this.__nextIndex__<this.__list__.length?this.__nextIndex__++:void this._unBind()})),next:c((function(){return this._createResult(this._next())})),_createResult:c((function(t){return void 0===t?{done:!0,value:void 0}:{done:!1,value:this._resolve(t)}})),_resolve:c((function(t){return this.__list__[t]})),_unBind:c((function(){this.__list__=null,delete this.__redo__,this.__context__&&(this.__context__.off("_add",this._onAdd),this.__context__.off("_delete",this._onDelete),this.__context__.off("_clear",this._onClear),this.__context__=null)})),toString:c((function(){return"[object "+(this[l.toStringTag]||"Object")+"]"}))},u({_onAdd:c((function(t){t>=this.__nextIndex__||(++this.__nextIndex__,this.__redo__?(this.__redo__.forEach((function(e,r){e>=t&&(this.__redo__[r]=++e)}),this),this.__redo__.push(t)):p(this,"__redo__",c("c",[t])))})),_onDelete:c((function(t){var e;t>=this.__nextIndex__||(--this.__nextIndex__,this.__redo__&&(-1!==(e=this.__redo__.indexOf(t))&&this.__redo__.splice(e,1),this.__redo__.forEach((function(e,r){e>t&&(this.__redo__[r]=--e)}),this)))})),_onClear:c((function(){this.__redo__&&o.call(this.__redo__),this.__nextIndex__=0}))}))),p(n.prototype,l.iterator,c((function(){return this})))},3182:(t,e,r)=>{"use strict";var n=r(6766),o=r(6914),i=r(4349),s=r(8641).iterator,a=Array.isArray;t.exports=function(t){return!(!o(t)||!a(t)&&!i(t)&&!n(t)&&"function"!=typeof t[s])}},1746:(t,e,r)=>{"use strict";var n,o=r(1386),i=r(1804),s=r(8641),a=r(2764),c=Object.defineProperty;n=t.exports=function(t){if(!(this instanceof n))throw new TypeError("Constructor requires 'new'");t=String(t),a.call(this,t),c(this,"__length__",i("",t.length))},o&&o(n,a),delete n.prototype.constructor,n.prototype=Object.create(a.prototype,{_next:i((function(){if(this.__list__)return this.__nextIndex__<this.__length__?this.__nextIndex__++:void this._unBind()})),_resolve:i((function(t){var e,r=this.__list__[t];return this.__nextIndex__===this.__length__?r:(e=r.charCodeAt(0))>=55296&&e<=56319?r+this.__list__[this.__nextIndex__++]:r}))}),c(n.prototype,s.toStringTag,i("c","String Iterator"))},2233:(t,e,r)=>{"use strict";var n=r(3182);t.exports=function(t){if(!n(t))throw new TypeError(t+" is not iterable");return t}},8641:(t,e,r)=>{"use strict";t.exports=r(8821)()?r(7933).Symbol:r(5127)},8821:(t,e,r)=>{"use strict";var n=r(7933),o={object:!0,symbol:!0};t.exports=function(){var t,e=n.Symbol;if("function"!=typeof e)return!1;t=e("test symbol");try{String(t)}catch(t){return!1}return!!o[typeof e.iterator]&&!!o[typeof e.toPrimitive]&&!!o[typeof e.toStringTag]}},5386:t=>{"use strict";t.exports=function(t){return!!t&&("symbol"==typeof t||!!t.constructor&&"Symbol"===t.constructor.name&&"Symbol"===t[t.constructor.toStringTag])}},7451:(t,e,r)=>{"use strict";var n=r(1804),o=Object.create,i=Object.defineProperty,s=Object.prototype,a=o(null);t.exports=function(t){for(var e,r,o=0;a[t+(o||"")];)++o;return a[t+=o||""]=!0,i(s,e="@@"+t,n.gs(null,(function(t){r||(r=!0,i(this,e,n(t)),r=!1)}))),e}},7628:(t,e,r)=>{"use strict";var n=r(1804),o=r(7933).Symbol;t.exports=function(t){return Object.defineProperties(t,{hasInstance:n("",o&&o.hasInstance||t("hasInstance")),isConcatSpreadable:n("",o&&o.isConcatSpreadable||t("isConcatSpreadable")),iterator:n("",o&&o.iterator||t("iterator")),match:n("",o&&o.match||t("match")),replace:n("",o&&o.replace||t("replace")),search:n("",o&&o.search||t("search")),species:n("",o&&o.species||t("species")),split:n("",o&&o.split||t("split")),toPrimitive:n("",o&&o.toPrimitive||t("toPrimitive")),toStringTag:n("",o&&o.toStringTag||t("toStringTag")),unscopables:n("",o&&o.unscopables||t("unscopables"))})}},277:(t,e,r)=>{"use strict";var n=r(1804),o=r(8541),i=Object.create(null);t.exports=function(t){return Object.defineProperties(t,{for:n((function(e){return i[e]?i[e]:i[e]=t(String(e))})),keyFor:n((function(t){var e;for(e in o(t),i)if(i[e]===t)return e}))})}},5127:(t,e,r)=>{"use strict";var n,o,i,s=r(1804),a=r(8541),c=r(7933).Symbol,u=r(7451),l=r(7628),p=r(277),f=Object.create,_=Object.defineProperties,h=Object.defineProperty;if("function"==typeof c)try{String(c()),i=!0}catch(t){}else c=null;o=function(t){if(this instanceof o)throw new TypeError("Symbol is not a constructor");return n(t)},t.exports=n=function t(e){var r;if(this instanceof t)throw new TypeError("Symbol is not a constructor");return i?c(e):(r=f(o.prototype),e=void 0===e?"":String(e),_(r,{__description__:s("",e),__name__:s("",u(e))}))},l(n),p(n),_(o.prototype,{constructor:s(n),toString:s("",(function(){return this.__name__}))}),_(n.prototype,{toString:s((function(){return"Symbol ("+a(this).__description__+")"})),valueOf:s((function(){return a(this)}))}),h(n.prototype,n.toPrimitive,s("",(function(){var t=a(this);return"symbol"==typeof t?t:t.toString()}))),h(n.prototype,n.toStringTag,s("c","Symbol")),h(o.prototype,n.toStringTag,s("c",n.prototype[n.toStringTag])),h(o.prototype,n.toPrimitive,s("c",n.prototype[n.toPrimitive]))},8541:(t,e,r)=>{"use strict";var n=r(5386);t.exports=function(t){if(!n(t))throw new TypeError(t+" is not a symbol");return t}},7845:(t,e,r)=>{"use strict";t.exports=r(5789)()?WeakMap:r(4165)},5789:t=>{"use strict";t.exports=function(){var t,e;if("function"!=typeof WeakMap)return!1;try{t=new WeakMap([[e={},"one"],[{},"two"],[{},"three"]])}catch(t){return!1}return"[object WeakMap]"===String(t)&&"function"==typeof t.set&&t.set({},1)===t&&"function"==typeof t.delete&&"function"==typeof t.has&&"one"===t.get(e)}},6208:t=>{"use strict";t.exports="function"==typeof WeakMap&&"[object WeakMap]"===Object.prototype.toString.call(new WeakMap)},4165:(t,e,r)=>{"use strict";var n,o=r(6914),i=r(1386),s=r(2959),a=r(2745),c=r(4701),u=r(1804),l=r(3226),p=r(9334),f=r(8641).toStringTag,_=r(6208),h=Array.isArray,y=Object.defineProperty,d=Object.prototype.hasOwnProperty,v=Object.getPrototypeOf;t.exports=n=function(){var t,e=arguments[0];if(!(this instanceof n))throw new TypeError("Constructor requires 'new'");return t=_&&i&&WeakMap!==n?i(new WeakMap,v(this)):this,o(e)&&(h(e)||(e=l(e))),y(t,"__weakMapData__",u("c","$weakMap$"+c())),e?(p(e,(function(e){a(e),t.set(e[0],e[1])})),t):t},_&&(i&&i(n,WeakMap),n.prototype=Object.create(WeakMap.prototype,{constructor:u(n)})),Object.defineProperties(n.prototype,{delete:u((function(t){return!!d.call(s(t),this.__weakMapData__)&&(delete t[this.__weakMapData__],!0)})),get:u((function(t){if(d.call(s(t),this.__weakMapData__))return t[this.__weakMapData__]})),has:u((function(t){return d.call(s(t),this.__weakMapData__)})),set:u((function(t,e){return y(s(t),this.__weakMapData__,u("c",e)),this})),toString:u((function(){return"[object WeakMap]"}))}),y(n.prototype,f,u("c","WeakMap"))},7145:t=>{var e=function(){if("object"==typeof self&&self)return self;if("object"==typeof window&&window)return window;throw new Error("Unable to resolve global `this`")};t.exports=function(){if(this)return this;try{Object.defineProperty(Object.prototype,"__global__",{get:function(){return this},configurable:!0})}catch(t){return e()}try{return __global__||e()}finally{delete Object.prototype.__global__}}()},7933:(t,e,r)=>{"use strict";t.exports=r(6344)()?globalThis:r(7145)},6344:t=>{"use strict";t.exports=function(){return"object"==typeof globalThis&&!!globalThis&&globalThis.Array===Array}},8929:(t,e,r)=>{var n=r(8403),o=r(5393)((function(t,e,r){return e=e.toLowerCase(),t+(r?n(e):e)}));t.exports=o},8403:(t,e,r)=>{var n=r(9833),o=r(1700);t.exports=function(t){return o(n(t).toLowerCase())}},1865:(t,e,r)=>{var n=r(5393)((function(t,e,r){return t+(r?"_":"")+e.toLowerCase()}));t.exports=n},7889:(t,e,r)=>{"use strict";r.r(e);var n=r(7462),o=r(4942),i=(r(5674),r(9307)),s=r(5736),a=r(1383),c=r(9674),u=r(5609);const l=s.__;class p extends i.Component{render(){const{data:t,onChange:e}=this.props,{verification:r,underConstruction:n,urls:o}=t,{google:s,bing:c}=r,{facebook:p,linkedin:f,twitter:_,youtube:h,instagram:y,pinterest:d}=o;return(0,i.createElement)(i.Fragment,null,(0,i.createElement)(a.M$,{title:l("Webmaster Tools","newspack"),description:l("Add verification meta tags to your site","newspack")}),(0,i.createElement)(a.rj,null,(0,i.createElement)(a.w4,{label:"Google",onChange:t=>e({verification:{...r,google:t}}),value:s,help:(0,i.createElement)(i.Fragment,null,l("Get your verification code in","newspack")+" ",(0,i.createElement)(u.ExternalLink,{href:"https://www.google.com/webmasters/verification/verification?tid=alternate"},l("Google Search Console","newspack")))}),(0,i.createElement)(a.w4,{label:"Bing",onChange:t=>e({verification:{...r,bing:t}}),value:c,help:(0,i.createElement)(i.Fragment,null,l("Get your verification code in","newspack")+" ",(0,i.createElement)(u.ExternalLink,{href:"https://www.bing.com/toolbox/webmaster/#/Dashboard/"},l("Bing Webmaster Tool","newspack")))})),(0,i.createElement)(a.M$,{title:l("Social Accounts","newspack"),description:l("Let search engines know which social profiles are associated to your site","newspack")}),(0,i.createElement)(a.rj,{columns:1,gutter:64},(0,i.createElement)(a.rj,{columns:3,rowGap:16},(0,i.createElement)(a.w4,{label:l("Facebook Page","newspack"),onChange:t=>e({urls:{...o,facebook:t}}),value:p,placeholder:l("https://facebook.com/page","newspack")}),(0,i.createElement)(a.w4,{label:l("Twitter","newspack"),onChange:t=>e({urls:{...o,twitter:t}}),value:_,placeholder:l("username","newspack")}),(0,i.createElement)(a.w4,{label:"Instagram",onChange:t=>e({urls:{...o,instagram:t}}),value:y,placeholder:l("https://instagram.com/user","newspack")}),(0,i.createElement)(a.w4,{label:"LinkedIn",onChange:t=>e({urls:{...o,linkedin:t}}),value:f,placeholder:l("https://linkedin.com/user","newspack")}),(0,i.createElement)(a.w4,{label:"YouTube",onChange:t=>e({urls:{...o,youtube:t}}),value:h,placeholder:l("https://youtube.com/c/channel","newspack")}),(0,i.createElement)(a.w4,{label:"Pinterest",onChange:t=>e({urls:{...o,pinterest:t}}),value:d,placeholder:l("https://pinterest.com/user","newspack")})),(0,i.createElement)(a.fM,{isMedium:!0,title:l("Under construction","newspack"),description:l("Discourage search engines from indexing this site.","newspack"),toggleChecked:n,toggleOnChange:t=>e({underConstruction:t})})))}}p.defaultProps={data:{}};const f=(0,a.a4)(p);var _=r(4237),h=r.n(_),y=r(8929),d=r.n(y),v=r(1865),w=r.n(v);const b=s.__,{HashRouter:g,Redirect:x,Route:m,Switch:O}=c.Z;class j extends i.Component{constructor(){super(...arguments),(0,o.Z)(this,"state",{underConstruction:!1,urls:{facebook:"",twitter:"",instagram:"",youtube:"",linkedin:"",pinterest:""},verification:{bing:"",google:""}}),(0,o.Z)(this,"onWizardReady",(()=>this.fetch())),(0,o.Z)(this,"sanitizeResponse",(t=>h()(t,(t=>d()(t)))))}fetch(){const{setError:t,wizardApiFetch:e}=this.props;return e({path:"/newspack/v1/wizard/newspack-seo-wizard/settings"}).then((t=>this.setState(this.sanitizeResponse(t)))).catch((e=>t(e)))}update(){const{setError:t,wizardApiFetch:e}=this.props;return e({path:"/newspack/v1/wizard/newspack-seo-wizard/settings",method:"POST",data:h()(this.state,(t=>w()(t))),quiet:!0}).then((t=>this.setState(this.sanitizeResponse(t)))).catch((e=>t(e)))}render(){const{pluginRequirements:t}=this.props,e=b("SEO","newspack"),r=b("Configure basic SEO settings","newspack"),o=b("Save Settings","newspack"),s=b("Advanced Settings","newspack"),a={data:this.state,headerText:e,subHeaderText:r};return(0,i.createElement)(i.Fragment,null,(0,i.createElement)(g,{hashType:"slash"},(0,i.createElement)(O,null,t,(0,i.createElement)(m,{exact:!0,path:"/",render:()=>(0,i.createElement)(f,(0,n.Z)({},a,{buttonAction:()=>this.update(),buttonText:o,onChange:t=>this.setState(t),secondaryButtonText:s}))}),(0,i.createElement)(x,{to:"/"}))))}}(0,i.render)((0,i.createElement)((0,a.uF)(j,["wordpress-seo","jetpack"])),document.getElementById("newspack-seo-wizard"))},372:(t,e,r)=>{"use strict";var n=r(6060);t.exports=function(t){if("function"!=typeof t)return!1;if(!hasOwnProperty.call(t,"length"))return!1;try{if("number"!=typeof t.length)return!1;if("function"!=typeof t.call)return!1;if("function"!=typeof t.apply)return!1}catch(t){return!1}return!n(t)}},3875:(t,e,r)=>{"use strict";var n=r(5618),o=r(3940),i=r(4281),s=r(7088),a=function(t,e){return t.replace("%v",s(e))};t.exports=function(t,e,r){if(!o(r))throw new TypeError(a(e,t));if(!n(t)){if("default"in r)return r.default;if(r.isOptional)return null}var s=i(r.errorMessage);throw n(s)||(s=e),new TypeError(a(s,t))}},8237:t=>{"use strict";t.exports=function(t){try{return t.toString()}catch(e){try{return String(t)}catch(t){return null}}}},7088:(t,e,r)=>{"use strict";var n=r(8237),o=/[\n\r\u2028\u2029]/g;t.exports=function(t){var e=n(t);return null===e?"<Non-coercible to string value>":(e.length>100&&(e=e.slice(0,99)+"…"),e=e.replace(o,(function(t){switch(t){case"\n":return"\\n";case"\r":return"\\r";case"\u2028":return"\\u2028";case"\u2029":return"\\u2029";default:throw new Error("Unexpected character")}})))}},3940:(t,e,r)=>{"use strict";var n=r(5618),o={object:!0,function:!0,undefined:!0};t.exports=function(t){return!!n(t)&&hasOwnProperty.call(o,typeof t)}},1516:(t,e,r)=>{"use strict";var n=r(3875),o=r(7205);t.exports=function(t){return o(t)?t:n(t,"%v is not a plain function",arguments[1])}},7205:(t,e,r)=>{"use strict";var n=r(372),o=/^\s*class[\s{/}]/,i=Function.prototype.toString;t.exports=function(t){return!!n(t)&&!o.test(i.call(t))}},6060:(t,e,r)=>{"use strict";var n=r(3940);t.exports=function(t){if(!n(t))return!1;try{return!!t.constructor&&t.constructor.prototype===t}catch(t){return!1}}},4281:(t,e,r)=>{"use strict";var n=r(5618),o=r(3940),i=Object.prototype.toString;t.exports=function(t){if(!n(t))return null;if(o(t)){var e=t.toString;if("function"!=typeof e)return null;if(e===i)return null}try{return""+t}catch(t){return null}}},5536:(t,e,r)=>{"use strict";var n=r(3875),o=r(5618);t.exports=function(t){return o(t)?t:n(t,"Cannot use %v",arguments[1])}},5618:t=>{"use strict";t.exports=function(t){return null!=t}},9196:t=>{"use strict";t.exports=window.React},2819:t=>{"use strict";t.exports=window.lodash},6292:t=>{"use strict";t.exports=window.moment},6989:t=>{"use strict";t.exports=window.wp.apiFetch},5609:t=>{"use strict";t.exports=window.wp.components},9818:t=>{"use strict";t.exports=window.wp.data},9307:t=>{"use strict";t.exports=window.wp.element},2694:t=>{"use strict";t.exports=window.wp.hooks},2629:t=>{"use strict";t.exports=window.wp.htmlEntities},5736:t=>{"use strict";t.exports=window.wp.i18n},9630:t=>{"use strict";t.exports=window.wp.keycodes},444:t=>{"use strict";t.exports=window.wp.primitives},6483:t=>{"use strict";t.exports=window.wp.url}},r={};function n(t){var o=r[t];if(void 0!==o)return o.exports;var i=r[t]={id:t,loaded:!1,exports:{}};return e[t].call(i.exports,i,i.exports,n),i.loaded=!0,i.exports}n.m=e,t=[],n.O=(e,r,o,i)=>{if(!r){var s=1/0;for(l=0;l<t.length;l++){r=t[l][0],o=t[l][1],i=t[l][2];for(var a=!0,c=0;c<r.length;c++)(!1&i||s>=i)&&Object.keys(n.O).every((t=>n.O[t](r[c])))?r.splice(c--,1):(a=!1,i<s&&(s=i));if(a){t.splice(l--,1);var u=o();void 0!==u&&(e=u)}}return e}i=i||0;for(var l=t.length;l>0&&t[l-1][2]>i;l--)t[l]=t[l-1];t[l]=[r,o,i]},n.n=t=>{var e=t&&t.__esModule?()=>t.default:()=>t;return n.d(e,{a:e}),e},n.d=(t,e)=>{for(var r in e)n.o(e,r)&&!n.o(t,r)&&Object.defineProperty(t,r,{enumerable:!0,get:e[r]})},n.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(t){if("object"==typeof window)return window}}(),n.o=(t,e)=>Object.prototype.hasOwnProperty.call(t,e),n.r=t=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n.nmd=t=>(t.paths=[],t.children||(t.children=[]),t),n.j=485,(()=>{var t;n.g.importScripts&&(t=n.g.location+"");var e=n.g.document;if(!t&&e&&(e.currentScript&&(t=e.currentScript.src),!t)){var r=e.getElementsByTagName("script");r.length&&(t=r[r.length-1].src)}if(!t)throw new Error("Automatic publicPath is not supported in this browser");t=t.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/"),n.p=t})(),(()=>{var t={485:0};n.O.j=e=>0===t[e];var e=(e,r)=>{var o,i,s=r[0],a=r[1],c=r[2],u=0;if(s.some((e=>0!==t[e]))){for(o in a)n.o(a,o)&&(n.m[o]=a[o]);if(c)var l=c(n)}for(e&&e(r);u<s.length;u++)i=s[u],n.o(t,i)&&t[i]&&t[i][0](),t[s[u]]=0;return n.O(l)},r=self.webpackChunkwebpack=self.webpackChunkwebpack||[];r.forEach(e.bind(null,0)),r.push=e.bind(null,r.push.bind(r))})();var o=n.O(void 0,[351],(()=>n(7889)));o=n.O(o);var i=window;for(var s in o)i[s]=o[s];o.__esModule&&Object.defineProperty(i,"__esModule",{value:!0})})();