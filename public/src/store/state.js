export default () => {
  return {
    user_fetched: false,
    user_fetching: false,
    projects_fetched: false,
    projects_fetching: false,

    active_user_eid: '',
    active_document_eid: '',
    active_project_eid: '',

    objects: {}
  }
}
