export default {
  getActiveDocument (state, getters, rootState) {
    var name = state.active_document_identifier
    var item = _.find(state.connections.items, { name })
    if (!item) {
      item = _.find(state.pipes.items, { name })
    }
    return item
  },
}
