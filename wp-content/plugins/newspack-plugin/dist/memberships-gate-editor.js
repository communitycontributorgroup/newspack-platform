!function(){"use strict";var e,t={8516:function(e,t,n){n.r(t);var a=n(9307),l=n(5736),r=n(9818),o=n(5609),i=window.wp.editPost,s=window.wp.plugins,c=n(7462),p=n(4184),u=n.n(p);const m=l.__;function w(e){let{allowedPositions:t,value:n,label:l,help:r,onChange:i,size:s,...p}=e;const w="full-width"===s?[{value:"top",label:m("Top","newspack")},{value:"center",label:m("Center","newspack")},{value:"bottom",label:m("Bottom","newspack")}]:[{value:"top_left",label:m("Top Left","newspack")},{value:"top",label:m("Top Center","newspack")},{value:"top_right",label:m("Top Right","newspack")},{value:"center_left",label:m("Center Left","newspack")},{value:"center",label:m("Center","newspack")},{value:"center_right",label:m("Center Right","newspack")},{value:"bottom_left",label:m("Bottom Left","newspack")},{value:"bottom",label:m("Bottom Center","newspack")},{value:"bottom_right",label:m("Bottom Right","newspack")}];return(0,a.createElement)("div",{className:u()("newspack-position-placement-control","size-"+s)},(0,a.createElement)("p",{className:"components-base-control__label"},l),(0,a.createElement)(o.ButtonGroup,(0,c.Z)({"aria-label":m("Select Position","newspack")},p),w.map(((e,l)=>(0,a.createElement)("div",{key:`newspack-position-placement-item-${l}`,className:e.value===n?"is-selected":null},(0,a.createElement)(o.Button,{isSmall:!0,title:e.label,"aria-label":e.label,isPrimary:e.value===n,onClick:()=>{i(e.value)},disabled:t?.length&&!t.includes(e.value)}))))),(0,a.createElement)("p",{className:"components-base-control__help"},r))}const g=l.__,b=[{value:"inline",label:g("Inline","newspack")},{value:"overlay",label:g("Overlay","newspack")}],d={center:g("center","newspack"),bottom:g("bottom","newspack")},h=[{value:"x-small",label:g("Extra Small","newspack")},{value:"small",label:g("Small","newspack")},{value:"medium",label:g("Medium","newspack")},{value:"large",label:g("Large","newspack")},{value:"full-width",label:g("Full Width","newspack")}];(0,s.registerPlugin)("newspack-memberships-gate",{render:function(){const{meta:e}=(0,r.useSelect)((e=>{const{getEditedPostAttribute:t}=e("core/editor");return{meta:t("meta")}})),{editPost:t}=(0,r.useDispatch)("core/editor");return(0,a.useEffect)((()=>{const t=document.querySelector(".editor-styles-wrapper");t&&("overlay"===e.style?t.setAttribute("data-overlay-size",e.overlay_size):t.removeAttribute("data-overlay-size"))}),[e.style,e.overlay_size]),(0,a.createElement)(a.Fragment,null,newspack_memberships_gate.has_campaigns&&(0,a.createElement)(i.PluginPostStatusInfo,null,(0,a.createElement)("p",null,g("Newspack Campaign prompts won't be displayed when rendering gated content.","newspack"))),(0,a.createElement)(i.PluginDocumentSettingPanel,{name:"memberships-gate-styles-panel",title:g("Styles","newspack")},(0,a.createElement)("div",{className:"newspack-memberships-gate-style-selector"},b.map((n=>(0,a.createElement)(o.Button,{key:n.value,variant:e.style===n.value?"primary":"secondary",isPressed:e.style===n.value,onClick:()=>t({meta:{style:n.value}}),"aria-current":e.style===n.value},n.label)))),"inline"===e.style&&(0,a.createElement)(o.CheckboxControl,{label:g("Apply fade to last paragraph","newspack"),checked:e.inline_fade,onChange:e=>t({meta:{inline_fade:e}}),help:g("Whether to apply a gradient fade effect before rendering the gate.","newspack")}),"overlay"===e.style&&(0,a.createElement)(a.Fragment,null,(0,a.createElement)(o.SelectControl,{label:g("Size","newspack"),value:e.overlay_size,options:h,onChange:e=>t({meta:{overlay_size:e}})}),(0,a.createElement)(w,{label:g("Position","newspack"),value:e.overlay_position,size:e.overlay_size,allowedPositions:["bottom","center"],onChange:e=>t({meta:{overlay_position:e}}),help:(0,l.sprintf)(g("The gate will be displayed at the %s of the screen.","newspack"),d[e.overlay_position])}))),(0,a.createElement)(i.PluginDocumentSettingPanel,{name:"memberships-gate-settings-panel",title:g("Settings","newspack")},(0,a.createElement)(o.TextControl,{type:"number",min:"0",value:e.visible_paragraphs,label:g("Default paragraph count","newspack"),onChange:e=>t({meta:{visible_paragraphs:e}}),help:g("Number of paragraphs that readers can see above the content gate.","newspack")}),(0,a.createElement)("hr",null),(0,a.createElement)(o.CheckboxControl,{label:g("Use “More” tag to manually place content gate","newspack"),checked:e.use_more_tag,onChange:e=>t({meta:{use_more_tag:e}}),help:g("Override the default paragraph count on pages where a “More” block has been placed.","newspack")})),(0,a.createElement)(i.PluginDocumentSettingPanel,{name:"memberships-gate-metering-panel",title:g("Metering","newspack")},(0,a.createElement)(o.CheckboxControl,{label:g("Enable metering","newspack"),checked:e.metering,onChange:e=>t({meta:{metering:e}}),help:g("Implement metering to configure access to restricted content before showing the gate.","newspack")}),e.metering&&(0,a.createElement)(a.Fragment,null,(0,a.createElement)(o.TextControl,{type:"number",min:"0",value:e.metering_anonymous_count,label:g("Available views for anonymous readers","newspack"),onChange:e=>t({meta:{metering_anonymous_count:e}}),help:g("Number of times an anonymous reader can view gated content. If set to 0, anonymous readers will always render the gate.","newspack")}),(0,a.createElement)(o.TextControl,{type:"number",min:"0",value:e.metering_registered_count,label:g("Available views for registered readers","newspack"),onChange:e=>t({meta:{metering_registered_count:e}}),help:g("Number of times a registered reader can view gated content. If set to 0, registered readers without membership plan will always render the gate.","newspack")}),(0,a.createElement)(o.SelectControl,{label:g("Time period","newspack"),value:e.metering_period,options:[{value:"day",label:g("Day","newspack")},{value:"week",label:g("Week","newspack")},{value:"month",label:g("Month","newspack")}],onChange:e=>t({meta:{metering_period:e}}),help:g("The time period during which the metering views will be counted. For example, if the metering period is set to a week, the metering views will be reset every week.","newspack")}))))},icon:null})},5609:function(e){e.exports=window.wp.components},9818:function(e){e.exports=window.wp.data},9307:function(e){e.exports=window.wp.element},5736:function(e){e.exports=window.wp.i18n}},n={};function a(e){var l=n[e];if(void 0!==l)return l.exports;var r=n[e]={exports:{}};return t[e].call(r.exports,r,r.exports,a),r.exports}a.m=t,e=[],a.O=function(t,n,l,r){if(!n){var o=1/0;for(p=0;p<e.length;p++){n=e[p][0],l=e[p][1],r=e[p][2];for(var i=!0,s=0;s<n.length;s++)(!1&r||o>=r)&&Object.keys(a.O).every((function(e){return a.O[e](n[s])}))?n.splice(s--,1):(i=!1,r<o&&(o=r));if(i){e.splice(p--,1);var c=l();void 0!==c&&(t=c)}}return t}r=r||0;for(var p=e.length;p>0&&e[p-1][2]>r;p--)e[p]=e[p-1];e[p]=[n,l,r]},a.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return a.d(t,{a:t}),t},a.d=function(e,t){for(var n in t)a.o(t,n)&&!a.o(e,n)&&Object.defineProperty(e,n,{enumerable:!0,get:t[n]})},a.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(e){if("object"==typeof window)return window}}(),a.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},a.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},a.j=281,function(){var e;a.g.importScripts&&(e=a.g.location+"");var t=a.g.document;if(!e&&t&&(t.currentScript&&(e=t.currentScript.src),!e)){var n=t.getElementsByTagName("script");n.length&&(e=n[n.length-1].src)}if(!e)throw new Error("Automatic publicPath is not supported in this browser");e=e.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/"),a.p=e}(),function(){var e={281:0};a.O.j=function(t){return 0===e[t]};var t=function(t,n){var l,r,o=n[0],i=n[1],s=n[2],c=0;if(o.some((function(t){return 0!==e[t]}))){for(l in i)a.o(i,l)&&(a.m[l]=i[l]);if(s)var p=s(a)}for(t&&t(n);c<o.length;c++)r=o[c],a.o(e,r)&&e[r]&&e[r][0](),e[o[c]]=0;return a.O(p)},n=self.webpackChunkwebpack=self.webpackChunkwebpack||[];n.forEach(t.bind(null,0)),n.push=t.bind(null,n.push.bind(n))}();var l=a.O(void 0,[351],(function(){return a(8516)}));l=a.O(l);var r=window;for(var o in l)r[o]=l[o];l.__esModule&&Object.defineProperty(r,"__esModule",{value:!0})}();