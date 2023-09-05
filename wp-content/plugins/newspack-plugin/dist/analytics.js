(()=>{"use strict";var e,t={8598:(e,t,a)=>{a.r(t),a(5674);var n=a(9307),r=a(5736),s=a(1383),o=a(9674);const i=r.__;class c extends n.Component{render(){return(0,n.createElement)(n.Fragment,null,(0,n.createElement)(s.fM,{title:i("Google Analytics"),description:i("Configure and view site analytics"),actionText:i("View"),handoff:"google-site-kit",editLink:newspack_analytics_wizard_data.analyticsConnectionError?void 0:"admin.php?page=googlesitekit-module-analytics"}))}}const l=(0,s.a4)(c);var d=a(4942);const p=r.__;class u extends n.Component{constructor(){super(...arguments),(0,d.Z)(this,"state",{ga4Credendials:newspack_analytics_wizard_data.ga4_credentials,error:!1}),(0,d.Z)(this,"handleAPIError",(e=>{let{message:t}=e;return this.setState({error:t})})),(0,d.Z)(this,"updateGa4Credentials",(()=>{const{wizardApiFetch:e}=this.props;e({path:"/newspack/v1/wizard/analytics/ga4-credentials",method:"POST",quiet:!0,data:{measurement_id:this.state.ga4Credendials.measurement_id,measurement_protocol_secret:this.state.ga4Credendials.measurement_protocol_secret}}).then((e=>this.setState({ga4Credendials:e,error:!1}))).catch(this.handleAPIError)}))}render(){const{error:e,ga4Credendials:t}=this.state,{isLoading:a}=this.props;return(0,n.createElement)("div",{className:"newspack__analytics-configuration"},(0,n.createElement)("div",{className:"newspack__analytics-configuration__header"},(0,n.createElement)(s.M$,{title:p("Activate Newspack Custom Events","newspack"),description:p("Allows Newspack to send enhanced custom event data to your Google Analytics.","newspack"),noMargin:!0}),(0,n.createElement)("p",null,p("Newspack already sends some custom event data to your GA account, but adding the credentials below enables enhanced events that are fired from your site's backend. For example, when a donation is confirmed or when a user successfully subscribes to a newsletter.","newspack"))),e&&(0,n.createElement)(s.qX,{isError:!0,noticeText:e}),(0,n.createElement)(s.rj,{noMargin:!0,rowGap:16},(0,n.createElement)(s.w4,{value:t?.measurement_id,label:p("Measurement ID","newspack"),help:p("You can find this in Site Kit Settings, or in Google Analytics > Admin > Data Streams and clickng the data stream. Example: G-ABCD1234","newspack"),onChange:e=>this.setState({...this.state,ga4Credendials:{...t,measurement_id:e}}),disabled:a,autoComplete:"off"}),(0,n.createElement)(s.w4,{type:"password",value:t?.measurement_protocol_secret,label:p("Measurement Protocol API Secret","newspack"),help:p('Generate an API secret from your GA dashboard in Admin > Data Streams and opening your data stream. Select "Measurement Protocol API secrets" under the Events section. Create a new secret.',"newspack"),onChange:e=>this.setState({...this.state,ga4Credendials:{...t,measurement_protocol_secret:e}}),disabled:a,autoComplete:"off"})),(0,n.createElement)(s.zx,{className:"newspack__analytics-newspack-custom-events__save-button",variant:"primary",disabled:a,onClick:this.updateGa4Credentials},p("Save","newspack")))}}const m=(0,s.a4)(u),w=r.__,{HashRouter:h,Redirect:g,Route:v,Switch:f}=o.Z,y=[{label:w("Plugins","newspack"),path:"/",exact:!0},{label:w("Newspack Custom Events","newspack"),path:"/newspack-custom-events"}];class _ extends n.Component{render(){const{pluginRequirements:e,wizardApiFetch:t,isLoading:a}=this.props,r={headerText:w("Analytics","newspack"),subHeaderText:w("Manage Google Analytics Configuration","newspack"),tabbedNavigation:y,wizardApiFetch:t,isLoading:a};return(0,n.createElement)(n.Fragment,null,(0,n.createElement)(h,{hashType:"slash"},(0,n.createElement)(f,null,e,(0,n.createElement)(v,{path:"/newspack-custom-events",exact:!0,render:()=>(0,n.createElement)(m,r)}),(0,n.createElement)(v,{path:"/",exact:!0,render:()=>(0,n.createElement)(l,r)}),(0,n.createElement)(g,{to:"/"}))))}}(0,n.render)((0,n.createElement)((0,s.uF)(_,["google-site-kit"])),document.getElementById("newspack-analytics-wizard"))},9196:e=>{e.exports=window.React},6292:e=>{e.exports=window.moment},6989:e=>{e.exports=window.wp.apiFetch},5609:e=>{e.exports=window.wp.components},9818:e=>{e.exports=window.wp.data},9307:e=>{e.exports=window.wp.element},2694:e=>{e.exports=window.wp.hooks},2629:e=>{e.exports=window.wp.htmlEntities},5736:e=>{e.exports=window.wp.i18n},9630:e=>{e.exports=window.wp.keycodes},444:e=>{e.exports=window.wp.primitives},6483:e=>{e.exports=window.wp.url}},a={};function n(e){var r=a[e];if(void 0!==r)return r.exports;var s=a[e]={id:e,loaded:!1,exports:{}};return t[e].call(s.exports,s,s.exports,n),s.loaded=!0,s.exports}n.m=t,e=[],n.O=(t,a,r,s)=>{if(!a){var o=1/0;for(d=0;d<e.length;d++){a=e[d][0],r=e[d][1],s=e[d][2];for(var i=!0,c=0;c<a.length;c++)(!1&s||o>=s)&&Object.keys(n.O).every((e=>n.O[e](a[c])))?a.splice(c--,1):(i=!1,s<o&&(o=s));if(i){e.splice(d--,1);var l=r();void 0!==l&&(t=l)}}return t}s=s||0;for(var d=e.length;d>0&&e[d-1][2]>s;d--)e[d]=e[d-1];e[d]=[a,r,s]},n.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return n.d(t,{a:t}),t},n.d=(e,t)=>{for(var a in t)n.o(t,a)&&!n.o(e,a)&&Object.defineProperty(e,a,{enumerable:!0,get:t[a]})},n.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(e){if("object"==typeof window)return window}}(),n.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),n.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.nmd=e=>(e.paths=[],e.children||(e.children=[]),e),n.j=142,(()=>{var e;n.g.importScripts&&(e=n.g.location+"");var t=n.g.document;if(!e&&t&&(t.currentScript&&(e=t.currentScript.src),!e)){var a=t.getElementsByTagName("script");a.length&&(e=a[a.length-1].src)}if(!e)throw new Error("Automatic publicPath is not supported in this browser");e=e.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/"),n.p=e})(),(()=>{var e={142:0};n.O.j=t=>0===e[t];var t=(t,a)=>{var r,s,o=a[0],i=a[1],c=a[2],l=0;if(o.some((t=>0!==e[t]))){for(r in i)n.o(i,r)&&(n.m[r]=i[r]);if(c)var d=c(n)}for(t&&t(a);l<o.length;l++)s=o[l],n.o(e,s)&&e[s]&&e[s][0](),e[o[l]]=0;return n.O(d)},a=self.webpackChunkwebpack=self.webpackChunkwebpack||[];a.forEach(t.bind(null,0)),a.push=t.bind(null,a.push.bind(a))})();var r=n.O(void 0,[351],(()=>n(8598)));r=n.O(r);var s=window;for(var o in r)s[o]=r[o];r.__esModule&&Object.defineProperty(s,"__esModule",{value:!0})})();