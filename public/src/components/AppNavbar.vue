<template>
  <nav>
    <div class="flex flex-row items-center bg-white pv1 ph2 ph3-ns" style="min-height: 50px">
      <div class="flex-fill flex flex-row items-center" style="letter-spacing: 0.03em">
        <router-link to="/pipes" class="mr3 dib link v-mid min-w3 hint--bottom" aria-label="Home">
          <img src="../assets/logo-flexio-navbar.png" class="dib" alt="Flex.io">
        </router-link>
        <router-link :to="pipe_route" class="fw6 f6 ttu link nav-link" style="margin: 0 12px">Pipes</router-link>
        <router-link :to="connection_route" class="fw6 f6 ttu link nav-link" style="margin: 0 12px">Connections</router-link>
      </div>
      <div class="flex-none">
        <div v-if="user_fetching"></div>
        <UserDropdown v-else-if="is_logged_in" />
        <div v-else>
          <router-link to="/signin" class="dib link f6 f6-ns ttu fw6 br2 pv2 ph2 mr1 mr2-ns underline-hover gray">Sign in</router-link>
          <router-link to="/signup" class="dib link f6 f6-ns ttu fw6 br2 pv2 ph2 ph3-ns no-underline white bg-orange darken-10">
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
  import { ROUTE_APP_PIPES, ROUTE_APP_CONNECTIONS } from '../constants/route'
  import UserDropdown from '@comp/UserDropdown'

  export default {
    components: {
      UserDropdown
    },
    computed: {
      ...mapState([
        'active_user_eid',
        'routed_user',
        'user_fetching'
      ]),
      pipe_route() {
        return { name: ROUTE_APP_PIPES, params: { user_identifier: this.routed_user } }
      },
      connection_route() {
        return { name: ROUTE_APP_CONNECTIONS, params: { user_identifier: this.routed_user } }
      },
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
