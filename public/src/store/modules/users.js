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
    is_signing_up: false,
    is_signing_in: false,
    is_signing_out: false,
    is_initializing: false, // when fetching 'me'
    is_changing_password: false,
    is_updating: false,
    is_deleting: false,
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

  'FETCHED_USER' (state, item) {
    var meta = _.assign(getDefaultMeta(), { is_fetched: true })
    addItem(state, item, meta)
  },

  'UPDATING_USER' (state, is_updating) {
    state.is_updating = is_updating
  },

  'UPDATED_USER' (state, { eid, item }) {
    updateItem(state, eid, item)
  },

  'DELETING_USER' (state, is_deleting) {
    state.is_deleting = is_deleting
  },

  'DELETED_USER' (state, eid) {
    removeItem(state, eid)
  },

  'INITIALIZING_USER' (state, is_initializing) {
    state.is_initializing = is_initializing
  },

  'INITIALIZED_USER' (state, item) {
    var meta = _.assign(getDefaultMeta(), { is_fetched: true })
    addItem(state, item, meta)

    // store the active user eid
    state.active_user_eid = item.eid
  },

  'SIGNING_UP' (state, is_signing_up) {
    state.is_signing_up = is_signing_up
  },

  'SIGNED_UP' (state, item) {
    var meta = _.assign(getDefaultMeta())
    addItem(state, item, meta)
  },

  'SIGNING_IN' (state, is_signing_in) {
    state.is_signing_in = is_signing_in
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

  'CHANGING_PASSWORD' (state, is_changing_password) {
    state.is_changing_password = is_changing_password
  },

  'CHANGED_PASSWORD' (state) {},
}

const actions = {
  'fetch' ({ commit, dispatch }, { eid }) {
    if (eid == 'me') {
      commit('INITIALIZING_USER', true)
    }

    // fetching a single item
    return api.fetchUser(eid).then(response => {
      var user = response.data
      if (eid == 'me') {
        commit('INITIALIZING_USER', false)
        commit('INITIALIZED_USER', user)
        dispatch('identify', user)
      }
      commit('FETCHED_USER', user)
      return response
    }).catch(error => {
      commit('INITIALIZING_USER', false)
      throw error
    })
  },

  'update' ({ commit }, { eid, attrs }) {
    commit('UPDATING_USER', true)
    return api.updateUser(eid, attrs).then(response => {
      commit('UPDATING_USER', false)
      commit('UPDATED_USER', { eid, item: response.data })
      return response
    }).catch(error => {
      commit('UPDATING_USER', false)
      throw error
    })
  },

  'delete' ({ commit }, { eid, attrs }) {
    commit('DELETING_USER', true)
    return api.deleteUser(eid, attrs).then(response => {
      commit('DELETING_USER', false)
      commit('DELETED_USER', eid)
      return response
    }).catch(error => {
      commit('DELETING_USER', false)
      throw error
    })
  },

  'signOut' ({ commit, dispatch }) {
    commit('SIGNING_OUT', true)

    return api.signOut().then(response => {
      // we need to give just a bit of breathing room for the UI to change
      // the route to the sign in page before we update the state with these commits
      setTimeout(() => {
        commit('SIGNING_OUT', false)
        commit('SIGNED_OUT')
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

  'signIn' ({ commit, dispatch }, attrs) {
    commit('SIGNING_IN', true)

    return api.signIn(attrs).then(response => {
      var user = response.data
      commit('SIGNING_IN', false)
      commit('SIGNED_IN', user)
      dispatch('identify', user)
      setTimeout(() => {
        dispatch('track', _.assign({}, user, { event_name: 'Signed In' }))
      }, 100)
      return response
    }).catch(error => {
      commit('SIGNING_IN', false)
      throw error
    })
  },

  'signUp' ({ commit, dispatch }, attrs) {
    commit('SIGNING_UP', true)

    return api.signUp(attrs).then(response => {
      var user = response.data
      commit('SIGNING_UP', false)
      commit('SIGNED_UP', user)
      dispatch('identify', user)
      setTimeout(() => {
        dispatch('track', _.assign({}, user, { event_name: 'Signed Up' }))
      }, 100)
      return response
    }).catch(error => {
      commit('SIGNING_UP', false)
      throw error
    })
  },

  'changePassword' ({ commit, dispatch }, { eid, attrs }) {
    commit('CHANGING_PASSWORD', true)
    return api.changePassword(eid, attrs).then(response => {
      commit('CHANGING_PASSWORD', false)
      commit('CHANGED_PASSWORD')
      dispatch('track', { event_name: 'Changed Password' })
      return response
    }).catch(error => {
      commit('CHANGING_PASSWORD', false)
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

  getActiveUser (state) {
    return _.find(state.items, { eid: state.active_user_eid })
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

