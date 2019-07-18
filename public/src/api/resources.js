import axios from 'axios'
import store from '@/store'

export const API_V2_ROOT = '/api/v2'

export const AxiosResource = (team_name) => {
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
    var api_root = API_V2_ROOT
    var api_user_root = team_name || 'me'

    var allowed = false
    var allowed_routes = ['/connections', '/pipes', '/processes', '/streams', '/vfs']
    allowed_routes.forEach((route) => {
      allowed = allowed || url.indexOf(route) == 0
    })

    if (allowed && api_user_root != 'admin') {
      api_user_root = store.state.active_team_name

      // no username is specified; default to 'me'
      if (!api_user_root || api_user_root.length == 0) {
        api_user_root = 'me'
      }
    }

    return team_name === null ? api_root : api_root + '/' + api_user_root
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
