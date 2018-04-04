import Vue from 'vue'
import VueResource from 'vue-resource'

Vue.use(VueResource)

Vue.http.options.crossOrigin = true
Vue.http.options.credentials = true

// this allows the URL path to be extended via a set of parameters
var SUFFIX = '{/p1}{/p2}{/p3}{/p4}'

// api root
export const API_ROOT = '/api/v1'
export const V1_ROOT = '/api/v1'
export const V2_ROOT = '/api/v2'

//export const SignupResource     = Vue.resource(V2_ROOT+'/signup'+SUFFIX)
//export const LoginResource      = Vue.resource(V2_ROOT+'/login')
export const LogoutResource     = Vue.resource(V2_ROOT+'/logout')
export const ValidateResource   = Vue.resource(V2_ROOT+'/validate'+SUFFIX)

export const RightsResource     = Vue.resource(V1_ROOT+'/rights{/eid}'+SUFFIX)
export const HelpResource       = Vue.resource(V1_ROOT+'/help/conversation')
export const UserResource       = Vue.resource(V1_ROOT+'/users{/eid}'+SUFFIX)
export const ConnectionResource = Vue.resource(V1_ROOT+'/connections{/eid}'+SUFFIX)
export const PipeResource       = Vue.resource(V1_ROOT+'/pipes{/eid}'+SUFFIX)
export const ProcessResource    = Vue.resource(V1_ROOT+'/processes{/eid}'+SUFFIX)
export const AdminResource      = Vue.resource(V1_ROOT+'/admin/statistics{/type}')
export const StatisticsResource = Vue.resource(V1_ROOT+'/statistics{/type}')
export const StreamResource     = Vue.resource(V1_ROOT+'/streams{/eid}')
export const TestResource       = Vue.resource(V1_ROOT+'/tests'+SUFFIX)
export const VfsResource        = Vue.resource(V1_ROOT+'/vfs{/action}'+SUFFIX)
