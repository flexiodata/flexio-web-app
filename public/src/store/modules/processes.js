import api from '@/api'
import {
  addItem,
  updateItem,
  removeItem,
  updateMeta,
  removeMeta,
} from '@/store/helpers'
import {
  PROCESS_STATUS_PENDING,
  PROCESS_STATUS_WAITING,
  PROCESS_STATUS_RUNNING
} from '@/constants/process'

const getDefaultMeta = () => {
  return {
    is_fetching: false,
    is_fetched: false/*,
    is_canceling: false,
    is_canceled: false*/
  }
}

const getDefaultState = () => {
  return {
    is_fetching: false,
    is_fetched: false,
    items: {},
  }
}

const state = getDefaultState()

const mutations = {
  'RESET_STATE' (state) {
    _.assign(state, getDefaultState())
  },

  'CREATED_ITEM' (state, item) {
    addItem(state, item, getDefaultMeta())
  },

  'FETCHING_ITEMS' (state, is_fetching) {
    state.is_fetching = is_fetching
    if (is_fetching === true) {
      state.is_fetched = false
    }
  },

  'FETCHED_ITEMS' (state, items) {
    addItem(state, items, getDefaultMeta())
    state.is_fetched = true
  },

  'FETCHED_ITEM' (state, item) {
    var meta = _.assign(getDefaultMeta(), { is_fetched: true })
    addItem(state, item, meta)
  },

  'FETCHED_ITEM_LOG' (state, { eid, item }) {
    updateItem(state, eid, item)
  },

  'STARTED_ITEM' ({ commit }, { eid, item }) {
    updateItem(state, eid, item)
  },

  'CANCELING_ITEM' ({ commit }, { eid, is_canceling }) {
    updateMeta(state, eid, { is_canceling })
  },

  'CANCELED_ITEM' (state, { eid, item }) {
    updateItem(state, eid, item)
  },
}

const actions = {
  'create' ({ commit, dispatch }, { team_name, attrs }) {
    return api.v2_createProcess(team_name, attrs).then(response => {
      var process = response.data
      var eid = process.eid

      commit('CREATED_ITEM', process)

      // the 'run' parameter was specified which means
      // we've started the process; poll for the process
      if (_.get(attrs, 'run', false) === true && eid) {
        dispatch('fetch', { team_name, eid, poll: true })
      }

      return response
    }).catch(error => {
      throw error
    })
  },

  'fetch' ({ commit, dispatch }, { team_name, eid, poll, attrs }) {
    if (eid) {
      // fetching a single item
      return api.v2_fetchProcess(team_name, eid).then(response => {
        var process = response.data

        commit('FETCHED_ITEM', process)

        if (poll === true) {
          // poll the process while it is still running
          var status = _.get(process, 'process_status')
          if (_.includes([
                PROCESS_STATUS_PENDING,
                PROCESS_STATUS_WAITING,
                PROCESS_STATUS_RUNNING
              ], status)) {
            _.delay(function() {
              dispatch('fetch', { team_name, eid, poll: true })
            }, 500)
          }
        }

        return response
      }).catch(error => {
        throw error
      })
    } else {
      // fetching a collection of items
      commit('FETCHING_ITEMS', true)

      return api[team_name == 'admin' ? 'v2_fetchAdminProcesses' : 'v2_fetchProcesses'](team_name, attrs).then(response => {
        commit('FETCHED_ITEMS', response.data)
        commit('FETCHING_ITEMS', false)
        return response
      }).catch(error => {
        commit('FETCHING_ITEMS', false)
        throw error
      })
    }
  },

  'fetchLog' ({ commit, dispatch }, { team_name, eid }) {
    return api.v2_fetchProcessLog(team_name, eid).then(response => {
      var item = { log: response.data }
      commit('FETCHED_ITEM_LOG', { eid, item })
      return response
    }).catch(error => {
      throw error
    })
  },

  'run' ({ commit, dispatch }, { team_name, eid, cfg }) {
    dispatch('fetch', { team_name, eid, poll: true })

    return api.v2_runProcess(team_name, eid, cfg).then(response => {
      var process = { eid, process_status: PROCESS_STATUS_RUNNING }
      commit('STARTED_ITEM', { eid, item: process })
      return response
    }).catch(error => {
      dispatch('fetch', { team_name, eid })
      throw error
    })
  },

  /*
  'cancel' ({ commit, dispatch }, { team_name, eid }) {
    commit('CANCELING_ITEM', { eid, is_canceling: true })

    return api.v2_cancelProcess(team_name, eid).then(response => {
      commit('CANCELED_ITEM', { eid, item: response.data })
      commit('CANCELING_ITEM', { eid, is_canceling: false })
      return response
    }).catch(error => {
      commit('CANCELING_ITEM', { eid, is_canceling: false })
      throw error
    })
  },
  */
}

const getters = {
  getAllProcesses (state) {
    var items = _.sortBy(state.items, [ function(p) { return new Date(p.created) } ])
    return items.reverse()
  },

  getActiveDocumentProcesses (state, getters, root_state) {
    var doc_id = root_state.active_document_identifier
    var items = getters.getAllProcesses
    return _.filter(items, p => _.get(p, 'parent.eid') === doc_id || _.get(p, 'parent.name') === doc_id)
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
}

