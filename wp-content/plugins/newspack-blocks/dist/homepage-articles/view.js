!function(t,e){for(var r in e)t[r]=e[r]}(window,function(t){var e={};function r(n){if(e[n])return e[n].exports;var o=e[n]={i:n,l:!1,exports:{}};return t[n].call(o.exports,o,o.exports,r),o.l=!0,o.exports}return r.m=t,r.c=e,r.d=function(t,e,n){r.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},r.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},r.t=function(t,e){if(1&e&&(t=r(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(r.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var o in t)r.d(n,o,function(e){return t[e]}.bind(null,o));return n},r.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return r.d(e,"a",e),e},r.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},r.p="",r(r.s=72)}({18:function(t,e,r){var n=r(30),o=r(31),i=r(32);t.exports=function(t){return n(t)||o(t)||i()}},22:function(t,e,r){"object"==typeof window&&window.Jetpack_Block_Assets_Base_Url&&(r.p=window.Jetpack_Block_Assets_Base_Url)},26:function(t,e,r){"use strict";r.r(e);r(22)},30:function(t,e){t.exports=function(t){if(Array.isArray(t)){for(var e=0,r=new Array(t.length);e<t.length;e++)r[e]=t[e];return r}}},31:function(t,e){t.exports=function(t){if(Symbol.iterator in Object(t)||"[object Arguments]"===Object.prototype.toString.call(t))return Array.from(t)}},32:function(t,e){t.exports=function(){throw new TypeError("Invalid attempt to spread non-iterable instance")}},36:function(t,e,r){},72:function(t,e,r){r(26),t.exports=r(73)},73:function(t,e,r){"use strict";r.r(e);var n=r(18),o=r.n(n),i=(r(36),3);function s(t,e){return Object.prototype.hasOwnProperty.call(t,e)}Array.prototype.forEach.call(document.querySelectorAll(".wp-block-newspack-blocks-homepage-articles.has-more-button"),(function(t){var e=t.querySelector("[data-next]");if(!e)return;var r=t.querySelector("[data-posts]"),n=!1,a=!1;e.addEventListener("click",(function(){if(n||a)return!1;n=!0,t.classList.remove("is-error"),t.classList.add("is-loading");var u,c,l=e.getAttribute("data-next")+"&exclude_ids="+(u=document.querySelectorAll(".wp-block-newspack-blocks-homepage-articles [data-post-id]"),c=Array.from(u).map((function(t){return t.getAttribute("data-post-id")})),o()(new Set(c))).join(",");function f(){n=!1,t.classList.remove("is-loading"),t.classList.add("is-error")}!function t(e,r){var n=new XMLHttpRequest;n.onreadystatechange=function(){if(4===n.readyState){if(n.status>=200&&n.status<300){var o=JSON.parse(n.responseText);return e.onSuccess(o)}return r?t(e,r-1):e.onError()}},n.open("GET",e.url),n.send()}({url:encodeURI(l),onSuccess:function(o){if(!function(t){var e=!1;t&&s(t,"items")&&Array.isArray(t.items)&&s(t,"next")&&"string"==typeof t.next&&(e=!0,!t.items.length||s(t.items[0],"html")&&"string"==typeof t.items[0].html||(e=!1));return e}(o))return f();if(o.items.length){var i=o.items.map((function(t){return t.html})).join("");r.insertAdjacentHTML("beforeend",i)}o.next&&e.setAttribute("data-next",o.next);o.items.length&&o.next||(a=!0,t.classList.remove("has-more-button"));n=!1,t.classList.remove("is-loading")},onError:f},i)}))}))}}));