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

export const LoginResource      = Vue.resource(API_ROOT+'/login')
export const LogoutResource     = Vue.resource(API_ROOT+'/logout')
export const SignupResource     = Vue.resource(API_ROOT+'/signup'+SUFFIX)
export const ValidateResource   = Vue.resource(API_ROOT+'/validate'+SUFFIX)
export const RightsResource     = Vue.resource(API_ROOT+'/rights{/eid}'+SUFFIX)
export const HelpResource       = Vue.resource(API_ROOT+'/help/conversation')
export const UserResource       = Vue.resource(API_ROOT+'/users{/eid}'+SUFFIX)
export const ProjectResource    = Vue.resource(API_ROOT+'/projects{/eid}'+SUFFIX)
export const ConnectionResource = Vue.resource(API_ROOT+'/connections{/eid}'+SUFFIX)
export const PipeResource       = Vue.resource(API_ROOT+'/pipes{/eid}'+SUFFIX)
export const ProcessResource    = Vue.resource(API_ROOT+'/processes{/eid}'+SUFFIX)
export const AdminResource      = Vue.resource(API_ROOT+'/admin/statistics{/type}')
export const StatisticsResource = Vue.resource(API_ROOT+'/statistics{/type}')
export const StreamResource     = Vue.resource(API_ROOT+'/streams{/eid}')
export const TestResource       = Vue.resource(API_ROOT+'/tests'+SUFFIX)
export const TrashResource      = Vue.resource(API_ROOT+'/trash'+SUFFIX)
export const VfsResource        = Vue.resource(API_ROOT+'/vfs{/action}'+SUFFIX)
