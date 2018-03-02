import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const fetchCurrentUser = ({ commit, dispatch }) => {
  commit(types.FETCHING_USER, true)

  return api.fetchUser({ eid: 'me' }).then(response => {
    var user = response.body

    // success callback
    commit(types.FETCHED_USER, user)
    commit(types.FETCHING_USER, false)

    dispatch('analyticsIdentify', { attrs: user })

    return response
  }, response => {
    // error callback
    commit(types.FETCHING_USER, false)
    return response
  })
}

export const analyticsIdentify = ({ commit }, { attrs }) => {
  var analytics_payload = _.pick(attrs, ['first_name','last_name','email'])

  // add Segment-friendly keys
  _.assign(analytics_payload, {
    firstName: _.get(attrs, 'first_name'),
    lastName: _.get(attrs, 'last_name'),
    username: _.get(attrs, 'user_name'),
    createdAt: _.get(attrs, 'created')
  })

  // add current pathname as 'label' (for Google Analytics)
  _.assign(analytics_payload, {
    label: window.location.pathname
  })

  analytics.identify(_.get(attrs, 'eid'), analytics_payload)
}

/*
export const fetchCurrentUserStatistics = ({ commit, state }) => {
  var user = _.get(state, 'objects.'+state.active_user_eid)

  return api.fetchUserStatistics({ eid: 'me' }).then(response => {
    var analytics_payload = _.pick(user, ['first_name','last_name','email'])

    // add Segment-friendly keys
    _.assign(analytics_payload, {
      username: _.get(user, 'user_name'),
      createdAt: _.get(user, 'created')
    }, response.body)

    setTimeout(() => {
      analytics.identify(_.get(user, 'eid'), analytics_payload)
    }, 500)
  })
}
*/

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

    analytics.track('Changed Password')

    return response
  }, response => {
    // error callback
    return response
  })
}

export const signIn = ({ commit }, { attrs }) => {
  commit(types.SIGNING_IN, true)

  return api.login({ attrs }).then(response => {
    var user = response.body

    // success callback
    commit(types.SIGNED_IN, user)
    commit(types.SIGNING_IN, false)

    //analytics.track('Signed In', _.omit(attrs, ['password']))

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

    //analytics.track('Signed Out')

    return response
  }, response => {
    // error callback
    commit(types.SIGNING_OUT, false)
    return response
  })
}

export const signUp = ({ commit }, { attrs }) => {
  commit(types.SIGNING_UP, true)

  return api.signUp({ attrs }).then(response => {
    // success callback
    commit(types.SIGNED_UP)
    commit(types.SIGNING_UP, false)

    //analytics.track('Signed Up', _.omit(attrs, ['password']))

    return response
  }, response => {
    // error callback
    commit(types.SIGNING_UP, false)
    return response
  })
}
