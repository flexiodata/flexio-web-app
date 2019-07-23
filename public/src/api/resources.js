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
    var api_user_root = team_name || store.state.teams.active_team_name

    if (team_name === null) {
      return API_V2_ROOT
    } else {
      return API_V2_ROOT + '/' + api_user_root
    }
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
