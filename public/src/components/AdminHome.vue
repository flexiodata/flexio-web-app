<template>
  <!-- fetching -->
  <div v-if="is_fetching">
    <div class="flex flex-column justify-center bg-nearer-white h-100">
      <Spinner size="large" message="Loading..." />
    </div>
  </div>

  <!-- fetched -->
  <div class="flex flex-column flex-row-l items-stretch bg-nearer-white" v-else-if="is_fetched">
    <template v-if="is_superuser">
      <div class="flex flex-row flex-column-l flex-none items-stretch bb bb-0-l br-l b--black-05 pv0 pt4-l ph1-l overflow-auto trans-pm">
        <router-link to="/" class="mv1 ph1 pl3-l pr5-l pv2">
          <div class="dib hint--bottom" aria-label="Home">
            <img src="../assets/logo-flexio-navbar.png" alt="Flex.io">
          </div>
        </router-link>
        <router-link
          class="flex-auto flex-none-l no-underline truncate tc tl-l link f5 fw6 mt1 ph1 pl3-l pr5-l pv2 bb bb-0-l bw1 bw2-l css-nav-item"
          :to="item.route"
          :key="item.route"
          v-for="item in nav_items"
        >
          <i class="material-icons md-24 v-mid" :class="item.class">{{item.icon}}</i>
          <span class="dn dib-ns v-mid">{{item.name}}</span>
        </router-link>
      </div>
      <router-view class="flex-fill relative"></router-view>
    </template>
    <PageNotFound class="flex-fill" v-else />
  </div>
</template>

<script>
  import { mapState, mapGetters } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import PageNotFound from '@/components/PageNotFound'

  const nav_items = [
    {
      route: 'users',
      name: 'User Activity',
      icon: 'group'
    },{
      route: 'processes',
      name: 'Process Activity',
      icon: 'network_check'
    },{
      route: 'tests',
      name: 'Tests',
      icon: 'gavel'
    }
  ]

  export default {
    components: {
      Spinner,
      PageNotFound
    },
    data() {
      return {
        nav_items,
        is_fetching: false,
        is_fetched: false,
      }
    },
    computed: {
      ...mapState({
        active_user_eid: state => state.users.active_user_eid
      }),
      is_superuser() {
        var member = _.find(this.getAllMembers(), { eid: this.active_user_eid })
        return _.indexOf(_.get(member, 'rights', []), 'action.system.read') >= 0
      }
    },
    mounted() {
      this.is_fetching = true

      var team_name = this.getActiveUsername()
      var eid = this.active_user_eid
      this.$store.dispatch('members/fetchRights', { team_name, eid, username: team_name }).then(response => {
        this.is_fetched = true
      }).finally(() => {
        this.is_fetching = false
      })
    },
    methods: {
      ...mapGetters('members', {
        'getAllMembers': 'getAllMembers',
      }),
      ...mapGetters('users', {
        'getActiveUsername': 'getActiveUsername'
      })
    }
  }
</script>
