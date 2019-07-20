<template>
  <div id="app" class="flex flex-column fixed absolute--fill overflow-hidden">
    <template v-if="requires_auth && is_initializing">
      <!-- match navbar height -->
      <div class="bg-nearer-white" style="height: 60px"></div>
      <div class="flex-fill flex flex-column justify-center bg-nearer-white">
        <Spinner size="large" message="Initializing..." />
      </div>
    </template>
    <template v-else-if="requires_auth && (is_404 || !is_allowed)">
      <PageNotFound class="flex-fill bg-nearer-white" />
    </template>
    <template v-else>
      <AppNavbar v-if="show_navbar && is_logged_in" />
      <router-view class="flex-fill bt b--black-05"></router-view>
      <el-button
        type="primary"
        circle
        id="open-intercom-inbox"
        class="fixed bottom-0 right-0"
        style="z-index: 2147482000; padding: 12px; margin: 16px 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.4)"
        v-if="show_intercom_button"
      >
        <i class="material-icons md-24 relative" style="top: 1px">chat</i>
      </el-button>
    </template>
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import AppNavbar from '@comp/AppNavbar'
  import PageNotFound from '@comp/PageNotFound'

  export default {
    name: 'App',
    metaInfo: {
      // all titles will be injected into this template
      titleTemplate: (chunk) => {
        // if undefined or blank then we don't need the pipe
        return chunk ? `${chunk} | Flex.io` : 'Flex.io';
      },
      meta: [
        {
          vmid: 'description',
          name: 'description',
          content: 'Flex.io enables you to stitch together serverless functions with out-of-the-box helper tasks that take the pain out of OAuth, notifications, scheduling, local storage, library dependencies and other "glue" code.'
        }
      ]
    },
    components: {
      Spinner,
      AppNavbar,
      PageNotFound
    },
    computed: {
      ...mapState({
        'active_user_eid': 'active_user_eid',
        'is_initializing': 'members_fetching',
        'is_initialized': 'members_fetched'
      }),
      route_name() {
        return _.get(this.$route, 'name')
      },
      is_404() {
        return !this.route_name
      },
      requires_auth() {
        return _.get(this.$route, 'meta.requiresAuth', false)
      },
      is_allowed() {
        var members = this.getAllMembers()
        if (members.length > 0) {
          // if the active user is part of the members list, they're allowed
          var user = _.find(members, { eid: this.active_user_eid })
          return !!_.get(user, 'eid', false)
        }

        // there are no members; we're done
        return false
      },
      is_logged_in() {
        return this.active_user_eid.length > 0
      },
      show_navbar() {
        return this.requires_auth
      },
      show_intercom_button() {
        return this.requires_auth
      }
    },
    methods: {
      ...mapGetters([
        'getAllMembers'
      ])
    }
  }
</script>

<style lang="stylus">
  @import '../stylesheets/style'
</style>
