<template>
  <div class="cursor-default">
    <router-link
      v-if="show_first_descendant"
      to="/home"
      class="flex flex-row items-center ml2 ml3-ns pl2 pl3-ns link b--black-10 bl mid-gray hover-black"
    ><i class="material-icons">home</i>
    </router-link>
    <i v-if="show_first_descendant" class="material-icons md-24 black-20 fa-rotate-270">arrow_drop_down</i>
    <router-link
      v-if="show_first_descendant && show_second_descendant"
      :to="first_link"
      class="link mid-gray hover-black truncate"
    >{{first_name}}
    </router-link>
    <div
      v-else
      class="link mid-gray truncate"
    >{{first_name}}
    </div>
    <i v-if="show_second_descendant" class="material-icons md-24 black-20 fa-rotate-270">arrow_drop_down</i>
    <div
      v-if="show_second_descendant"
      class="dib mid-gray truncate"
    >{{document_name}}
    </div>
  </div>
</template>

<script>
  import { ROUTE_ACCOUNT } from '../constants/route'
  import { mapState, mapGetters } from 'vuex'

  export default {
    computed: {
      ...mapState([
        'active_project_eid',
        'active_document_eid'
      ]),
      show_first_descendant() {
        return this.active_project_eid.length > 0 || this.$route.name == ROUTE_ACCOUNT
      },
      show_second_descendant() {
        return this.show_first_descendant &&
          this.active_project_eid.length > 0 &&
          this.active_document_eid.length > 0 &&
          this.active_project_eid != this.active_document_eid
      },
      home_link() {
        return '/home'
      },
      first_link() {
        return this.active_project_eid.length > 0 ? '/project/'+this.active_project_eid :
          this.$route.name == ROUTE_ACCOUNT ? '/account' : ''
      },
      first_name() {
        return this.active_project_eid.length > 0 ? _.get(this.getActiveProject(), 'name', '') :
          this.$route.name == ROUTE_ACCOUNT ? 'Account' : ''
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
