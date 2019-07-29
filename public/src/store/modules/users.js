import api from '@/api'
import {
  addItem,
  updateItem,
  removeItem,
  updateMeta,
  removeMeta,
} from '@/store/helpers'

const getDefaultMeta = () => {
  return {
    is_fetching: false,
    is_fetched: false,
  }
}

const getDefaultState = () => {
  return {
    active_user_eid: '',
    is_signing_in: false,
    is_signing_out: false,
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

  'FETCHED_ITEM' (state, item) {
    var meta = _.assign(getDefaultMeta(), { is_fetched: true })
    addItem(state, item, meta)
  },

  'UPDATED_ITEM' (state, { eid, item }) {
    updateItem(state, eid, item)
  },

  'DELETED_ITEM' (state, eid) {
    removeItem(state, eid)
  },

  'SIGNING_IN' (state, is_signing_in) {
    this.is_signing_in = is_signing_in
  },

  'SIGNED_IN' (state, item) {
    var meta = _.assign(getDefaultMeta(), { is_fetched: true })
    addItem(state, item, meta)

    // store the active user eid
    state.active_user_eid = item.eid
  },

  'SIGNING_OUT' (state, is_signing_out) {
    state.is_signing_out = is_signing_out
  },

  'SIGNED_OUT' (state) {
    state.active_user_eid = ''
  },
}

const actions = {
  'fetch' ({ commit, dispatch }, { eid }) {
    if (eid == 'me') {
      commit('SIGNING_IN', true)
    }

    // fetching a single item
    return api.fetchUser(eid).then(response => {
      var user = response.data
      commit('FETCHED_ITEM', user)
      if (eid == 'me') {
        commit('SIGNED_IN', user)
        commit('SIGNING_IN', false)
        dispatch('identify', user)
      }
      return response
    }).catch(error => {
      commit('SIGNING_IN', false)
      throw error
    })
  },

  'update' ({ commit }, { eid, attrs }) {
    return api.updateUser(eid, attrs).then(response => {
      commit('UPDATED_ITEM', { eid, item: response.data })
      return response
    }).catch(error => {
      throw error
    })
  },

  'delete' ({ commit }, { eid, attrs }) {
    return api.deleteUser(eid, attrs).then(response => {
      commit('DELETED_ITEM', eid)
      return response
    }).catch(error => {
      throw error
    })
  },

  'signOut' ({ commit, dispatch }) {
    commit('SIGNING_OUT', true)

    return api.logout().then(response => {
      // we need to give just a bit of breathing room for the UI to change
      // the route to the sign in page before we update the state with these commits
      setTimeout(() => {
        commit('SIGNED_OUT')
        commit('SIGNING_OUT', false)
        dispatch('track', { event_name: 'Signed Out' })

        // reset the store state globally (including all modules)
        dispatch('resetState', {}, { root: true })
      }, 1)
      return response
    }).catch(error => {
      commit('SIGNING_OUT', false)
      throw error
    })
  },

  'changePassword' ({ commit, dispatch }, { eid, attrs }) {
    return api.changePassword(eid, attrs).then(response => {
      dispatch('track', { event_name: 'Changed Password' })
      return response
    }).catch(error => {
      throw error
    })
  },

  'identify' ({}, attrs) {
    var analytics_payload = _.pick(attrs, ['first_name', 'last_name', 'email'])

    // add Segment-friendly keys
    _.assign(analytics_payload, {
      firstName: _.get(attrs, 'first_name', null),
      lastName: _.get(attrs, 'last_name', null),
      username: _.get(attrs, 'username', null),
      createdAt: _.get(attrs, 'created', null)
    })

    // add current pathname as 'label' (for Google Analytics)
    _.set(analytics_payload, 'label', window.location.pathname)

    // remove null values
    analytics_payload = _.omitBy(analytics_payload, _.isNil)

    analytics.identify(_.get(attrs, 'eid'), analytics_payload)
  },

  'track' ({}, attrs) {
    var event_name = _.get(attrs, 'event_name', 'Event')
    var analytics_payload = _.assign({}, _.omit(attrs, ['event_name']))

    // add Segment-friendly keys
    _.assign(analytics_payload, {
      firstName: _.get(attrs, 'first_name', null),
      lastName: _.get(attrs, 'last_name', null),
      username: _.get(attrs, 'username', null),
      createdAt: _.get(attrs, 'created', null)
    })

    // add current pathname as 'label' (for Google Analytics)
    _.set(analytics_payload, 'label', window.location.pathname)

    // remove null values
    analytics_payload = _.omitBy(analytics_payload, _.isNil)

    analytics.track(event_name, analytics_payload)
  },
}

const getters = {
  getAllUsers (state) {
    var items = _.sortBy(state.items, [ item => new Date(item.created) ])
    return items.reverse()
  },

  getActiveUser (state, getters) {
    return _.find(getters.getAllUsers, { eid: state.active_user_eid })
  },

  getActiveUserEmail (state, getters) {
    return _.get(getters.getActiveUser, 'email', state.active_user_eid)
  },

  getActiveUsername (state, getters) {
    return _.get(getters.getActiveUser, 'username', state.active_user_eid)
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
}

