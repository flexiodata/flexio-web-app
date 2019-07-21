import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const v2_action_fetchCurrentUser = ({ commit, dispatch }) => {
  commit(types.FETCHING_USER, true)

  return api.v2_fetchUser('me').then(response => {
    var user = response.data

    if (_.get(user, 'eid', '').length > 0) {
      commit(types.FETCHED_USER, user)
      dispatch('analyticsIdentify', user)
    }

    commit(types.FETCHING_USER, false)
    return response
  }).catch(error => {
    commit(types.FETCHING_USER, false)
    throw error
  })
}

// ----------------------------------------------------------------------- //

export const v2_action_fetchUser = ({ commit, dispatch }, { eid }) => {
  commit(types.FETCHING_USER, true)

  return api.v2_fetchUser(eid).then(response => {
    var user = response.data

    if (_.get(user, 'eid', '').length > 0) {
      commit(types.FETCHED_USER, user)
    }

    commit(types.FETCHING_USER, false)
    return response
  }).catch(error => {
    commit(types.FETCHING_USER, false)
    throw error
  })
}

// ----------------------------------------------------------------------- //

export const v2_action_updateUser = ({ commit }, { eid, attrs }) => {
  commit(types.UPDATING_USER, { eid, attrs })

  return api.v2_updateUser(eid, attrs).then(response => {
    var attrs = response.data
    commit(types.UPDATED_USER, { eid, attrs })
    return response
  }).catch(error => {
    throw error
  })
}

export const v2_action_deleteUser = ({ commit }, { eid, attrs }) => {
  commit(types.DELETING_USER, { eid, attrs })

  return api.v2_deleteUser(eid, attrs).then(response => {
    commit(types.DELETED_USER, { eid, attrs })
    return response
  }).catch(error => {
    throw error
  })
}

// ----------------------------------------------------------------------- //

/*
export const signUp = ({ commit, dispatch }, { attrs }) => {
  commit(types.SIGNING_UP, true)

  return api.signUp({ attrs }).then(response => {
    // success callback
    commit(types.SIGNED_UP)
    commit(types.SIGNING_UP, false)

    var analytics_payload = _.omit(attrs, ['password'])
    //dispatch('analyticsTrack', _.assign(analytics_payload, { event_name: 'Signed Up' }))

    return response
  }, response => {
    // error callback
    commit(types.SIGNING_UP, false)
    return response
  })
}

export const signIn = ({ commit, dispatch }, { attrs }) => {
  commit(types.SIGNING_IN, true)

  return api.login({ attrs }).then(response => {
    var user = response.data

    // success callback
    commit(types.SIGNED_IN, user)
    commit(types.SIGNING_IN, false)

    var analytics_payload = _.omit(attrs, ['password'])
    //dispatch('analyticsTrack', _.assign(analytics_payload, { event_name: 'Signed In' }))

    return response
  }, response => {
    // error callback
    commit(types.SIGNING_IN, false)
    return response
  })
}
*/

export const v2_action_signOut = ({ commit, dispatch }) => {
  commit(types.SIGNING_OUT, true)

  return api.v2_logout().then(response => {
    // reset store state
    commit('RESET_STATE')
    commit('builderdoc/RESET_STATE')
    commit('pipedoc/RESET_STATE')

    commit(types.SIGNED_OUT)
    commit(types.SIGNING_OUT, false)

    dispatch('analyticsTrack', { event_name: 'Signed Out' })

    return response
  }).catch(error => {
    commit(types.SIGNING_OUT, false)
    throw error
  })
}

export const v2_action_changePassword = ({ commit, dispatch }, { eid, attrs }) => {
  commit(types.CHANGING_PASSWORD, { eid, attrs })

  return api.v2_changePassword(eid, attrs).then(response => {
    var attrs = response.data
    commit(types.CHANGED_PASSWORD, { eid, attrs })
    dispatch('analyticsTrack', { event_name: 'Changed Password' })
    return response
  }).catch(error => {
    throw error
  })
}

export const analyticsIdentify = ({}, attrs) => {
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
}

export const analyticsTrack = ({}, attrs) => {
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
}

