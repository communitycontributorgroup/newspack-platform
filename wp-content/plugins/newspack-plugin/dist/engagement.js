!function(){"use strict";var e,t={864:function(e,t,a){a.r(t);var n=a(7462),r=a(4942),s=(a(5674),a(9307)),i=a(5736),l=a(1383),c=a(9674),o=a(2819),p=a(6989),d=a.n(p),m=a(5609);const u=i.__,w=e=>{let{className:t,onUpdate:a,initialProvider:r,newslettersConfig:c,isOnboarding:p=!0,authUrl:w=!1,setInitialProvider:g=(()=>{}),setAuthUrl:h=(()=>{}),setLockedLists:k=(()=>{})}=e;const[f,v]=(0,s.useState)(!1),[E,_]=(0,s.useState)(!1),[b,y]=l.PT.useObjectState({});(0,s.useEffect)((()=>{const e=c?.newspack_newsletters_service_provider;k(!(!r||e===r)),!r&&e&&g(e)}),[c?.newspack_newsletters_service_provider]),(0,s.useEffect)((()=>{x(c?.newspack_newsletters_service_provider)}),[c?.newspack_newsletters_service_provider]);const x=e=>{h(!1),e&&"constant_contact"===e&&(v(!0),d()({path:`/newspack-newsletters/v1/${e}/verify_token`}).then((e=>{!e.valid&&e.auth_url?h(e.auth_url):h(!1)})).catch((()=>{h(!1)})).finally((()=>{v(!1)})))},S=e=>{y(e),a&&a((0,o.mapValues)(e.settings,(0,o.property)("value")))},C=()=>{_(!1),d()({path:"/newspack/v1/wizard/newspack-engagement-wizard/newsletters"}).then(S).catch(_)},P=()=>{w&&(window.open(w,"esp_oauth","width=500,height=600").opener={verify:(0,o.once)((()=>{window.location.reload()}))})},z=async()=>{_(!1),v(!0),d()({path:"/newspack/v1/wizard/newspack-engagement-wizard/newsletters",method:"POST",data:c}).finally((()=>{g(c?.newspack_newsletters_service_provider),x(c?.newspack_newsletters_service_provider),k(!1),v(!1)}))};(0,s.useEffect)(C,[]);const T=e=>({disabled:f,value:b.settings[e]?.value||"",checked:Boolean(b.settings[e]?.value),label:b.settings[e]?.description,placeholder:b.settings[e]?.placeholder,options:b.settings[e]?.options?.map((e=>({value:e.value,label:e.name})))||null,onChange:t=>S({settings:{[e]:{value:t}}})});return!E&&(0,o.isEmpty)(b)?(0,s.createElement)("div",{className:"flex justify-around mt4"},(0,s.createElement)(l.Pi,null)):(0,s.createElement)("div",{className:t},!1===b.configured&&(0,s.createElement)(l.xf,{plugins:["newspack-newsletters"],withoutFooterButton:!0,onStatus:e=>{let{complete:t}=e;return t&&C()}}),!0===b.configured&&(()=>{const e=T("newspack_newsletters_service_provider");return(0,s.createElement)(l.fM,{isMedium:!0,title:u("Email Service Provider","newspack"),description:u("Connect an email service provider (ESP) to author and send newsletters.","newspack"),notification:E?E?.message||u("Something went wrong.","newspack"):null,notificationLevel:"error",hasGreyHeader:!0,actionContent:(0,s.createElement)(l.zx,{disabled:f,variant:"primary",onClick:z},u("Save Settings","newspack")),disabled:f},(0,s.createElement)(l.rj,{gutter:16,columns:1},!1!==w&&(0,s.createElement)(l.Zb,{isSmall:!0},(0,s.createElement)("h3",null,u("Authorize Application","newspack")),(0,s.createElement)("p",null,(0,i.sprintf)(u("Authorize %s to connect to Newspack.","newspack-newsletters"),(()=>{const e=b.settings.newspack_newsletters_service_provider,t=e?.value;return e?.options?.find((e=>e.value===t))?.name})())),(0,s.createElement)(l.zx,{isSecondary:!0,onClick:P},u("Authorize","newspack"))),(0,o.values)(b.settings).filter((t=>!t.provider||t.provider===e.value)).map((e=>{if(p&&!e.onboarding)return null;switch(e.type){case"select":return(0,s.createElement)(l.Yw,(0,n.Z)({key:e.key},T(e.key)));case"checkbox":return(0,s.createElement)(m.CheckboxControl,(0,n.Z)({key:e.key},T(e.key)));default:return(0,s.createElement)(l.rj,{columns:1,gutter:8,key:e.key},(0,s.createElement)(l.w4,T(e.key)),e.help&&e.helpURL&&(0,s.createElement)("p",null,(0,s.createElement)(m.ExternalLink,{href:e.helpURL},e.help)))}}))))})())},g=e=>{let{lockedLists:t,onUpdate:a,initialProvider:n}=e;const[r,i]=(0,s.useState)(!1),[c,o]=(0,s.useState)(!1),[p,w]=(0,s.useState)([]),g=e=>{w(e),"function"==typeof a&&a(e)},h=(e,t)=>a=>{const n=[...p];n[e][t]=a,g(n)};return(0,s.useEffect)((()=>{i(!1),o(!0),d()({path:"/newspack-newsletters/v1/lists"}).then(g).catch(i).finally((()=>o(!1)))}),[n]),c||p?.length||r?!c||p?.length||r?(0,s.createElement)(s.Fragment,null,(0,s.createElement)(l.fM,{isMedium:!0,title:u("Subscription Lists","newspack"),description:u("Manage the lists available to readers for subscription.","newspack"),notification:r?r?.message||u("Something went wrong.","newspack"):t?u("Please save your ESP settings before changing your subscription lists.","newspack"):null,notificationLevel:r?"error":"warning",hasGreyHeader:!0,actionContent:(0,s.createElement)(s.Fragment,null,newspack_engagement_wizard.new_subscription_lists_url&&(0,s.createElement)(l.zx,{variant:"secondary",disabled:c||t,href:newspack_engagement_wizard.new_subscription_lists_url},u("Add New","newspack")),(0,s.createElement)(l.zx,{isPrimary:!0,onClick:()=>{i(!1),o(!0),d()({path:"/newspack-newsletters/v1/lists",method:"post",data:{lists:p}}).then(g).catch(i).finally((()=>o(!1)))},disabled:c||t},u("Save Subscription Lists","newspack"))),disabled:c||t},!t&&p.map(((e,t)=>(0,s.createElement)(l.fM,{key:e.id,isSmall:!0,simple:!0,hasWhiteHeader:!0,title:e.name,description:e?.type_label?e.type_label:null,disabled:c,toggleOnChange:h(t,"active"),toggleChecked:e.active,actionText:e?.edit_link?(0,s.createElement)(m.ExternalLink,{href:e.edit_link},u("Edit","newspack_newsletters")):null},e.active&&"local"!==e?.type&&(0,s.createElement)(s.Fragment,null,(0,s.createElement)(l.w4,{label:u("List title","newspack"),value:e.title,disabled:c||"local"===e?.type,onChange:h(t,"title")}),(0,s.createElement)(m.TextareaControl,{label:u("List description","newspack"),value:e.description,disabled:c||"local"===e?.type,onChange:h(t,"description")}))))))):(0,s.createElement)("div",{className:"flex justify-around mt4"},(0,s.createElement)(l.Pi,null)):null},h=()=>{const[{newslettersConfig:e},t]=l.PT.useObjectState({}),[a,n]=(0,s.useState)(""),[r,i]=(0,s.useState)(!1),[c,o]=(0,s.useState)(!1);return(0,s.createElement)(s.Fragment,null,(0,s.createElement)(w,{isOnboarding:!1,onUpdate:e=>t({newslettersConfig:e}),authUrl:c,setAuthUrl:o,newslettersConfig:e,setLockedLists:i,initialProvider:a,setInitialProvider:n}),(0,s.createElement)(g,{lockedLists:r,initialProvider:a}),"mailchimp"===e?.newspack_newsletters_service_provider&&(0,s.createElement)(s.Fragment,null,(0,s.createElement)("hr",null),(0,s.createElement)(l.M$,{title:u("WooCommerce integration","newspack")}),(0,s.createElement)(l.xf,{plugins:["mailchimp-for-woocommerce"],withoutFooterButton:!0})))};var k=(0,l.a4)((()=>(0,s.createElement)(s.Fragment,null,(0,s.createElement)(h,null)))),f=a(4350);const v=i.__;function E(e){let{config:t,getSharedProps:a,inFlight:r,prerequisite:c,saveConfig:o}=e;const{href:p}=c;return(0,s.createElement)(l.fM,{className:"newspack-ras-wizard__prerequisite",isMedium:!0,expandable:!0,collapse:c.active,title:c.label,description:(0,i.sprintf)(v("Status: %s","newspack"),c.active?v("Ready","newspack"):v("Pending","newspack")),checkbox:c.active?"checked":"unchecked",notificationLevel:"info",notification:c.active&&c.fields&&c.warning&&Object.keys(c.fields).filter((e=>""===t[e])).length?c.warning:null},(0,s.createElement)(s.Fragment,null,c.description&&(0,s.createElement)("p",null,c.description,c.help_url&&(0,s.createElement)(s.Fragment,null," ",(0,s.createElement)(m.ExternalLink,{href:c.help_url},v("Learn more","newspack")))),c.fields&&(0,s.createElement)(l.rj,{columns:2,gutter:16},(0,s.createElement)("div",null,Object.keys(c.fields).map((e=>(0,s.createElement)(l.w4,(0,n.Z)({key:e,label:c.fields[e].label,help:c.fields[e].description},a(e,"text"))))),(0,s.createElement)(l.zx,{isPrimary:!0,onClick:()=>{const e={};Object.keys(c.fields).forEach((a=>{e[a]=t[a]})),o(e)},disabled:r},r?v("Saving…","newspack"):(0,i.sprintf)(v("%s settings","newspack"),c.active?v("Update","newspack"):v("Save","newspack"))))),p&&c.action_text&&(0,s.createElement)(l.rj,{columns:2,gutter:16},(0,s.createElement)("div",null,(!c.hasOwnProperty("action_enabled")||c.action_enabled)&&(0,s.createElement)(l.zx,{isPrimary:!0,onClick:()=>{c.instructions&&window.localStorage.setItem(f.rq,JSON.stringify({message:(0,i.sprintf)(v("%1$s%2$sReturn to the Reader Activation page to complete the settings and activate%3$s.","newspack"),c.instructions+" ",window.newspack_engagement_wizard?.reader_activation_url?`<a href="${window.newspack_engagement_wizard.reader_activation_url}">`:"",window.newspack_engagement_wizard?.reader_activation_url?"</a>":"")})),window.location.href=p}},(c.active?v("Update ","newspack"):c.fields?v("Save ","newspack"):v("Configure ","newspack"))+c.action_text),c.hasOwnProperty("action_enabled")&&!c.action_enabled&&(0,s.createElement)(l.zx,{isSecondary:!0,disabled:!0},c.disabled_text||c.action_text)))))}const _=i.__;function b(e){let{value:t,onChange:a}=e;const[n,r]=(0,s.useState)(!1),[i,c]=(0,s.useState)([]),[o,p]=(0,s.useState)(!1);return(0,s.useEffect)((()=>{p(!1),r(!0),d()({path:"/newspack-newsletters/v1/lists"}).then(c).catch(p).finally((()=>r(!1)))}),[]),(0,s.createElement)(s.Fragment,null,o&&(0,s.createElement)(l.qX,{noticeText:o?.message||_("Something went wrong.","newspack"),isError:!0}),(0,s.createElement)(l.M$,{title:_("ActiveCampaign","newspack"),description:_("Settings for the ActiveCampaign integration.","newspack")}),(0,s.createElement)(l.Yw,{label:_("Master List","newspack"),help:_("Choose a list to which all registered readers will be added.","newspack"),disabled:n,value:t.masterList,onChange:("masterList",e=>a&&a("masterList",e)),options:[{value:"",label:_("None","newspack")},...i.map((e=>({label:e.name,value:e.id})))]}))}const y=i.__;var x=(0,l.a4)((()=>{const[e,t]=(0,s.useState)(!1),[a,r]=(0,s.useState)({}),[c,o]=(0,s.useState)({}),[p,u]=(0,s.useState)(!1),[w,g]=(0,s.useState)(!1),[h,k]=(0,s.useState)(!1),[v,_]=(0,s.useState)(null),[x,S]=(0,s.useState)([]),[C,P]=(0,s.useState)(!1),z=(e,t)=>{r({...a,[e]:t})},T=()=>{u(!1),t(!0),d()({path:"/newspack/v1/wizard/newspack-engagement-wizard/reader-activation"}).then((e=>{let{config:t,prerequisites_status:a,memberships:n}=e;_(a),r(t),o(n)})).catch(u).finally((()=>t(!1)))},L=e=>{u(!1),t(!0),d()({path:"/newspack/v1/wizard/newspack-engagement-wizard/reader-activation",method:"post",data:e}).then((e=>{let{config:t,prerequisites_status:a,memberships:n}=e;_(a),r(t),o(n)})).catch(u).finally((()=>t(!1)))};(0,s.useEffect)((()=>{window.scrollTo(0,0),T(),window.localStorage.removeItem(f.rq)}),[]),(0,s.useEffect)((()=>{d()({path:"/newspack/v1/wizard/newspack-engagement-wizard/newsletters"}).then((e=>{k("active_campaign"===e?.settings?.newspack_newsletters_service_provider?.value)}))}),[]),(0,s.useEffect)((()=>{const e=!x.length&&v&&Object.keys(v).every((e=>v[e]?.active));g(e),v&&S(Object.keys(v).reduce(((e,t)=>{const a=v[t];if(a.plugins)for(const t in a.plugins)a.plugins[t]||e.push(t);return e}),[]))}),[v]);const A=function(t){let n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"checkbox";const r={onChange:e=>z(t,e)};switch("enabled"!==t&&(r.disabled=e),n){case"checkbox":r.checked=Boolean(a[t]);break;case"text":r.value=a[t]||""}return r},M=Object.values(a.emails||{});return(0,s.createElement)(s.Fragment,null,(0,s.createElement)(l.M$,{title:y("Reader Activation","newspack"),description:()=>(0,s.createElement)(s.Fragment,null,y("Newspack’s Reader Activation system is a set of features that aim to increase reader loyalty, promote engagement, and drive revenue. ","newspack"),(0,s.createElement)(m.ExternalLink,{href:"https://help.newspack.com/engagement/reader-activation-system"},y("Learn more","newspack-plugin")))}),p&&(0,s.createElement)(l.qX,{noticeText:p?.message||y("Something went wrong.","newspack"),isError:!0}),0<x.length&&(0,s.createElement)(l.qX,{noticeText:y("The following plugins are required.","newspack"),isWarning:!0}),0===x.length&&v&&!w&&(0,s.createElement)(l.qX,{noticeText:y("Complete these settings to enable Reader Activation.","newspack"),isWarning:!0}),v&&w&&a.enabled&&(0,s.createElement)(l.qX,{noticeText:y("Reader Activation is enabled.","newspack"),isSuccess:!0}),!v&&(0,s.createElement)(s.Fragment,null,(0,s.createElement)(l.Pi,{isLeft:!0}),y("Retrieving status…","newspack")),0<x.length&&v&&(0,s.createElement)(l.xf,{plugins:x,withoutFooterButton:!0,onStatus:e=>{let{complete:t}=e;return t&&T()}}),!x.length&&v&&Object.keys(v).map((t=>(0,s.createElement)(E,{key:t,config:a,getSharedProps:A,inFlight:e,prerequisite:v[t],fetchConfig:T,saveConfig:L}))),a.enabled&&(0,s.createElement)(s.Fragment,null,(0,s.createElement)("hr",null),(0,s.createElement)(l.zx,{variant:"link",onClick:()=>P(!C)},(0,i.sprintf)(y("%s Advanced Settings","newspack"),y(C?"Hide":"Show","newspack")))),C&&(0,s.createElement)(l.Zb,{noBorder:!0},newspack_engagement_wizard.has_memberships&&c?(0,s.createElement)(s.Fragment,null,(0,s.createElement)(l.M$,{title:y("Memberships Integration","newspack"),description:y("Improve the reader experience on content gating.","newspack")}),(0,s.createElement)(l.fM,{title:y("Content Gate","newspack"),titleLink:c.edit_gate_url,href:c.edit_gate_url,description:(()=>{let e=y("Configure the gate rendered on content with restricted access.","newspack");return"publish"===c?.gate_status?e+=" "+y("The gate is currently published.","newspack"):"draft"!==c?.gate_status&&"trash"!==c?.gate_status||(e+=" "+y("The gate is currently a draft.","newspack")),e})(),actionText:y("Configure","newspack")}),(0,s.createElement)("hr",null)):null,M?.length>0&&(0,s.createElement)(s.Fragment,null,(0,s.createElement)(l.M$,{title:y("Transactional Email Content","newspack"),description:y("Customize the content of transactional emails.","newspack")}),M.map((e=>(0,s.createElement)(l.fM,{key:e.post_id,title:e.label,titleLink:e.edit_link,href:e.edit_link,description:e.description,actionText:y("Edit","newspack"),isSmall:!0}))),(0,s.createElement)("hr",null)),(0,s.createElement)(l.M$,{title:y("Email Service Provider (ESP) Advanced Settings","newspack"),description:y("Settings for Newspack Newsletters integration.","newspack")}),(0,s.createElement)(l.w4,(0,n.Z)({label:y("Newsletter subscription text on registration","newspack"),help:y("The text to display while subscribing to newsletters from the sign-in modal.","newspack")},A("newsletters_label","text"))),a.sync_esp&&(0,s.createElement)(s.Fragment,null,(0,s.createElement)(l.w4,(0,n.Z)({label:y("Metadata field prefix","newspack"),help:y("A string to prefix metadata fields attached to each contact synced to the ESP. Required to ensure that metadata field names are unique. Default: NP_","newspack")},A("metadata_prefix","text"))),h&&(0,s.createElement)(b,{value:{masterList:a.active_campaign_master_list},onChange:(e,t)=>{"masterList"===e&&z("active_campaign_master_list",t)}})),(0,s.createElement)("div",{className:"newspack-buttons-card"},(0,s.createElement)(l.zx,{isPrimary:!0,onClick:()=>{L({newsletters_label:a.newsletters_label,metadata_prefix:a.metadata_prefix,active_campaign_master_list:a.active_campaign_master_list})},disabled:e},y("Save advanced settings","newspack")))))})),S=a(129);const C=i.__;function P(e){let{inFlight:t,prompt:a,setInFlight:n,setPrompts:r}=e;const[c,o]=(0,s.useState)({}),[p,u]=(0,s.useState)(!1),[w,g]=(0,s.useState)(!1),[h,k]=(0,s.useState)(null),[f,v]=(0,s.useState)(!1);(0,s.useEffect)((()=>{if(Array.isArray(a?.user_input_fields)){const e={...c};a.user_input_fields.forEach((t=>{e[t.name]=t.value||t.default})),o(e)}a.featured_image_id&&(n(!0),d()({path:`/wp/v2/media/${a.featured_image_id}`}).then((e=>{(e?.source_url||e?.url)&&k({url:e.source_url||e.url})})).catch(u).finally((()=>{n(!1)})))}),[a]),(0,s.useEffect)((()=>{setTimeout((()=>g(!1)),5e3)}),[w]);const E=(0,s.createElement)(m.SVG,{xmlns:"http://www.w3.org/2000/svg",height:"24",viewBox:"0 0 24 24",width:"24"},(0,s.createElement)(m.Path,{fillRule:"evenodd",clipRule:"evenodd",d:"M4.5001 13C5.17092 13.3354 5.17078 13.3357 5.17066 13.3359L5.17346 13.3305C5.1767 13.3242 5.18233 13.3135 5.19036 13.2985C5.20643 13.2686 5.23209 13.2218 5.26744 13.1608C5.33819 13.0385 5.44741 12.8592 5.59589 12.6419C5.89361 12.2062 6.34485 11.624 6.95484 11.0431C8.17357 9.88241 9.99767 8.75 12.5001 8.75C15.0025 8.75 16.8266 9.88241 18.0454 11.0431C18.6554 11.624 19.1066 12.2062 19.4043 12.6419C19.5528 12.8592 19.662 13.0385 19.7328 13.1608C19.7681 13.2218 19.7938 13.2686 19.8098 13.2985C19.8179 13.3135 19.8235 13.3242 19.8267 13.3305L19.8295 13.3359C19.8294 13.3357 19.8293 13.3354 20.5001 13C21.1709 12.6646 21.1708 12.6643 21.1706 12.664L21.1702 12.6632L21.1693 12.6614L21.1667 12.6563L21.1588 12.6408C21.1522 12.6282 21.1431 12.6108 21.1315 12.5892C21.1083 12.5459 21.0749 12.4852 21.0311 12.4096C20.9437 12.2584 20.8146 12.0471 20.6428 11.7956C20.2999 11.2938 19.7823 10.626 19.0798 9.9569C17.6736 8.61759 15.4977 7.25 12.5001 7.25C9.50252 7.25 7.32663 8.61759 5.92036 9.9569C5.21785 10.626 4.70033 11.2938 4.35743 11.7956C4.1856 12.0471 4.05654 12.2584 3.96909 12.4096C3.92533 12.4852 3.89191 12.5459 3.86867 12.5892C3.85705 12.6108 3.84797 12.6282 3.84141 12.6408L3.83346 12.6563L3.8309 12.6614L3.82997 12.6632L3.82959 12.664C3.82943 12.6643 3.82928 12.6646 4.5001 13ZM12.5001 16C14.4331 16 16.0001 14.433 16.0001 12.5C16.0001 10.567 14.4331 9 12.5001 9C10.5671 9 9.0001 10.567 9.0001 12.5C9.0001 14.433 10.5671 16 12.5001 16Z",fill:t?"#828282":"#3366FF"})),_=a.help_info||null;return(0,s.createElement)(l.fM,{isMedium:!0,expandable:!0,collapse:a.ready&&!f,title:a.title,description:(0,i.sprintf)(C("Status: %s","newspack"),a.ready?C("Ready","newspack"):C("Pending","newspack")),checkbox:a.ready?"checked":"unchecked"},(0,s.createElement)(l.rj,{columns:2,gutter:64,className:"newspack-ras-campaign__grid"},(0,s.createElement)("div",{className:"newspack-ras-campaign__fields"},a.user_input_fields.map((e=>(0,s.createElement)(s.Fragment,{key:e.name},"array"===e.type&&Array.isArray(e.options)&&(0,s.createElement)(m.BaseControl,{id:`newspack-engagement-wizard__${e.name}`,label:e.label},e.options.map((a=>(0,s.createElement)(m.BaseControl,{key:a.id,id:`newspack-engagement-wizard__${a.id}`,className:"newspack-checkbox-control",help:a.description},(0,s.createElement)(m.CheckboxControl,{disabled:t,label:a.label,value:a.id,checked:c[e.name]?.indexOf(a.id)>-1,onChange:t=>{const n={...c};!t&&n[e.name].indexOf(a.id)>-1&&(n[e.name].value=n[e.name].splice(n[e.name].indexOf(a.id),1)),t&&-1===n[e.name].indexOf(a.id)&&n[e.name].push(a.id),o(n)}}))))),"string"===e.type&&e.max_length&&150<e.max_length&&(0,s.createElement)(m.TextareaControl,{className:"newspack-textarea-control",label:e.label,disabled:t,help:`${c[e.name]?.length||0} / ${e.max_length}`,onChange:t=>{if(t.length>e.max_length)return;const a={...c};a[e.name]=t,JSON.stringify(a),JSON.stringify(c),o(a)},placeholder:e.default,rows:10,value:c[e.name]||""}),"string"===e.type&&e.max_length&&150>=e.max_length&&(0,s.createElement)(l.w4,{label:e.label,disabled:t,help:`${c[e.name]?.length||0} / ${e.max_length}`,onChange:t=>{if(t.length>e.max_length)return;const a={...c};a[e.name]=t,JSON.stringify(a),JSON.stringify(c),o(a)},placeholder:e.default,value:c[e.name]||""}),"int"===e.type&&"featured_image_id"===e.name&&(0,s.createElement)(m.BaseControl,{id:`newspack-engagement-wizard__${e.name}`,label:e.label},(0,s.createElement)(l.Ur,{buttonLabel:C("Select file","newspack"),disabled:t,image:h,onChange:t=>{const a={...c};a[e.name]=t?.id||0,a[e.name],c[e.name],o(a),k(t?.url?t:null)}}))))),p&&(0,s.createElement)(l.qX,{noticeText:p?.message||C("Something went wrong.","newspack"),isError:!0}),w&&(0,s.createElement)(l.qX,{noticeText:w,isSuccess:!0}),(0,s.createElement)("div",{className:"newspack-buttons-card"},(0,s.createElement)(l.zx,{isPrimary:!0,onClick:()=>{var e,t;v(!1),e=a.slug,t=c,new Promise(((a,s)=>{u(!1),g(!1),n(!0),d()({path:"/newspack-popups/v1/reader-activation/campaign",method:"post",data:{slug:e,data:t}}).then((e=>{r(e),g(C("Prompt saved.","newspack")),a()})).catch((e=>{u(e),s(e)})).finally((()=>{n(!1)}))}))},disabled:t},t?C("Saving…","newspack"):(0,i.sprintf)(C("%s prompt settings","newspack"),a.ready?C("Update","newspack"):C("Save","newspack"))),(0,s.createElement)(l.BA,{url:(e=>{let{options:t,slug:a}=e;const{placement:n,trigger_type:r}=t,s=window.newspack_engagement_wizard.preview_query_keys,i={preset:a,values:c};Object.keys(t).forEach((e=>{s.hasOwnProperty(e)&&(i[s[e]]=t[e])}));let l="/";return"archives"===n&&window.newspack_engagement_wizard?.preview_archive?l=window.newspack_engagement_wizard.preview_archive:("inline"===n||"scroll"===r)&&window&&window.newspack_engagement_wizard?.preview_post&&(l=window.newspack_engagement_wizard?.preview_post),`${l}?${(0,S.stringify)({...i})}`})(a),renderButton:e=>{let{showPreview:a}=e;return(0,s.createElement)(l.zx,{disabled:t,icon:E,isSecondary:!0,onClick:async()=>a()},C("Preview prompt","newspack"))}}))),_&&(0,s.createElement)("div",{className:"newspack-ras-campaign__help"},_.screenshot&&(0,s.createElement)("img",{src:_.screenshot,alt:a.title}),_.description&&(0,s.createElement)("p",null,(0,s.createElement)("span",{dangerouslySetInnerHTML:{__html:_.description}})," ",_.url&&(0,s.createElement)(m.ExternalLink,{href:_.url},C("Learn more","newspack"))),_.recommendations&&(0,s.createElement)(s.Fragment,null,(0,s.createElement)("h4",{className:"newspack-ras-campaign__recommendation-heading"},C("We recommend","newspack")),(0,s.createElement)("ul",null,_.recommendations.map(((e,t)=>(0,s.createElement)("li",{key:t},(0,s.createElement)("span",{dangerouslySetInnerHTML:{__html:e}})))))))))}const z=i.__;var T=(0,l.a4)((()=>{const[e,t]=(0,s.useState)(!1),[a,n]=(0,s.useState)(!1),[r,i]=(0,s.useState)(null),[c,o]=(0,s.useState)(!1);return(0,s.useEffect)((()=>{window.scrollTo(0,0),n(!1),t(!0),d()({path:"/newspack-popups/v1/reader-activation/campaign"}).then((e=>{i(e)})).catch(n).finally((()=>t(!1)))}),[]),(0,s.useEffect)((()=>{Array.isArray(r)&&0<r.length&&o(r.every((e=>e.ready)))}),[r]),(0,s.createElement)("div",{className:"newspack-ras-campaign__prompt-wizard"},(0,s.createElement)(l.M$,{title:z("Set Up Reader Activation Campaign","newspack"),description:z("Preview and customize the prompts, or use our suggested defaults.","newspack")}),a&&(0,s.createElement)(l.qX,{noticeText:a?.message||z("Something went wrong.","newspack"),isError:!0}),!r&&!a&&(0,s.createElement)(s.Fragment,null,(0,s.createElement)(l.Pi,{isLeft:!0}),z("Retrieving prompts…","newspack")),r&&r.map((a=>(0,s.createElement)(P,{key:a.slug,prompt:a,inFlight:e,setInFlight:t,setPrompts:i}))),(0,s.createElement)("div",{className:"newspack-buttons-card"},(0,s.createElement)(l.zx,{isPrimary:!0,disabled:e||!c,href:"/wp-admin/admin.php?page=newspack-engagement-wizard#/reader-activation/complete"},z("Continue","newspack")),(0,s.createElement)(l.zx,{isSecondary:!0,disabled:e,href:"#/"},z("Back","newspack"))))}));const L=i.__,{useHistory:A}=l.F0,M=[L("Your <strong>current segments and prompts</strong> will be deactivated and archived.","newspack"),L("<strong>Reader registration</strong> will be activated to enable better targeting for driving engagement and conversations.","newspack"),L("The <strong>Reader Activation campaign</strong> will be activated with default segments and settings.","newspack")],F=[L("Setting up new segments…","newspack"),L("Activating reader registration…","newspack"),L("Activating Reader Activation Campaign…","newspack")];var O=(0,l.a4)((()=>{const[e,t]=(0,s.useState)(!1),[a,n]=(0,s.useState)(!1),[r,i]=(0,s.useState)(null),[c,o]=(0,s.useState)(!1),[p,u]=(0,s.useState)(!1),w=(0,s.useRef)(),g=A();return(0,s.useEffect)((()=>{w.current&&clearTimeout(w.current),a&&t(!1),!a&&e&&0<=r&&r<F.length&&(o(F[r]),w.current=setTimeout((()=>{i((e=>e+1))}),(2e3,1e3+1e3*Math.random()))),r===F.length&&p&&(i(F.length+1),o(L("Done!","newspack")),setTimeout((()=>{t(!1),g.push("/reader-activation")}),3e3))}),[p,r]),(0,s.createElement)("div",{className:"newspack-ras-campaign__completed"},(0,s.createElement)(l.M$,{title:L("Enable Reader Activation","newspack"),description:()=>(0,s.createElement)(s.Fragment,null,L("An easy way to let your readers register for your site, sign up for newsletters, or become donors and paid members. ","newspack"),(0,s.createElement)(m.ExternalLink,{href:"https://help.newspack.com"},L("Learn more","newspack-plugin")))}),e&&(0,s.createElement)(l.Zb,{className:"newspack-ras-campaign__completed-card"},(0,s.createElement)(l.ko,{completed:r,displayFraction:!1,total:F.length+1,label:c})),!e&&(0,s.createElement)(l.Zb,{className:"newspack-ras-campaign__completed-card"},(0,s.createElement)("h2",null,L("You're all set to enable Reader Activation!","newspack")),(0,s.createElement)("p",null,L("This is what will happen next:","newspack")),(0,s.createElement)(l.Zb,{noBorder:!0,className:"justify-center"},(0,s.createElement)(l.YH,{stepsListItems:M,narrowList:!0})),a&&(0,s.createElement)(l.qX,{noticeText:a?.message||L("Something went wrong.","newspack"),isError:!0}),(0,s.createElement)(l.Zb,{buttonsCard:!0,noBorder:!0,className:"justify-center"},(0,s.createElement)(l.zx,{isPrimary:!0,onClick:()=>(async()=>{n(!1),t(!0),i(0);try{u(await d()({path:"/newspack/v1/wizard/newspack-engagement-wizard/reader-activation/activate",method:"post"}))}catch(e){n(e)}})()},L("Enable Reader Activation","newspack ")))),(0,s.createElement)("div",{className:"newspack-buttons-card"},(0,s.createElement)(l.zx,{isSecondary:!0,disabled:e,href:"/wp-admin/admin.php?page=newspack-engagement-wizard#/reader-activation/campaign"},L("Back","newspack"))))}));function N(e){let{title:t,description:a,pixelKey:n,fieldDescription:r,fieldHelp:i,pixelValueType:c}=e;const o=`/newspack/v1/wizard/newspack-engagement-wizard/social/${n}_pixel`,[p,m]=(0,s.useState)(!1),[u,w]=(0,s.useState)(null),[g,h]=(0,s.useState)(null);if((0,s.useEffect)((()=>{(async()=>{m(!0);try{const e=await d()({path:o});h(e)}catch(e){h(null)}m(!1)})()}),[]),!g)return null;const k=[{key:"pixel_id",type:c,description:r,help:i,value:g.pixel_id}];return(0,s.createElement)(l.d5.Section,{error:u,disabled:p,sectionKey:"pixel-settings",title:t,description:a,active:g.active,fields:k,onUpdate:async e=>{w(null),m(!0);try{const t=await d()({path:o,method:"POST",data:{...g,...e}});h(t)}catch(e){w(e)}m(!1)},onChange:(e,t)=>{h({...g,[e]:t})}})}const j=i.__;var R=()=>(0,s.createElement)(N,{title:j("Meta Pixel","newspack"),pixelKey:"meta",pixelValueType:"integer",description:j("Add the Meta pixel (formely known as Facebook pixel) to your site.","newspack"),fieldDescription:j("Pixel ID","newspack"),fieldHelp:(0,s.createInterpolateElement)(j("The Meta Pixel ID. You only need to add the number, not the full code. Example: 123456789123456789. You can get this information <linkToFb>here</linkToFb>.","newspack"),{linkToFb:(0,s.createElement)("a",{href:"https://www.facebook.com/ads/manager/pixel/facebook_pixel",target:"_blank",rel:"noopener noreferrer"})})});const $=i.__;var Z=()=>(0,s.createElement)(N,{title:$("Twitter Pixel","newspack"),pixelKey:"twitter",description:$("Add the Twitter pixel to your site.","newspack"),pixelValueType:"text",fieldDescription:$("Pixel ID","newspack"),fieldHelp:(0,s.createInterpolateElement)($("The Twitter Pixel ID. You only need to add the ID, not the full code. Example: ny3ad. You can read more about it <link>here</link>.","newspack"),{link:(0,s.createElement)("a",{href:"https://business.twitter.com/en/help/campaign-measurement-and-analytics/conversion-tracking-for-websites.html",target:"_blank",rel:"noopener noreferrer"})})});const q=i.__;class I extends s.Component{render(){return(0,s.createElement)(s.Fragment,null,(0,s.createElement)(l.fM,{title:q("Publicize"),badge:"Jetpack",description:q("Publicize makes it easy to share your site’s posts on several social media networks automatically when you publish a new post."),actionText:q("Configure"),handoff:"jetpack",editLink:"admin.php?page=jetpack#/sharing"}),(0,s.createElement)(R,null),(0,s.createElement)(Z,null))}}var B=(0,l.a4)(I);const H=i.__;class U extends s.Component{render(){const{onChange:e,relatedPostsEnabled:t,relatedPostsError:a,relatedPostsMaxAge:n}=this.props;return(0,s.createElement)(s.Fragment,null,a&&(0,s.createElement)(l.qX,{noticeText:a,isError:!0}),(0,s.createElement)(l.fM,{title:H("Related Posts","newspack"),badge:"Jetpack",description:H("Automatically add related content at the bottom of each post.","newspack"),actionText:H("Configure"),handoff:"jetpack",editLink:"admin.php?page=jetpack#/traffic"}),t&&(0,s.createElement)(l.rj,null,(0,s.createElement)(l.Zb,{noBorder:!0},(0,s.createElement)(l.w4,{help:H("If set, posts will be shown as related content only if published within the past number of months. If 0, any published post can be shown, regardless of publish date.","newspack"),label:H("Maximum age of related content, in months","newspack"),onChange:t=>e(t),placeholder:H("Maximum age of related content, in months"),type:"number",value:n||0}))))}}var D=(0,l.a4)(U);const X=i.__;var Y=(0,l.a4)((()=>(0,s.createElement)(s.Fragment,null,(0,s.createElement)(l.fM,{title:X("WordPress Commenting"),description:X("Native WordPress commenting system."),actionText:X("Configure"),handoff:"wordpress-settings-discussion"}))));const W=i.__,{HashRouter:J,Redirect:V,Route:G,Switch:K}=c.Z;class Q extends s.Component{constructor(e){super(e),(0,r.Z)(this,"onWizardReady",(()=>{const{setError:e,wizardApiFetch:t}=this.props;return t({path:"/newspack/v1/wizard/newspack-engagement-wizard/related-content"}).then((e=>this.setState(e))).catch((t=>e(t)))})),(0,r.Z)(this,"updatedRelatedContentSettings",(async()=>{const{wizardApiFetch:e}=this.props,{relatedPostsMaxAge:t}=this.state;try{await e({path:"/newspack/v1/wizard/newspack-engagement-wizard/related-posts-max-age",method:"POST",data:{relatedPostsMaxAge:t}}),this.setState({relatedPostsError:null,relatedPostsUpdated:!1})}catch(e){this.setState({relatedPostsError:e.message||W("There was an error saving settings. Please try again.","newspack")})}})),this.state={relatedPostsEnabled:!1,relatedPostsMaxAge:0,relatedPostsUpdated:!1,relatedPostsError:null}}render(){const{pluginRequirements:e}=this.props,{relatedPostsEnabled:t,relatedPostsError:a,relatedPostsMaxAge:r,relatedPostsUpdated:i}=this.state,l=newspack_engagement_wizard.has_reader_activation?"/reader-activation":"/newsletters",c=[{label:W("Newsletters","newspack"),path:"/newsletters",exact:!0},{label:W("Commenting","newspack"),path:"/commenting"},{label:W("Social","newspack"),path:"/social",exact:!0},{label:W("Recirculation","newspack"),path:"/recirculation"}];newspack_engagement_wizard.has_reader_activation&&c.unshift({label:W("Reader Activation","newspack"),path:"/reader-activation",exact:!0,activeTabPaths:["/reader-activation","/reader-activation/campaign","/reader-activation/complete"]});const o={headerText:W("Engagement","newspack"),tabbedNavigation:c};return(0,s.createElement)(s.Fragment,null,(0,s.createElement)(J,{hashType:"slash"},(0,s.createElement)(K,null,e,newspack_engagement_wizard.has_reader_activation&&(0,s.createElement)(G,{path:"/reader-activation",exact:!0,render:()=>(0,s.createElement)(x,(0,n.Z)({subHeaderText:W("Configure your reader activation settings","newspack")},o))}),newspack_engagement_wizard.has_reader_activation&&(0,s.createElement)(G,{path:"/reader-activation/campaign",render:()=>(0,s.createElement)(T,(0,n.Z)({subHeaderText:W("Preview and customize the reader activation prompts","newspack")},o))}),newspack_engagement_wizard.has_reader_activation&&(0,s.createElement)(G,{path:"/reader-activation/complete",render:()=>(0,s.createElement)(O,(0,n.Z)({subHeaderText:W("Preview and customize the reader activation prompts","newspack")},o))}),(0,s.createElement)(G,{path:"/newsletters",render:()=>(0,s.createElement)(k,(0,n.Z)({subHeaderText:W("Configure your newsletter settings","newspack")},o))}),(0,s.createElement)(G,{path:"/social",exact:!0,render:()=>(0,s.createElement)(B,(0,n.Z)({subHeaderText:W("Share your content to social media","newspack")},o))}),(0,s.createElement)(G,{path:"/commenting",exact:!0,render:()=>(0,s.createElement)(Y,(0,n.Z)({subHeaderText:W("Set up the commenting system for your site","newspack")},o))}),(0,s.createElement)(G,{path:"/recirculation",exact:!0,render:()=>(0,s.createElement)(D,(0,n.Z)({},o,{subHeaderText:W("Engage visitors with related content","newspack"),relatedPostsEnabled:t,relatedPostsError:a,buttonAction:()=>this.updatedRelatedContentSettings(),buttonText:W("Save Settings","newspack"),buttonDisabled:!t||!i,relatedPostsMaxAge:r,onChange:e=>{this.setState({relatedPostsMaxAge:e,relatedPostsUpdated:!0})}}))}),(0,s.createElement)(V,{to:l}))))}}(0,s.render)((0,s.createElement)((0,l.uF)(Q,["jetpack"])),document.getElementById("newspack-engagement-wizard"))},9196:function(e){e.exports=window.React},2819:function(e){e.exports=window.lodash},6292:function(e){e.exports=window.moment},6989:function(e){e.exports=window.wp.apiFetch},5609:function(e){e.exports=window.wp.components},9818:function(e){e.exports=window.wp.data},9307:function(e){e.exports=window.wp.element},2694:function(e){e.exports=window.wp.hooks},2629:function(e){e.exports=window.wp.htmlEntities},5736:function(e){e.exports=window.wp.i18n},9630:function(e){e.exports=window.wp.keycodes},444:function(e){e.exports=window.wp.primitives},6483:function(e){e.exports=window.wp.url}},a={};function n(e){var r=a[e];if(void 0!==r)return r.exports;var s=a[e]={exports:{}};return t[e].call(s.exports,s,s.exports,n),s.exports}n.m=t,e=[],n.O=function(t,a,r,s){if(!a){var i=1/0;for(p=0;p<e.length;p++){a=e[p][0],r=e[p][1],s=e[p][2];for(var l=!0,c=0;c<a.length;c++)(!1&s||i>=s)&&Object.keys(n.O).every((function(e){return n.O[e](a[c])}))?a.splice(c--,1):(l=!1,s<i&&(i=s));if(l){e.splice(p--,1);var o=r();void 0!==o&&(t=o)}}return t}s=s||0;for(var p=e.length;p>0&&e[p-1][2]>s;p--)e[p]=e[p-1];e[p]=[a,r,s]},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,{a:t}),t},n.d=function(e,t){for(var a in t)n.o(t,a)&&!n.o(e,a)&&Object.defineProperty(e,a,{enumerable:!0,get:t[a]})},n.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(e){if("object"==typeof window)return window}}(),n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.j=103,function(){var e;n.g.importScripts&&(e=n.g.location+"");var t=n.g.document;if(!e&&t&&(t.currentScript&&(e=t.currentScript.src),!e)){var a=t.getElementsByTagName("script");a.length&&(e=a[a.length-1].src)}if(!e)throw new Error("Automatic publicPath is not supported in this browser");e=e.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/"),n.p=e}(),function(){var e={103:0};n.O.j=function(t){return 0===e[t]};var t=function(t,a){var r,s,i=a[0],l=a[1],c=a[2],o=0;if(i.some((function(t){return 0!==e[t]}))){for(r in l)n.o(l,r)&&(n.m[r]=l[r]);if(c)var p=c(n)}for(t&&t(a);o<i.length;o++)s=i[o],n.o(e,s)&&e[s]&&e[s][0](),e[i[o]]=0;return n.O(p)},a=self.webpackChunkwebpack=self.webpackChunkwebpack||[];a.forEach(t.bind(null,0)),a.push=t.bind(null,a.push.bind(a))}();var r=n.O(void 0,[351],(function(){return n(864)}));r=n.O(r);var s=window;for(var i in r)s[i]=r[i];r.__esModule&&Object.defineProperty(s,"__esModule",{value:!0})}();