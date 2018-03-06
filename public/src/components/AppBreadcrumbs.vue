<template>
  <div class="cursor-default">
    <router-link
      to="/home"
      class="flex flex-row items-center ml2 ml3-ns pl2 pl3-ns link b--black-10 bl mid-gray hover-black"
      v-if="show_breadcrumbs"
    >
      <i class="material-icons">home</i>
    </router-link>
    <i class="material-icons md-24 black-20 rotate-270" v-if="show_breadcrumbs">expand_more</i>
    <div class="dib mid-gray truncate" v-if="show_breadcrumbs">{{document_name}}</div>
  </div>
</template>

<script>
  import { ROUTE_ACCOUNT, ROUTE_PIPES } from '../constants/route'
  import { mapState, mapGetters } from 'vuex'

  export default {
    computed: {
      ...mapState([
        'active_document_eid'
      ]),
      show_breadcrumbs() {
        return this.$route.name == ROUTE_ACCOUNT || this.$route.name == ROUTE_PIPES
      },
      document_name() {
        if (this.$route.name == ROUTE_ACCOUNT)
          return 'Account'

        return _.get(this.getActiveDocument(), 'name', '')
      }
    },
    methods: {
      ...mapGetters([
        'getActiveDocument'
      ])
    }
  }
</script>
