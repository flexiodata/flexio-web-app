<template>
  <nav class="z-10 bb b--black-20">
    <div class="flex flex-row items-center bg-white pv1 ph2 ph3-ns" style="min-height: 54px">
      <div class="flex-fill flex flex-row items-center">
        <router-link to="/pipes" class="mr3 dib link v-mid min-w3 hint--bottom" aria-label="Home">
          <img src="../assets/logo-flexio-navbar.png" class="dib" alt="Flex.io">
        </router-link>
        <router-link to="/pipes" class="fw6 f6 ttu link nav-link" style="margin: 0 10px" data-tour-step="pipe-onboarding-8">Pipes</router-link>
        <router-link to="/connections" class="fw6 f6 ttu link nav-link" style="margin: 0 10px">Connections</router-link>
        <router-link to="/storage" class="fw6 f6 ttu link nav-link" style="margin: 0 10px">Storage</router-link>
        <AppBreadcrumbs class="flex flex-row items-center pl2 pl3-ns pv1 b--black-10 bl" v-if="false" />
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

<style lang="stylus" scoped>
  @import '../stylesheets/variables.styl'

  .nav-link
    padding-top: 4px
    padding-bottom: 2px
    border-bottom: 2px solid transparent
    color: $body-color
    transition: border 0.3s ease-in-out

    &.router-link-active
    &:hover
      border-bottom-color: $blue
</style>
