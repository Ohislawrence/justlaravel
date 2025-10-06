import{P as B,f as h,x as I,i as y,z as L,c as U,o as l,w as f,a as e,b as i,g as M,j as s,d,h as w,F as D,l as P,t as k,n as R,k as _,G as A,S as F}from"./app-kQpkt3Sc.js";import{A as q}from"./AppLayout-BYZkZj5l.js";import{_ as m}from"./InputError-C_fJ27Qq.js";import{_ as u}from"./InputLabel-BjJlasTh.js";import{_ as N}from"./TextInput-od1qMv9T.js";import{_ as E}from"./Textarea-D9IFMUpV.js";import{_ as H}from"./Checkbox-tdsXQ9zc.js";import{R as O}from"./RichTextEditor-DeUwknnd.js";import{_ as G}from"./Modal-Z3SQPRgF.js";import{_ as W}from"./_plugin-vue_export-helper-DlAUqK2U.js";/* empty css                                                                  */const J={class:"flex justify-between items-center"},K={class:"py-6"},Q={class:"max-w-7xl mx-auto sm:px-6 lg:px-8"},X={class:"bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100"},Y={class:"p-6 bg-white border-b border-gray-200"},Z={class:"grid grid-cols-1 gap-6"},ee={class:"space-y-6"},te={class:"flex items-center"},oe={class:"space-y-6"},se={key:0,class:"mt-4"},re=["src"],ie={class:"space-y-6"},ae={class:"bg-gray-50 p-4 rounded-lg border border-gray-200"},ne={class:"grid grid-cols-1 md:grid-cols-3 gap-2 text-sm"},le={class:"bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-mono mr-2"},de={class:"text-gray-600"},ce={class:"flex justify-between items-center pt-6"},me=["disabled"],ue=["disabled"],ge={key:0,class:"w-4 h-4 mr-2",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor"},pe={key:1,class:"flex items-center"},fe={key:2},ve={class:"p-6"},be={class:"relative",style:{width:"29.7cm",height:"21cm",margin:"0 auto"}},xe=["innerHTML"],he={class:"mt-6 flex justify-end"},ye={__name:"Create",props:{user:Object,organization:Object},setup(C){B();const g=C,v=h(()=>{var r;return(r=g.organization)!=null&&r.logo?`/storage/${g.organization.logo}`:"/images/default-organization-logo.png"}),b=h(()=>{var r;return(r=g.organization)!=null&&r.certificate_seal?`/storage/${g.organization.certificate_seal}`:"/images/default-seal.png"}),z={"{{user.name}}":"Full name of the certificate recipient","{{user.email}}":"Email of the recipient","{{quiz.title}}":"Title of the quiz","{{quiz.description}}":"Description of the quiz","{{certificate.number}}":"Certificate serial number","{{certificate.issued_at}}":"Issue date (formatted)","{{certificate.expires_at}}":"Expiry date (formatted)","{{score}}":"Percentage score achieved","{{date}}":"Current date (formatted)"},T=`
<div class="certificate-landscape" 
   style="width: 29.7cm; height: 21cm; margin: 0 auto; position: relative; font-family: 'Times New Roman', serif; background: #fdfdfd; ; box-shadow: 0 0 20px rgba(0,0,0,0.1);">

<!-- Background Watermark -->
<div v-if="backgroundImage" class="background-image" 
     style="background-size: cover; background-position: center; background-repeat: no-repeat; opacity: 0.15; position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 1;">
</div>

<!-- Certificate Content -->
<div class="content" style="position: relative; z-index: 10; padding: 2cm; height: 100%; text-align: center;">

  <!-- Logo -->
  <div class="logo" style="display: flex; justify-content: center; margin-bottom: 1cm;">
    <img src="${v.value}" style="max-height: 90px;" alt="Organization Logo">
  </div>

  <!-- Main Title -->
  <h1 style="font-size: 40pt; color: #1a365d; margin-bottom: 0.3cm; letter-spacing: 3px; text-transform: uppercase; font-weight: bold;">
    Certificate of Achievement
  </h1>
  <hr style="width: 40%; margin: 0.5cm auto 1.5cm auto; border: 1px solid #cbd5e0;">

  <!-- Certification Text -->
  <p style="font-size: 14pt; color: #4a5568; margin-bottom: 0.5cm;">This is proudly presented to</p>

  <!-- Recipient -->
  <h2 style="font-size: 30pt; color: #2b6cb0; margin-bottom: 0.8cm; border-bottom: 3px solid #2b6cb0; display: inline-block; padding: 0 1cm 0.3cm 1cm; font-weight: bold;">
    {{user.name}}
  </h2>

  <!-- Achievement Statement -->
  <p style="font-size: 14pt; color: #4a5568; margin-bottom: 0.5cm;">for successfully completing</p>

  <!-- Course Title -->
  <h3 style="font-size: 22pt; color: #1a365d; margin-bottom: 1cm; font-weight: 600;">
    {{quiz.title}}
  </h3>

  <!-- Details -->
  <div style="font-size: 11pt; color: #2d3748; margin-bottom: 2cm; line-height: 1.6;">
    <p>Completed on <strong>{{date}}</strong></p>
    <p>Final Score: <strong>{{score}}%</strong></p>
    <p>Certificate ID: <strong>{{certificate.number}}</strong></p>
  </div>

  <!-- Footer (Signatures + Seal) -->
  <div style="position: absolute; bottom: 2cm; left: 2cm; right: 2cm; display: flex; justify-content: space-between; align-items: flex-end;">
    
    <!-- Instructor Signature -->
    <div style="width: 40%; text-align: center;">
      <div style="border-top: 1px solid #4a5568; width: 70%; margin: 0 auto; padding-top: 0.3cm;"></div>
      <p style="margin-top: 0.2cm; font-size: 10pt; color: #4a5568;">Instructor / Supervisor</p>
    </div>

    <!-- Seal -->
    <div style="width: 100px; text-align: center;">
      <img src="${b.value}" style="width: 100px; opacity: 0.85;" alt="Organization Seal">
    </div>

    <!-- Date Signature -->
    <div style="width: 40%; text-align: center;">
      <div style="border-top: 1px solid #4a5568; width: 70%; margin: 0 auto; padding-top: 0.3cm;"></div>
      <p style="margin-top: 0.2cm; font-size: 10pt; color: #4a5568;">Date</p>
    </div>
  </div>
</div>
</div>
`,o=I({name:"",description:"",content:T,background_image:null,is_default:!1}),n=y(null),c=y(!1),x=y("");h(()=>n.value?{backgroundImage:`url(${n.value})`,backgroundSize:"cover",backgroundPosition:"center",backgroundRepeat:"no-repeat"}:{});const S=r=>{const t=r.target.files[0];if(t){o.background_image=t;const a=new FileReader;a.onload=p=>{n.value=p.target.result},a.readAsDataURL(t)}else n.value=null};L(()=>o.content,r=>{c.value&&(x.value=V(r))});const V=r=>{let t=r;return t=t.replace(/\{\{currentLogoUrl\}\}/g,v.value).replace(/\{\{currentSealUrl\}\}/g,b.value),t=t.replace(/v-if="[^"]*"/g,"").replace(/:style="[^"]*"/g,""),t},j=()=>{let r=o.content;r=r.replace(/\{\{currentLogoUrl\}\}/g,v.value).replace(/\{\{currentSealUrl\}\}/g,b.value),r=r.replace(/v-if="[^"]*"/g,"").replace(/:style="[^"]*"/g,""),x.value=r,c.value=!0},$=()=>{o.post(route("examiner.certificate-templates.store"),{preserveScroll:!0,onSuccess:()=>{o.reset()}})};return(r,t)=>(l(),U(q,{title:"Create Certificate Template"},{header:f(()=>[e("div",J,[t[7]||(t[7]=e("h2",{class:"font-semibold text-xl text-gray-800 leading-tight"}," Create New Certificate Template ",-1)),i(s(F),{href:r.route("examiner.certificate-templates.index"),class:"inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors duration-150"},{default:f(()=>t[6]||(t[6]=[e("svg",{class:"w-4 h-4 mr-1",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M10 19l-7-7m0 0l7-7m-7 7h18"})],-1),_(" Back to Templates ")])),_:1,__:[6]},8,["href"])])]),default:f(()=>[e("div",K,[e("div",Q,[e("div",X,[e("div",Y,[e("form",{onSubmit:M($,["prevent"])},[e("div",Z,[e("div",ee,[t[8]||(t[8]=e("h3",{class:"text-lg font-medium"},"Template Information",-1)),e("div",null,[i(u,{for:"name",value:"Template Name *"}),i(N,{id:"name",modelValue:s(o).name,"onUpdate:modelValue":t[0]||(t[0]=a=>s(o).name=a),type:"text",class:"mt-1 block w-full focus:ring-green-500 focus:border-green-500 transition-colors duration-200",required:"",autofocus:""},null,8,["modelValue"]),i(m,{class:"mt-2",message:s(o).errors.name},null,8,["message"])]),e("div",null,[i(u,{for:"description",value:"Description"}),i(E,{id:"description",modelValue:s(o).description,"onUpdate:modelValue":t[1]||(t[1]=a=>s(o).description=a),class:"mt-1 block w-full focus:ring-green-500 focus:border-green-500 transition-colors duration-200",rows:"3",placeholder:"Brief description of this template..."},null,8,["modelValue"]),i(m,{class:"mt-2",message:s(o).errors.description},null,8,["message"])]),e("div",te,[i(H,{id:"is_default",checked:s(o).is_default,"onUpdate:checked":t[2]||(t[2]=a=>s(o).is_default=a)},null,8,["checked"]),i(u,{for:"is_default",value:"Set as default template",class:"ml-2"}),i(m,{class:"mt-2",message:s(o).errors.is_default},null,8,["message"])])]),e("div",oe,[t[11]||(t[11]=e("h3",{class:"text-lg font-medium"},"Design",-1)),e("div",null,[i(u,{for:"background_image",value:"Background Image"}),t[10]||(t[10]=e("p",{class:"text-sm text-gray-500 mb-2"},"Recommended size: 3508×2480 pixels (A4 at 300dpi)",-1)),e("input",{id:"background_image",type:"file",class:"mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition-colors duration-200",accept:"image/jpeg,image/png,image/jpg",onChange:S},null,32),i(m,{class:"mt-2",message:s(o).errors.background_image},null,8,["message"]),n.value?(l(),d("div",se,[t[9]||(t[9]=e("p",{class:"text-sm font-medium text-gray-700 mb-1"},"Preview:",-1)),e("img",{src:n.value,class:"max-w-full h-48 object-contain border border-gray-200 rounded-lg shadow-sm",alt:"Preview"},null,8,re)])):w("",!0)])]),e("div",ie,[t[13]||(t[13]=e("h3",{class:"text-lg font-medium"},"Template Content",-1)),e("div",null,[i(u,{value:"HTML Content *"}),i(O,{modelValue:s(o).content,"onUpdate:modelValue":t[3]||(t[3]=a=>s(o).content=a),class:"mt-1",error:s(o).errors.content},null,8,["modelValue","error"]),i(m,{class:"mt-2",message:s(o).errors.content},null,8,["message"])]),e("div",ae,[t[12]||(t[12]=e("h4",{class:"font-medium text-gray-700 mb-2"},"Available Variables:",-1)),e("div",ne,[(l(),d(D,null,P(z,(a,p)=>e("div",{key:p,class:"flex items-start"},[e("span",le,k(p),1),e("span",de,k(a),1)])),64))])])]),e("div",ce,[e("button",{type:"button",onClick:j,disabled:s(o).processing||!s(o).content,class:R([{"opacity-50 cursor-not-allowed":s(o).processing||!s(o).content},"inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"])},t[14]||(t[14]=[e("svg",{class:"w-4 h-4 mr-2",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M15 12a3 3 0 11-6 0 3 3 0 016 0z"}),e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"})],-1),_(" Preview Template ")]),10,me),e("button",{type:"submit",disabled:s(o).processing,class:"inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition-colors duration-200"},[s(o).processing?w("",!0):(l(),d("svg",ge,t[15]||(t[15]=[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M12 6v6m0 0v6m0-6h6m-6 0H6"},null,-1)]))),s(o).processing?(l(),d("span",pe,t[16]||(t[16]=[e("svg",{class:"animate-spin -ml-1 mr-2 h-4 w-4 text-white",xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24"},[e("circle",{class:"opacity-25",cx:"12",cy:"12",r:"10",stroke:"currentColor","stroke-width":"4"}),e("path",{class:"opacity-75",fill:"currentColor",d:"M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"})],-1),_(" Creating... ")]))):(l(),d("span",fe,"Create Template"))],8,ue)])])],32)])])])]),i(G,{show:c.value,onClose:t[5]||(t[5]=a=>c.value=!1),"max-width":"4xl"},{default:f(()=>[e("div",ve,[t[17]||(t[17]=e("h2",{class:"text-lg font-medium text-gray-900 mb-4"}," Template Preview ",-1)),e("div",be,[n.value?(l(),d("div",{key:0,class:"absolute inset-0 z-0",style:A({backgroundImage:`url(${n.value})`,backgroundSize:"cover",backgroundPosition:"center",backgroundRepeat:"no-repeat",opacity:.15})},null,4)):w("",!0),e("div",{class:"relative z-10 h-full overflow-hidden",innerHTML:x.value},null,8,xe)]),e("div",he,[e("button",{onClick:t[4]||(t[4]=a=>c.value=!1),class:"inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"}," Close ")])])]),_:1},8,["show"])]),_:1}))}},Ie=W(ye,[["__scopeId","data-v-cd389f94"]]);export{Ie as default};
