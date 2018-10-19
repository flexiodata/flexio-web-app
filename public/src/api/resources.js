import Vue from 'vue'
import VueResource from 'vue-resource'

Vue.use(VueResource)

Vue.http.options.crossOrigin = true
Vue.http.options.credentials = true

// this allows the URL path to be extended via a set of parameters
var SUFFIX = '{/p1}{/p2}{/p3}{/p4}'

// api root
export const API_V2_ROOT = '/api/v2'

export const SignupResource        = Vue.resource(API_V2_ROOT+'/signup')
export const LoginResource         = Vue.resource(API_V2_ROOT+'/login')
export const LogoutResource        = Vue.resource(API_V2_ROOT+'/logout')
export const ValidateResource      = Vue.resource(API_V2_ROOT+'/validate')
export const ResetPasswordResource = Vue.resource(API_V2_ROOT+'/resetpassword')

export const BaseResource          = Vue.resource(API_V2_ROOT+'/me'+SUFFIX)
export const AccountResource       = Vue.resource(API_V2_ROOT+'{/eid}/account'+SUFFIX)
export const RightsResource        = Vue.resource(API_V2_ROOT+'/me/auth/rights{/eid}'+SUFFIX)
export const TokenResource         = Vue.resource(API_V2_ROOT+'/me/auth/keys{/eid}'+SUFFIX)
export const PipeResource          = Vue.resource(API_V2_ROOT+'/me/pipes{/eid}'+SUFFIX)
export const ConnectionResource    = Vue.resource(API_V2_ROOT+'/me/connections{/eid}'+SUFFIX)
export const ProcessResource       = Vue.resource(API_V2_ROOT+'/me/processes{/eid}'+SUFFIX)
export const StreamResource        = Vue.resource(API_V2_ROOT+'/me/streams{/eid}')
export const VfsResource           = Vue.resource(API_V2_ROOT+'/me/vfs{/action}'+SUFFIX)

export const AdminTestResource     = Vue.resource(API_V2_ROOT+'/admin/tests'+SUFFIX)
export const AdminInfoResource     = Vue.resource(API_V2_ROOT+'/admin/info'+SUFFIX)

export const UserProcessResource  = Vue.resource(API_V2_ROOT+'{/user_eid}/processes{/eid}'+SUFFIX)
