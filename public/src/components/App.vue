'<template>
  <div id="app" class="flex flex-column fixed absolute--fill overflow-hidden">
    <AppNavbar v-if="show_intercom_button && show_navbar" />
    <router-view class="flex-fill bt b--black-05"></router-view>
    <el-button
      type="primary"
      circle
      id="open-intercom-inbox"
      class="fixed bottom-0 right-0"
      style="z-index: 2147482000; padding: 12px; margin: 24px; box-shadow: 0 1px 0 rgba(255,255,255,1), 0 2px 12px rgba(0,0,0,0.4)"
      v-if="show_intercom_button"
    ><i class="material-icons md-24 relative" style="top: 1px">chat</i>
    </el-button>
  </div>
</template>

<script>
  import {
    ROUTE_SIGNIN,
    ROUTE_SIGNUP,
    ROUTE_FORGOTPASSWORD,
    ROUTE_RESETPASSWORD
  } from '../constants/route'
  import { mapState, mapGetters } from 'vuex'
  import AppNavbar from './AppNavbar.vue'

  export default {
    name: 'App',
    components: {
      AppNavbar
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      route_name() {
        return _.get(this.$route, 'name', '')
      },
      is_logged_in() {
        return this.active_user_eid.length > 0
      },
      show_navbar() {
        return this.is_logged_in
      },
      show_intercom_button() {
        switch (this.route_name) {
          case ROUTE_SIGNIN:
          case ROUTE_SIGNUP:
          case ROUTE_FORGOTPASSWORD:
          case ROUTE_RESETPASSWORD:
            return false

          // 404
          case null:
            return true
        }

        return true
      }
    }
  }
</script>

<style lang="stylus">
  @import '../stylesheets/style'
</style>
