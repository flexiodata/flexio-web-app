import axios from 'axios'
import store from '../store'

export const API_V2_ROOT = '/api/v2'

export const AxiosResource = (user_eid) => {
  var base_cfg = {
    method: 'get',
    withCredentials: true
    /*
    url: '',
    headers: {},
    data: {}
    */
  }

  var getBaseUrl = (url) => {
    // all calls default to 'me'
    var user_eid = 'me'

    var allowed = false
    var allowed_routes = ['/connections', '/pipes', '/processes', '/streams', '/vfs']
    allowed_routes.forEach((route) => {
      allowed = allowed || url.indexOf(route) == 0
    })

    if (allowed) {
      var user_eid = store.state.routed_user

      // no username is specified; default to 'me'
      if (!user_eid || user_eid.length == 0) {
        user_eid = 'me'
      }
    }

    return API_V2_ROOT + '/' + user_eid
  }

  var getCfg = ({ method, url, data, cfg }) => {
    var url = getBaseUrl(url) + url

    // use the `data` object as GET params
    if (method == 'get') {
      var cfg = _.assign({}, cfg, { params: data })
      return _.assign({}, base_cfg, { method, url }, cfg)
    }

    return _.assign({}, base_cfg, { method, url, data }, cfg)
  }

  return {
    get    (url, data, cfg) { return axios(getCfg({ url, data, cfg, method: 'get'    })) },
    post   (url, data, cfg) { return axios(getCfg({ url, data, cfg, method: 'post'   })) },
    put    (url, data, cfg) { return axios(getCfg({ url, data, cfg, method: 'put'    })) },
    delete (url, data, cfg) { return axios(getCfg({ url, data, cfg, method: 'delete' })) },
  }
}
