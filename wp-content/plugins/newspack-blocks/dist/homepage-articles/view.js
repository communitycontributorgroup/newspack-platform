(()=>{"use strict";function t(e,r){const s=new XMLHttpRequest;s.onreadystatechange=()=>{if(4===s.readyState){if(s.status>=200&&s.status<300){const t=JSON.parse(s.responseText);return e.onSuccess(t)}return r?t(e,r-1):e.onError()}},s.open("GET",e.url),s.send()}function e(t,e){return Object.prototype.hasOwnProperty.call(t,e)}Array.prototype.forEach.call(document.querySelectorAll(".wp-block-newspack-blocks-homepage-articles.has-more-button"),(function(r){const s=r.querySelector("[data-next]");if(!s)return;const n=r.querySelector("[data-posts]");let o=!1,i=!1;s.addEventListener("click",(()=>{if(o||i)return!1;function a(){o=!1,r.classList.remove("is-loading"),r.classList.add("is-error")}o=!0,r.classList.remove("is-error"),r.classList.add("is-loading"),t({url:s.getAttribute("data-next")+"&exclude_ids="+function(){const t=document.querySelectorAll("[class^='wp-block-newspack-blocks'] [data-post-id]"),e=Array.from(t).map((t=>t.getAttribute("data-post-id")));return e.push(document.querySelector("div[data-current-post-id]").getAttribute("data-current-post-id")),[...new Set(e)]}().join(","),onSuccess:function(t){if(!function(t){let r=!1;return t&&e(t,"items")&&Array.isArray(t.items)&&e(t,"next")&&"string"==typeof t.next&&(r=!0,!t.items.length||e(t.items[0],"html")&&"string"==typeof t.items[0].html||(r=!1)),r}(t))return a();if(t.items.length){const e=t.items.map((t=>t.html)).join("");n.insertAdjacentHTML("beforeend",e)}t.next&&s.setAttribute("data-next",t.next),t.items.length&&t.next||(i=!0,r.classList.remove("has-more-button")),o=!1,r.classList.remove("is-loading")},onError:a},3)}))}))})();