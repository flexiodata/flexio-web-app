import api from '../../api'
import * as types from '../mutation-types'

// ----------------------------------------------------------------------- //

export const fetchTrash = ({ commit }) => {
  commit(types.FETCHING_TRASH, { fetching: true })

  return api.fetchTrash().then(response => {
    // success callback
    commit(types.FETCHED_TRASH, { trash: response.body })
    commit(types.FETCHING_TRASH, { fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_TRASH, { fetching: false })
    return response
  })
}

// DEPRECATED
export const fetchProjectTrash = ({ commit }, project_eid) => {
  commit(types.FETCHING_TRASH, { project_eid, fetching: true })

  return api.fetchProjectTrash({ eid: project_eid }).then(response => {
    // success callback
    commit(types.FETCHED_TRASH, { project_eid, trash: response.body })
    commit(types.FETCHING_TRASH, { project_eid, fetching: false })
    return response
  }, response => {
    // error callback
    commit(types.FETCHING_TRASH, { project_eid, fetching: false })
    return response
  })
}

// ----------------------------------------------------------------------- //

export const emptyTrash = ({ commit }, { attrs }) => {
  commit(types.BULK_DELETING_ITEMS, { attrs })

  var items = _.map(attrs.items, (item) => { return item.eid })
  return api.emptyTrash({ attrs: { items: items } }).then(response => {
    // success callback
    commit(types.BULK_DELETED_ITEMS, { attrs })
    return response
  }, response => {
    // error callback
    return response
  })
}
