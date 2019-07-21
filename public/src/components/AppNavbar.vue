<template>
  <nav>
    <div class="flex flex-row items-center bg-white pv1 ph2 ph3-ns" style="height: 60px">
      <div class="flex-fill flex flex-row items-center" style="letter-spacing: 0.03em">
        <router-link to="/pipes" class="mr3 dib link v-mid min-w3 hint--bottom" aria-label="Home">
          <img src="../assets/logo-flexio-navbar.png" class="dib" alt="Flex.io">
        </router-link>
        <TeamDropdown class="ml3" />
        <template v-if="isActiveMemberAvailable()">
          <router-link :to="pipe_route" class="fw6 f6 ttu link nav-link">Pipes</router-link>
          <router-link :to="connection_route" class="fw6 f6 ttu link nav-link">Connections</router-link>
          <router-link :to="member_route" class="fw6 f6 ttu link nav-link">Members</router-link>
        </template>
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
  import { mapState, mapGetters } from 'vuex'
  import {
    ROUTE_APP_PIPES,
    ROUTE_APP_CONNECTIONS,
    ROUTE_APP_MEMBERS
  } from '../constants/route'
  import TeamDropdown from '@comp/TeamDropdown'
  import UserDropdown from '@comp/UserDropdown'

  export default {
    components: {
      TeamDropdown,
      UserDropdown
    },
    computed: {
      ...mapState([
        'active_user_eid',
        'active_team_name',
        'user_fetching'
      ]),
      pipe_route() {
        return { name: ROUTE_APP_PIPES, params: { team_name: this.active_team_name } }
      },
      connection_route() {
        return { name: ROUTE_APP_CONNECTIONS, params: { team_name: this.active_team_name } }
      },
      member_route() {
        return { name: ROUTE_APP_MEMBERS, params: { team_name: this.active_team_name } }
      },
      is_logged_in() {
        return this.active_user_eid.length > 0
      }
    },
    methods: {
      ...mapGetters([
        'isActiveMemberAvailable'
      ])
    }
  }
</script>

<style lang="stylus" scoped>
  @import '../stylesheets/variables.styl'

  .nav-link
    margin: 0 0 0 1rem
    padding-top: 4px
    padding-bottom: 2px
    border-bottom: 2px solid transparent
    color: $body-color
    transition: border 0.3s ease-in-out

    &.router-link-active
    &:hover
      border-bottom-color: $blue
</style>
