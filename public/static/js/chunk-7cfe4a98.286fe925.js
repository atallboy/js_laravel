(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-7cfe4a98"],{"2c10":function(t,e,n){"use strict";n.r(e);var r=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"app-container"},[n("div",{staticClass:"header-container"},[n("div",{staticClass:"search-container"},[n("el-form",[n("el-input",{staticClass:"filter-item",staticStyle:{width:"200px","margin-right":"10px"},attrs:{placeholder:"代理姓名"},nativeOn:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.handleFilter(e)}},model:{value:t.searchForm.name,callback:function(e){t.$set(t.searchForm,"name",e)},expression:"searchForm.name"}}),n("el-select",{staticClass:"filter-item",staticStyle:{width:"90px","margin-right":"10px"},attrs:{placeholder:"状态",clearable:""},model:{value:t.searchForm.status,callback:function(e){t.$set(t.searchForm,"status",e)},expression:"searchForm.status"}},[n("el-option",{attrs:{label:"已通过",value:"1"}}),n("el-option",{attrs:{label:"已拒绝",value:"2"}}),n("el-option",{attrs:{label:"待审核",value:"0"}})],1),n("el-button",{staticClass:"filter-item",attrs:{type:"primary",icon:"el-icon-search"},on:{click:t.handleFilter}},[t._v(" 搜索 ")])],1)],1)]),n("div",{staticClass:"table-container"},[n("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.tableLoading,expression:"tableLoading"}],staticStyle:{width:"100%"},attrs:{data:t.list,border:"",fit:""}},[n("el-table-column",{attrs:{label:"ID",prop:"id",align:"center",width:"80"}}),n("el-table-column",{attrs:{label:"用户ID",prop:"user_id",align:"center",width:""}}),n("el-table-column",{attrs:{label:"经营数据","class-name":"status-col",width:"400"},scopedSlots:t._u([{key:"default",fn:function(e){var r=e.row;return[n("div",{staticClass:"business-wrap"},[n("div",{staticClass:"business-item"},[n("div",{staticClass:"business-item-label"},[t._v("账户余额")]),n("div",{staticClass:"business-item-value"},[t._v(t._s(r.user.balance))])]),n("div",{staticClass:"business-item"},[n("div",{staticClass:"business-item-label"},[t._v("已提现")]),n("div",{staticClass:"business-item-value"},[t._v(t._s(r.user.withdrawal))])]),n("div",{staticClass:"business-item"},[n("div",{staticClass:"business-item-label"},[t._v("订单金额")]),n("div",{staticClass:"business-item-value"},[t._v(t._s(r.order_money))])]),n("div",{staticClass:"business-item"},[n("div",{staticClass:"business-item-label"},[t._v("订单数量")]),n("div",{staticClass:"business-item-value"},[t._v(t._s(r.order_num))])])])]}}])}),n("el-table-column",{attrs:{label:"代理姓名",prop:"name",align:"center",width:""}}),n("el-table-column",{attrs:{label:"电话",prop:"tel",align:"center",width:""}}),n("el-table-column",{attrs:{label:"省市区","class-name":"status-col",width:"300"},scopedSlots:t._u([{key:"default",fn:function(e){var r=e.row;return[n("span",[t._v(t._s(r.province)+" "+t._s(r.city)+" "+t._s(r.district))])]}}])}),n("el-table-column",{attrs:{prop:"remark",label:"备注",align:"center",width:""}}),n("el-table-column",{attrs:{label:"状态","class-name":"status-col",width:"150"},scopedSlots:t._u([{key:"default",fn:function(e){var r=e.row;return[n("el-tag",{attrs:{type:t._f("statusFilter")(r.status)}},[0==r.status?n("span",[t._v("待审核")]):t._e(),1==r.status?n("span",[t._v("已通过")]):t._e(),2==r.status?n("span",[t._v("已拒绝")]):t._e()])]}}])}),n("el-table-column",{attrs:{prop:"update_at",label:"最近编辑",align:"center",width:"150"}}),n("el-table-column",{attrs:{fixed:"right",label:"操作",align:"center",width:"180","class-name":"small-padding fixed-width"},scopedSlots:t._u([{key:"default",fn:function(e){var r=e.row,a=e.$index;return[n("el-button",{attrs:{type:"text",size:"mini"},on:{click:function(e){return t.edit(r)}}},[t._v("编辑")]),"deleted"!=r.status?n("el-button",{attrs:{size:"mini",type:"text"},on:{click:function(e){return t.del(r,a)}}},[t._v("删除")]):t._e()]}}])})],1)],1),n("div",{staticClass:"pagination-container"},[n("pagination",{attrs:{layout:"total, prev, pager, next",total:t.total,page:t.searchForm.page,limit:t.searchForm.limit},on:{"update:page":function(e){return t.$set(t.searchForm,"page",e)},"update:limit":function(e){return t.$set(t.searchForm,"limit",e)},pagination:t.getList}})],1),n("el-dialog",{attrs:{title:"编辑",visible:t.formDialog},on:{"update:visible":function(e){t.formDialog=e}}},[n("el-form",{ref:"form",staticStyle:{"margin-left":"50px","padding-right":"30px"},attrs:{model:t.form,"label-position":"right","label-width":"130px"}},[n("el-form-item",{attrs:{label:"姓名",prop:"name"}},[n("el-input",{model:{value:t.form.name,callback:function(e){t.$set(t.form,"name",e)},expression:"form.name"}})],1),n("el-form-item",{attrs:{label:"状态",prop:"status",rules:{required:!0,message:"请选择",trigger:"change"}}},[n("el-radio-group",{model:{value:t.form.status,callback:function(e){t.$set(t.form,"status",e)},expression:"form.status"}},[n("el-radio",{attrs:{label:1}},[t._v("通过")]),n("el-radio",{attrs:{label:2}},[t._v("拒绝")])],1)],1)],1),n("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[n("el-button",{on:{click:function(e){t.formDialog=!1}}},[t._v(" 取消 ")]),n("el-button",{attrs:{type:"primary"},on:{click:function(e){return t.submitForm("form")}}},[t._v(" 确定 ")])],1)],1)],1)},a=[],i=n("3b10"),o=n("333d"),u={components:{Pagination:o["a"]},filters:{statusFilter:function(t){var e={0:"info",1:"success",2:"danger"};return e[t]}},data:function(){return{searchForm:{page:1,limit:10,name:""},tableLoading:!1,list:[],total:0,form:{},formDialog:!1,uploadUrl:"api/upload"}},computed:{},created:function(){this.getList()},methods:{getList:function(){var t=this;this.listLoading=!0,Object(i["a"])(this.searchForm).then((function(e){console.log(e),t.list=e.data.list,t.total=e.data.total,setTimeout((function(){t.loading=!1}),1500)}))},edit:function(t){this.form=t||{status:1},this.formDialog=!0},submitForm:function(t){var e=this;this.$refs[t].validate((function(t){t&&(e.listLoading=!0,Object(i["I"])(e.form).then((function(t){e.$message({type:"success",message:"保存成功!"}),e.formDialog=!1,e.getList(),e.listLoading=!1})))}))},del:function(t){var e=this;this.$confirm("确认删除？","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then((function(){console.log(t),e.listLoading=!0,Object(i["e"])({id:t.id}).then((function(t){console.log(t),e.getList(),e.listLoading=!1,e.$message({type:"success",message:"删除成功!"})}))})).catch((function(){}))},handleFilter:function(){console.log(this.searchForm),this.searchForm.page=1,this.getList()},uploadSuccess:function(t){console.log(t),this.form.pic=t,console.log(this.form.pic),this.formDialog=!1,this.formDialog=!0}}},s=u,l=(n("9bb0"),n("2877")),c=Object(l["a"])(s,r,a,!1,null,"1e6b6635",null);e["default"]=c.exports},"333d":function(t,e,n){"use strict";var r=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"pagination-container",class:{hidden:t.hidden}},[n("el-pagination",t._b({attrs:{background:t.background,"current-page":t.currentPage,"page-size":t.pageSize,layout:t.layout,"page-sizes":t.pageSizes,total:t.total},on:{"update:currentPage":function(e){t.currentPage=e},"update:current-page":function(e){t.currentPage=e},"update:pageSize":function(e){t.pageSize=e},"update:page-size":function(e){t.pageSize=e},"size-change":t.handleSizeChange,"current-change":t.handleCurrentChange}},"el-pagination",t.$attrs,!1))],1)},a=[];n("a9e3");Math.easeInOutQuad=function(t,e,n,r){return t/=r/2,t<1?n/2*t*t+e:(t--,-n/2*(t*(t-2)-1)+e)};var i=function(){return window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||function(t){window.setTimeout(t,1e3/60)}}();function o(t){document.documentElement.scrollTop=t,document.body.parentNode.scrollTop=t,document.body.scrollTop=t}function u(){return document.documentElement.scrollTop||document.body.parentNode.scrollTop||document.body.scrollTop}function s(t,e,n){var r=u(),a=t-r,s=20,l=0;e="undefined"===typeof e?500:e;var c=function t(){l+=s;var u=Math.easeInOutQuad(l,r,a,e);o(u),l<e?i(t):n&&"function"===typeof n&&n()};c()}var l={name:"Pagination",props:{total:{required:!0,type:Number},page:{type:Number,default:1},limit:{type:Number,default:20},pageSizes:{type:Array,default:function(){return[10,20,30,50]}},layout:{type:String,default:"total, sizes, prev, pager, next, jumper"},background:{type:Boolean,default:!0},autoScroll:{type:Boolean,default:!0},hidden:{type:Boolean,default:!1}},computed:{currentPage:{get:function(){return this.page},set:function(t){this.$emit("update:page",t)}},pageSize:{get:function(){return this.limit},set:function(t){this.$emit("update:limit",t)}}},methods:{handleSizeChange:function(t){this.$emit("pagination",{page:this.currentPage,limit:t}),this.autoScroll&&s(0,800)},handleCurrentChange:function(t){this.$emit("pagination",{page:t,limit:this.pageSize}),this.autoScroll&&s(0,800)}}},c=l,d=(n("5660"),n("2877")),m=Object(d["a"])(c,r,a,!1,null,"6af373ef",null);e["a"]=m.exports},"3b10":function(t,e,n){"use strict";n.d(e,"H",(function(){return a})),n.d(e,"C",(function(){return i})),n.d(e,"P",(function(){return o})),n.d(e,"W",(function(){return u})),n.d(e,"U",(function(){return s})),n.d(e,"q",(function(){return l})),n.d(e,"u",(function(){return c})),n.d(e,"L",(function(){return d})),n.d(e,"h",(function(){return m})),n.d(e,"y",(function(){return f})),n.d(e,"M",(function(){return p})),n.d(e,"j",(function(){return g})),n.d(e,"z",(function(){return b})),n.d(e,"v",(function(){return h})),n.d(e,"O",(function(){return v})),n.d(e,"l",(function(){return j})),n.d(e,"c",(function(){return O})),n.d(e,"A",(function(){return w})),n.d(e,"i",(function(){return y})),n.d(e,"G",(function(){return _})),n.d(e,"T",(function(){return C})),n.d(e,"p",(function(){return S})),n.d(e,"b",(function(){return k})),n.d(e,"J",(function(){return x})),n.d(e,"f",(function(){return F})),n.d(e,"d",(function(){return z})),n.d(e,"K",(function(){return L})),n.d(e,"g",(function(){return $})),n.d(e,"a",(function(){return T})),n.d(e,"I",(function(){return D})),n.d(e,"e",(function(){return P})),n.d(e,"X",(function(){return E})),n.d(e,"V",(function(){return N})),n.d(e,"r",(function(){return q})),n.d(e,"E",(function(){return B})),n.d(e,"R",(function(){return I})),n.d(e,"n",(function(){return A})),n.d(e,"D",(function(){return M})),n.d(e,"Q",(function(){return J})),n.d(e,"m",(function(){return Q})),n.d(e,"x",(function(){return R})),n.d(e,"N",(function(){return U})),n.d(e,"k",(function(){return G})),n.d(e,"F",(function(){return H})),n.d(e,"S",(function(){return K})),n.d(e,"o",(function(){return V})),n.d(e,"s",(function(){return W})),n.d(e,"t",(function(){return X})),n.d(e,"B",(function(){return Y})),n.d(e,"w",(function(){return Z}));var r=n("b775");function a(t){return Object(r["a"])({url:"/summary/index",method:"get",params:t})}function i(t){return Object(r["a"])({url:"/setting/index",method:"get",params:t})}function o(t){return Object(r["a"])({url:"/setting/edit",method:"post",data:t})}function u(t){return Object(r["a"])({url:"/user/list",method:"get",params:t})}function s(t){return Object(r["a"])({url:"/user/edit",method:"post",params:t})}function l(t){return Object(r["a"])({url:"/user/del",method:"get",params:t})}function c(t){return Object(r["a"])({url:"/item/list",method:"get",params:t})}function d(t){return Object(r["a"])({url:"/item/edit",method:"post",data:t})}function m(t){return Object(r["a"])({url:"/item/del",method:"get",params:t})}function f(t){return Object(r["a"])({url:"/master/list",method:"get",params:t})}function p(t){return Object(r["a"])({url:"/master/edit",method:"post",data:t})}function g(t){return Object(r["a"])({url:"/master/del",method:"get",params:t})}function b(t){return Object(r["a"])({url:"/order/list",method:"get",params:t})}function h(t){return Object(r["a"])({url:"/jiazhongorder/jiazhonglist",method:"get",params:t})}function v(t){return Object(r["a"])({url:"/order/edit",method:"post",params:t})}function j(t){return Object(r["a"])({url:"/order/del",method:"get",params:t})}function O(t){return Object(r["a"])({url:"/order/changeOrderMaster",method:"post",params:t})}function w(t){return Object(r["a"])({url:"/order/refund",method:"post",params:t})}function y(t){return Object(r["a"])({url:"/jiazhongorder/jzdel",method:"get",params:t})}function _(t){return Object(r["a"])({url:"/suggest/list",method:"get",params:t})}function C(t){return Object(r["a"])({url:"/suggest/edit",method:"post",params:t})}function S(t){return Object(r["a"])({url:"/suggest/del",method:"get",params:t})}function k(t){return Object(r["a"])({url:"/banner/list",method:"get",params:t})}function x(t){return Object(r["a"])({url:"/banner/edit",method:"post",params:t})}function F(t){return Object(r["a"])({url:"/banner/del",method:"get",params:t})}function z(t){return Object(r["a"])({url:"/coupon/list",method:"get",params:t})}function L(t){return Object(r["a"])({url:"/coupon/edit",method:"post",params:t})}function $(t){return Object(r["a"])({url:"/coupon/del",method:"get",params:t})}function T(t){return Object(r["a"])({url:"/agent/list",method:"get",params:t})}function D(t){return Object(r["a"])({url:"/agent/edit",method:"post",params:t})}function P(t){return Object(r["a"])({url:"/agent/del",method:"get",params:t})}function E(t){return Object(r["a"])({url:"/withdrawal/list",method:"get",params:t})}function N(t){return Object(r["a"])({url:"/withdrawal/edit",method:"post",params:t})}function q(t){return Object(r["a"])({url:"/withdrawal/del",method:"get",params:t})}function B(t){return Object(r["a"])({url:"/settleSolution/list",method:"get",params:t})}function I(t){return Object(r["a"])({url:"/settleSolution/edit",method:"post",params:t})}function A(t){return Object(r["a"])({url:"/settleSolution/del",method:"get",params:t})}function M(t){return Object(r["a"])({url:"/settleLadder/list",method:"get",params:t})}function J(t){return Object(r["a"])({url:"/settleLadder/edit",method:"post",params:t})}function Q(t){return Object(r["a"])({url:"/settleLadder/del",method:"get",params:t})}function R(t){return Object(r["a"])({url:"/mch/list",method:"get",params:t})}function U(t){return Object(r["a"])({url:"/mch/edit",method:"post",data:t})}function G(t){return Object(r["a"])({url:"/mch/del",method:"get",params:t})}function H(t){return Object(r["a"])({url:"/subscribe/list",method:"get",params:t})}function K(t){return Object(r["a"])({url:"/subscribe/edit",method:"post",params:t})}function V(t){return Object(r["a"])({url:"/subscribe/del",method:"get",params:t})}function W(t){return Object(r["a"])({url:"/subscribe/getSubscribeEvent",method:"get",params:t})}function X(t){return Object(r["a"])({url:"/subscribe/getSubscribeParam",method:"get",params:t})}function Y(t){return Object(r["a"])({url:"/subscribe/sendSubscribeTest",method:"get",params:t})}function Z(t){return Object(r["a"])({url:"/log/list",method:"get",params:t})}},"53fb":function(t,e,n){},5660:function(t,e,n){"use strict";n("7a30")},"7a30":function(t,e,n){},"9bb0":function(t,e,n){"use strict";n("53fb")}}]);