export default () => {
  return {
    user_fetched: false,
    user_fetching: false,
    projects_fetched: false,
    projects_fetching: false,
    pipes_fetched: false,
    pipes_fetching: false,
    connections_fetched: false,
    connections_fetching: false,
    trash_fetched: false,
    trash_fetching: false,

    active_user_eid: '',
    active_document_eid: '',

    objects: {},
    statistics: {}
  }
}
