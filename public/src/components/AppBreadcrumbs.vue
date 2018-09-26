<template>
  <el-breadcrumb
    class="flex flex-row items-center"
    separator-class="el-icon-arrow-right"
    v-if="show_breadcrumbs"
  >
    <el-breadcrumb-item
      class="flex flex-row items-center"
      to="/dashboard"
    >
      <i class="material-icons md-18 hint--bottom" aria-label="Back to dashboard">home</i>
    </el-breadcrumb-item>
    <el-breadcrumb-item
      to="/pipes"
      v-if="is_pipes"
      data-tour-step="pipe-onboarding-6"
    >
      <span class="hint--bottom fw4" aria-label="Back to pipe list">Pipes</span>
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
      is_pipes() {
        return this.$route.name == ROUTE_PIPES
      }
    },
    methods: {
      ...mapGetters([
        'getActiveDocument'
      ])
    }
  }
</script>
