import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const fetchCurrentUser = ({ commit, dispatch }) => {
  commit(types.FETCHING_USER, true)

  return api.fetchUser({ eid: 'me' }).then(response => {
    var user = response.body

    if (_.get(user, 'eid', '').length > 0)
    {
      commit(types.FETCHED_USER, user)
      dispatch('analyticsIdentify', user)
    }

    commit(types.FETCHING_USER, false)
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_USER, false)
    return response
  })
}

// ----------------------------------------------------------------------- //

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

export const deleteUser = ({ commit }, { eid, attrs }) => {
  commit(types.DELETING_USER, { eid, attrs })

  return api.deleteUser({ eid, attrs }).then(response => {
    // success callback
    commit(types.DELETED_USER, { eid, attrs })
    return response
  }, response => {
    // error callback
    return response
  })
}

// ----------------------------------------------------------------------- //

export const changePassword = ({ commit, dispatch }, { eid, attrs }) => {
  commit(types.CHANGING_PASSWORD, { eid, attrs })

  return api.changePassword({ eid, attrs }).then(response => {
    // success callback
    commit(types.CHANGED_PASSWORD, { eid, attrs: response.body })

    dispatch('analyticsTrack', { event_name: 'Changed Password' })

    return response
  }, response => {
    // error callback
    return response
  })
}

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
    var user = response.body

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

export const signOut = ({ commit, dispatch }) => {
  commit(types.SIGNING_OUT, true)

  return api.logout().then(response => {
    // success callback
    commit(types.SIGNED_OUT)
    commit(types.SIGNING_OUT, false)

    dispatch('analyticsTrack', { event_name: 'Signed Out' })

    return response
  }, response => {
    // error callback
    commit(types.SIGNING_OUT, false)
    return response
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

