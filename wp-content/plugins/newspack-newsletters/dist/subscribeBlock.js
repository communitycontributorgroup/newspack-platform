(()=>{"use strict";var e,t={r:e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})}},n={};t.r(n),e=function(){document.querySelectorAll(".newspack-newsletters-subscribe").forEach((function(e){var t=e.querySelector("form");if(t){var n=e.querySelector(".newspack-newsletters-subscribe-response"),r=e.querySelector('input[type="email"]'),a=e.querySelector('input[type="submit"]');t.endFlow=function(i){var c=arguments.length>1&&void 0!==arguments[1]?arguments[1]:500,o=arguments.length>2&&void 0!==arguments[2]&&arguments[2],s=document.createElement("p");r.removeAttribute("disabled"),a.removeAttribute("disabled"),s.innerHTML=o?e.getAttribute("data-success-message"):i,s.className="message status-".concat(c),200===c?e.replaceChild(s,t):n.appendChild(s)},t.addEventListener("submit",(function(e){var i;if(e.preventDefault(),n.innerHTML="",a.disabled=!0,a.setAttribute("disabled","true"),null===(i=t.npe)||void 0===i||!i.value)return t.endFlow(newspack_newsletters_subscribe_block.invalid_email,400);new Promise((function(e,t){var n=document.getElementById("newspack-recaptcha-js");if(!n)return e("");var r=window.grecaptcha;if(!r)return e("");var a=n.getAttribute("src").split("?render=").pop();if(!a)return e("");null!=r&&r.ready||t(newspack_newsletters_subscribe_block.recaptcha_error),r.ready((function(){r.execute(a,{action:"submit"}).then((function(t){return e(t)})).catch((function(e){return t(e)}))}))})).then((function(e){if(e){var n=t.captcha_token;n||((n=document.createElement("input")).setAttribute("type","hidden"),n.setAttribute("name","captcha_token"),t.appendChild(n)),n.value=e}})).catch((function(e){t.endFlow(e,400)})).finally((function(){var e=new FormData(t);if(!e.has("npe")||!e.get("npe"))return t.endFlow(newspack_newsletters_subscribe_block.invalid_email,400);r.disabled=!0,r.setAttribute("disabled","true"),fetch(t.getAttribute("action")||window.location.pathname,{method:"POST",headers:{Accept:"application/json"},body:e}).then((function(e){r.disabled=!1,a.disabled=!1,e.json().then((function(n){var r=n.message,a=n.newspack_newsletters_subscribed;t.endFlow(r,e.status,a)}))}))}))}))}}))},"undefined"!=typeof document&&("complete"!==document.readyState&&"interactive"!==document.readyState?document.addEventListener("DOMContentLoaded",e):e());var r=window;for(var a in n)r[a]=n[a];n.__esModule&&Object.defineProperty(r,"__esModule",{value:!0})})();