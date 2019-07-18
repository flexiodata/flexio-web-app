import Vue from 'vue'
import getDefaultState from '../state'

// -- default properties to add to their respective objects when being added to the store --

const FETCH_DEFAULTS = {
  is_fetched: false,
  is_fetching: false
}

const PIPE_DEFAULTS = {
  processes_fetched: false,
  processes_fetching: false
}

const STREAM_DEFAULTS = {
  uploading: false,
  uploaded: false
}

const CONNECTION_DEFAULTS = {
  is_testing: false,
  is_disconnecting: false
}

const USER_DEFAULTS = { }
const MEMBER_DEFAULTS = { }
const PROCESS_DEFAULTS = { }
const TOKEN_DEFAULTS = { }

// -- helper functions --

const addAbstract = (state, objs, defaults, extra_defaults) => {
  // convert string (eid) to an array containing a single object
  if (_.isString(objs))
    objs = [{ eid: objs }]

  // convert object to an array containing a single object
  if (_.isObject(objs) && !_.isArray(objs))
    objs = [objs]

  // map each object to a key value
  var to_insert = _.keyBy(objs, 'eid')

  // make sure we know what defaults we're going to add to each object
  var obj_defaults = _.assign({}, FETCH_DEFAULTS, defaults, extra_defaults)

  // add project defaults to each project
  _.each(to_insert, function(obj, eid, collection) {
    // overwrite object defaults with existing defaults from the store; we do this
    // here to make sure we don't overwrite object defaults once they've been created
    var old_obj = _.get(state, 'objects.'+eid, {})
    collection[eid] = _.assign({}, obj_defaults, old_obj, obj)
  })

  // add the objects to the objects node in the state
  state['objects'] = _.assign({}, state['objects'], to_insert)
}

const updateAbstract = (state, eid, attrs) => {
  // make sure we create a new object due to Javascript object reference caching
  var new_obj = _.cloneDeep(state['objects'][eid])

  // update new object attributes
  _.each(attrs, function(val, key) {
    new_obj[key] = val
  })

  // store the new object
  state['objects'][eid] = _.assign({}, new_obj)
}

const removeAbstract = (state, objs) => {
  // convert string (eid) to an array containing a single object
  if (_.isString(objs))
    objs = [{ eid: objs }]

  // convert object to an array containing a single object
  if (_.isObject(objs) && !_.isArray(objs))
    objs = [objs]

  // map each object to a key value
  var to_delete = _.keyBy(objs, 'eid')

  // now get all of the keys (eids) as an array
  to_delete = _.keys(to_delete)

  // remove the corresponding objects from the objects node in the state
  state['objects'] = _.omit(state['objects'], to_delete)
}

const removeKeys = (state, objs, keys) => {
  // convert string (eid) to an array containing a single object
  if (_.isString(objs))
    objs = [{ eid: objs }]

  // convert object to an array containing a single object
  if (_.isObject(objs) && !_.isArray(objs))
    objs = [objs]

  // map each object to a key value
  objs = _.keyBy(objs, 'eid')

  _.each(objs, (obj, eid) => {
    // remove the corresponding keys from each object in the state
    state['objects'][eid] = _.omit(state['objects'][eid], keys)
  })
}

export const addUser           = (state, objs, extra_defaults) => { addAbstract(state, objs, USER_DEFAULTS,       extra_defaults) }
export const addPipe           = (state, objs, extra_defaults) => { addAbstract(state, objs, PIPE_DEFAULTS,       extra_defaults) }
export const addMember         = (state, objs, extra_defaults) => { addAbstract(state, objs, MEMBER_DEFAULTS,     extra_defaults) }
export const addConnection     = (state, objs, extra_defaults) => { addAbstract(state, objs, CONNECTION_DEFAULTS, extra_defaults) }
export const addProcess        = (state, objs, extra_defaults) => { addAbstract(state, objs, PROCESS_DEFAULTS,    extra_defaults) }
export const addStream         = (state, objs, extra_defaults) => { addAbstract(state, objs, STREAM_DEFAULTS,     extra_defaults) }
export const addToken          = (state, objs, extra_defaults) => { addAbstract(state, objs, TOKEN_DEFAULTS,      extra_defaults) }

export const updateObject      = (state, eid, attrs) => { updateAbstract(state, eid, attrs) }
export const updateUser        = (state, eid, attrs) => { updateAbstract(state, eid, attrs) }
export const updatePipe        = (state, eid, attrs) => { updateAbstract(state, eid, attrs) }
export const updateMember      = (state, eid, attrs) => { updateAbstract(state, eid, attrs) }
export const updateConnection  = (state, eid, attrs) => { updateAbstract(state, eid, attrs) }
export const updateProcess     = (state, eid, attrs) => { updateAbstract(state, eid, attrs) }
export const updateStream      = (state, eid, attrs) => { updateAbstract(state, eid, attrs) }

export const removeObject      = (state, objs) => { removeAbstract(state, objs) }
export const removeObjectKeys  = (state, objs, keys) => { removeKeys(state, objs, keys) }

export const resetState = (state) => {
  Object.assign(state, getDefaultState())
}
