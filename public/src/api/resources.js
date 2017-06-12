import Vue from 'vue'
import VueResource from 'vue-resource'
import { HOSTNAME } from '../constants/common'

Vue.use(VueResource)

Vue.http.options.crossOrigin = true
Vue.http.options.credentials = true

// this allows the URL path to be extended via a set of parameters
var SUFFIX = '{/p1}{/p2}{/p3}{/p4}'

// api root
export const API_ROOT = '/api/v1'

// auth
export const LoginResource = Vue.resource(API_ROOT+'/login')
export const LogoutResource = Vue.resource(API_ROOT+'/logout')
export const SignupResource = Vue.resource(API_ROOT+'/signup'+SUFFIX)

// validation
export const ValidateResource = Vue.resource(API_ROOT+'/validate'+SUFFIX)

// rights
export const RightsResource = Vue.resource(API_ROOT+'/rights{/eid}'+SUFFIX)

// help
export const HelpResource = Vue.resource(API_ROOT+'/help/conversation')

// user
export const UserResource = Vue.resource(API_ROOT+'/users{/eid}'+SUFFIX)
//export const UserActivateResource = Vue.resource(API_ROOT+'/users/activate')
//export const UserSendVerificationResource = Vue.resource(API_ROOT+'/users/resendverify')

// project
export const ProjectResource = Vue.resource(API_ROOT+'/projects{/eid}'+SUFFIX)

// connection
export const ConnectionResource = Vue.resource(API_ROOT+'/connections{/eid}'+SUFFIX)

// pipe
export const PipeResource = Vue.resource(API_ROOT+'/pipes{/eid}'+SUFFIX)

// process
export const ProcessResource = Vue.resource(API_ROOT+'/processes{/eid}'+SUFFIX)

// stream
export const StreamResource = Vue.resource(API_ROOT+'/streams{/eid}')

// test
export const TestResource = Vue.resource(API_ROOT+'/tests'+SUFFIX)

// trash
export const TrashResource = Vue.resource(API_ROOT+'/trash'+SUFFIX)
