<template>
  <div class="flex flex-column flex-row-l items-stretch">
    <div class="flex flex-row flex-column-l flex-none items-stretch bg-near-white bb bb-0-l b--black-10 pv0 ph3-l overflow-auto trans-pm" style="padding-top: 2rem">
      <router-link to="/home" class="flex-none-l dn db-l no-underline link pv1 mh3 mb4" title="Home">
        <img src="../assets/logo-flexio-navbar.png" class="dib" alt="Flex.io">
      </router-link>
      <router-link v-for="item in nav_items" :to="item.route" class="flex-auto flex-none-l no-underline truncate tc tl-l link f5 fw6 mv0 mb1-l ph-1 pl3-l pr5-l pv2 bb bb-0-l bw1 bw2-l css-nav2">
        <i class="material-icons md-24 v-mid dn" :class="item.class">{{item.icon}}</i>
        <span class="dn dib-ns v-mid">{{item.name}}</span>
      </router-link>
    </div>
    <router-view :project-eid="eid" class="flex flex-column flex-fill pa4 relative overflow-auto" style="padding-top: 2rem"></router-view>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import SetActiveProject from './mixins/set-active-project'

  const nav_items = [
    {
      route: 'pipes',
      name: 'Pipes',
      icon: 'storage'
    },
    {
      route: 'connections',
      name: 'Connections',
      icon: 'repeat'
    },
    {
      route: 'members',
      name: 'Members',
      icon: 'person'
    },
    {
      route: 'trash',
      name: 'Trash',
      icon: 'delete'
    }
  ]

  export default {
    mixins: [SetActiveProject],
    data() {
      return {
        eid: this.$route.params.eid,
        nav_items: nav_items
      }
    },
    computed: {
      project() {
        return _.find(this.getAllProjects(), { eid: this.eid })
      },

      is_fetched() {
        return _.get(this.project, 'is_fetched', false)
      },

      is_fetching() {
        return _.get(this.project, 'is_fetching', false)
      }
    },
    created() {
      this.setActiveProject(this.eid)
    },
    methods: {
      ...mapGetters([
        'getAllProjects'
      ])
    }
  }
</script>

<style lang="less">
  // match .blue color to Material Design's 'Blue A600' color
  @blue: #1e88e5;
  @black-60: rgba(0,0,0,.6);
  @bg-color: #eee;

  .css-nav2 {
    border-color: transparent;
    color: @black-60;

    &:hover {
      border-color: @black-60;
      color: #333;
    }

    &.router-link-active {
      border-color: @blue;
      color: @blue;
    }
  }
</style>
