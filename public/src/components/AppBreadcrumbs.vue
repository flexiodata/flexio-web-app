<template>
  <div class="cursor-default lh-title f7" v-if="show_breadcrumbs">
    <router-link
      class="flex flex-row items-center link mid-gray hover-black"
      :to="home_link"
    >
      <i
        class="material-icons md-18 hint--bottom"
        :aria-label="home_title"
      >
        home
      </i>
    </router-link>
    <i class="material-icons md-18 black-20 rotate-270">expand_more</i>
    <div class="dib mid-gray truncate">{{doc_name}}</div>
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
        switch (this.$route.name) {
          case ROUTE_ACCOUNT:
          case ROUTE_PIPES:
            return true
        }

        return false
      },
      doc_name() {
        if (this.$route.name == ROUTE_ACCOUNT) {
          return 'Account'
        }

        return _.get(this.getActiveDocument(), 'name', '')
      },
      home_title() {
        switch (this.$route.name) {
          case ROUTE_PIPES: return 'Back to pipe list'
        }

        return 'Back to dashboard'
      },
      home_link() {
        switch (this.$route.name) {
          case ROUTE_PIPES: return '/pipes'
        }

        return '/dashboard'
      }
    },
    methods: {
      ...mapGetters([
        'getActiveDocument'
      ])
    }
  }
</script>
