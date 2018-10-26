import axios from 'axios'

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

  var base_url = API_V2_ROOT
  if (user_eid !== null) {
    base_url += '/' + (user_eid || 'me')
  }

  var getCfg = ({ method, url, data, cfg }) => {
    var url = base_url + url

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
