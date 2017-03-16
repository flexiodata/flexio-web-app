<template>
  <div v-if="is_fetching">
    <spinner size="medium" show-text loading-text="Loading projects..."></spinner>
  </div>
  <div v-else>
    <!-- control bar -->
    <div class="flex-none flex flex-row ph2 ph0-l pv2 pv3-ns">
      <div class="flex-fill">
        <input
          type="text"
          class="input-reset ba b--black-20 focus-b--transparent focus-outline focus-ow1 focus-o--blue pa2 w-90 w-70-m w-60-l f6"
          placeholder="Filter projects..."
          @keydown.esc="filter = ''"
          v-model="filter"
        >
      </div>
      <div class="flex-none">
        <btn btn-md btn-primary class="btn-add ttu b ba" @click="openAddModal()">New project</btn>
      </div>
    </div>

    <!-- list -->
    <project-list
      :filter="filter"
      @item-edit="openEditModal"
      @item-delete="openDeleteModal"
      class="flex-fill"
    ></project-list>

    <!-- add modal -->
    <project-props-modal
      open-from=".btn-add"
      close-to=".btn-add"
      ref="modal-add"
      @submit="tryCreateProject"
    ></project-props-modal>

    <!-- edit modal -->
    <project-props-modal
      ref="modal-edit"
      @submit="tryUpdateProject"
    ></project-props-modal>

    <!-- delete modal -->
    <project-delete-modal
      ref="modal-delete"
      @submit-delete="tryDeleteProject"
    ></project-delete-modal>
  </div>
</template>

<script>
  import Spinner from './Spinner.vue'
  import ProjectList from './ProjectList.vue'
  import ProjectPropsModal from './ProjectPropsModal.vue'
  import ProjectDeleteModal from './ProjectDeleteModal.vue'
  import ConfirmModal from './ConfirmModal.vue'
  import Btn from './Btn.vue'
  import { mapState, mapGetters } from 'vuex'

  export default {
    components: {
      Spinner,
      ProjectList,
      ProjectPropsModal,
      ProjectDeleteModal,
      Btn
    },
    data() {
      return {
        filter: ''
      }
    },
    computed: {
      ...mapState({
        'is_fetching': 'projects_fetching'
      })
    },
    created() {
      this.tryFetchProjects()
    },
    methods: {
      ...mapGetters([
        'hasProjects'
      ]),
      openAddModal(ref, attrs) {
        this.$refs['modal-add'].open()
      },
      openEditModal(item) {
        this.$refs['modal-edit'].open(item)
      },
      openDeleteModal(item) {
        this.$refs['modal-delete'].open(item)
      },
      tryFetchProjects() {
        if (!this.hasProjects())
          this.$store.dispatch('fetchProjects')
      },
      tryCreateProject(attrs, modal) {
        attrs = _.pick(attrs, ['name', 'description'])
        this.$store.dispatch('createProject', { attrs }).then(response => {
          if (response.ok)
          {
            modal.close()
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      tryUpdateProject(attrs, modal) {
        var eid = attrs.eid
        attrs = _.pick(attrs, ['name', 'description'])
        this.$store.dispatch('updateProject', { eid, attrs }).then(response => {
          if (response.ok)
          {
            modal.close()
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      tryDeleteProject(attrs, modal) {
        this.$store.dispatch('deleteProject', { attrs }).then(response => {
          if (response.ok)
          {
            modal.close()
          }
           else
          {
            // TODO: add error handling
          }
        })
      }
    }
  }
</script>
