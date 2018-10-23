import axios from 'axios'

export const API_V2_ROOT = '/api/v2'

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

  var base_url = user_eid === null ? API_V2_ROOT : API_V2_ROOT + '/' + (user_eid ? user_eid : 'me')

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
