!function(){"use strict";var e,t={7783:function(e,t,n){n.r(t);var a=n(4942),r=(n(5674),n(9307)),i=n(5736),s=n(7718),c=n(8614);const o=i.__;class p extends r.Component{render(){const{unsupportedPlugins:e,missingPlugins:t,deactivateAllPlugins:n}=this.props;return(0,r.createElement)(s.rj,{columns:1,gutter:64},t.length?(0,r.createElement)(s.rj,{columns:1,gutter:16},(0,r.createElement)(s.qX,{noticeText:o("These plugins shoud be active:","newspack"),isWarning:!0}),(0,r.createElement)(s.xf,{plugins:t})):null,e.length?(0,r.createElement)(s.rj,{columns:1,gutter:16},(0,r.createElement)(s.qX,{noticeText:o("Newspack does not support these plugins:","newspack"),isError:!0}),e.map((e=>(0,r.createElement)(s.fM,{title:e.Name,key:e.Slug,description:e.Description,className:"newspack-card__is-unsupported"}))),(0,r.createElement)("div",{className:"newspack-buttons-card"},(0,r.createElement)(s.zx,{isPrimary:!0,onClick:n},o("Deactivate All","newspack")))):(0,r.createElement)(s.qX,{noticeText:o("No unsupported plugins found.","newspack"),isSuccess:!0}))}}var u=(0,s.a4)(p);const l=i.__;class d extends r.Component{render(){const{configurationStatus:e,missingPlugins:t,hasData:n,repairConfiguration:a}=this.props,{amp:i,jetpack:c,sitekit:o}=e||{},p=-1!==t.indexOf("amp");return n&&(0,r.createElement)(r.Fragment,null,(0,r.createElement)(s.fM,{className:i?"newspack-card__is-supported":"newspack-card__is-unsupported",title:l("AMP","newspack"),description:l(p?"AMP plugin is not active.":i?"AMP plugin is in standard mode.":"AMP plugin is not in standard mode. ","newspack"),actionText:!p&&!i&&l("Repair","newspack"),onClick:()=>a("amp")}),(0,r.createElement)(s.fM,{className:c?"newspack-card__is-supported":"newspack-card__is-unsupported",title:l("Jetpack","newspack"),description:l(c?"Jetpack is connected.":"Jetpack is not connected. ","newspack"),actionText:!c&&l("Connect","newspack"),handoff:"jetpack"}),(0,r.createElement)(s.fM,{className:o?"newspack-card__is-supported":"newspack-card__is-unsupported",title:l("Google Site Kit","newspack"),description:l(o?"Site Kit is connected.":"Site Kit is not connected. ","newspack"),actionText:!o&&l("Connect","newspack"),handoff:"google-site-kit"}))}}var h=(0,s.a4)(d);const w=i.__,{HashRouter:f,Redirect:g,Route:m,Switch:k}=c.Z;class v extends r.Component{constructor(e){super(e),(0,a.Z)(this,"onWizardReady",(()=>{this.fetchHealthData()})),(0,a.Z)(this,"fetchHealthData",(()=>{const{wizardApiFetch:e,setError:t}=this.props;e({path:"/newspack/v1/wizard/newspack-health-check-wizard/"}).then((e=>this.setState({healthCheckData:e,hasData:!0}))).catch((e=>{t(e)}))})),(0,a.Z)(this,"deactivateAllPlugins",(()=>{const{wizardApiFetch:e,setError:t}=this.props;e({path:"/newspack/v1/wizard/newspack-health-check-wizard/unsupported_plugins",method:"delete"}).then((e=>this.setState({healthCheckData:e}))).catch((e=>{t(e)}))})),(0,a.Z)(this,"repairConfiguration",(e=>{const{wizardApiFetch:t,setError:n}=this.props;t({path:"/newspack/v1/wizard/newspack-health-check-wizard/repair/"+e}).then((e=>this.setState({healthCheckData:e}))).catch((e=>{n(e)}))})),this.state={hasData:!1,healthCheckData:{unsupported_plugins:{},missing_plugins:{}}}}render(){const{hasData:e,healthCheckData:t}=this.state,{unsupported_plugins:n,missing_plugins:a,configuration_status:i}=t,s=[{label:w("Plugins","newspack"),path:"/",exact:!0},{label:w("Configuration"),path:"/configuration"}];return(0,r.createElement)(r.Fragment,null,(0,r.createElement)(f,{hashType:"slash"},(0,r.createElement)(k,null,(0,r.createElement)(m,{path:"/",exact:!0,render:()=>(0,r.createElement)(u,{headerText:w("Health Check","newspack"),subHeaderText:w("Verify and correct site health issues","newspack"),deactivateAllPlugins:this.deactivateAllPlugins,tabbedNavigation:s,missingPlugins:Object.keys(a),unsupportedPlugins:Object.keys(n).map((e=>({...n[e],Slug:e})))})}),(0,r.createElement)(m,{path:"/configuration",exact:!0,render:()=>(0,r.createElement)(h,{hasData:e,headerText:w("Health Check","newspack"),subHeaderText:w("Verify and correct site health issues","newspack"),tabbedNavigation:s,configurationStatus:i,missingPlugins:Object.keys(a),repairConfiguration:this.repairConfiguration})}),(0,r.createElement)(g,{to:"/"}))))}}(0,r.render)((0,r.createElement)((0,s.uF)(v)),document.getElementById("newspack-health-check-wizard"))},9196:function(e){e.exports=window.React},2819:function(e){e.exports=window.lodash},6292:function(e){e.exports=window.moment},6989:function(e){e.exports=window.wp.apiFetch},5609:function(e){e.exports=window.wp.components},9818:function(e){e.exports=window.wp.data},9307:function(e){e.exports=window.wp.element},2694:function(e){e.exports=window.wp.hooks},2629:function(e){e.exports=window.wp.htmlEntities},5736:function(e){e.exports=window.wp.i18n},9630:function(e){e.exports=window.wp.keycodes},444:function(e){e.exports=window.wp.primitives},6483:function(e){e.exports=window.wp.url}},n={};function a(e){var r=n[e];if(void 0!==r)return r.exports;var i=n[e]={exports:{}};return t[e].call(i.exports,i,i.exports,a),i.exports}a.m=t,e=[],a.O=function(t,n,r,i){if(!n){var s=1/0;for(u=0;u<e.length;u++){n=e[u][0],r=e[u][1],i=e[u][2];for(var c=!0,o=0;o<n.length;o++)(!1&i||s>=i)&&Object.keys(a.O).every((function(e){return a.O[e](n[o])}))?n.splice(o--,1):(c=!1,i<s&&(s=i));if(c){e.splice(u--,1);var p=r();void 0!==p&&(t=p)}}return t}i=i||0;for(var u=e.length;u>0&&e[u-1][2]>i;u--)e[u]=e[u-1];e[u]=[n,r,i]},a.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return a.d(t,{a:t}),t},a.d=function(e,t){for(var n in t)a.o(t,n)&&!a.o(e,n)&&Object.defineProperty(e,n,{enumerable:!0,get:t[n]})},a.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(e){if("object"==typeof window)return window}}(),a.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},a.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},a.j=619,function(){var e;a.g.importScripts&&(e=a.g.location+"");var t=a.g.document;if(!e&&t&&(t.currentScript&&(e=t.currentScript.src),!e)){var n=t.getElementsByTagName("script");n.length&&(e=n[n.length-1].src)}if(!e)throw new Error("Automatic publicPath is not supported in this browser");e=e.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/"),a.p=e}(),function(){var e={619:0};a.O.j=function(t){return 0===e[t]};var t=function(t,n){var r,i,s=n[0],c=n[1],o=n[2],p=0;if(s.some((function(t){return 0!==e[t]}))){for(r in c)a.o(c,r)&&(a.m[r]=c[r]);if(o)var u=o(a)}for(t&&t(n);p<s.length;p++)i=s[p],a.o(e,i)&&e[i]&&e[i][0](),e[s[p]]=0;return a.O(u)},n=self.webpackChunkwebpack=self.webpackChunkwebpack||[];n.forEach(t.bind(null,0)),n.push=t.bind(null,n.push.bind(n))}();var r=a.O(void 0,[351],(function(){return a(7783)}));r=a.O(r);var i=window;for(var s in r)i[s]=r[s];r.__esModule&&Object.defineProperty(i,"__esModule",{value:!0})}();