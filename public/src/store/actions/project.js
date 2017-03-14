import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const fetchProjects = ({ commit }) => {
  commit(types.FETCHING_PROJECTS, true)

  return api.fetchProjects().then(response => {
    // success callback
    commit(types.FETCHED_PROJECTS, response.body)
    commit(types.FETCHING_PROJECTS, false)
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_PROJECTS, false)
    return response
  })
}

// ----------------------------------------------------------------------- //

export const createProject = ({ commit }, { attrs }) => {
  commit(types.CREATING_PROJECT, { attrs })

  return api.createProject({ attrs }).then(response => {
    // success callback
    commit(types.CREATED_PROJECT, { attrs, project: response.body })

    analytics.track('Created Project', _.assign({}, response.body, { project_count: 43 }))

    return response
  }, response => {
    // error callback
    return response
  })
}

export const fetchProject = ({ commit }, { eid }) => {
  commit(types.FETCHING_PROJECT, { eid, fetching: true })

  return api.fetchProject({ eid }).then(response => {
    // success callback
    commit(types.FETCHED_PROJECT, response.body)
    commit(types.FETCHING_PROJECT, { eid, fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_PROJECT, { eid, fetching: false })
    return response
  })
}

export const updateProject = ({ commit }, { eid, attrs }) => {
  commit(types.UPDATING_PROJECT, { eid, attrs })

  return api.updateProject({ eid, attrs }).then(response => {
    // success callback
    commit(types.UPDATED_PROJECT, { eid, attrs: response.body })
    return response
  }, response => {
    // error callback
    return response
  })
}

export const deleteProject = ({ commit }, { attrs }) => {
  commit(types.DELETING_PROJECT, { attrs })

  var eid = _.get(attrs, 'eid', '')
  return api.deleteProject({ eid }).then(response => {
    // success callback
    commit(types.DELETED_PROJECT, { attrs })
    return response
  }, response => {
    // error callback
    return response
  })
}

// ----------------------------------------------------------------------- //

export const bulkDeleteProjectItems = ({ commit }, { project_eid, attrs }) => {
  commit(types.BULK_DELETING_PROJECT_ITEMS, { project_eid, attrs })

  var items = _.map(attrs.items, (item) => { return item.eid })
  return api.bulkDeleteProjectItems({ eid: project_eid, attrs: { items: items } }).then(response => {
    // success callback
    commit(types.BULK_DELETED_PROJECT_ITEMS, { project_eid, attrs })
    return response
  }, response => {
    // error callback
    return response
  })
}
