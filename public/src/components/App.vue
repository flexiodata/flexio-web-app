<template>
  <div id="app" class="flex flex-column fixed absolute--fill overflow-hidden">
    <AppNavbar v-if="show_intercom_button && is_logged_in && !is_404" />
    <router-view class="flex-fill bt b--black-05"></router-view>
    <el-button
      type="primary"
      circle
      id="open-intercom-inbox"
      class="fixed bottom-0 right-0"
      style="z-index: 2147482000; padding: 12px; margin: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.4)"
      v-if="show_intercom_button && !is_404"
    ><i class="material-icons md-24 relative" style="top: 1px">chat</i>
    </el-button>
  </div>
</template>

<script>
  import {
    ROUTE_SIGNIN_PAGE,
    ROUTE_SIGNUP_PAGE,
    ROUTE_FORGOTPASSWORD_PAGE,
    ROUTE_RESETPASSWORD_PAGE
  } from '../constants/route'
  import { mapState, mapGetters } from 'vuex'
  import AppNavbar from '@comp/AppNavbar'

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
      AppNavbar
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      route_name() {
        return _.get(this.$route, 'name')
      },
      is_404() {
        return !this.route_name
      },
      is_logged_in() {
        return this.active_user_eid.length > 0
      },
      show_intercom_button() {
        switch (this.route_name) {
          case ROUTE_SIGNIN_PAGE:
          case ROUTE_SIGNUP_PAGE:
          case ROUTE_FORGOTPASSWORD_PAGE:
          case ROUTE_RESETPASSWORD_PAGE:
            return false
        }

        return true
      }
    }
  }
</script>

<style lang="stylus">
  @import '../stylesheets/style'
</style>
