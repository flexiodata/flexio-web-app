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

  'CREATED_PROCESS' (state, item) {
    addItem(state, item, getDefaultMeta())
  },

  'FETCHING_PROCESSES' (state, is_fetching) {
    state.is_fetching = is_fetching
    if (is_fetching === true) {
      state.is_fetched = false
    }
  },

  'FETCHED_PROCESSES' (state, items) {
    addItem(state, items, getDefaultMeta())
    state.is_fetched = true
  },

  'FETCHED_PROCESS' (state, item) {
    var meta = _.assign(getDefaultMeta(), { is_fetched: true })
    addItem(state, item, meta)
  },

  'FETCHED_PROCESS_LOG' (state, { eid, item }) {
    updateItem(state, eid, item)
  },

  'STARTED_PROCESS' ({ commit }, { eid, item }) {
    updateItem(state, eid, item)
  },

  'CANCELING_PROCESS' ({ commit }, { eid, is_canceling }) {
    updateMeta(state, eid, { is_canceling })
  },

  'CANCELED_PROCESS' (state, { eid, item }) {
    updateItem(state, eid, item)
  },
}

const actions = {
  'create' ({ commit, dispatch }, { team_name, attrs }) {
    return api.createProcess(team_name, attrs).then(response => {
      var process = response.data
      var eid = process.eid

      commit('CREATED_PROCESS', process)

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
      return api.fetchProcess(team_name, eid).then(response => {
        var process = response.data

        commit('FETCHED_PROCESS', process)

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
      commit('FETCHING_PROCESSES', true)

      return api[team_name == 'admin' ? 'fetchAdminProcesses' : 'fetchProcesses'](team_name, attrs).then(response => {
        commit('FETCHED_PROCESSES', response.data)
        commit('FETCHING_PROCESSES', false)
        return response
      }).catch(error => {
        commit('FETCHING_PROCESSES', false)
        throw error
      })
    }
  },

  'run' ({ commit, dispatch }, { team_name, eid, cfg }) {
    dispatch('fetch', { team_name, eid, poll: true })

    return api.runProcess(team_name, eid, cfg).then(response => {
      var process = { eid, process_status: PROCESS_STATUS_RUNNING }
      commit('STARTED_PROCESS', { eid, item: process })
      return response
    }).catch(error => {
      dispatch('fetch', { team_name, eid })
      throw error
    })
  },

  /*
  'cancel' ({ commit, dispatch }, { team_name, eid }) {
    commit('CANCELING_PROCESS', { eid, is_canceling: true })

    return api.cancelProcess(team_name, eid).then(response => {
      commit('CANCELED_PROCESS', { eid, item: response.data })
      commit('CANCELING_PROCESS', { eid, is_canceling: false })
      return response
    }).catch(error => {
      commit('CANCELING_PROCESS', { eid, is_canceling: false })
      throw error
    })
  },
  */
}

const getters = {
  getAllProcesses (state) {
    var items = _.sortBy(state.items, [ item => new Date(item.created) ])
    return items.reverse()
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
}

