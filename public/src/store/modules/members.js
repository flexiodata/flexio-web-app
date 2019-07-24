import api from '@/api'
import {
  addItem,
  updateItem,
  removeItem,
  updateMeta,
  removeMeta,
} from '@/store/helpers'
import {OBJECT_STATUS_AVAILABLE } from '@/constants/object-status'

const getDefaultMeta = () => {
  return {
    is_invite_resending: false,
    is_invite_resent: false,
    is_fetching: false,
    is_fetched: false,
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
    var meta = _.assign(getDefaultMeta(), { is_fetched: true })
    addItem(state, item, meta)
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

  'UPDATED_ITEM' (state, { eid, item }) {
    updateItem(state, eid, item)
  },

  'DELETED_ITEM' (state, eid) {
    removeItem(state, eid)
  },

  'RESENDING_INVITE' (state, { eid, is_invite_resending }) {
    updateMeta(state, eid, { is_invite_resending })
  },

  'RESENT_INVITE' (state, { eid, is_invite_resent }) {
    updateMeta(state, eid, { is_invite_resent })
  },
}

const actions = {
  'create' ({ commit, dispatch }, { team_name, attrs }) {
    return api.v2_createMember(team_name, attrs).then(response => {
      commit('CREATED_ITEM', response.data)
      return response
    }).catch(error => {
      throw error
    })
  },

  'fetch' ({ commit }, { team_name, eid }) {
    if (eid) {
      // fetching a single item
      return api.v2_fetchMember(team_name, eid).then(response => {
        commit('FETCHED_ITEM', response.data)
        return response
      }).catch(error => {
        throw error
      })
    } else {
      // fetching a collection of items
      commit('FETCHING_ITEMS', true)

      return api.v2_fetchMembers(team_name).then(response => {
        commit('FETCHED_ITEMS', response.data)
        commit('FETCHING_ITEMS', false)
        return response
      }).catch(error => {
        commit('FETCHING_ITEMS', false)
        throw error
      })
    }
  },

  'update' ({ commit }, { team_name, eid, attrs }) {
    return api.v2_updateMember(team_name, eid, attrs).then(response => {
      commit('UPDATED_ITEM', { eid, item: response.data })
      return response
    }).catch(error => {
      throw error
    })
  },

  'delete' ({ commit }, { team_name, eid }) {
    return api.v2_deleteMember(team_name, eid).then(response => {
      commit('DELETED_ITEM', eid)
      return response
    }).catch(error => {
      throw error
    })
  },

  'resendInvite' ({ commit }, { team_name, eid }) {
    commit('RESENDING_INVITE', { eid, is_invite_resending: true })

    return api.v2_reinviteMember(team_name, eid).then(response => {
      commit('RESENDING_INVITE', { eid, is_invite_resending: false })
      commit('RESENT_INVITE', { eid, is_invite_resent: true })
      setTimeout(() => { commit('RESENT_INVITE', { eid, is_invite_resent: false }) }, 4000)
      return response
    }).catch(error => {
      commit('RESENDING_INVITE', { eid, is_invite_resending: false })
      throw error
    })
  },
}

const getters = {
  getAllMembers (state) {
    var items = _.sortBy(state.items, [ item => new Date(item.created) ])
    return items
  },

  isActiveMemberAvailable (state, getters, root_state) {
    var member = _.find(getters.getAllMembers, { eid: root_state.users.active_user_eid })
    return _.get(member, 'member_status') == OBJECT_STATUS_AVAILABLE
  },
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters,
}
