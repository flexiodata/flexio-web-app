import Vue from 'vue'
import VueResource from 'vue-resource'
import axios from 'axios'

Vue.use(VueResource)

Vue.http.options.crossOrigin = true
Vue.http.options.credentials = true

// api root
export const API_V2_ROOT = '/api/v2'

// this allows the URL path to be extended via a set of parameters
const SUFFIX = '{/p1}{/p2}{/p3}{/p4}'

export const AxiosResource = (user_eid) => {
  /*
  var base_cfg = {
    method: 'get',
    url: '/api/v2/me/account',
    withCredentials: true,
    headers: {},
    data: {}
  }
  */
  var base_cfg = {
    method: 'get',
    withCredentials: true
  }

  var base_url = API_V2_ROOT + '/' + (user_eid ? user_eid : 'me')

  var getCfg = ({ method, url, data, cfg }) => {
    var url = base_url + url

    if (method == 'get') {
      var cfg = _.assign({}, cfg, { params: data })
      return _.assign({}, base_cfg, { method, url }, cfg)
    }

    return _.assign({}, base_cfg, { method, url, data }, cfg)
  }

  return {
    get(url, data, cfg) {
      return axios(getCfg({ url, data, cfg, method: 'get' }))
    },
    post(url, data, cfg) {
      return axios(getCfg({ url, data, cfg, method: 'post' }))
    },
    put(url, data, cfg) {
      return axios(getCfg({ url, data, cfg, method: 'put' }))
    },
    delete(url, data, cfg) {
      return axios(getCfg({ url, data, cfg, method: 'delete' }))
    }
  }
}

export const SignupResource        = Vue.resource(API_V2_ROOT+'/signup')
export const LoginResource         = Vue.resource(API_V2_ROOT+'/login')
export const LogoutResource        = Vue.resource(API_V2_ROOT+'/logout')
export const ValidateResource      = Vue.resource(API_V2_ROOT+'/validate')
export const ResetPasswordResource = Vue.resource(API_V2_ROOT+'/resetpassword')

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

export const UserProcessResource   = Vue.resource(API_V2_ROOT+'{/user_eid}/processes{/eid}'+SUFFIX)
