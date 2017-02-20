import store from '../../store'
import { getAllProjects } from '../../store/getters'
import { CHANGE_ACTIVE_PROJECT } from '../../store/mutation-types'

// allows any component to set the active project; if the project hasn't yet
// been fetched from the server, this mixin will do that as well and then
// set the active project after it has been fetched)

export default {
  methods: {
    setActiveProject: function(project_eid) {
      if (!project_eid)
        return

      var project = _.find(getAllProjects(store.state), { eid: project_eid })

      if (!_.get(project, 'is_fetched', false))
      {
        store.dispatch('fetchProject', { eid: project_eid }).then(response => {
          if (response.ok)
          {
            store.commit(CHANGE_ACTIVE_PROJECT, project_eid)
          }
           else
          {
            // TODO: add error handling
          }
        })
      }
       else
      {
        store.commit(CHANGE_ACTIVE_PROJECT, project_eid)
      }
    }
  }
}
