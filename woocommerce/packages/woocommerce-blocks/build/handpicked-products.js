this.wc=this.wc||{},this.wc.blocks=this.wc.blocks||{},this.wc.blocks["handpicked-products"]=function(e){function t(t){for(var r,l,i=t[0],a=t[1],s=t[2],d=0,b=[];d<i.length;d++)l=i[d],Object.prototype.hasOwnProperty.call(n,l)&&n[l]&&b.push(n[l][0]),n[l]=0;for(r in a)Object.prototype.hasOwnProperty.call(a,r)&&(e[r]=a[r]);for(u&&u(t);b.length;)b.shift()();return o.push.apply(o,s||[]),c()}function c(){for(var e,t=0;t<o.length;t++){for(var c=o[t],r=!0,i=1;i<c.length;i++){var a=c[i];0!==n[a]&&(r=!1)}r&&(o.splice(t--,1),e=l(l.s=c[0]))}return e}var r={},n={14:0},o=[];function l(t){if(r[t])return r[t].exports;var c=r[t]={i:t,l:!1,exports:{}};return e[t].call(c.exports,c,c.exports,l),c.l=!0,c.exports}l.m=e,l.c=r,l.d=function(e,t,c){l.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:c})},l.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},l.t=function(e,t){if(1&t&&(e=l(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var c=Object.create(null);if(l.r(c),Object.defineProperty(c,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)l.d(c,r,function(t){return e[t]}.bind(null,r));return c},l.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return l.d(t,"a",t),t},l.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},l.p="";var i=window.webpackWcBlocksJsonp=window.webpackWcBlocksJsonp||[],a=i.push.bind(i);i.push=t,i=i.slice();for(var s=0;s<i.length;s++)t(i[s]);var u=a;return o.push([378,0]),c()}({0:function(e,t){e.exports=window.wp.element},1:function(e,t){e.exports=window.wp.i18n},10:function(e,t){e.exports=window.wp.compose},100:function(e,t,c){"use strict";c.d(t,"a",(function(){return y}));var r=c(6),n=c.n(r),o=c(0),l=c(1),i=c(3),a=c(115),s=c(529),u=c(4),d=c.n(u),b=c(10),g=c(21),m=c(35),p=c(528),h=c(14);const E=e=>{let{id:t,label:c,popoverContents:r,remove:n,screenReaderLabel:s,className:u=""}=e;const[g,m]=Object(o.useState)(!1),O=Object(b.useInstanceId)(E);if(s=s||c,!c)return null;c=Object(h.decodeEntities)(c);const j=d()("woocommerce-tag",u,{"has-remove":!!n}),w="woocommerce-tag__label-"+O,f=Object(o.createElement)(o.Fragment,null,Object(o.createElement)("span",{className:"screen-reader-text"},s),Object(o.createElement)("span",{"aria-hidden":"true"},c));return Object(o.createElement)("span",{className:j},r?Object(o.createElement)(i.Button,{className:"woocommerce-tag__text",id:w,onClick:()=>m(!0)},f):Object(o.createElement)("span",{className:"woocommerce-tag__text",id:w},f),r&&g&&Object(o.createElement)(i.Popover,{onClose:()=>m(!1)},r),n&&Object(o.createElement)(i.Button,{className:"woocommerce-tag__remove",onClick:n(t),label:Object(l.sprintf)(// Translators: %s label.
Object(l.__)("Remove %s","woocommerce"),c),"aria-describedby":w},Object(o.createElement)(a.a,{icon:p.a,size:20,className:"clear-icon"})))};var O=E;const j=e=>Object(o.createElement)(m.b,e),w=e=>{const{list:t,selected:c,renderItem:r,depth:l=0,onSelect:i,instanceId:a,isSingle:s,search:u}=e;return t?Object(o.createElement)(o.Fragment,null,t.map(t=>{const d=-1!==c.findIndex(e=>{let{id:c}=e;return c===t.id});return Object(o.createElement)(o.Fragment,{key:t.id},Object(o.createElement)("li",null,r({item:t,isSelected:d,onSelect:i,isSingle:s,search:u,depth:l,controlId:a})),Object(o.createElement)(w,n()({},e,{list:t.children,depth:l+1})))})):null},f=e=>{let{isLoading:t,isSingle:c,selected:r,messages:n,onChange:a,onRemove:s}=e;if(t||c||!r)return null;const u=r.length;return Object(o.createElement)("div",{className:"woocommerce-search-list__selected"},Object(o.createElement)("div",{className:"woocommerce-search-list__selected-header"},Object(o.createElement)("strong",null,n.selected(u)),u>0?Object(o.createElement)(i.Button,{isLink:!0,isDestructive:!0,onClick:()=>a([]),"aria-label":n.clear},Object(l.__)("Clear all","woocommerce")):null),u>0?Object(o.createElement)("ul",null,r.map((e,t)=>Object(o.createElement)("li",{key:t},Object(o.createElement)(O,{label:e.name,id:e.id,remove:s})))):null)},_=e=>{let{filteredList:t,search:c,onSelect:r,instanceId:n,...i}=e;const{messages:u,renderItem:d,selected:b,isSingle:g}=i,m=d||j;return 0===t.length?Object(o.createElement)("div",{className:"woocommerce-search-list__list is-not-found"},Object(o.createElement)("span",{className:"woocommerce-search-list__not-found-icon"},Object(o.createElement)(a.a,{icon:s.a})),Object(o.createElement)("span",{className:"woocommerce-search-list__not-found-text"},c?Object(l.sprintf)(u.noResults,c):u.noItems)):Object(o.createElement)("ul",{className:"woocommerce-search-list__list"},Object(o.createElement)(w,{list:t,selected:b,renderItem:m,onSelect:r,instanceId:n,isSingle:g,search:c}))},y=e=>{const{className:t="",isCompact:c,isHierarchical:r,isLoading:l,isSingle:a,list:s,messages:u=g.a,onChange:m,onSearch:p,selected:h,debouncedSpeak:E}=e,[O,j]=Object(o.useState)(""),w=Object(b.useInstanceId)(y),x=Object(o.useMemo)(()=>({...g.a,...u}),[u]),k=Object(o.useMemo)(()=>Object(g.c)(s,O,r),[s,O,r]);Object(o.useEffect)(()=>{E&&E(x.updated)},[E,x]),Object(o.useEffect)(()=>{"function"==typeof p&&p(O)},[O,p]);const v=Object(o.useCallback)(e=>()=>{a&&m([]);const t=h.findIndex(t=>{let{id:c}=t;return c===e});m([...h.slice(0,t),...h.slice(t+1)])},[a,h,m]),S=Object(o.useCallback)(e=>()=>{-1===h.findIndex(t=>{let{id:c}=t;return c===e.id})?m(a?[e]:[...h,e]):v(e.id)()},[a,v,m,h]);return Object(o.createElement)("div",{className:d()("woocommerce-search-list",t,{"is-compact":c})},Object(o.createElement)(f,n()({},e,{onRemove:v,messages:x})),Object(o.createElement)("div",{className:"woocommerce-search-list__search"},Object(o.createElement)(i.TextControl,{label:x.search,type:"search",value:O,onChange:e=>j(e)})),l?Object(o.createElement)("div",{className:"woocommerce-search-list__list is-loading"},Object(o.createElement)(i.Spinner,null)):Object(o.createElement)(_,n()({},e,{search:O,filteredList:k,messages:x,onSelect:S,instanceId:w})))};Object(i.withSpokenMessages)(y)},101:function(e,t,c){"use strict";var r=c(0),n=c(1),o=c(3);t.a=e=>{let{value:t,setAttributes:c}=e;return Object(r.createElement)(o.SelectControl,{label:Object(n.__)("Order products by","woocommerce"),value:t,options:[{label:Object(n.__)("Newness - newest first","woocommerce"),value:"date"},{label:Object(n.__)("Price - low to high","woocommerce"),value:"price_asc"},{label:Object(n.__)("Price - high to low","woocommerce"),value:"price_desc"},{label:Object(n.__)("Rating - highest first","woocommerce"),value:"rating"},{label:Object(n.__)("Sales - most first","woocommerce"),value:"popularity"},{label:Object(n.__)("Title - alphabetical","woocommerce"),value:"title"},{label:Object(n.__)("Menu Order","woocommerce"),value:"menu_order"}],onChange:e=>c({orderby:e})})}},11:function(e,t){e.exports=window.wp.primitives},13:function(e,t){e.exports=window.wp.blocks},137:function(e,t,c){"use strict";c.d(t,"a",(function(){return n}));var r=c(0);const n=Object(r.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 230 250",style:{width:"100%"}},Object(r.createElement)("title",null,"Grid Block Preview"),Object(r.createElement)("rect",{width:"65.374",height:"65.374",x:".162",y:".779",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"47.266",height:"5.148",x:"9.216",y:"76.153",fill:"#E1E3E6",rx:"2.574"}),Object(r.createElement)("rect",{width:"62.8",height:"15",x:"1.565",y:"101.448",fill:"#E1E3E6",rx:"5"}),Object(r.createElement)("rect",{width:"65.374",height:"65.374",x:".162",y:"136.277",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"47.266",height:"5.148",x:"9.216",y:"211.651",fill:"#E1E3E6",rx:"2.574"}),Object(r.createElement)("rect",{width:"62.8",height:"15",x:"1.565",y:"236.946",fill:"#E1E3E6",rx:"5"}),Object(r.createElement)("rect",{width:"65.374",height:"65.374",x:"82.478",y:".779",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"47.266",height:"5.148",x:"91.532",y:"76.153",fill:"#E1E3E6",rx:"2.574"}),Object(r.createElement)("rect",{width:"62.8",height:"15",x:"83.882",y:"101.448",fill:"#E1E3E6",rx:"5"}),Object(r.createElement)("rect",{width:"65.374",height:"65.374",x:"82.478",y:"136.277",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"47.266",height:"5.148",x:"91.532",y:"211.651",fill:"#E1E3E6",rx:"2.574"}),Object(r.createElement)("rect",{width:"62.8",height:"15",x:"83.882",y:"236.946",fill:"#E1E3E6",rx:"5"}),Object(r.createElement)("rect",{width:"65.374",height:"65.374",x:"164.788",y:".779",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"47.266",height:"5.148",x:"173.843",y:"76.153",fill:"#E1E3E6",rx:"2.574"}),Object(r.createElement)("rect",{width:"62.8",height:"15",x:"166.192",y:"101.448",fill:"#E1E3E6",rx:"5"}),Object(r.createElement)("rect",{width:"65.374",height:"65.374",x:"164.788",y:"136.277",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"47.266",height:"5.148",x:"173.843",y:"211.651",fill:"#E1E3E6",rx:"2.574"}),Object(r.createElement)("rect",{width:"62.8",height:"15",x:"166.192",y:"236.946",fill:"#E1E3E6",rx:"5"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"13.283",y:"86.301",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"21.498",y:"86.301",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"29.713",y:"86.301",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"37.927",y:"86.301",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"46.238",y:"86.301",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"95.599",y:"86.301",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"103.814",y:"86.301",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"112.029",y:"86.301",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"120.243",y:"86.301",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"128.554",y:"86.301",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"177.909",y:"86.301",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"186.124",y:"86.301",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"194.339",y:"86.301",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"202.553",y:"86.301",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"210.864",y:"86.301",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"13.283",y:"221.798",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"21.498",y:"221.798",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"29.713",y:"221.798",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"37.927",y:"221.798",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"46.238",y:"221.798",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"95.599",y:"221.798",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"103.814",y:"221.798",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"112.029",y:"221.798",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"120.243",y:"221.798",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"128.554",y:"221.798",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"177.909",y:"221.798",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"186.124",y:"221.798",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"194.339",y:"221.798",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"202.553",y:"221.798",fill:"#E1E3E6",rx:"3"}),Object(r.createElement)("rect",{width:"6.177",height:"6.177",x:"210.864",y:"221.798",fill:"#E1E3E6",rx:"3"}))},14:function(e,t){e.exports=window.wp.htmlEntities},15:function(e,t){e.exports=window.wp.apiFetch},16:function(e,t){e.exports=window.wp.url},178:function(e,t,c){"use strict";var r=c(6),n=c.n(r),o=c(0),l=c(22),i=c(26),a=c(114),s=c(28);t.a=e=>t=>{let{selected:c,...r}=t;const[u,d]=Object(o.useState)(!0),[b,g]=Object(o.useState)(null),[m,p]=Object(o.useState)([]),h=l.o.productCount>100,E=async e=>{const t=await Object(s.a)(e);g(t),d(!1)},O=Object(o.useRef)(c);Object(o.useEffect)(()=>{Object(i.h)({selected:O.current}).then(e=>{p(e),d(!1)}).catch(E)},[O]);const j=Object(a.a)(e=>{Object(i.h)({selected:c,search:e}).then(e=>{p(e),d(!1)}).catch(E)},400),w=Object(o.useCallback)(e=>{d(!0),j(e)},[d,j]);return Object(o.createElement)(e,n()({},r,{selected:c,error:b,products:m,isLoading:u,onSearch:h?w:null}))}},2:function(e,t){e.exports=window.wc.wcSettings},21:function(e,t,c){"use strict";c.d(t,"a",(function(){return l})),c.d(t,"c",(function(){return a})),c.d(t,"d",(function(){return s})),c.d(t,"b",(function(){return u}));var r=c(0),n=c(7),o=c(1);const l={clear:Object(o.__)("Clear all selected items","woocommerce"),noItems:Object(o.__)("No items found.","woocommerce"),
/* Translators: %s search term */
noResults:Object(o.__)("No results for %s","woocommerce"),search:Object(o.__)("Search for items","woocommerce"),selected:e=>Object(o.sprintf)(
/* translators: Number of items selected from list. */
Object(o._n)("%d item selected","%d items selected",e,"woocommerce"),e),updated:Object(o.__)("Search results updated.","woocommerce")},i=function(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:e;const c=Object(n.groupBy)(e,"parent"),r=Object(n.keyBy)(t,"id"),o=["0"],l=function(){let e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{};if(!e.parent)return e.name?[e.name]:[];const t=l(r[e.parent]);return[...t,e.name]},i=e=>e.map(e=>{const t=c[e.id];return o.push(""+e.id),{...e,breadcrumbs:l(r[e.parent]),children:t&&t.length?i(t):[]}}),a=i(c[0]||[]);return Object.entries(c).forEach(e=>{let[t,c]=e;o.includes(t)||a.push(...i(c||[]))}),a},a=(e,t,c)=>{if(!t)return c?i(e):e;const r=new RegExp(t.replace(/[-\/\\^$*+?.()|[\]{}]/g,"\\$&"),"i"),n=e.map(e=>!!r.test(e.name)&&e).filter(Boolean);return c?i(n,e):n},s=(e,t)=>{if(!t)return e;const c=new RegExp(t.replace(/[-\/\\^$*+?.()|[\]{}]/g,"\\$&"),"ig");return e.split(c).map((e,c)=>0===c?e:Object(r.createElement)(r.Fragment,{key:c},Object(r.createElement)("strong",null,t),e))},u=e=>1===e.length?e.slice(0,1).toString():2===e.length?e.slice(0,1).toString()+" › "+e.slice(-1).toString():e.slice(0,1).toString()+" … "+e.slice(-1).toString()},22:function(e,t,c){"use strict";c.d(t,"o",(function(){return o})),c.d(t,"m",(function(){return l})),c.d(t,"l",(function(){return i})),c.d(t,"n",(function(){return a})),c.d(t,"j",(function(){return s})),c.d(t,"e",(function(){return u})),c.d(t,"f",(function(){return d})),c.d(t,"g",(function(){return b})),c.d(t,"k",(function(){return g})),c.d(t,"c",(function(){return m})),c.d(t,"d",(function(){return p})),c.d(t,"h",(function(){return h})),c.d(t,"a",(function(){return E})),c.d(t,"i",(function(){return O})),c.d(t,"b",(function(){return j}));var r,n=c(2);const o=Object(n.getSetting)("wcBlocksConfig",{buildPhase:1,pluginUrl:"",productCount:0,defaultAvatar:"",restApiRoutes:{},wordCountType:"words"}),l=o.pluginUrl+"images/",i=o.pluginUrl+"build/",a=o.buildPhase,s=null===(r=n.STORE_PAGES.shop)||void 0===r?void 0:r.permalink,u=n.STORE_PAGES.checkout.id,d=n.STORE_PAGES.checkout.permalink,b=n.STORE_PAGES.privacy.permalink,g=(n.STORE_PAGES.privacy.title,n.STORE_PAGES.terms.permalink),m=(n.STORE_PAGES.terms.title,n.STORE_PAGES.cart.id),p=n.STORE_PAGES.cart.permalink,h=(n.STORE_PAGES.myaccount.permalink?n.STORE_PAGES.myaccount.permalink:Object(n.getSetting)("wpLoginUrl","/wp-login.php"),Object(n.getSetting)("shippingCountries",{})),E=Object(n.getSetting)("allowedCountries",{}),O=Object(n.getSetting)("shippingStates",{}),j=Object(n.getSetting)("allowedStates",{})},26:function(e,t,c){"use strict";c.d(t,"h",(function(){return s})),c.d(t,"e",(function(){return u})),c.d(t,"b",(function(){return d})),c.d(t,"i",(function(){return b})),c.d(t,"f",(function(){return g})),c.d(t,"c",(function(){return m})),c.d(t,"d",(function(){return p})),c.d(t,"g",(function(){return h})),c.d(t,"a",(function(){return E}));var r=c(16),n=c(15),o=c.n(n),l=c(7),i=c(2),a=c(22);const s=e=>{let{selected:t=[],search:c="",queryArgs:n={}}=e;const i=(e=>{let{selected:t=[],search:c="",queryArgs:n={}}=e;const o=a.o.productCount>100,l={per_page:o?100:0,catalog_visibility:"any",search:c,orderby:"title",order:"asc"},i=[Object(r.addQueryArgs)("/wc/store/v1/products",{...l,...n})];return o&&t.length&&i.push(Object(r.addQueryArgs)("/wc/store/v1/products",{catalog_visibility:"any",include:t,per_page:0})),i})({selected:t,search:c,queryArgs:n});return Promise.all(i.map(e=>o()({path:e}))).then(e=>Object(l.uniqBy)(Object(l.flatten)(e),"id").map(e=>({...e,parent:0}))).catch(e=>{throw e})},u=e=>o()({path:"/wc/store/v1/products/"+e}),d=()=>o()({path:"wc/store/v1/products/attributes"}),b=e=>o()({path:`wc/store/v1/products/attributes/${e}/terms`}),g=e=>{let{selected:t=[],search:c}=e;const n=(e=>{let{selected:t=[],search:c}=e;const n=Object(i.getSetting)("limitTags",!1),o=[Object(r.addQueryArgs)("wc/store/v1/products/tags",{per_page:n?100:0,orderby:n?"count":"name",order:n?"desc":"asc",search:c})];return n&&t.length&&o.push(Object(r.addQueryArgs)("wc/store/v1/products/tags",{include:t})),o})({selected:t,search:c});return Promise.all(n.map(e=>o()({path:e}))).then(e=>Object(l.uniqBy)(Object(l.flatten)(e),"id"))},m=e=>o()({path:Object(r.addQueryArgs)("wc/store/v1/products/categories",{per_page:0,...e})}),p=e=>o()({path:"wc/store/v1/products/categories/"+e}),h=e=>o()({path:Object(r.addQueryArgs)("wc/store/v1/products",{per_page:0,type:"variation",parent:e})}),E=(e,t)=>{if(!e.title.raw)return e.slug;const c=1===t.filter(t=>t.title.raw===e.title.raw).length;return e.title.raw+(c?"":" - "+e.slug)}},267:function(e){e.exports=JSON.parse('{"name":"woocommerce/handpicked-products","title":"Hand-picked Products","category":"woocommerce","keywords":["Handpicked Products","WooCommerce"],"description":"Display a selection of hand-picked products in a grid.","supports":{"align":["wide","full"],"html":false},"attributes":{"align":{"type":"string"},"columns":{"type":"number","default":3},"contentVisibility":{"type":"object","default":{"image":true,"title":true,"price":true,"rating":true,"button":true},"properties":{"image":{"type":"boolean","image":true},"title":{"type":"boolean","title":true},"price":{"type":"boolean","price":true},"rating":{"type":"boolean","rating":true},"button":{"type":"boolean","button":true}}},"orderby":{"type":"string","enum":["date","popularity","price_asc","price_desc","rating","title","menu_order"],"default":"date"},"products":{"type":"array","default":[]},"alignButtons":{"type":"boolean","default":false},"isPreview":{"type":"boolean","default":false}},"textdomain":"woocommerce","apiVersion":2,"$schema":"https://schemas.wp.org/trunk/block.json"}')},28:function(e,t,c){"use strict";c.d(t,"a",(function(){return o})),c.d(t,"b",(function(){return l}));var r=c(1),n=c(14);const o=async e=>{if("function"==typeof e.json)try{const t=await e.json();return{message:t.message,type:t.type||"api"}}catch(e){return{message:e.message,type:"general"}}return{message:e.message,type:e.type||"general"}},l=e=>{if(e.data&&"rest_invalid_param"===e.code){const t=Object.values(e.data.params);if(t[0])return t[0]}return null!=e&&e.message?Object(n.decodeEntities)(e.message):Object(r.__)("Something went wrong. Please contact us to get assistance.","woocommerce")}},290:function(e,t){},3:function(e,t){e.exports=window.wp.components},32:function(e,t,c){"use strict";var r=c(0),n=c(1),o=c(33);t.a=e=>{let{error:t}=e;return Object(r.createElement)("div",{className:"wc-block-error-message"},(e=>{let{message:t,type:c}=e;return t?"general"===c?Object(r.createElement)("span",null,Object(n.__)("The following error was returned","woocommerce"),Object(r.createElement)("br",null),Object(r.createElement)("code",null,Object(o.escapeHTML)(t))):"api"===c?Object(r.createElement)("span",null,Object(n.__)("The following error was returned from the API","woocommerce"),Object(r.createElement)("br",null),Object(r.createElement)("code",null,Object(o.escapeHTML)(t))):t:Object(n.__)("An unknown error occurred which prevented the block from being updated.","woocommerce")})(t))}},33:function(e,t){e.exports=window.wp.escapeHtml},35:function(e,t,c){"use strict";c.d(t,"a",(function(){return i}));var r=c(6),n=c.n(r),o=c(0),l=c(21);const i=e=>{let{countLabel:t,className:c,depth:r=0,controlId:i="",item:a,isSelected:s,isSingle:u,onSelect:d,search:b="",...g}=e;const m=null!=t&&void 0!==a.count&&null!==a.count,p=[c,"woocommerce-search-list__item"];p.push("depth-"+r),u&&p.push("is-radio-button"),m&&p.push("has-count");const h=a.breadcrumbs&&a.breadcrumbs.length,E=g.name||"search-list-item-"+i,O=`${E}-${a.id}`;return Object(o.createElement)("label",{htmlFor:O,className:p.join(" ")},u?Object(o.createElement)("input",n()({type:"radio",id:O,name:E,value:a.value,onChange:d(a),checked:s,className:"woocommerce-search-list__item-input"},g)):Object(o.createElement)("input",n()({type:"checkbox",id:O,name:E,value:a.value,onChange:d(a),checked:s,className:"woocommerce-search-list__item-input"},g)),Object(o.createElement)("span",{className:"woocommerce-search-list__item-label"},h?Object(o.createElement)("span",{className:"woocommerce-search-list__item-prefix"},Object(l.b)(a.breadcrumbs)):null,Object(o.createElement)("span",{className:"woocommerce-search-list__item-name"},Object(l.d)(a.name,b))),!!m&&Object(o.createElement)("span",{className:"woocommerce-search-list__item-count"},t||a.count))};t.b=i},378:function(e,t,c){e.exports=c(495)},495:function(e,t,c){"use strict";c.r(t);var r=c(0),n=c(13),o=c(2),l=c(115),i=c(527),a=(c(290),c(267)),s=c(6),u=c.n(s),d=c(5),b=c(3),g=c(1),m=c(63),p=c(101),h=c(100),E=c(178),O=c(32);const j=e=>{let{error:t,onChange:c,onSearch:n,selected:o,products:l,isLoading:i,isCompact:a}=e;const s={clear:Object(g.__)("Clear all products","woocommerce"),list:Object(g.__)("Products","woocommerce"),noItems:Object(g.__)("Your store doesn't have any products.","woocommerce"),search:Object(g.__)("Search for products to display","woocommerce"),selected:e=>Object(g.sprintf)(
/* translators: %d is the number of selected products. */
Object(g._n)("%d product selected","%d products selected",e,"woocommerce"),e),updated:Object(g.__)("Product search results updated.","woocommerce")};return t?Object(r.createElement)(O.a,{error:t}):Object(r.createElement)(h.a,{className:"woocommerce-products",list:l.map(e=>{const t=e.sku?" ("+e.sku+")":"";return{...e,name:`${e.name}${t}`}}),isCompact:a,isLoading:i,selected:l.filter(e=>{let{id:t}=e;return o.includes(t)}),onSearch:n,onChange:c,messages:s})};j.defaultProps={selected:[],products:[],isCompact:!1,isLoading:!0};var w=Object(E.a)(j);const f=e=>{const{attributes:t,setAttributes:c}=e,{columns:n,contentVisibility:l,orderby:i,alignButtons:a}=t;return Object(r.createElement)(d.InspectorControls,{key:"inspector"},Object(r.createElement)(b.PanelBody,{title:Object(g.__)("Layout","woocommerce"),initialOpen:!0},Object(r.createElement)(b.RangeControl,{label:Object(g.__)("Columns","woocommerce"),value:n,onChange:e=>c({columns:e}),min:Object(o.getSetting)("min_columns",1),max:Object(o.getSetting)("max_columns",6)}),Object(r.createElement)(b.ToggleControl,{label:Object(g.__)("Align Buttons","woocommerce"),help:a?Object(g.__)("Buttons are aligned vertically.","woocommerce"):Object(g.__)("Buttons follow content.","woocommerce"),checked:a,onChange:()=>c({alignButtons:!a})})),Object(r.createElement)(b.PanelBody,{title:Object(g.__)("Content","woocommerce"),initialOpen:!0},Object(r.createElement)(m.a,{settings:l,onChange:e=>c({contentVisibility:e})})),Object(r.createElement)(b.PanelBody,{title:Object(g.__)("Order By","woocommerce"),initialOpen:!1},Object(r.createElement)(p.a,{setAttributes:c,value:i})),Object(r.createElement)(b.PanelBody,{title:Object(g.__)("Products","woocommerce"),initialOpen:!1},Object(r.createElement)(w,{selected:t.products,onChange:function(){let e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:[];const t=e.map(e=>{let{id:t}=e;return t});c({products:t})},isCompact:!0})))},_=e=>{const{attributes:t,setAttributes:c,debouncedSpeak:n,isEditing:o,setIsEditing:a}=e;return Object(r.createElement)(b.Placeholder,{icon:Object(r.createElement)(l.a,{icon:i.a}),label:Object(g.__)("Hand-picked Products","woocommerce"),className:"wc-block-products-grid wc-block-handpicked-products"},Object(g.__)("Display a selection of hand-picked products in a grid.","woocommerce"),Object(r.createElement)("div",{className:"wc-block-handpicked-products__selection"},Object(r.createElement)(w,{selected:t.products,onChange:function(){let e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:[];const t=e.map(e=>{let{id:t}=e;return t});c({products:t})}}),Object(r.createElement)(b.Button,{isPrimary:!0,onClick:()=>{a(!o),n(Object(g.__)("Showing Hand-picked Products block preview.","woocommerce"))}},Object(g.__)("Done","woocommerce"))))};var y=c(55),x=c.n(y),k=c(137);const v=e=>{const{attributes:t,name:c}=e;return t.isPreview?k.a:Object(r.createElement)(x.a,{block:c,attributes:t})},S=Object(b.withSpokenMessages)(e=>{const t=Object(d.useBlockProps)(),{attributes:{products:c}}=e,[n,o]=Object(r.useState)(!c.length);return Object(r.createElement)("div",t,Object(r.createElement)(d.BlockControls,null,Object(r.createElement)(b.ToolbarGroup,{controls:[{icon:"edit",title:Object(g.__)("Edit selected products","woocommerce"),onClick:()=>o(!n),isActive:n}]})),Object(r.createElement)(f,e),n?Object(r.createElement)(_,u()({isEditing:n,setIsEditing:o},e)):Object(r.createElement)(b.Disabled,null,Object(r.createElement)(v,e)))});Object(n.registerBlockType)(a,{icon:{src:Object(r.createElement)(l.a,{icon:i.a,className:"wc-block-editor-components-block-icon"})},attributes:{...a.attributes,columns:{type:"number",default:Object(o.getSetting)("default_columns",3)}},edit:S,save:()=>null})},5:function(e,t){e.exports=window.wp.blockEditor},55:function(e,t){e.exports=window.wp.serverSideRender},63:function(e,t,c){"use strict";var r=c(0),n=c(1),o=c(3);t.a=e=>{let{onChange:t,settings:c}=e;const{image:l,button:i,price:a,rating:s,title:u}=c,d=!1!==l;return Object(r.createElement)(r.Fragment,null,Object(r.createElement)(o.ToggleControl,{label:Object(n.__)("Product image","woocommerce"),help:d?Object(n.__)("Product image is visible.","woocommerce"):Object(n.__)("Product image is hidden.","woocommerce"),checked:d,onChange:()=>t({...c,image:!d})}),Object(r.createElement)(o.ToggleControl,{label:Object(n.__)("Product title","woocommerce"),help:u?Object(n.__)("Product title is visible.","woocommerce"):Object(n.__)("Product title is hidden.","woocommerce"),checked:u,onChange:()=>t({...c,title:!u})}),Object(r.createElement)(o.ToggleControl,{label:Object(n.__)("Product price","woocommerce"),help:a?Object(n.__)("Product price is visible.","woocommerce"):Object(n.__)("Product price is hidden.","woocommerce"),checked:a,onChange:()=>t({...c,price:!a})}),Object(r.createElement)(o.ToggleControl,{label:Object(n.__)("Product rating","woocommerce"),help:s?Object(n.__)("Product rating is visible.","woocommerce"):Object(n.__)("Product rating is hidden.","woocommerce"),checked:s,onChange:()=>t({...c,rating:!s})}),Object(r.createElement)(o.ToggleControl,{label:Object(n.__)("Add to Cart button","woocommerce"),help:i?Object(n.__)("Add to Cart button is visible.","woocommerce"):Object(n.__)("Add to Cart button is hidden.","woocommerce"),checked:i,onChange:()=>t({...c,button:!i})}))}},7:function(e,t){e.exports=window.lodash},8:function(e,t){e.exports=window.React}});