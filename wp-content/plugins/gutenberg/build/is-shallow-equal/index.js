window.wp=window.wp||{},window.wp.isShallowEqual=function(r){var e={};function t(n){if(e[n])return e[n].exports;var u=e[n]={i:n,l:!1,exports:{}};return r[n].call(u.exports,u,u.exports,t),u.l=!0,u.exports}return t.m=r,t.c=e,t.d=function(r,e,n){t.o(r,e)||Object.defineProperty(r,e,{enumerable:!0,get:n})},t.r=function(r){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(r,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(r,"__esModule",{value:!0})},t.t=function(r,e){if(1&e&&(r=t(r)),8&e)return r;if(4&e&&"object"==typeof r&&r&&r.__esModule)return r;var n=Object.create(null);if(t.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:r}),2&e&&"string"!=typeof r)for(var u in r)t.d(n,u,function(e){return r[e]}.bind(null,u));return n},t.n=function(r){var e=r&&r.__esModule?function(){return r.default}:function(){return r};return t.d(e,"a",e),e},t.o=function(r,e){return Object.prototype.hasOwnProperty.call(r,e)},t.p="",t(t.s=409)}({409:function(r,e,t){"use strict";function n(r,e){if(r===e)return!0;var t=Object.keys(r),n=Object.keys(e);if(t.length!==n.length)return!1;for(var u=0;u<t.length;){var o=t[u],i=r[o];if(void 0===i&&!e.hasOwnProperty(o)||i!==e[o])return!1;u++}return!0}function u(r,e){if(r===e)return!0;if(r.length!==e.length)return!1;for(var t=0,n=r.length;t<n;t++)if(r[t]!==e[t])return!1;return!0}function o(r,e){if(r&&e){if(r.constructor===Object&&e.constructor===Object)return n(r,e);if(Array.isArray(r)&&Array.isArray(e))return u(r,e)}return r===e}t.r(e),t.d(e,"isShallowEqualObjects",(function(){return n})),t.d(e,"isShallowEqualArrays",(function(){return u})),t.d(e,"default",(function(){return o}))}});