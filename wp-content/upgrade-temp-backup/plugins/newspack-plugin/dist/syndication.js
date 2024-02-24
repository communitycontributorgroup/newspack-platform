(()=>{"use strict";var e,t={7685:(e,t,r)=>{r.r(t),r(5674);var n=r(9307),o=r(5736),a=r(264),i=r(9818);const s=o.__,p=()=>{const e=a.en.useWizardData("settings"),{saveWizardSettings:t}=(0,i.useDispatch)(a.en.STORE_NAMESPACE);return(0,n.createElement)(n.Fragment,null,(0,n.createElement)(a.fM,{title:s("RSS Enhancements","newspack"),description:s("Create and manage customized RSS feeds for syndication partners","newspack"),toggleChecked:Boolean(e.module_enabled_rss),toggleOnChange:e=>{t({slug:"newspack-settings-wizard",updatePayload:{path:["module_enabled_rss"],value:e}}).then((()=>{window.location.reload(!0)}))}}),(0,n.createElement)(a.Wy,{plugins:{"publish-to-apple-news":{name:s("Apple News","newspack")},distributor:{name:s("Distributor","newspack")}}}))},l=o.__;(0,n.render)((0,n.createElement)((()=>(0,n.createElement)(a.en,{headerText:l("Syndication","newspack"),subHeaderText:l("Distribute your content across multiple websites","newspack"),sections:[{label:l("Main","newspack"),path:"/",render:p}]}))),document.getElementById("newspack-syndication-wizard"))},9196:e=>{e.exports=window.React},6292:e=>{e.exports=window.moment},6989:e=>{e.exports=window.wp.apiFetch},5609:e=>{e.exports=window.wp.components},9818:e=>{e.exports=window.wp.data},9307:e=>{e.exports=window.wp.element},2694:e=>{e.exports=window.wp.hooks},2629:e=>{e.exports=window.wp.htmlEntities},5736:e=>{e.exports=window.wp.i18n},9630:e=>{e.exports=window.wp.keycodes},444:e=>{e.exports=window.wp.primitives},6483:e=>{e.exports=window.wp.url}},r={};function n(e){var o=r[e];if(void 0!==o)return o.exports;var a=r[e]={id:e,loaded:!1,exports:{}};return t[e].call(a.exports,a,a.exports,n),a.loaded=!0,a.exports}n.m=t,e=[],n.O=(t,r,o,a)=>{if(!r){var i=1/0;for(d=0;d<e.length;d++){for(var[r,o,a]=e[d],s=!0,p=0;p<r.length;p++)(!1&a||i>=a)&&Object.keys(n.O).every((e=>n.O[e](r[p])))?r.splice(p--,1):(s=!1,a<i&&(i=a));if(s){e.splice(d--,1);var l=o();void 0!==l&&(t=l)}}return t}a=a||0;for(var d=e.length;d>0&&e[d-1][2]>a;d--)e[d]=e[d-1];e[d]=[r,o,a]},n.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return n.d(t,{a:t}),t},n.d=(e,t)=>{for(var r in t)n.o(t,r)&&!n.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},n.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(e){if("object"==typeof window)return window}}(),n.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),n.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.nmd=e=>(e.paths=[],e.children||(e.children=[]),e),n.j=57,(()=>{var e;n.g.importScripts&&(e=n.g.location+"");var t=n.g.document;if(!e&&t&&(t.currentScript&&(e=t.currentScript.src),!e)){var r=t.getElementsByTagName("script");r.length&&(e=r[r.length-1].src)}if(!e)throw new Error("Automatic publicPath is not supported in this browser");e=e.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/"),n.p=e})(),(()=>{var e={57:0};n.O.j=t=>0===e[t];var t=(t,r)=>{var o,a,[i,s,p]=r,l=0;if(i.some((t=>0!==e[t]))){for(o in s)n.o(s,o)&&(n.m[o]=s[o]);if(p)var d=p(n)}for(t&&t(r);l<i.length;l++)a=i[l],n.o(e,a)&&e[a]&&e[a][0](),e[i[l]]=0;return n.O(d)},r=globalThis.webpackChunkwebpack=globalThis.webpackChunkwebpack||[];r.forEach(t.bind(null,0)),r.push=t.bind(null,r.push.bind(r))})();var o=n.O(void 0,[351],(()=>n(7685)));o=n.O(o);var a=window;for(var i in o)a[i]=o[i];o.__esModule&&Object.defineProperty(a,"__esModule",{value:!0})})();