import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const fetchPipes = ({ commit }, project_eid) => {
  commit(types.FETCHING_PIPES, { project_eid, fetching: true })

  return api.fetchProjectPipes({ eid: project_eid }).then(response => {
    // success callback
    commit(types.FETCHED_PIPES, { project_eid, pipes: response.body })
    commit(types.FETCHING_PIPES, { project_eid, fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_PIPES, { project_eid, fetching: false })
    return response
  })
}

// ----------------------------------------------------------------------- //

export const createPipe = ({ commit }, { attrs }) => {
  commit(types.CREATING_PIPE, { attrs })

  return api.createPipe({ attrs }).then(response => {
    // success callback
    var pipe = response.body
    var analytics_payload = _.pick(pipe, ['eid', 'name', 'description', 'ename'])

    // add custom info
    _.assign(analytics_payload, {
      inputType: _.get(pipe, 'task[0].metadata.connection_type', '')
    })

    // add Segment-friendly keys
    _.assign(analytics_payload, {
      createdAt: _.get(pipe, 'created')
    })

    analytics.track('Created Pipe', analytics_payload)

    commit(types.CREATED_PIPE, { attrs, pipe })

    return response
  }, response => {
    // error callback
    return response
  })
}

export const fetchPipe = ({ commit }, { eid }) => {
  commit(types.FETCHING_PIPE, { eid, fetching: true })

  return api.fetchPipe({ eid }).then(response => {
    // success callback
    commit(types.FETCHED_PIPE, response.body)
    commit(types.FETCHING_PIPE, { eid, fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_PIPE, { eid, fetching: false })
    return response
  })
}

export const updatePipe = ({ commit }, { eid, attrs }) => {
  commit(types.UPDATING_PIPE, { eid, attrs })

  return api.updatePipe({ eid, attrs }).then(response => {
    // success callback
    commit(types.UPDATED_PIPE, { eid, attrs: response.body })
    return response
  }, response => {
    // error callback
    return response
  })
}

export const deletePipe = ({ commit }, { attrs }) => {
  commit(types.DELETING_PIPE, { attrs })

  var eid = _.get(attrs, 'eid', '')
  return api.deletePipe({ eid }).then(response => {
    // success callback
    commit(types.DELETED_PIPE, { attrs })
    return response
  }, response => {
    // error callback
    return response
  })
}
