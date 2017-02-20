<template>
  <div v-if="is_fetching">
    <spinner size="medium" show-text loading-text="Loading projects..."></spinner>
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
      @activate="onItemActivate"
    >
    </project-item>
  </div>
</template>

<script>
  import Spinner from './Spinner.vue'
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
      onItemActivate(item) {
        this.$emit('item-activate', item)
      }
    }
  }
</script>
