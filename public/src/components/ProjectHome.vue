<template>
  <div class="flex flex-column flex-row-l items-stretch">
    <div class="flex flex-row flex-column-l flex-none items-stretch bb bb-0-l br-l b--black-10 pv0 pv3-l overflow-auto trans-pm">
      <router-link v-for="item in nav_items" :to="item.route" class="flex-auto flex-none-l no-underline truncate tc tl-l link f5 fw6 black-60 mv0 mb1-l ph-1 pl3-l pr5-l pv2 pv3-l bb bl-l bb-0-l bw2 bw2-l css-nav">
        <i class="material-icons md-24 v-mid" :class="item.class">{{item.icon}}</i>
        <span class="dn dib-ns v-mid">{{item.name}}</span>
      </router-link>
    </div>
    <router-view :project-eid="eid" class="flex flex-column flex-fill relative overflow-auto"></router-view>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import setActiveProject from './mixins/set-active-project'

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
    mixins: [setActiveProject],
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

  .css-nav {
    border-color: transparent;

    &:hover {
      border-color: @black-60;
      background-color: @bg-color;
      box-shadow: inset 0 0 1px rgba(0,0,0,0.1);
    }

    &.router-link-active {
      color: @blue;
      border-color: @blue;
      background-color: @bg-color;
      box-shadow: inset 0 0 1px rgba(0,0,0,0.1);
    }
  }
</style>
