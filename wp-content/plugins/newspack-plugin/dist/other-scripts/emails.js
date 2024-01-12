(()=>{"use strict";var e,t={8967:(e,t,n)=>{n.r(t);var i=n(9307),o=n(5736),a=n(9818);const r=window.wp.compose;var s=n(5609),l=n(6989),c=n.n(l);const p=window.wp.editPost,w=window.wp.plugins;var d=n(264);const m=o.__,u=(0,r.compose)([(0,a.withSelect)((e=>{const{getEditedPostAttribute:t,getCurrentPostId:n}=e("core/editor"),i=t("meta");return{title:t("title"),postId:n(),postMeta:i}})),(0,a.withDispatch)((e=>{const{savePost:t,editPost:n}=e("core/editor"),{createNotice:i}=e("core/notices");return{savePost:t,createNotice:i,updatePostMeta:e=>t=>n({meta:{[e]:t}}),updatePostTitle:e=>n({title:e})}}))])((({postId:e,savePost:t,title:n,postMeta:a,updatePostTitle:r,createNotice:l})=>{const[w,u]=(0,i.useState)(!1),[g,h]=d.PT.useObjectState({testRecipient:newspack_emails.current_user_email}),f=a[newspack_emails.email_config_name_meta],v=newspack_emails.configs[f];return(0,i.useEffect)((()=>{v?.editor_notice&&l("info",v.editor_notice,{isDismissible:!1}),l("info",(0,o.sprintf)(m("This email will be sent from %1$s <%2$s>.","newspack"),v.from_name||newspack_emails.from_name,v.from_email||newspack_emails.from_email),{isDismissible:!1})}),[]),(0,i.createElement)(i.Fragment,null,v.available_placeholders?.length&&(0,i.createElement)(p.PluginDocumentSettingPanel,{name:"email-instructions-panel",title:m("Instructions","newspack")},m("Use the following placeholders to insert dynamic content in the email:","newspack"),(0,i.createElement)("ul",null,v.available_placeholders.map(((e,t)=>(0,i.createElement)("li",{key:t},"– ",(0,i.createElement)("code",null,e.template),": ",e.label))))),(0,i.createElement)(p.PluginDocumentSettingPanel,{name:"email-settings-panel",title:m("Settings","newspack")},(0,i.createElement)(s.TextControl,{label:m("Subject","newspack"),value:n,onChange:r})),(0,i.createElement)(p.PluginDocumentSettingPanel,{name:"email-testing-panel",title:m("Testing","newspack")},(0,i.createElement)(s.TextControl,{label:m("Send to","newspack"),value:g.testRecipient,type:"email",onChange:h("testRecipient")}),(0,i.createElement)("div",{className:"newspack__testing-controls"},(0,i.createElement)(s.Button,{isPrimary:!0,onClick:async()=>{u(!0),await t(),c()({path:"/newspack/v1/newspack-emails/test",method:"POST",data:{recipient:g.testRecipient,post_id:e}}).then((()=>{l("success",m("Test email sent!","newspack"))})).catch((()=>{l("error",m("Test email was not sent.","newspack"))})).finally((()=>{u(!1)}))},disabled:w},m(w?"Sending…":"Send","newspack")),w&&(0,i.createElement)(s.Spinner,null))))}));(0,w.registerPlugin)("newspack-emails-sidebar",{render:u,icon:null})},9196:e=>{e.exports=window.React},6292:e=>{e.exports=window.moment},6989:e=>{e.exports=window.wp.apiFetch},5609:e=>{e.exports=window.wp.components},9818:e=>{e.exports=window.wp.data},9307:e=>{e.exports=window.wp.element},2694:e=>{e.exports=window.wp.hooks},2629:e=>{e.exports=window.wp.htmlEntities},5736:e=>{e.exports=window.wp.i18n},9630:e=>{e.exports=window.wp.keycodes},444:e=>{e.exports=window.wp.primitives},6483:e=>{e.exports=window.wp.url}},n={};function i(e){var o=n[e];if(void 0!==o)return o.exports;var a=n[e]={id:e,loaded:!1,exports:{}};return t[e].call(a.exports,a,a.exports,i),a.loaded=!0,a.exports}i.m=t,e=[],i.O=(t,n,o,a)=>{if(!n){var r=1/0;for(p=0;p<e.length;p++){for(var[n,o,a]=e[p],s=!0,l=0;l<n.length;l++)(!1&a||r>=a)&&Object.keys(i.O).every((e=>i.O[e](n[l])))?n.splice(l--,1):(s=!1,a<r&&(r=a));if(s){e.splice(p--,1);var c=o();void 0!==c&&(t=c)}}return t}a=a||0;for(var p=e.length;p>0&&e[p-1][2]>a;p--)e[p]=e[p-1];e[p]=[n,o,a]},i.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return i.d(t,{a:t}),t},i.d=(e,t)=>{for(var n in t)i.o(t,n)&&!i.o(e,n)&&Object.defineProperty(e,n,{enumerable:!0,get:t[n]})},i.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(e){if("object"==typeof window)return window}}(),i.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),i.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},i.nmd=e=>(e.paths=[],e.children||(e.children=[]),e),i.j=909,(()=>{var e;i.g.importScripts&&(e=i.g.location+"");var t=i.g.document;if(!e&&t&&(t.currentScript&&(e=t.currentScript.src),!e)){var n=t.getElementsByTagName("script");n.length&&(e=n[n.length-1].src)}if(!e)throw new Error("Automatic publicPath is not supported in this browser");e=e.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/"),i.p=e+"../"})(),(()=>{var e={909:0};i.O.j=t=>0===e[t];var t=(t,n)=>{var o,a,[r,s,l]=n,c=0;if(r.some((t=>0!==e[t]))){for(o in s)i.o(s,o)&&(i.m[o]=s[o]);if(l)var p=l(i)}for(t&&t(n);c<r.length;c++)a=r[c],i.o(e,a)&&e[a]&&e[a][0](),e[r[c]]=0;return i.O(p)},n=globalThis.webpackChunkwebpack=globalThis.webpackChunkwebpack||[];n.forEach(t.bind(null,0)),n.push=t.bind(null,n.push.bind(n))})();var o=i.O(void 0,[351],(()=>i(8967)));o=i.O(o);var a=window;for(var r in o)a[r]=o[r];o.__esModule&&Object.defineProperty(a,"__esModule",{value:!0})})();