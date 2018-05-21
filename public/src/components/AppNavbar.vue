<template>
  <nav class="z-10" style="box-shadow: 0 2px 16px -5px rgba(0,0,0,0.2)">
    <div class="flex flex-row items-center bg-white pv1 ph2 ph3-ns" style="min-height: 54px">
      <div class="flex-fill flex flex-row items-center">
        <router-link to="/home" class="dib link v-mid min-w3 hint--bottom" aria-label="Home">
          <img src="../assets/logo-flexio-navbar.png" class="dib" alt="Flex.io">
        </router-link>
        <AppBreadcrumbs class="flex flex-row items-center ml2 ml3-ns pl2 pl3-ns pv1 b--black-10 bl" />
      </div>
      <div class="flex-none">
        <div v-if="user_fetching"></div>
        <UserDropdown v-else-if="is_logged_in" />
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
        'active_user_eid',
        'user_fetching'
      ]),
      is_logged_in() {
        return this.active_user_eid.length > 0
      }
    }
  }
</script>
