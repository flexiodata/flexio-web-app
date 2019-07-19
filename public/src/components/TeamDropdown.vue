<template>
  <el-dropdown trigger="click">
    <span class="el-dropdown-link pointer f5 fw6">
      <div class="flex flex-row items-center">
        <span>{{active_team_label}}</span>
        <i class="material-icons black-30">expand_more</i>
      </div>
    </span>
    <el-dropdown-menu style="min-width: 12rem" slot="dropdown">
      <div>
        <article
          class="el-dropdown-menu__item flex flex-row items-center"
          :key="team.eid"
          v-for="team in teams"
        >
          <i class="material-icons md-18 b mr3" v-if="isActiveTeam(team)">check</i>
          <i class="material-icons md-18 b mr3" style="color: transparent" v-else>check</i>
          <div class="pr2">{{getFullTeamName(team)}}</div>
        </article>
        <el-dropdown-item divided v-if="false"></el-dropdown-item>
        <article
          class="el-dropdown-menu__item flex flex-row items-center"
          v-if="false"
        >
          <i class="material-icons md-18 b mr3">add</i>
          <div class="pr2">Create new team</div>
        </article>
      </div>
    </el-dropdown-menu>
  </el-dropdown>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import api from '@/api'

  export default {
    data() {
      return {
        teams: []
      }
    },
    computed: {
      ...mapState([
        'active_team_name'
      ]),
      username() {
        return _.get(this.getActiveUser(), 'username', '')
      },
      active_team_label() {
        var user = this.getActiveUser()
        return this.getFullTeamName(user)
      }
    },
    created() {
      api.v2_fetchTeams(this.username).then(response => {
        this.teams = response.data
      })
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ]),
      getFullTeamName(team) {
        var first_name = _.get(team, 'first_name', '')
        var last_name = _.get(team, 'last_name', '')
        return `${first_name} ${last_name}` + "'s team"
      },
      isActiveTeam(team) {
        return team.username == this.active_team_name
      }
    }
  }
</script>

<style lang="stylus" scoped>
  @import '../stylesheets/variables.styl'

  .el-dropdown-link
    color: $body-color
</style>
