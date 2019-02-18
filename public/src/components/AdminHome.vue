<template>
  <div class="flex flex-column flex-row-l items-stretch bg-nearer-white">
    <div class="flex flex-row flex-column-l flex-none items-stretch bb bb-0-l br-l b--black-05 pv0 pt4-l ph1-l overflow-auto trans-pm">
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
  </div>
</template>

<script>
  const nav_items = [
    {
      route: 'users',
      name: 'User Activity',
      icon: 'group'
    },
    {
      route: 'processes',
      name: 'Process Activity',
      icon: 'network_check'
    },
    {
      route: 'tests',
      name: 'Tests',
      icon: 'gavel'
    },
    {
      route: 'builder',
      name: 'Builder',
      icon: 'build'
    }/*,
    {
      route: 'translator',
      name: 'Code Translator',
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
    }*/,
    {
      route: 'prototype',
      name: 'Prototype',
      icon: 'featured_play_list'
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
