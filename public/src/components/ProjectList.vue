<template>
  <div class="flex flex-column justify-center h-100" v-if="is_fetching">
    <spinner size="large" message="Loading projects..."></spinner>
  </div>
  <empty-item v-else-if="projects.length == 0 && filter.length > 0">
    <i slot="icon" class="material-icons">assignment</i>
    <span slot="text">No projects match the filter criteria</span>
  </empty-item>
  <empty-item v-else-if="projects.length == 0">
    <i slot="icon" class="material-icons">assignment</i>
    <span slot="text">No projects to show</span>
  </empty-item>
  <div v-else>
    <project-item
      v-for="(project, index) in projects"
      :item="project"
      :index="index"
      @edit="onItemEdit"
      @leave="onItemLeave"
      @delete="onItemDelete"
    >
    </project-item>
  </div>
</template>

<script>
  import Spinner from 'vue-simple-spinner'
  import ProjectItem from './ProjectItem.vue'
  import EmptyItem from './EmptyItem.vue'
  import { mapState, mapGetters } from 'vuex'
  import commonFilter from './mixins/common-filter'

  export default {
    props: ['filter'],
    mixins: [commonFilter],
    components: {
      Spinner,
      ProjectItem,
      EmptyItem
    },
    computed: {
      // mix this into the outer object with the object spread operator
      ...mapState({
        'is_fetching': 'projects_fetching'
      }),
      projects() {
        return this.commonFilter(this.getActiveUserProjects(), this.filter, ['name', 'description'])
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUserProjects'
      ]),
      onItemEdit(item) {
        this.$emit('item-edit', item)
      },
      onItemLeave(item) {
        this.$emit('item-leave', item)
      },
      onItemDelete(item) {
        this.$emit('item-delete', item)
      }
    }
  }
</script>
