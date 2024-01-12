(()=>{"use strict";var e,t={8516:(e,t,a)=>{a.r(t);var n=a(9307),l=a(5736),r=a(9818),s=a(5609);const o=window.wp.editPost,i=window.wp.plugins;var c=a(7462),p=a(4184),m=a.n(p);const u=l.__;function g({allowedPositions:e,value:t,label:a,help:l,onChange:r,size:o,...i}){const p="full-width"===o?[{value:"top",label:u("Top","newspack")},{value:"center",label:u("Center","newspack")},{value:"bottom",label:u("Bottom","newspack")}]:[{value:"top_left",label:u("Top Left","newspack")},{value:"top",label:u("Top Center","newspack")},{value:"top_right",label:u("Top Right","newspack")},{value:"center_left",label:u("Center Left","newspack")},{value:"center",label:u("Center","newspack")},{value:"center_right",label:u("Center Right","newspack")},{value:"bottom_left",label:u("Bottom Left","newspack")},{value:"bottom",label:u("Bottom Center","newspack")},{value:"bottom_right",label:u("Bottom Right","newspack")}];return(0,n.createElement)("div",{className:m()("newspack-position-placement-control","size-"+o)},(0,n.createElement)("p",{className:"components-base-control__label"},a),(0,n.createElement)(s.ButtonGroup,(0,c.Z)({"aria-label":u("Select Position","newspack")},i),p.map(((a,l)=>(0,n.createElement)("div",{key:`newspack-position-placement-item-${l}`,className:a.value===t?"is-selected":null},(0,n.createElement)(s.Button,{isSmall:!0,title:a.label,"aria-label":a.label,isPrimary:a.value===t,onClick:()=>{r(a.value)},disabled:e?.length&&!e.includes(a.value)}))))),(0,n.createElement)("p",{className:"components-base-control__help"},l))}const w=l.__,d=[{value:"inline",label:w("Inline","newspack")},{value:"overlay",label:w("Overlay","newspack")}],h={center:w("center","newspack"),bottom:w("bottom","newspack")},b=[{value:"x-small",label:w("Extra Small","newspack")},{value:"small",label:w("Small","newspack")},{value:"medium",label:w("Medium","newspack")},{value:"large",label:w("Large","newspack")},{value:"full-width",label:w("Full Width","newspack")}];(0,i.registerPlugin)("newspack-memberships-gate",{render:function(){const{meta:e}=(0,r.useSelect)((e=>{const{getEditedPostAttribute:t}=e("core/editor");return{meta:t("meta")}})),{editPost:t}=(0,r.useDispatch)("core/editor");(0,n.useEffect)((()=>{const t=document.querySelector(".editor-styles-wrapper");t&&("overlay"===e.style?t.setAttribute("data-overlay-size",e.overlay_size):t.removeAttribute("data-overlay-size"))}),[e.style,e.overlay_size]);const{createNotice:a}=(0,r.useDispatch)("core/notices");return(0,n.useEffect)((()=>{Object.keys(newspack_memberships_gate.gate_plans).length&&a("info",(0,l.sprintf)(w("You're currently editing a gate for content restricted by: %s","newspack"),Object.values(newspack_memberships_gate.gate_plans).join(", ")))}),[]),(0,n.createElement)(n.Fragment,null,newspack_memberships_gate.has_campaigns&&(0,n.createElement)(o.PluginPostStatusInfo,null,(0,n.createElement)("p",null,w("Newspack Campaign prompts won't be displayed when rendering gated content.","newspack"))),newspack_memberships_gate.plans.length>1&&(0,n.createElement)(o.PluginDocumentSettingPanel,{name:"memberships-gate-plans",title:w("WooCommerce Memberships","newspack")},Object.keys(newspack_memberships_gate.gate_plans).length?(0,n.createElement)(n.Fragment,null,(0,n.createElement)("p",null,(0,l.sprintf)(w("This gate will be rendered for the following membership plans: %s","newspack"),Object.values(newspack_memberships_gate.gate_plans).join(", "))),(0,n.createElement)("hr",null),(0,n.createElement)("p",{dangerouslySetInnerHTML:{__html:(0,l.sprintf)(w('Edit the <a href="%s">primary gate</a>, or:',"newspack"),newspack_memberships_gate.edit_gate_url)}})):(0,n.createElement)(n.Fragment,null,(0,n.createElement)("p",null,w("This gate will be rendered for all membership plans. Manage custom gates for when the content is locked behind a specific plan:","newspack"))),(0,n.createElement)("ul",null,(()=>{const e=Object.keys(newspack_memberships_gate.gate_plans)||[];return newspack_memberships_gate.plans.filter((t=>!e.includes(t.id.toString())))})().map((e=>(0,n.createElement)("li",{key:e.id},e.name," (",!1!==e.gate_id&&(0,n.createElement)(n.Fragment,null,(0,n.createElement)("strong",null,"publish"===e.gate_status?w("published","newspack"):w("draft","newspack"))," ","-"," "),(0,n.createElement)("a",{href:newspack_memberships_gate.edit_gate_url+"&plan_id="+e.id},e.gate_id?w("edit gate","newspack"):w("create gate","newspack")),")"))))),(0,n.createElement)(o.PluginDocumentSettingPanel,{name:"memberships-gate-styles-panel",title:w("Styles","newspack")},(0,n.createElement)("div",{className:"newspack-memberships-gate-style-selector"},d.map((a=>(0,n.createElement)(s.Button,{key:a.value,variant:e.style===a.value?"primary":"secondary",isPressed:e.style===a.value,onClick:()=>t({meta:{style:a.value}}),"aria-current":e.style===a.value},a.label)))),"inline"===e.style&&(0,n.createElement)(s.CheckboxControl,{label:w("Apply fade to last paragraph","newspack"),checked:e.inline_fade,onChange:e=>t({meta:{inline_fade:e}}),help:w("Whether to apply a gradient fade effect before rendering the gate.","newspack")}),"overlay"===e.style&&(0,n.createElement)(n.Fragment,null,(0,n.createElement)(s.SelectControl,{label:w("Size","newspack"),value:e.overlay_size,options:b,onChange:e=>t({meta:{overlay_size:e}})}),(0,n.createElement)(g,{label:w("Position","newspack"),value:e.overlay_position,size:e.overlay_size,allowedPositions:["bottom","center"],onChange:e=>t({meta:{overlay_position:e}}),help:(0,l.sprintf)(w("The gate will be displayed at the %s of the screen.","newspack"),h[e.overlay_position])}))),(0,n.createElement)(o.PluginDocumentSettingPanel,{name:"memberships-gate-settings-panel",title:w("Settings","newspack")},(0,n.createElement)(s.TextControl,{type:"number",min:"0",value:e.visible_paragraphs,label:w("Default paragraph count","newspack"),onChange:e=>t({meta:{visible_paragraphs:e}}),help:w("Number of paragraphs that readers can see above the content gate.","newspack")}),(0,n.createElement)("hr",null),(0,n.createElement)(s.CheckboxControl,{label:w("Use “More” tag to manually place content gate","newspack"),checked:e.use_more_tag,onChange:e=>t({meta:{use_more_tag:e}}),help:w("Override the default paragraph count on pages where a “More” block has been placed.","newspack")})),(0,n.createElement)(o.PluginDocumentSettingPanel,{name:"memberships-gate-metering-panel",title:w("Metering","newspack")},(0,n.createElement)(s.CheckboxControl,{label:w("Enable metering","newspack"),checked:e.metering,onChange:e=>t({meta:{metering:e}}),help:w("Implement metering to configure access to restricted content before showing the gate.","newspack")}),e.metering&&(0,n.createElement)(n.Fragment,null,(0,n.createElement)(s.TextControl,{type:"number",min:"0",value:e.metering_anonymous_count,label:w("Available views for anonymous readers","newspack"),onChange:e=>t({meta:{metering_anonymous_count:e}}),help:w("Number of times an anonymous reader can view gated content. If set to 0, anonymous readers will always render the gate.","newspack")}),(0,n.createElement)(s.TextControl,{type:"number",min:"0",value:e.metering_registered_count,label:w("Available views for registered readers","newspack"),onChange:e=>t({meta:{metering_registered_count:e}}),help:w("Number of times a registered reader can view gated content. If set to 0, registered readers without membership plan will always render the gate.","newspack")}),(0,n.createElement)(s.SelectControl,{label:w("Time period","newspack"),value:e.metering_period,options:[{value:"day",label:w("Day","newspack")},{value:"week",label:w("Week","newspack")},{value:"month",label:w("Month","newspack")}],onChange:e=>t({meta:{metering_period:e}}),help:w("The time period during which the metering views will be counted. For example, if the metering period is set to a week, the metering views will be reset every week.","newspack")}))))},icon:null})},5609:e=>{e.exports=window.wp.components},9818:e=>{e.exports=window.wp.data},9307:e=>{e.exports=window.wp.element},5736:e=>{e.exports=window.wp.i18n}},a={};function n(e){var l=a[e];if(void 0!==l)return l.exports;var r=a[e]={id:e,loaded:!1,exports:{}};return t[e].call(r.exports,r,r.exports,n),r.loaded=!0,r.exports}n.m=t,e=[],n.O=(t,a,l,r)=>{if(!a){var s=1/0;for(p=0;p<e.length;p++){for(var[a,l,r]=e[p],o=!0,i=0;i<a.length;i++)(!1&r||s>=r)&&Object.keys(n.O).every((e=>n.O[e](a[i])))?a.splice(i--,1):(o=!1,r<s&&(s=r));if(o){e.splice(p--,1);var c=l();void 0!==c&&(t=c)}}return t}r=r||0;for(var p=e.length;p>0&&e[p-1][2]>r;p--)e[p]=e[p-1];e[p]=[a,l,r]},n.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return n.d(t,{a:t}),t},n.d=(e,t)=>{for(var a in t)n.o(t,a)&&!n.o(e,a)&&Object.defineProperty(e,a,{enumerable:!0,get:t[a]})},n.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(e){if("object"==typeof window)return window}}(),n.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),n.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.nmd=e=>(e.paths=[],e.children||(e.children=[]),e),n.j=281,(()=>{var e;n.g.importScripts&&(e=n.g.location+"");var t=n.g.document;if(!e&&t&&(t.currentScript&&(e=t.currentScript.src),!e)){var a=t.getElementsByTagName("script");a.length&&(e=a[a.length-1].src)}if(!e)throw new Error("Automatic publicPath is not supported in this browser");e=e.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/"),n.p=e})(),(()=>{var e={281:0};n.O.j=t=>0===e[t];var t=(t,a)=>{var l,r,[s,o,i]=a,c=0;if(s.some((t=>0!==e[t]))){for(l in o)n.o(o,l)&&(n.m[l]=o[l]);if(i)var p=i(n)}for(t&&t(a);c<s.length;c++)r=s[c],n.o(e,r)&&e[r]&&e[r][0](),e[s[c]]=0;return n.O(p)},a=globalThis.webpackChunkwebpack=globalThis.webpackChunkwebpack||[];a.forEach(t.bind(null,0)),a.push=t.bind(null,a.push.bind(a))})();var l=n.O(void 0,[351],(()=>n(8516)));l=n.O(l);var r=window;for(var s in l)r[s]=l[s];l.__esModule&&Object.defineProperty(r,"__esModule",{value:!0})})();