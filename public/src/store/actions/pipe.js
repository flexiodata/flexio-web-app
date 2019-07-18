import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const v2_action_fetchPipes = ({ commit }, { team_name }) => {
  commit(types.FETCHING_PIPES, { fetching: true })

  return api.v2_fetchPipes(team_name).then(response => {
    var pipes = response.data
    commit(types.FETCHED_PIPES, { pipes })
    commit(types.FETCHING_PIPES, { fetching: false })
    return response
  }).catch(error => {
    commit(types.FETCHING_PIPES, { fetching: false })
    throw error
  })
}

// ----------------------------------------------------------------------- //

export const v2_action_createPipe = ({ commit, dispatch }, { team_name, attrs }) => {
  commit(types.CREATING_PIPE, { attrs })

  return api.v2_createPipe(team_name, attrs).then(response => {
    var pipe = response.data
    commit(types.CREATED_PIPE, { attrs, pipe })
    return response
  }).catch(error => {
    throw error
  })
}

export const v2_action_fetchPipe = ({ commit }, { team_name, eid }) => {
  commit(types.FETCHING_PIPE, { eid, fetching: true })

  return api.v2_fetchPipe(team_name, eid).then(response => {
    var pipe = response.data
    commit(types.FETCHED_PIPE, pipe)
    commit(types.FETCHING_PIPE, { eid, fetching: false })
    return response
  }).catch(error => {
    commit(types.FETCHING_PIPE, { eid, fetching: false })
    throw error
  })
}

export const v2_action_updatePipe = ({ commit }, { team_name, eid, attrs }) => {
  commit(types.UPDATING_PIPE, { eid, attrs })

  return api.v2_updatePipe(team_name, eid, attrs).then(response => {
    var attrs = response.data
    commit(types.UPDATED_PIPE, { eid, attrs })
    return response
  }).catch(error => {
    throw error
  })
}

export const v2_action_deletePipe = ({ commit }, { team_name, eid }) => {
  var attrs = { eid }
  commit(types.DELETING_PIPE, { attrs })

  return api.v2_deletePipe(team_name, eid).then(response => {
    commit(types.DELETED_PIPE, { attrs })
    return response
  }).catch(error => {
    throw error
  })
}
