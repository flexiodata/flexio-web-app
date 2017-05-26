<template>
  <nav v-if="render_nav" v-show="show_nav" class="bb b--black-20">
    <div class="flex flex-row bg-white pa1 pl3-ns pr2-ns items-center" style="min-height: 50px">
      <div class="flex-fill flex flex-row items-center truncate">
        <router-link to="/home" class="dib link v-mid min-w3" title="Home">
          <img src="../assets/logo-flexio-navbar.png" class="dib" alt="Flex.io">
        </router-link>
        <app-breadcrumbs class="flex flex-row items-center lh-title f6 fw6 f4-ns fw4-ns"></app-breadcrumbs>
      </div>
      <div class="flex-none">
        <div v-if="user_fetching"></div>
        <user-dropdown v-else-if="logged_in"></user-dropdown>
        <div v-else>
          <router-link to="/signin" class="link underline-hover dib f6 f6-ns ttu b black-60 ph2 pv1 mr1 mr2-ns">Sign in</router-link>
          <router-link to="/signup" class="link no-underline dib f6 f6-ns ttu b br1 white bg-orange darken-10 ph2 ph3-ns pv2 mv1">
            <span class="di dn-ns">Sign up</span>
            <span class="dn di-ns">Sign up for free</span>
          </router-link>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
  import {
    ROUTE_ACCOUNT,
    ROUTE_EMBEDHOME,
    ROUTE_HOME,
    ROUTE_PROJECTPIPES,
    ROUTE_PROJECTMEMBERS,
    ROUTE_PROJECTCONNECTIONS,
    ROUTE_PROJECTTRASH,
    ROUTE_SIGNIN,
    ROUTE_SIGNUP,
    ROUTE_FORGOTPASSWORD,
    ROUTE_RESETPASSWORD
  } from '../constants/route'
  import { HOSTNAME } from '../constants/common'
  import { mapState, mapGetters } from 'vuex'
  import AppBreadcrumbs from './AppBreadcrumbs.vue'
  import UserDropdown from './UserDropdown.vue'

  export default {
    components: {
      AppBreadcrumbs,
      UserDropdown
    },
    computed: {
      ...mapState([
        'user_fetching'
      ]),
      render_nav() {
        return false

        switch (this.$route.name)
        {
          case ROUTE_EMBEDHOME:
          case ROUTE_PROJECTPIPES:
          case ROUTE_PROJECTMEMBERS:
          case ROUTE_PROJECTCONNECTIONS:
          case ROUTE_PROJECTTRASH:
            return false

          default:
            return true
        }
      },
      show_nav() {
        switch (this.$route.name)
        {
          case ROUTE_SIGNIN:
          case ROUTE_SIGNUP:
          case ROUTE_FORGOTPASSWORD:
          case ROUTE_RESETPASSWORD:
            return false

          default:
            return true
        }
      },
      user_eid() {
        return _.get(this.getActiveUser(), 'eid', '')
      },
      logged_in() {
        return this.user_eid.length > 0
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ])
    }
  }
</script>
