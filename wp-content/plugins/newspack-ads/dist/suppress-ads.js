(()=>{"use strict";const s=window.wp.plugins,e=window.wp.editPost,n=window.wp.i18n,p=window.wp.components,t=window.wp.element,a=window.wp.compose,d=window.wp.data,c=window.ReactJSXRuntime;class o extends t.Component{render(){const s=window.newspackAdsSuppressAds?.placements||{},{newspack_ads_suppress_ads:a,newspack_ads_suppress_ads_placements:d,updateSuppressAds:o,updateSuppressPlacements:i}=this.props;return(0,c.jsxs)(e.PluginDocumentSettingPanel,{name:"newspack-ad-free",title:(0,n.__)("Newspack Ads Settings","newspack-ads"),className:"newspack-subtitle",children:[(0,c.jsx)(p.ToggleControl,{label:(0,n.__)("Don't show ads on this content","newspack-ads"),checked:a,onChange:s=>{o(s)}}),!a&&(0,c.jsxs)(t.Fragment,{children:[(0,c.jsx)("p",{children:(0,n.__)("Suppress specific placements:","newspack-ads")}),Object.keys(s).map((e=>(0,c.jsx)(p.ToggleControl,{label:s[e].name,checked:d&&-1!==d.indexOf(e),onChange:()=>{const s=d?.length?[...d]:[];-1!==s.indexOf(e)?s.splice(s.indexOf(e),1):s.push(e),i(s)}},e)))]})]})}}const i=(0,a.compose)([(0,d.withSelect)((s=>{const{newspack_ads_suppress_ads:e,newspack_ads_suppress_ads_placements:n}=s("core/editor").getEditedPostAttribute("meta");return{newspack_ads_suppress_ads:e,newspack_ads_suppress_ads_placements:n}})),(0,d.withDispatch)((s=>({updateSuppressAds(e){s("core/editor").editPost({meta:{newspack_ads_suppress_ads:e}})},updateSuppressPlacements(e){s("core/editor").editPost({meta:{newspack_ads_suppress_ads_placements:e}})}})))])(o);(0,s.registerPlugin)("plugin-document-setting-panel-newspack-suppress-ads",{render:i,icon:null})})();