<template>
  <div class="flex flex-column flex-row-l items-stretch">
    <div class="flex flex-row flex-column-l flex-none items-stretch bg-near-white bb bb-0-l b--black-10 pv0 pt4-l ph3-l overflow-auto trans-pm">
      <router-link v-for="item in nav_items" :to="item.route" class="flex-auto flex-none-l no-underline truncate tc tl-l link f5 fw6 mv0 mv1-l ph1 pl3-l pr5-l pv2 bb bb-0-l bw1 bw2-l css-nav2">
        <i class="material-icons md-24 v-mid" :class="item.class">{{item.icon}}</i>
        <span class="dn dib-ns v-mid">{{item.name}}</span>
      </router-link>
    </div>
    <router-view class="flex-fill relative"></router-view>
  </div>
</template>

<script>
  const nav_items = [
    {
      route: 'dashboard',
      name: 'Dashboard',
      icon: 'dashboard'
    },
    {
      route: 'tests',
      name: 'Tests',
      icon: 'gavel'
    },
    {
      route: 'parser',
      name: 'Parser',
      icon: 'translate'
    },
    {
      route: 'code',
      name: 'Code Editor',
      icon: 'code'
    },
    {
      route: 'modals',
      name: 'Modals',
      icon: 'picture_in_picture'
    },
    {
      route: 'playground',
      name: 'Playground',
      icon: 'local_play'
    }
  ]

  export default {
    data() {
      return {
        eid: this.$route.params.eid,
        nav_items: nav_items
      }
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
