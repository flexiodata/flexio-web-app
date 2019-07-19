import {
  OBJECT_TYPE_USER,
  OBJECT_TYPE_PIPE,
  OBJECT_TYPE_STREAM,
  OBJECT_TYPE_CONNECTION,
  OBJECT_TYPE_PROCESS,
  OBJECT_TYPE_TOKEN
} from '../constants/object-type'
import { OBJECT_STATUS_AVAILABLE } from '../constants/object-status'

export const getAllUsers = state => {
  return _.filter(state.objects, { eid_type: OBJECT_TYPE_USER })
}

export const getAllMembers = state => {
  return _.filter(getAllUsers(state), user => _.get(user, 'member_of.eid', '').length > 0)
}

export const getAllPipes = state => {
  // NOTE: it's really important to include the '_' on the same line
  // as the 'return', otherwise JS will return without doing anything
  return _
    .chain(state.objects)
    .filter({ eid_type: OBJECT_TYPE_PIPE })
    .sortBy([ function(p) { return new Date(p.created) } ])
    .reverse()
    .value()
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

export const getFirstToken = state => {
  var tokens = getAllTokens(state)

  if (tokens.length == 0) {
    return ''
  }

  return _.get(tokens, '[0].access_code', '')
}

export const getSdkOptions = state => {
  switch (window.location.hostname) {
    case 'localhost':    return { host: 'localhost', insecure: true }
    case 'test.flex.io': return { host: 'test.flex.io' }
    case 'www.flex.io':  return { host: 'www.flex.io' }
  }

  return {}
}

export const getActiveUser = state => {
  return _.find(getAllUsers(state), { eid: state.active_user_eid })
}

export const getActiveDocument = state => {
  return _.find(state.objects, (obj) => {
    var active_id = state.active_document_identifier
    return obj.eid === active_id || obj.name === active_id
  })
}

export const getActiveDocumentProcesses = state => {
  // NOTE: it's really important to include the '_' on the same line
  // as the 'return', otherwise JS will return without doing anything
  return _
    .chain(state.objects)
    .filter({ eid_type: OBJECT_TYPE_PROCESS })
    .filter(function(p) {
      var eid = _.get(p, 'parent.eid')
      var name =_.get(p, 'parent.name')
      var active_id = state.active_document_identifier
      return eid === active_id || name === active_id
    })
    .sortBy([ function(p) { return new Date(p.started) } ])
    .value()
}
