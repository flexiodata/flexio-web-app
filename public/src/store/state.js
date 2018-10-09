export default () => {
  return {
    // TODO: move all of these into `objects_fetched` and `objects_fetching` objects?
    user_fetched: false,
    user_fetching: false,
    processes_fetched: false,
    processes_fetching: false,
    process_summary_fetched: false,
    process_summary_fetching: false,
    projects_fetched: false,
    projects_fetching: false,
    pipes_fetched: false,
    pipes_fetching: false,
    connections_fetched: false,
    connections_fetching: false,
    tokens_fetched: false,
    tokens_fetching: false,
    trash_fetched: false,
    trash_fetching: false,

    active_user_eid: '',
    active_document_eid: '',

    objects: {}
  }
}
