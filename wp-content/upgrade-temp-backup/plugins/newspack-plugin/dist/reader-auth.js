(()=>{"use strict";var e={};(e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})})(e);const t=["signin_modal","register_modal"];let a;window.newspackRAS=window.newspackRAS||[],window.newspackRAS.push((function(e){var n;n=function(){const n=[...document.querySelectorAll(".newspack-reader-auth")],o=[...document.querySelectorAll(".woocommerce-message")];if(!n.length)return;let r,l;const i=function(){r=document.querySelectorAll(".newspack-reader__account-link"),l=document.querySelectorAll(`[data-newspack-reader-account-link],[href="${newspack_ras_config.account_url}"]`),l.forEach((e=>{e.addEventListener("click",d)}))},c=function(e){a=window.location.hash.replace("#",""),t.includes(a)&&(e&&e.preventDefault(),d())};function s(){const t=[...document.querySelectorAll(".newspack-reader-auth")];t.length&&t.forEach((t=>{const a=t.querySelector("form"),n=t.querySelector('input[name="npe"]'),o=t.querySelector('input[name="redirect"]'),l=e.getReader();if(n&&(n.value=l?.email||""),r?.length&&r.forEach((e=>{l?.email&&!l?.authenticated?(e.setAttribute("data-redirect",e.getAttribute("href")),o.value=e.getAttribute("href")):e.removeAttribute("data-redirect");try{const t=JSON.parse(e.getAttribute("data-labels"));e.querySelector(".newspack-reader__account-link__label").textContent=l?.email?t.signedin:t.signedout}catch{}})),l?.authenticated){const e=t.querySelector(".newspack-reader__auth-form__response__content");e&&a&&a.replaceWith(e.parentNode)}}))}function d(t){const a=e.getReader();if(a?.authenticated)return;const n=document.querySelector(".newspack-reader-auth:not(.newspack-reader__auth-form__inline)");if(!n)return;t&&t.preventDefault();const o=n.querySelector("[data-has-auth-link]"),r=n.querySelector('input[name="npe"]'),l=n.querySelector('input[name="redirect"]'),i=n.querySelector('input[name="password"]'),c=n.querySelector('input[name="action"]');o&&(e.hasAuthLink()?o.style.display="flex":o.style.display="none"),r&&(r.value=a?.email||""),l&&t?.target?.getAttribute("data-redirect")&&(l.value=t.target.getAttribute("data-redirect")),n.hidden=!1,n.style.display="flex",document.body.classList.add("newspack-signin"),i&&r?.value&&"pwd"===c?.value?i.focus():r.focus(),n.overlayId=e.overlays.add()}window.addEventListener("hashchange",c),c(),i(),setTimeout(i,1e3),e.on("reader",s),s(),n.forEach((n=>{const r=n.querySelector("form");if(!r)return;let l;r.getAttribute("action-xhr")?(r.removeAttribute("action-xhr"),l=r.cloneNode(!0),r.replaceWith(l)):l=r;const i=l.querySelector('input[name="action"]'),c=l.querySelector('input[name="npe"]'),s=l.querySelector('input[name="otp_code"]'),d=l.querySelector('input[name="password"]'),u=l.querySelectorAll('[type="submit"]'),p=n.querySelector("button[data-close]");p&&p.addEventListener("click",(function(a){a.preventDefault(),n.classList.remove("newspack-reader__auth-form__visible"),n.style.display="none",document.body.classList.remove("newspack-signin"),t.includes(window.location.hash.replace("#",""))&&history.pushState("",document.title,window.location.pathname+window.location.search),n.overlayId&&e.overlays.remove(n.overlayId)}));const g=n.querySelector(".newspack-reader__auth-form__response__content");function h(t,a=!1){if("otp"!==t||e.getOTPHash()){["link","pwd"].includes(t)&&e.setAuthStrategy(t),i.value=t,n.removeAttribute("data-form-status"),g.innerHTML="",n.querySelectorAll("[data-action]").forEach((e=>{"none"!==e.style.display&&(e.prevDisplay=e.style.display),e.style.display="none"})),n.querySelectorAll('[data-action~="'+t+'"]').forEach((e=>{e.style.display=e.prevDisplay}));try{const e=JSON.parse(n.getAttribute("data-labels")),a="register"===t?e.register:e.signin;n.querySelector("h2").textContent=a}catch{}a&&("pwd"===t&&c.value?d.focus():"otp"===t?s.focus():c.focus())}}n.querySelector("[data-has-auth-link]").hidden=!0,h("register_modal"===a?"register":e.getAuthStrategy()||"link"),window.addEventListener("hashchange",(()=>{t.includes(a)&&h("register_modal"===a?"register":e.getAuthStrategy()||"link")})),e.on("reader",(()=>{e.getOTPHash()&&h("otp")})),n.querySelectorAll("[data-set-action]").forEach((e=>{e.addEventListener("click",(function(e){e.preventDefault(),h(e.target.getAttribute("data-set-action"),!0)}))})),l.startLoginFlow=()=>{n.removeAttribute("data-form-status"),u.forEach((e=>{e.disabled=!0})),g.innerHTML="",l.style.opacity=.5},l.endLoginFlow=(t=null,a=500,o=null,r)=>{if(t){const e=document.createElement("p");e.textContent=t,g.appendChild(e)}if(200===a&&o){const t=!!o?.authenticated;e.setReaderEmail(o.email),e.setAuthenticated(t),t?r&&(window.location=r):l.replaceWith(g.parentNode)}n.setAttribute("data-form-status",a),l.style.opacity=1,u.forEach((e=>{e.disabled=!1}))},l.addEventListener("submit",(t=>{t.preventDefault(),l.startLoginFlow(),0<o.length&&o.forEach((e=>e.style.display="none"));const r=l.action?.value;return l.npe?.value?"pwd"!==r||l.password?.value?void e.getCaptchaToken().then((e=>{if(!e)return;let t=l.captcha_token;t||(t=document.createElement("input"),t.setAttribute("type","hidden"),t.setAttribute("name","captcha_token"),t.setAttribute("autocomplete","off"),l.appendChild(t)),t.value=e})).catch((e=>{l.endLoginFlow(e,400)})).finally((()=>{const o=new FormData(t.target);if(!o.has("npe")||!o.get("npe"))return l.endFlow(newspack_reader_auth_labels.invalid_email,400);"otp"===r?e.authenticateOTP(o.get("otp_code")).then((e=>{l.endLoginFlow(e.message,200,e,a?"":o.get("redirect"))})).catch((e=>{e.expired&&h("link"),l.endLoginFlow(e.message,400)})):fetch(l.getAttribute("action")||window.location.pathname,{method:"POST",headers:{Accept:"application/json"},body:o}).then((t=>{n.setAttribute("data-form-status",t.status),t.json().then((({message:n,data:i})=>{let c=t.status,s=o.get("redirect");"register"===r&&(s=newspack_ras_config.account_url),a&&(s=""),200===c&&e.setReaderEmail(o.get("npe")),e.getOTPHash()&&["register","link"].includes(r)&&(200===c&&h("otp"),200===c&&"link"===r&&(c=null,n=null)),l.endLoginFlow(n,c,i,s)})).catch((()=>{l.endLoginFlow()}))})).catch((()=>{l.endLoginFlow()}))})):l.endLoginFlow(newspack_reader_auth_labels.invalid_password,400):l.endLoginFlow(newspack_reader_auth_labels.invalid_email,400)}))})),document.querySelectorAll('input[name="otp_code"]').forEach((e=>{const t=parseInt(e.getAttribute("maxlength"));if(!t)return;const a=e.parentNode;a.removeChild(e);const n=[],o=document.createElement("input");o.setAttribute("type","hidden"),o.setAttribute("name","otp_code"),a.appendChild(o);for(let e=0;e<t;e++){const r=document.createElement("input");r.setAttribute("type","text"),r.setAttribute("maxlength","1"),r.setAttribute("pattern","[0-9]"),r.setAttribute("autocomplete","off"),r.setAttribute("inputmode","numeric"),r.setAttribute("data-index",e),r.addEventListener("keydown",(t=>{const r=a.querySelector(`[data-index="${e-1}"]`),l=a.querySelector(`[data-index="${e+1}"]`);switch(t.key){case"Backspace":t.preventDefault(),t.target.value="",r&&r.focus(),n[e]="",o.value=n.join("");break;case"ArrowLeft":t.preventDefault(),r&&r.focus();break;case"ArrowRight":t.preventDefault(),l&&l.focus();break;default:t.key.match(/^[0-9]$/)&&(t.preventDefault(),t.target.value=t.key,t.target.dispatchEvent(new Event("input",{bubbles:!0,cancelable:!0})),l&&l.focus())}})),r.addEventListener("input",(t=>{t.target.value.match(/^[0-9]$/)?n[e]=t.target.value:t.target.value="",o.value=n.join("")})),r.addEventListener("paste",(e=>{e.preventDefault();const r=(e.clipboardData||window.clipboardData).getData("text");if(r.length===t){for(let e=0;e<t;e++)r[e].match(/^[0-9]$/)&&(a.querySelector(`[data-index="${e}"]`).value=r[e],n[e]=r[e]);o.value=n.join("")}})),a.appendChild(r)}})),[...document.querySelectorAll(".newspack-reader__logins")].forEach((e=>{e.classList.remove("newspack-reader__logins--disabled")})),document.querySelectorAll(".newspack-reader__logins__google").forEach((e=>{const t=e.closest("form"),a=t.querySelector('input[name="redirect"]');e.addEventListener("click",(()=>{t?.startLoginFlow&&t.startLoginFlow();const e=t?((e,t=[])=>Array.from(e.entries()).reduce(((e,[a,n])=>t.includes(a)?(a.indexOf("[]")>-1?(e[a=a.replace("[]","")]=e[a]||[],e[a].push(n)):e[a]=n,e):e),{}))(new FormData(t),["lists[]"]):{};e.current_page_url=window.location.href;const n=window.open("about:blank","newspack_google_login","width=500,height=600");fetch("/wp-json/newspack/v1/login/google").then((e=>e.json().then((t=>Promise.resolve({data:t,status:e.status}))))).then((({data:o,status:r})=>{if(200!==r)n&&n.close(),t?.endLoginFlow&&t.endLoginFlow(o.message,r);else if(n){n.location=o;const r=setInterval((()=>{n.closed&&((e=>{fetch(`/wp-json/newspack/v1/login/google/register?metadata=${JSON.stringify(e)}`).then((e=>{e.json().then((({message:n,data:o})=>{const r=a?.value||null;t?.endLoginFlow&&t.endLoginFlow(n,e.status,o,r)})).catch((a=>{t?.endLoginFlow&&t.endLoginFlow(a?.message,e.status)}))})).catch((e=>{t?.endLoginFlow&&t.endLoginFlow(e?.message)}))})(e),clearInterval(r))}),500)}else t?.endLoginFlow&&t.endLoginFlow(newspack_reader_auth_labels.blocked_popup)})).catch((e=>{console.log(e),t?.endLoginFlow&&t.endLoginFlow(e?.message,400),n&&n.close()}))}))}))},"undefined"!=typeof document&&("complete"!==document.readyState&&"interactive"!==document.readyState?document.addEventListener("DOMContentLoaded",n):n())}));var n=window;for(var o in e)n[o]=e[o];e.__esModule&&Object.defineProperty(n,"__esModule",{value:!0})})();