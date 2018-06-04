<template>
  <el-breadcrumb
    class="flex flex-row items-center"
    separator-class="el-icon-arrow-right"
    v-if="show_breadcrumbs"
  >
    <el-breadcrumb-item
      class="flex flex-row items-center"
      :to="home_link"
    >
      <i
        class="material-icons md-18 hint--bottom"
        :aria-label="home_title"
      >
        home
      </i>
    </el-breadcrumb-item>
    <el-breadcrumb-item>
      <span class="cursor-default">{{doc_name}}</span>
    </el-breadcrumb-item>
  </el-breadcrumb>
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
          return 'Account Settings'
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
