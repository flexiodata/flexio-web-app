<template>
  <nav class="bb b--black-20">
    <div class="flex flex-row bg-white pa1 pl3-ns pr2-ns items-center" style="min-height: 50px">
      <div class="flex-fill flex flex-row items-center truncate">
        <router-link to="/home" class="dib link v-mid min-w3" title="Home">
          <img src="../assets/logo-flexio-navbar.png" class="dib" alt="Flex.io">
        </router-link>
        <app-breadcrumbs class="lh-title f6 fw6 f4-ns fw4-ns"></app-breadcrumbs>
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
  import { mapState } from 'vuex'
  import AppBreadcrumbs from './AppBreadcrumbs.vue'
  import UserDropdown from './UserDropdown.vue'

  export default {
    components: {
      AppBreadcrumbs,
      UserDropdown
    },
    computed: {
      ...mapState([
        'user_fetching',
        'active_user_eid'
      ]),
      logged_in() {
        return this.active_user_eid.length > 0
      }
    }
  }
</script>
