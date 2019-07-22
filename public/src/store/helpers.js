const getKeyedObject = (objs) => {
  // convert string (eid) to an array containing a single object
  if (_.isString(objs))
    objs = [{ eid: objs }]

  // convert object to an array containing a single object
  if (_.isObject(objs) && !_.isArray(objs))
    objs = [objs]

  // map each object to a key value
  return _.keyBy(objs, 'eid')
}

export const addItem = (state, objs, vuex_meta) => {
  var to_insert = getKeyedObject(objs)

  // add meta info to each item
  _.each(to_insert, function(obj, eid, collection) {
    var existing_item = _.get(state, `items.${eid}.item`, {})
    var item = _.assign({}, existing_item, obj)
    collection[eid] = _.assign({}, item, { vuex_meta })
  })

  // add the items to the items node in the state
  state['items'] = _.assign({}, state['items'], to_insert)
}

export const updateItem = (state, eid, attrs) => {
  // make sure we create a new object due to Javascript object reference caching
  var new_obj = _.cloneDeep(state['items'][eid])

  // update new object attributes
  _.assign(new_obj, attrs)

  // store the new object
  state['items'][eid] = _.assign({}, new_obj)
}

export const removeItem = (state, objs) => {
  var to_delete = getKeyedObject(objs)

  // now get all of the keys (eids) as an array
  to_delete = _.keys(to_delete)

  // remove the corresponding items from the items node in the state
  state['items'] = _.omit(state['items'], to_delete)
}

export const updateMeta = (state, eid, new_meta) => {
  var vuex_meta = state['items'][eid]['vuex_meta']
  vuex_meta = _.assign({}, vuex_meta, new_meta)
  updateItem(state, eid, { vuex_meta })
}

export const removeMeta = (state, objs, keys) => {
  var keyed_objs = getKeyedObject(objs)

  _.each(keyed_objs, (obj, eid) => {
    // remove the corresponding keys from each object in the state
    state['items'][eid]['vuex_meta'] = _.omit(state['items'][eid]['vuex_meta'], keys)
  })
}
