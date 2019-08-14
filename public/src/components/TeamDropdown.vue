<template>
  <el-dropdown
    trigger="click"
    @visible-change="onVisibleChange"
  >
    <span
      class="el-dropdown-link pointer f5 fw6"
      :class="is_dropdown_open ? 'is-open' : ''"
    >
      <div class="flex flex-row items-center">
        <span>{{active_team_label}}</span>
        <i class="material-icons black-30 arrow member-arrow">expand_more</i>
      </div>
    </span>
    <el-dropdown-menu style="min-width: 12rem" slot="dropdown">
      <div>
        <article
          class="el-dropdown-menu__item flex flex-row items-center"
          :key="team.eid"
          @click="changeTeam(team)"
          v-for="team in getAllTeams()"
        >
          <i class="material-icons md-18 b mr3" v-if="isActiveTeam(team)">check</i>
          <i class="material-icons md-18 b mr3" style="color: transparent" v-else>check</i>
          <div class="pr3">{{getTeamLabel(team)}}</div>
        </article>
        <el-dropdown-item divided v-if="false"></el-dropdown-item>
        <article
          class="el-dropdown-menu__item flex flex-row items-center"
          v-if="false"
        >
          <i class="material-icons md-18 b mr3">add</i>
          <div class="pr3">Create new team</div>
        </article>
      </div>
    </el-dropdown-menu>
  </el-dropdown>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import { getFullName } from '@/utils'
  import api from '@/api'

  export default {
    data() {
      return {
        is_dropdown_open: false
      }
    },
    computed: {
      ...mapState({
        is_fetching: state => state.teams.is_fetching,
        is_fetched: state => state.teams.is_fetched,
        active_team_name: state => state.teams.active_team_name
      }),
      active_username() {
        return _.get(this.getActiveUser(), 'username', '')
      },
      active_team_label() {
        return this.getActiveTeamLabel()
      }
    },
    created() {
      this.tryFetchTeams()
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUser': 'getActiveUser'
      }),
      ...mapGetters('teams', {
        'getActiveTeamLabel': 'getActiveTeamLabel',
        'getAllTeams': 'getAllTeams'
      }),
      tryFetchTeams() {
        var team_name = this.active_username

        if (!this.is_fetched && !this.is_fetching) {
          this.$store.dispatch('teams/fetch', { team_name })
        }
      },
      getTeamLabel(team) {
        return team ? getFullName(team) + "'s team" : ''
      },
      isActiveTeam(team) {
        return team.username == this.active_team_name
      },
      changeTeam(team) {
        if (!this.isActiveTeam(team)) {
          var team_name = team.username
          this.$store.dispatch('teams/changeActiveTeam', { team_name }).then(response => {
            var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path', 'query'])
            this.$router.push({ path: `/${team_name}/pipes` })
          })
        }
      },
      onVisibleChange(is_open) {
        this.is_dropdown_open = is_open
      }
    }
  }
</script>

<style lang="stylus" scoped>
  @import '../stylesheets/variables.styl'

  .el-dropdown-link
    color: $body-color
    outline: none
    .arrow
      transition: transform .2s ease
    .member-arrow
      -webkit-transform: rotate(-90deg)
      transform: rotate(-90deg)
    &:hover
      .member-arrow
        -webkit-transform: rotate(0)
        transform: rotate(0)
    &.is-open
      .arrow
        -webkit-transform: rotate(180deg)
        transform: rotate(180deg)
</style>
