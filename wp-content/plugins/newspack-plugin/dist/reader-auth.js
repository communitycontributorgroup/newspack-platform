!function(){"use strict";var e,t,n={};(function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})})(n),e=window.newspackReaderActivation,t=function(){if(!e)return;const t=[...document.querySelectorAll(".newspack-reader-auth")];if(!t.length)return;let n,a,o=[],l=null;const r=function(){n=document.querySelectorAll(".newspack-reader__account-link"),a=document.querySelectorAll("[data-newspack-reader-account-link]"),a.forEach((e=>{e.addEventListener("click",c)}))};function i(){const t=[...document.querySelectorAll(".newspack-reader-auth")];t.length&&t.forEach((t=>{var a;const o=t.querySelector("form"),l=t.querySelector('input[name="npe"]'),r=t.querySelector('input[name="redirect"]'),i=e.getReader();if(l&&(l.value=(null==i?void 0:i.email)||""),null!==(a=n)&&void 0!==a&&a.length&&n.forEach((e=>{null==i||!i.email||null!=i&&i.authenticated?e.removeAttribute("data-redirect"):(e.setAttribute("data-redirect",e.getAttribute("href")),r.value=e.getAttribute("href"));try{const t=JSON.parse(e.getAttribute("data-labels"));e.querySelector(".newspack-reader__account-link__label").textContent=null!=i&&i.email?t.signedin:t.signedout}catch{}})),null!=i&&i.authenticated){const e=t.querySelector(".newspack-reader__auth-form__response__content");e&&o.replaceWith(e.parentNode)}}))}function c(t){const n=e.getReader();if(null!=n&&n.authenticated)return;const a=document.querySelector(".newspack-reader-auth:not(.newspack-reader__auth-form__inline)");if(!a)return;t.preventDefault();const r=a.querySelector("[data-has-auth-link]"),i=a.querySelector('input[name="npe"]'),c=a.querySelector('input[name="redirect"]'),s=a.querySelector('input[name="password"]'),u=a.querySelector('input[name="action"]');r&&(e.hasAuthLink()?r.style.display="flex":r.style.display="none"),i&&(i.value=(null==n?void 0:n.email)||""),c&&t.target.getAttribute("data-redirect")&&(c.value=t.target.getAttribute("data-redirect")),a.hidden=!1,a.style.display="flex",o=document.querySelectorAll(".newspack-lightbox:not([amp-access-hide])"),l=t.currentTarget.closest(".newspack-lightbox"),o.forEach((e=>e.setAttribute("amp-access-hide",""))),s&&null!=i&&i.value&&"pwd"===(null==u?void 0:u.value)?s.focus():i.focus()}r(),setTimeout(r,1e3),e.on("reader",i),i(),t.forEach((t=>{const n=t.querySelector("form");let a;n.getAttribute("action-xhr")?(n.removeAttribute("action-xhr"),a=n.cloneNode(!0),n.replaceWith(a)):a=n;const r=a.querySelector('input[name="action"]'),i=a.querySelector('input[name="npe"]'),c=a.querySelector('input[name="otp_code"]'),s=a.querySelector('input[name="password"]'),u=a.querySelectorAll('[type="submit"]'),d=t.querySelector("button[data-close]");d&&d.addEventListener("click",(function(n){n.preventDefault(),t.classList.remove("newspack-reader__auth-form__visible"),t.style.display="none",(()=>{const t=e.getReader(),n=(null==t?void 0:t.email)&&l;o.forEach((e=>{n&&l.isEqualNode(e)?e.setAttribute("amp-access-hide",""):e.removeAttribute("amp-access-hide")}))})()}));const p=t.querySelector(".newspack-reader__auth-form__response__content");function g(n){if("otp"!==n||e.getOTPHash()){["link","pwd"].includes(n)&&e.setAuthStrategy(n),r.value=n,t.removeAttribute("data-form-status"),p.innerHTML="",t.querySelectorAll("[data-action]").forEach((e=>{"none"!==e.style.display&&(e.prevDisplay=e.style.display),e.style.display="none"})),t.querySelectorAll('[data-action~="'+n+'"]').forEach((e=>{e.style.display=e.prevDisplay}));try{const e=JSON.parse(t.getAttribute("data-labels")),a="register"===n?e.register:e.signin;t.querySelector("h2").textContent=a}catch{}"pwd"===n&&i.value?s.focus():"otp"===n?c.focus():i.focus()}}t.querySelector("[data-has-auth-link]").hidden=!0,g(e.getAuthStrategy()||"pwd"),e.on("reader",(()=>{e.getOTPHash()&&g("otp")})),t.querySelectorAll("[data-set-action]").forEach((e=>{e.addEventListener("click",(function(e){e.preventDefault(),g(e.target.getAttribute("data-set-action"))}))})),a.startLoginFlow=()=>{t.removeAttribute("data-form-status"),u.forEach((e=>{e.disabled=!0})),p.innerHTML="",a.style.opacity=.5},a.endLoginFlow=function(){let n=arguments.length>0&&void 0!==arguments[0]?arguments[0]:null,o=arguments.length>1&&void 0!==arguments[1]?arguments[1]:500,l=arguments.length>2&&void 0!==arguments[2]?arguments[2]:null,r=arguments.length>3?arguments[3]:void 0;if(n){const e=document.createElement("p");e.textContent=n,p.appendChild(e)}if(200===o&&l){const t=!(null==l||!l.authenticated);e.setReaderEmail(l.email),e.setAuthenticated(t),t?r&&(window.location=r):a.replaceWith(p.parentNode)}t.setAttribute("data-form-status",o),a.style.opacity=1,u.forEach((e=>{e.disabled=!1}))},a.addEventListener("submit",(n=>{var o,l,r;n.preventDefault(),a.startLoginFlow();const i=null===(o=a.action)||void 0===o?void 0:o.value;return null!==(l=a.npe)&&void 0!==l&&l.value?"pwd"!==i||null!==(r=a.password)&&void 0!==r&&r.value?void e.getCaptchaToken().then((e=>{if(!e)return;let t=a.captcha_token;t||(t=document.createElement("input"),t.setAttribute("type","hidden"),t.setAttribute("name","captcha_token"),a.appendChild(t)),t.value=e})).catch((e=>{a.endLoginFlow(e,400)})).finally((()=>{const o=new FormData(n.target);if(!o.has("npe")||!o.get("npe"))return a.endFlow(newspack_reader_auth_labels.invalid_email,400);e.setReaderEmail(o.get("npe")),"otp"===i?e.authenticateOTP(o.get("otp_code")).then((e=>{a.endLoginFlow(e.message,200,e,o.get("redirect"))})).catch((e=>{e.expired&&g("link"),a.endLoginFlow(e.message,400)})):fetch(a.getAttribute("action")||window.location.pathname,{method:"POST",headers:{Accept:"application/json"},body:o}).then((n=>{const l=e.getOTPHash();"link"===i&&l?(a.endLoginFlow(null,0),g("otp")):(t.setAttribute("data-form-status",n.status),n.json().then((e=>{let{message:t,data:l}=e,r=o.get("redirect");"register"===i&&(r=newspack_reader_activation_data.account_url),a.endLoginFlow(t,n.status,l,r)})).catch((()=>{a.endLoginFlow()})))})).catch((()=>{a.endLoginFlow()}))})):a.endLoginFlow(newspack_reader_auth_labels.invalid_password,400):a.endLoginFlow(newspack_reader_auth_labels.invalid_email,400)}))})),[...document.querySelectorAll(".newspack-reader__logins")].forEach((e=>{e.classList.remove("newspack-reader__logins--disabled")})),document.querySelectorAll(".newspack-reader__logins__google").forEach((e=>{const t=e.closest("form"),n=t.querySelector('input[name="redirect"]');e.addEventListener("click",(()=>{null!=t&&t.startLoginFlow&&t.startLoginFlow();const e=t?function(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:[];return Array.from(e.entries()).reduce(((e,n)=>{let[a,o]=n;return t.includes(a)?(a.indexOf("[]")>-1?(a=a.replace("[]",""),e[a]=e[a]||[],e[a].push(o)):e[a]=o,e):e}),{})}(new FormData(t),["lists[]"]):{};e.current_page_url=window.location.href;const a=window.open("about:blank","newspack_google_login","width=500,height=600");fetch("/wp-json/newspack/v1/login/google").then((e=>e.json().then((t=>Promise.resolve({data:t,status:e.status}))))).then((o=>{let{data:l,status:r}=o;if(200!==r)a&&a.close(),null!=t&&t.endLoginFlow&&t.endLoginFlow(l.message,r);else if(a){a.location=l;const o=setInterval((()=>{a.closed&&((e=>{fetch(`/wp-json/newspack/v1/login/google/register?metadata=${JSON.stringify(e)}`).then((e=>{e.json().then((a=>{let{message:o,data:l}=a;const r=(null==n?void 0:n.value)||null;null!=t&&t.endLoginFlow&&t.endLoginFlow(o,e.status,l,r)})).catch((n=>{null!=t&&t.endLoginFlow&&t.endLoginFlow(null==n?void 0:n.message,e.status)}))})).catch((e=>{null!=t&&t.endLoginFlow&&t.endLoginFlow(null==e?void 0:e.message)}))})(e),clearInterval(o))}),500)}else null!=t&&t.endLoginFlow&&t.endLoginFlow(newspack_reader_auth_labels.blocked_popup)})).catch((e=>{console.log(e),null!=t&&t.endLoginFlow&&t.endLoginFlow(null==e?void 0:e.message,400),a&&a.close()}))}))}))},"undefined"!=typeof document&&("complete"!==document.readyState&&"interactive"!==document.readyState?document.addEventListener("DOMContentLoaded",t):t());var a=window;for(var o in n)a[o]=n[o];n.__esModule&&Object.defineProperty(a,"__esModule",{value:!0})}();