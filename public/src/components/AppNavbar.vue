<template>
  <nav>
    <FreeTrialNotice
      class="flex flex-row items-center justify-center f8"
      style="padding: 4px; color: #66b1ff; background-color: #ecf5ff"
      :show-upgrade="true"
      v-if="false"
    />
    <div class="flex flex-row items-center bg-white pv1 ph2 ph3-ns" style="height: 60px">
      <div class="flex-fill flex flex-row items-center" style="letter-spacing: 0.03em">
        <router-link to="/" class="mr3 link hint--bottom" aria-label="Home">
          <img src="../assets/logo-circle-48x48.png" alt="Flex.io" class="v-mid">
        </router-link>
        <div
          class="ml3"
          v-if="is_my_account"
        >
          My Account
        </div>
        <template
          v-else-if="show_team_nav"
        >
          <TeamDropdown class="ml3 nr2" />
          <template>
            <router-link :to="pipe_route" class="f5 link nav-link">Functions</router-link>
            <router-link :to="connection_route" class="f5 link nav-link">Connections</router-link>
            <router-link :to="member_route" class="f5 link nav-link">Members</router-link>
            <router-link :to="activity_route" class="f5 link nav-link">Activity</router-link>
          </template>
        </template>
      </div>
      <div class="flex-none">
        <UserDropdown v-if="is_logged_in" />
      </div>
    </div>
  </nav>
</template>


<script>
  import { mapState, mapGetters } from 'vuex'
  import {
    ROUTE_APP_ACCOUNT,
    ROUTE_APP_ACTIVITY,
    ROUTE_APP_FUNCTIONS,
    ROUTE_APP_CONNECTIONS,
    ROUTE_APP_MEMBERS
  } from '@/constants/route'
  import FreeTrialNotice from '@/components/FreeTrialNotice'
  import TeamDropdown from '@/components/TeamDropdown'
  import UserDropdown from '@/components/UserDropdown'

  export default {
    components: {
      FreeTrialNotice,
      TeamDropdown,
      UserDropdown
    },
    computed: {
      ...mapState({
        active_user_eid: state => state.users.active_user_eid,
        active_team_name:  state => state.teams.active_team_name
      }),
      pipe_route() {
        return { name: ROUTE_APP_FUNCTIONS, params: { team_name: this.active_team_name } }
      },
      connection_route() {
        return { name: ROUTE_APP_CONNECTIONS, params: { team_name: this.active_team_name } }
      },
      member_route() {
        return { name: ROUTE_APP_MEMBERS, params: { team_name: this.active_team_name } }
      },
      activity_route() {
        return { name: ROUTE_APP_ACTIVITY, params: { team_name: this.active_team_name } }
      },
      is_logged_in() {
        return this.active_user_eid.length > 0
      },
      is_my_account() {
        return _.get(this.$route, 'name') == ROUTE_APP_ACCOUNT
      },
      show_team_nav() {
        if (this.is_my_account) {
          return false
        }

        return this.isActiveUserMemberOfTeam() || this.isActiveUserSystemAdmin()
      }
    },
    methods: {
      ...mapGetters('members', {
        'isActiveUserMemberOfTeam': 'isActiveUserMemberOfTeam',
        'isActiveUserSystemAdmin': 'isActiveUserSystemAdmin'
      })
    }
  }
</script>

<style lang="stylus" scoped>
  @import '../stylesheets/variables.styl'

  .nav-link
    margin: 0 0 0 1.25rem
    padding-top: 4px
    padding-bottom: 2px
    border-bottom: 2px solid transparent
    color: $primary-text
    transition: border 0.3s ease-in-out

    &.router-link-active
    &:hover
      border-bottom-color: $blue
</style>
