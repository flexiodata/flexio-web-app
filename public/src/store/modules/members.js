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
    is_invite_resending: false,
    is_invite_resent: false,
    is_fetching: false,
    is_fetched: false,
    is_superuser: false
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

  'CREATED_MEMBER' (state, { item, meta }) {
    var _meta = _.assign(getDefaultMeta(), { is_fetched: true }, meta)
    addItem(state, item, _meta)
  },

  'FETCHING_MEMBERS' (state, is_fetching) {
    state.is_fetching = is_fetching
    if (is_fetching === true) {
      state.is_fetched = false
    }
  },

  'FETCHED_MEMBERS' (state, items) {
    addItem(state, items, getDefaultMeta())
    state.is_fetched = true
  },

  'FETCHED_MEMBER' (state, item) {
    var meta = _.assign(getDefaultMeta(), { is_fetched: true })
    addItem(state, item, meta)
  },

  'UPDATED_MEMBER' (state, { eid, item }) {
    updateItem(state, eid, item)
  },

  'DELETED_MEMBER' (state, eid) {
    removeItem(state, eid)
  },

  'FETCHED_MEMBER_RIGHTS' (state, { eid, rights }) {
    updateItem(state, eid, { rights })
  },

  'JOINED_TEAM' (state) {},

  'RESENDING_INVITE' (state, { eid, is_invite_resending }) {
    updateMeta(state, eid, { is_invite_resending })
  },

  'RESENT_INVITE' (state, { eid, is_invite_resent }) {
    updateMeta(state, eid, { is_invite_resent })
  },
}

const actions = {
  'create' ({ commit, dispatch }, { team_name, attrs }) {
    return api.createMember(team_name, attrs).then(response => {
      commit('CREATED_MEMBER', { item: response.data })
      return response
    }).catch(error => {
      throw error
    })
  },

  'fetch' ({ commit }, { team_name, eid }) {
    if (eid) {
      // fetching a single item
      return api.fetchMember(team_name, eid).then(response => {
        commit('FETCHED_MEMBER', response.data)
        return response
      }).catch(error => {
        throw error
      })
    } else {
      // fetching a collection of items
      commit('FETCHING_MEMBERS', true)

      return api.fetchMembers(team_name).then(response => {
        commit('FETCHED_MEMBERS', response.data)
        commit('FETCHING_MEMBERS', false)
        return response
      }).catch(error => {
        commit('FETCHING_MEMBERS', false)
        throw error
      })
    }
  },

  'update' ({ commit }, { team_name, eid, attrs }) {
    return api.updateMember(team_name, eid, attrs).then(response => {
      commit('UPDATED_MEMBER', { eid, item: response.data })
      return response
    }).catch(error => {
      throw error
    })
  },

  'delete' ({ commit }, { team_name, eid }) {
    return api.deleteMember(team_name, eid).then(response => {
      commit('DELETED_MEMBER', eid)
      return response
    }).catch(error => {
      throw error
    })
  },

  'fetchRights' ({ commit, dispatch, state, rootState }, { team_name, eid }) {
    return api.fetchMemberRights(team_name, eid).then(response => {
      // since we've already fetched the members; if the member doesn't
      // exist here, it's most likely because they are logged in as
      // a super-user; they are not an official member of the team,
      // but are given rights as if they are an owner of the team
      if (!_.has(state.items, eid)) {
        // try to lookup this user in the user module's `item's` list
        var user = _.get(rootState.users, 'items['+eid+']', null)
        var meta = { is_superuser: true }
        if (user) {
          commit('CREATED_MEMBER', { item: user, meta })
          commit('FETCHED_MEMBER_RIGHTS', { eid, rights: response.data })
        }
      } else {
        commit('FETCHED_MEMBER_RIGHTS', { eid, rights: response.data })
      }
      return response
    }).catch(error => {
      throw error
    })
  },

  'join' ({ commit, dispatch }, { team_name, attrs }) {
    return api.joinTeam(team_name, attrs).then(response => {
      // if a user joins a team, we need to reset the state on
      // a number of modules (pipes, connections, members, etc.)
      //
      // note that we need to force this initialization because
      // this is happening from the join team page and the active team
      // and the team we're initializing are the same, in which case
      // the `initializeTeam` action would do nothing to avoid
      // issuing API calls all the time
      dispatch('initializeTeam', { team_name, force: true }, { root: true })

      commit('JOINED_TEAM')
      return response
    }).catch(error => {
      throw error
    })
  },

  'resendInvite' ({ commit }, { team_name, eid }) {
    commit('RESENDING_INVITE', { eid, is_invite_resending: true })

    return api.reinviteMember(team_name, eid).then(response => {
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
    // make sure owner is always first in this list; the list members by the date
    // they were invited, with more recent invites at the top of the list
    var items = _.sortBy(state.items, [
      item => _.get(item, 'eid') == _.get(item, 'member_of.eid'),
      item => new Date(item.invited)
    ])
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
