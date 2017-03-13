<template>
  <div>
    <router-link
      v-if="show_project_title"
      to="/home"
      title="Project List"
      class="flex flex-row items-center ml2 ml3-ns pl2 pl3-ns link b--black-20 bl black-60 hover-black"
    ><i class="material-icons">home</i>
    </router-link>
    <i v-if="show_project_title" class="material-icons md-24 black-60 fa-rotate-270">arrow_drop_down</i>
    <router-link
      v-if="show_project_title && show_document_title"
      :to="project_link"
      title="Project Overview"
      class="link black-60 hover-black truncate"
    >{{project_name}}
    </router-link>
    <div
      v-else
      class="link black-60 truncate"
    >{{project_name}}
    </div>
    <i v-if="show_document_title" class="material-icons md-24 black-60 fa-rotate-270">arrow_drop_down</i>
    <div
      v-if="show_document_title"
      class="dib black-60 truncate"
    >{{document_name}}
    </div>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'

  export default {
    computed: {
      ...mapState([
        'active_project',
        'active_document'
      ]),
      show_project_title() {
        return this.active_project.length > 0
      },
      show_document_title() {
        return this.show_project_title && this.active_project != this.active_document
      },
      home_link() {
        return '/home'
      },
      project_link() {
        return '/project/'+this.active_project
      },
      project_name() {
        return _.get(this.getActiveProject(), 'name', '')
      },
      document_name() {
        return _.get(this.getActiveDocument(), 'name', '')
      }
    },
    methods: {
      ...mapGetters([
        'getActiveProject',
        'getActiveDocument',
        'getActiveUser'
      ])
    }
  }
</script>
