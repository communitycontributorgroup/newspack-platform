(()=>{var e={7597:(e,t)=>{"use strict";function r(e){return r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},r(e)}var n,o="https://js.stripe.com/v3",a=/^https:\/\/js\.stripe\.com\/v3\/?(\?.*)?$/,i="loadStripe.setLoadParameters was called but an existing Stripe.js script already exists in the document; existing script parameters will be used",u=null,c=function(e){return null!==u||(u=new Promise((function(t,r){if("undefined"!=typeof window)if(window.Stripe&&e&&console.warn(i),window.Stripe)t(window.Stripe);else try{var n=function(){for(var e=document.querySelectorAll('script[src^="'.concat(o,'"]')),t=0;t<e.length;t++){var r=e[t];if(a.test(r.src))return r}return null}();n&&e?console.warn(i):n||(n=function(e){var t=e&&!e.advancedFraudSignals?"?advancedFraudSignals=false":"",r=document.createElement("script");r.src="".concat(o).concat(t);var n=document.head||document.body;if(!n)throw new Error("Expected document.body not to be null. Stripe.js requires a <body> element.");return n.appendChild(r),r}(e)),n.addEventListener("load",(function(){window.Stripe?t(window.Stripe):r(new Error("Stripe.js not available"))})),n.addEventListener("error",(function(){r(new Error("Failed to load Stripe.js"))}))}catch(u){return void r(u)}else t(null)}))),u},s=function(e,t,r){if(null===e)return null;var n=e.apply(void 0,t);return function(e,t){e&&e._registerWrapper&&e._registerWrapper({name:"stripe-js",version:"1.38.1",startTime:t})}(n,r),n},l=!1,p=function(){for(var e=arguments.length,t=new Array(e),r=0;r<e;r++)t[r]=arguments[r];l=!0;var o=Date.now();return c(n).then((function(e){return s(e,t,o)}))};p.setLoadParameters=function(e){if(l)throw new Error("You cannot change load parameters after calling loadStripe");n=function(e){var t="invalid load parameters; expected object of shape\n\n    {advancedFraudSignals: boolean}\n\nbut received\n\n    ".concat(JSON.stringify(e),"\n");if(null===e||"object"!==r(e))throw new Error(t);if(1===Object.keys(e).length&&"boolean"==typeof e.advancedFraudSignals)return e;throw new Error(t)}(e)},t.loadStripe=p},7894:(e,t,r)=>{e.exports=r(7597)},5666:e=>{var t=function(e){"use strict";var t,r=Object.prototype,n=r.hasOwnProperty,o="function"==typeof Symbol?Symbol:{},a=o.iterator||"@@iterator",i=o.asyncIterator||"@@asyncIterator",u=o.toStringTag||"@@toStringTag";function c(e,t,r){return Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}),e[t]}try{c({},"")}catch(R){c=function(e,t,r){return e[t]=r}}function s(e,t,r,n){var o=t&&t.prototype instanceof h?t:h,a=Object.create(o.prototype),i=new O(n||[]);return a._invoke=function(e,t,r){var n=p;return function(o,a){if(n===d)throw new Error("Generator is already running");if(n===m){if("throw"===o)throw a;return q()}for(r.method=o,r.arg=a;;){var i=r.delegate;if(i){var u=E(i,r);if(u){if(u===y)continue;return u}}if("next"===r.method)r.sent=r._sent=r.arg;else if("throw"===r.method){if(n===p)throw n=m,r.arg;r.dispatchException(r.arg)}else"return"===r.method&&r.abrupt("return",r.arg);n=d;var c=l(e,t,r);if("normal"===c.type){if(n=r.done?m:f,c.arg===y)continue;return{value:c.arg,done:r.done}}"throw"===c.type&&(n=m,r.method="throw",r.arg=c.arg)}}}(e,r,i),a}function l(e,t,r){try{return{type:"normal",arg:e.call(t,r)}}catch(R){return{type:"throw",arg:R}}}e.wrap=s;var p="suspendedStart",f="suspendedYield",d="executing",m="completed",y={};function h(){}function v(){}function b(){}var g={};c(g,a,(function(){return this}));var w=Object.getPrototypeOf,x=w&&w(w(P([])));x&&x!==r&&n.call(x,a)&&(g=x);var k=b.prototype=h.prototype=Object.create(g);function S(e){["next","throw","return"].forEach((function(t){c(e,t,(function(e){return this._invoke(t,e)}))}))}function _(e,t){function r(o,a,i,u){var c=l(e[o],e,a);if("throw"!==c.type){var s=c.arg,p=s.value;return p&&"object"==typeof p&&n.call(p,"__await")?t.resolve(p.__await).then((function(e){r("next",e,i,u)}),(function(e){r("throw",e,i,u)})):t.resolve(p).then((function(e){s.value=e,i(s)}),(function(e){return r("throw",e,i,u)}))}u(c.arg)}var o;this._invoke=function(e,n){function a(){return new t((function(t,o){r(e,n,t,o)}))}return o=o?o.then(a,a):a()}}function E(e,r){var n=e.iterator[r.method];if(n===t){if(r.delegate=null,"throw"===r.method){if(e.iterator.return&&(r.method="return",r.arg=t,E(e,r),"throw"===r.method))return y;r.method="throw",r.arg=new TypeError("The iterator does not provide a 'throw' method")}return y}var o=l(n,e.iterator,r.arg);if("throw"===o.type)return r.method="throw",r.arg=o.arg,r.delegate=null,y;var a=o.arg;return a?a.done?(r[e.resultName]=a.value,r.next=e.nextLoc,"return"!==r.method&&(r.method="next",r.arg=t),r.delegate=null,y):a:(r.method="throw",r.arg=new TypeError("iterator result is not an object"),r.delegate=null,y)}function j(e){var t={tryLoc:e[0]};1 in e&&(t.catchLoc=e[1]),2 in e&&(t.finallyLoc=e[2],t.afterLoc=e[3]),this.tryEntries.push(t)}function L(e){var t=e.completion||{};t.type="normal",delete t.arg,e.completion=t}function O(e){this.tryEntries=[{tryLoc:"root"}],e.forEach(j,this),this.reset(!0)}function P(e){if(e){var r=e[a];if(r)return r.call(e);if("function"==typeof e.next)return e;if(!isNaN(e.length)){var o=-1,i=function r(){for(;++o<e.length;)if(n.call(e,o))return r.value=e[o],r.done=!1,r;return r.value=t,r.done=!0,r};return i.next=i}}return{next:q}}function q(){return{value:t,done:!0}}return v.prototype=b,c(k,"constructor",b),c(b,"constructor",v),v.displayName=c(b,u,"GeneratorFunction"),e.isGeneratorFunction=function(e){var t="function"==typeof e&&e.constructor;return!!t&&(t===v||"GeneratorFunction"===(t.displayName||t.name))},e.mark=function(e){return Object.setPrototypeOf?Object.setPrototypeOf(e,b):(e.__proto__=b,c(e,u,"GeneratorFunction")),e.prototype=Object.create(k),e},e.awrap=function(e){return{__await:e}},S(_.prototype),c(_.prototype,i,(function(){return this})),e.AsyncIterator=_,e.async=function(t,r,n,o,a){void 0===a&&(a=Promise);var i=new _(s(t,r,n,o),a);return e.isGeneratorFunction(r)?i:i.next().then((function(e){return e.done?e.value:i.next()}))},S(k),c(k,u,"Generator"),c(k,a,(function(){return this})),c(k,"toString",(function(){return"[object Generator]"})),e.keys=function(e){var t=[];for(var r in e)t.push(r);return t.reverse(),function r(){for(;t.length;){var n=t.pop();if(n in e)return r.value=n,r.done=!1,r}return r.done=!0,r}},e.values=P,O.prototype={constructor:O,reset:function(e){if(this.prev=0,this.next=0,this.sent=this._sent=t,this.done=!1,this.delegate=null,this.method="next",this.arg=t,this.tryEntries.forEach(L),!e)for(var r in this)"t"===r.charAt(0)&&n.call(this,r)&&!isNaN(+r.slice(1))&&(this[r]=t)},stop:function(){this.done=!0;var e=this.tryEntries[0].completion;if("throw"===e.type)throw e.arg;return this.rval},dispatchException:function(e){if(this.done)throw e;var r=this;function o(n,o){return u.type="throw",u.arg=e,r.next=n,o&&(r.method="next",r.arg=t),!!o}for(var a=this.tryEntries.length-1;a>=0;--a){var i=this.tryEntries[a],u=i.completion;if("root"===i.tryLoc)return o("end");if(i.tryLoc<=this.prev){var c=n.call(i,"catchLoc"),s=n.call(i,"finallyLoc");if(c&&s){if(this.prev<i.catchLoc)return o(i.catchLoc,!0);if(this.prev<i.finallyLoc)return o(i.finallyLoc)}else if(c){if(this.prev<i.catchLoc)return o(i.catchLoc,!0)}else{if(!s)throw new Error("try statement without catch or finally");if(this.prev<i.finallyLoc)return o(i.finallyLoc)}}}},abrupt:function(e,t){for(var r=this.tryEntries.length-1;r>=0;--r){var o=this.tryEntries[r];if(o.tryLoc<=this.prev&&n.call(o,"finallyLoc")&&this.prev<o.finallyLoc){var a=o;break}}a&&("break"===e||"continue"===e)&&a.tryLoc<=t&&t<=a.finallyLoc&&(a=null);var i=a?a.completion:{};return i.type=e,i.arg=t,a?(this.method="next",this.next=a.finallyLoc,y):this.complete(i)},complete:function(e,t){if("throw"===e.type)throw e.arg;return"break"===e.type||"continue"===e.type?this.next=e.arg:"return"===e.type?(this.rval=this.arg=e.arg,this.method="return",this.next="end"):"normal"===e.type&&t&&(this.next=t),y},finish:function(e){for(var t=this.tryEntries.length-1;t>=0;--t){var r=this.tryEntries[t];if(r.finallyLoc===e)return this.complete(r.completion,r.afterLoc),L(r),y}},catch:function(e){for(var t=this.tryEntries.length-1;t>=0;--t){var r=this.tryEntries[t];if(r.tryLoc===e){var n=r.completion;if("throw"===n.type){var o=n.arg;L(r)}return o}}throw new Error("illegal catch attempt")},delegateYield:function(e,r,n){return this.delegate={iterator:P(e),resultName:r,nextLoc:n},"next"===this.method&&(this.arg=t),y}},e}(e.exports);try{regeneratorRuntime=t}catch(r){"object"==typeof globalThis?globalThis.regeneratorRuntime=t:Function("r","regeneratorRuntime = r")(t)}}},t={};function r(n){var o=t[n];if(void 0!==o)return o.exports;var a=t[n]={exports:{}};return e[n](a,a.exports,r),a.exports}r.d=(e,t)=>{for(var n in t)r.o(t,n)&&!r.o(e,n)&&Object.defineProperty(e,n,{enumerable:!0,get:t[n]})},r.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),r.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})};var n={};(()=>{"use strict";function e(e,t,r){return t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}function t(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function o(r){for(var n=1;n<arguments.length;n++){var o=null!=arguments[n]?arguments[n]:{};n%2?t(Object(o),!0).forEach((function(t){e(r,t,o[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(r,Object.getOwnPropertyDescriptors(o)):t(Object(o)).forEach((function(e){Object.defineProperty(r,e,Object.getOwnPropertyDescriptor(o,e))}))}return r}function a(e,t,r,n,o,a,i){try{var u=e[a](i),c=u.value}catch(s){return void r(s)}u.done?t(c):Promise.resolve(c).then(n,o)}function i(e){return function(){var t=this,r=arguments;return new Promise((function(n,o){var i=e.apply(t,r);function u(e){a(i,n,o,u,c,"next",e)}function c(e){a(i,n,o,u,c,"throw",e)}u(void 0)}))}}function u(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,n=new Array(t);r<t;r++)n[r]=e[r];return n}function c(e,t){if(e){if("string"==typeof e)return u(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);return"Object"===r&&e.constructor&&(r=e.constructor.name),"Map"===r||"Set"===r?Array.from(e):"Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r)?u(e,t):void 0}}function s(e){return function(e){if(Array.isArray(e))return u(e)}(e)||function(e){if("undefined"!=typeof Symbol&&null!=e[Symbol.iterator]||null!=e["@@iterator"])return Array.from(e)}(e)||c(e)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}r.r(n),r.d(n,{processStreamlinedElements:()=>S});const l=window.wp.i18n;var p=r(7894);r(5666);function f(e,t){return function(e){if(Array.isArray(e))return e}(e)||function(e,t){var r=null==e?null:"undefined"!=typeof Symbol&&e[Symbol.iterator]||e["@@iterator"];if(null!=r){var n,o,a=[],_n=!0,i=!1;try{for(r=r.call(e);!(_n=(n=r.next()).done)&&(a.push(n.value),!t||a.length!==t);_n=!0);}catch(u){i=!0,o=u}finally{try{_n||null==r.return||r.return()}finally{if(i)throw o}}return a}}(e,t)||c(e,t)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}var __=l.__,d=function(e,t){var r,n={};r=e.email,/\S+@\S+/.test(r)||(n.email=__("Email address is invalid.","newspack-blocks"));var o=t.minimumDonation;return parseFloat(e.amount)<o&&(n.amount=(0,l.sprintf)(__("Amount must be at least %d.","newspack-blocks"),o)),0===e.full_name.length&&(n.amount=__("Full name should be provided.","newspack-blocks")),n},m=function(e,t){var r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"error";t.innerHTML="",e.forEach((function(e){if(e){var n=document.createElement("div");n.classList.add("type-".concat(r)),n.innerHTML=e,t.appendChild(n)}})),"success"===r&&t.parentElement&&t.parentElement.replaceWith(t)},y=function(e){var t=e.getAttribute("data-settings");if(!t)return{};var r=f(JSON.parse(t),12),n=r[0],o=r[1],a=r[2],i=r[3],u=r[4],c=r[5],s=r[6],l=r[7],p=r[8],d=r[9],m=r[10],y=r[11];return{currency:n.toLowerCase(),currencySymbol:o,siteName:a,isCurrencyZeroDecimal:i,countryCode:u,frequencies:c,feeMultiplier:parseFloat(s),feeStatic:parseFloat(l),stripePublishableKey:p,paymentRequestType:d,captchaSiteKey:m,minimumDonation:parseFloat(y)}},h=function(e){var t=Object.fromEntries(new FormData(e)),r="donation_value_".concat(t.donation_frequency);return t.amount=t[r],"other"===t.amount&&(t.amount=t["".concat(r,"_other")]),t.amount||(t.amount=t["".concat(r,"_untiered")]),t.cid&&"string"==typeof t.cid&&0===t.cid.indexOf("CLIENT_ID")&&(t.cid=document.cookie.split("; ").reduce((function(e,t){var r=t.split("=");return e[r[0]]=r[1],e}),{})["newspack-cid"]),t},v=function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{convertToSubunit:!1},r=t.convertToSubunit,n=y(e),o=h(e),a=o.amount,i=void 0===a?"0":a,u=o.agree_to_pay_fees,c=function(e){return r?e*(n.isCurrencyZeroDecimal?1:100):e},s=0;return u&&(s=c(g(e)||0)),c(parseFloat(i))+s},b=function(e){var t=y(e),r=h(e).donation_frequency,n=t.frequencies[r];return{label:"".concat(t.siteName," (").concat(n,")"),amount:v(e,{convertToSubunit:!0})}},g=function(e){var t=y(e),r=t.feeMultiplier,n=t.feeStatic;if(void 0===r||void 0===n||isNaN(r)||isNaN(n))return 0;var o=h(e).amount;return function(e,t,r){return parseFloat(((e+r)/(100-t)*100-e).toFixed(2))}(parseFloat(o),r,n)},w=function(){var e=i(regeneratorRuntime.mark((function e(t,r){var n,o=arguments;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return n=o.length>2&&void 0!==o[2]?o[2]:"POST",e.abrupt("return",fetch("/wp-json/newspack-blocks/v1".concat(t),{method:n,headers:{"Content-Type":"application/json"},body:JSON.stringify(r)}).then(function(){var e=i(regeneratorRuntime.mark((function e(t){var r;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,t.json();case 2:if(r=e.sent,!t.ok){e.next=5;break}return e.abrupt("return",r);case 5:return e.abrupt("return",{error:r});case 6:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}()));case 2:case"end":return e.stop()}}),e)})));return function(_x,t){return e.apply(this,arguments)}}(),x=function(e,t){var r=(0,l.sprintf)(__("Your payment has been processed. Thank you for your contribution! You will receive a confirmation email at %s.","newspack-blocks"),e);m([r],t,"success")},k=l.__,S=function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:document;return s(e.querySelectorAll(".stripe-payment")).forEach(function(){var e=i(regeneratorRuntime.mark((function e(t){var r,n,a,u,c,s,l,f,S,_,E,j,L,O,P,q,R;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(s=!1,l=t.closest("form")){e.next=4;break}return e.abrupt("return");case 4:if(f=t.querySelector(".stripe-payment__messages")){e.next=7;break}return e.abrupt("return");case 7:S=y(l),_=function(){return t.classList.add("stripe-payment--disabled")},(E=function(){return t.classList.remove("stripe-payment--disabled")})(),j=function(){if(!s){var e=t.querySelector(".stripe-payment__inputs.stripe-payment--hidden");e&&(e.classList.remove("stripe-payment--hidden"),s=!0)}},L=function(){var e=i(regeneratorRuntime.mark((function e(t){return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.abrupt("return",new Promise((function(e,r){var n=window.grecaptcha;if(!n)return e("");null!=n&&n.ready&&t||r(k("Error loading the reCaptcha library.","newspack-blocks")),n.ready(i(regeneratorRuntime.mark((function o(){var a;return regeneratorRuntime.wrap((function(o){for(;;)switch(o.prev=o.next){case 0:return o.prev=0,o.next=3,n.execute(t,{action:"submit"});case 3:return a=o.sent,o.abrupt("return",e(a));case 7:o.prev=7,o.t0=o.catch(0),r(o.t0);case 10:case"end":return o.stop()}}),o,null,[[0,7]])}))))})));case 1:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}(),O=function(){var e=i(regeneratorRuntime.mark((function e(t){var n,a,i,c,s,p,d,y,b,g,_,j,O,P,q=arguments;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(a=q.length>1&&void 0!==q[1]?q[1]:{},r){e.next=3;break}return e.abrupt("return");case 3:if(!(i=null==S?void 0:S.captchaSiteKey)){e.next=16;break}return e.prev=5,e.next=8,L(i);case 8:c=e.sent,e.next=16;break;case 11:return e.prev=11,e.t0=e.catch(5),s=e.t0 instanceof Error?e.t0.message:k("Error processing captcha request.","newspack-blocks"),m([s],f),e.abrupt("return",{error:!0});case 16:return p=h(l),d=l.closest("amp-layout.newspack-popup"),y=d&&d.hasAttribute("amp-access")?d.getAttribute("amp-access"):null,b=o({captchaToken:c,tokenData:t,amount:v(l),email:p.email,full_name:p.full_name,frequency:p.donation_frequency,newsletter_opt_in:Boolean(p.newsletter_opt_in),clientId:p.cid,origin:y},a),e.next=22,w("/donate",b);case 22:if(g=e.sent,200===(null===(n=g.data)||void 0===n?void 0:n.status)||!g.message){e.next=26;break}return m([g.message],f),e.abrupt("return",{error:!0});case 26:if(!g.error){e.next=29;break}return m([g.error],f),e.abrupt("return",{error:!0});case 29:if(_=function(e){return m([e],f),E(),{error:!0}},!g.client_secret||!u){e.next=53;break}return e.next=33,r.confirmCardPayment(g.client_secret,{payment_method:u});case 33:if(!(j=e.sent).error){e.next=38;break}return e.abrupt("return",_(j.error.message));case 38:if("succeeded"!==j.paymentIntent.status){e.next=42;break}x(b.email,f),e.next=53;break;case 42:if("requires_action"!==j.paymentIntent.status){e.next=52;break}return e.next=45,r.confirmCardPayment(g.client_secret);case 45:if(O=e.sent,!(P=O.error)){e.next=49;break}return e.abrupt("return",_(P.message));case 49:x(b.email,f),e.next=53;break;case 52:return e.abrupt("return",_(k("Something went wrong with the payment. Please try again later.","newspack-blocks")));case 53:return"success"===g.status&&x(b.email,f),e.abrupt("return",{});case 55:case"end":return e.stop()}}),e,null,[[5,11]])})));return function(t){return e.apply(this,arguments)}}(),P=function(){var e=i(regeneratorRuntime.mark((function e(){var o,s,f,d;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,(0,p.loadStripe)(S.stripePublishableKey);case 2:if(r=e.sent){e.next=5;break}return e.abrupt("return");case 5:if(o=t.querySelector(".stripe-payment__card")){e.next=8;break}return e.abrupt("return");case 8:return s=r.elements(),(n=s.create("card")).mount(o),a=r.paymentRequest({country:S.countryCode,currency:S.currency,total:b(l),requestPayerName:!0,requestPayerEmail:!0}),e.next=14,a.canMakePayment();case 14:if(!e.sent){e.next=28;break}if(a.on("token",function(){var e=i(regeneratorRuntime.mark((function e(t){return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:c=t.token;case 1:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}()),a.on("paymentmethod",function(){var e=i(regeneratorRuntime.mark((function e(t){var r;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return u=t.paymentMethod.id,e.next=3,O(c,{email:t.payerEmail,full_name:t.payerName,payment_method_id:u});case 3:r=e.sent,t.complete(null!=r&&r.error?"fail":"success");case 5:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}()),l.addEventListener("change",(function(){a.update({total:b(l)})})),f=s.create("paymentRequestButton",{paymentRequest:a,style:{paymentRequestButton:{type:S.paymentRequestType,height:"46px"}}}),d=t.querySelector(".stripe-payment__request-button")){e.next=23;break}return e.abrupt("return");case 23:f.mount(d),d.classList.remove("stripe-payment--hidden"),setTimeout((function(){d.classList.remove("stripe-payment__request-button--invisible")}),0),e.next=29;break;case 28:j();case 29:t.classList.remove("stripe-payment--invisible");case 30:case"end":return e.stop()}}),e)})));return function(){return e.apply(this,arguments)}}(),P(),(q=t.querySelector('button[type="submit"]'))&&(q.onclick=function(e){s||(e.preventDefault(),j())}),(R=function(){var e=t.querySelector("#stripe-fees-amount");if(e){var r=Object.fromEntries(new FormData(l)),n=g(l);if(0===n){var o=t.querySelector("#stripe-fees-amount-container");o&&(o.style.display="none")}"string"==typeof r.donation_frequency&&(e.innerHTML="(".concat(S.currencySymbol).concat(n.toFixed(2)," ").concat(S.frequencies[r.donation_frequency].toLowerCase(),")"))}})(),l.addEventListener("change",R),l.addEventListener("submit",function(){var e=i(regeneratorRuntime.mark((function e(t){var o,a,i,c,s,p;return regeneratorRuntime.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(r){e.next=2;break}return e.abrupt("return");case 2:if(t.preventDefault(),_(),m([k("Processing payment…","newspack-blocks")],f,"info"),a=h(l),!((i=Object.values(d(a,S))).length>0)){e.next=11;break}return m(i,f),E(),e.abrupt("return");case 11:return c=function(e){e.message&&i.push(e.message),m(i,f),E()},e.next=14,r.createToken(n);case 14:if(!(s=e.sent).error){e.next=18;break}return c(s.error),e.abrupt("return");case 18:return e.next=20,r.createPaymentMethod({type:"card",card:n,billing_details:{name:a.full_name,email:a.email}});case 20:if(!(p=e.sent).error){e.next=24;break}return c(p.error),e.abrupt("return");case 24:return u={card:n},e.next=27,O(s.token,{payment_method_id:p.paymentMethod.id});case 27:null!==(o=window.newspackReaderActivation)&&void 0!==o&&o.refreshAuthentication&&window.newspackReaderActivation.refreshAuthentication();case 28:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}());case 22:case"end":return e.stop()}}),e)})));return function(_x){return e.apply(this,arguments)}}())};S()})();var o=window;for(var a in n)o[a]=n[a];n.__esModule&&Object.defineProperty(o,"__esModule",{value:!0})})();