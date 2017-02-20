import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const fetchCurrentUser = ({ commit }) => {
  commit(types.FETCHING_USER, true)

  return api.fetchUser({ eid: 'me' }).then(response => {
    // success callback
    commit(types.FETCHED_USER, response.body)
    commit(types.FETCHING_USER, false)
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_USER, false)
    return response
  })
}

// ----------------------------------------------------------------------- //

export const createUser = ({ commit }, { attrs }) => {
  commit(types.CREATING_USER, { attrs })

  return api.createUser({ attrs }).then(response => {
    // success callback
    commit(types.CREATED_USER, { attrs, user: response.body })
    return response
  }, response => {
    // error callback
    return response
  })
}

export const updateUser = ({ commit }, { eid, attrs }) => {
  commit(types.UPDATING_USER, { eid, attrs })

  return api.updateUser({ eid, attrs }).then(response => {
    // success callback
    commit(types.UPDATED_USER, { eid, attrs: response.body })
    return response
  }, response => {
    // error callback
    return response
  })
}

// ----------------------------------------------------------------------- //

export const changePassword = ({ commit }, { eid, attrs }) => {
  commit(types.CHANGING_PASSWORD, { eid, attrs })

  return api.changePassword({ eid, attrs }).then(response => {
    // success callback
    commit(types.CHANGED_PASSWORD, { eid, attrs: response.body })
    return response
  }, response => {
    // error callback
    return response
  })
}

export const signIn = ({ commit, dispatch }, { attrs }) => {
  commit(types.SIGNING_IN, true)

  return api.login({ attrs }).then(response => {
    // success callback
    commit(types.SIGNED_IN)
    commit(types.SIGNING_IN, false)

    // now that we've signed in, fetch the active user's info from the server
    dispatch('fetchCurrentUser')

    return response
  }, response => {
    // error callback
    commit(types.SIGNING_IN, false)
    return response
  })
}

export const signOut = ({ commit }) => {
  commit(types.SIGNING_OUT, true)

  return api.logout().then(response => {
    // success callback
    commit(types.SIGNED_OUT)
    commit(types.SIGNING_OUT, false)
    return response
  }, response => {
    // error callback
    commit(types.SIGNING_OUT, false)
    return response
  })
}
