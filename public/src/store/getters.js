import {
  OBJECT_TYPE_USER,
  OBJECT_TYPE_PROJECT,
  OBJECT_TYPE_PIPE,
  OBJECT_TYPE_STREAM,
  OBJECT_TYPE_CONNECTION,
  OBJECT_TYPE_PROCESS,
  OBJECT_TYPE_TOKEN,
  OBJECT_TYPE_RIGHT
} from '../constants/object-type'

import {
  OBJECT_STATUS_AVAILABLE,
  OBJECT_STATUS_TRASH
} from '../constants/object-status'

export const getAllProjects = state => {
  return _.filter(state.objects, { eid_type: OBJECT_TYPE_PROJECT })
}

export const getAllPipes = state => {
  // NOTE: it's really important to include the '_' on the same line
  // as the 'return', otherwise JS will return without doing anything
  return _
    .chain(state.objects)
    .filter({ eid_type: OBJECT_TYPE_PIPE })
    .reject({ eid_status: OBJECT_STATUS_TRASH })
    .sortBy([ function(p) { return new Date(p.created) } ])
    .reverse()
    .value()
}

export const getAllUsers = state => {
  return _.filter(state.objects, { eid_type: OBJECT_TYPE_USER })
}

export const getAllConnections = state => {
  // NOTE: it's really important to include the '_' on the same line
  // as the 'return', otherwise JS will return without doing anything
  return _
    .chain(state.objects)
    .filter({ eid_type: OBJECT_TYPE_CONNECTION })
    .sortBy([ function(c) { return new Date(c.created) } ])
    .reverse()
    .value()
}

export const getAvailableConnections = state => {
  // NOTE: it's really important to include the '_' on the same line
  // as the 'return', otherwise JS will return without doing anything
  return _
    .chain(state.objects)
    .filter({ eid_type: OBJECT_TYPE_CONNECTION, eid_status: OBJECT_STATUS_AVAILABLE })
    .sortBy([ function(c) { return new Date(c.created) } ])
    .reverse()
    .value()
}

export const getAllProcesses = state => {
  return _.filter(state.objects, { eid_type: OBJECT_TYPE_PROCESS })
}

export const getAllStreams = state => {
  return _.filter(state.objects, { eid_type: OBJECT_TYPE_STREAM })
}

export const getAllTokens = state => {
  // NOTE: it's really important to include the '_' on the same line
  // as the 'return', otherwise JS will return without doing anything
  return _
    .chain(state.objects)
    .filter({ eid_type: OBJECT_TYPE_TOKEN })
    .filter(function(t) { return _.get(t, 'user_eid') == state.active_user_eid })
    .sortBy([ function(t) { return new Date(t.created) } ])
    .reverse()
    .value()
}

export const getAllRights = state => {
  return _.filter(state.objects, { eid_type: OBJECT_TYPE_RIGHT })
}

export const getAllTrash = state => {
  // NOTE: it's really important to include the '_' on the same line
  // as the 'return', otherwise JS will return without doing anything
  return _
    .chain(state.objects)
    .filter({ eid_status: OBJECT_STATUS_TRASH })
    .sortBy([ function(t) { return new Date(t.created) } ])
    .reverse()
    .value()
}

export const getActiveUser = state => {
  return _.find(getAllUsers(state), { eid: state.active_user_eid })
}

export const getActiveProject = state => {
  return _.find(getAllProjects(state), { eid: state.active_project_eid })
}

export const getActiveDocument = state => {
  return _.find(state.objects, { eid: state.active_document_eid })
}

export const getActiveUserProjects = state => {
  // NOTE: it's really important to include the '_' on the same line
  // as the 'return', otherwise JS will return without doing anything
  return _
    .chain(getAllProjects(state))
    .sortBy([ function(p) { return new Date(p.created) } ])
    .reverse()
    .value()
}

export const getActiveDocumentProcesses = state => {
  // NOTE: it's really important to include the '_' on the same line
  // as the 'return', otherwise JS will return without doing anything
  return _
    .chain(state.objects)
    .filter({ eid_type: OBJECT_TYPE_PROCESS })
    .filter(function(p) { return _.get(p, 'parent.eid') == state.active_document_eid })
    .sortBy([ function(p) { return new Date(p.started) } ])
    .value()
}

export const hasProjects = state => {
  return state.projects_fetched && _.size(getAllProjects(state)) > 0
}

export const hasPipes = state => {
  return state.pipes_fetched && _.size(getAllPipes(state)) > 0
}

export const hasConnections = state => {
  return state.connections_fetched && _.size(getAvailableConnections(state)) > 0
}
