<template>
  <div class="flex flex-column flex-row-l items-stretch">
    <div class="flex flex-row flex-column-l flex-none items-stretch bg-nearer-white br bb bb-0-l b--black-05 pv0 pt4-l ph3-l overflow-auto trans-pm">
      <div class="flex-none-l dn db-l no-underline link mh3 mb4" style="margin-top: 6px" v-if="false">
        <img src="../assets/logo-flexio-navbar.png" class="dib" alt="Flex.io">
      </div>
      <router-link v-for="item in nav_items" :to="item.route" class="flex-auto flex-none-l no-underline truncate tc tl-l link f5 fw6 mv0 mb2-l ph1 pl3-l pr5-l pv2 bb bb-0-l bw1 bw2-l css-nav">
        <i class="material-icons md-24 v-mid dib" :class="item.class">{{item.icon}}</i>
        <span class="dn dib-ns v-mid">{{item.name}}</span>
      </router-link>
    </div>
    <router-view class="flex flex-column flex-fill relative overflow-auto"></router-view>
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
      route: 'storage',
      name: 'Storage',
      icon: 'layers'
    },
    /*{
      route: 'members',
      name: 'Members',
      icon: 'person'
    },
    {
      route: 'sharing',
      name: 'Sharing',
      icon: 'share'
    },*/
    {
      route: 'trash',
      name: 'Trash',
      icon: 'delete'
    }
  ]

  export default {
    data() {
      return {
        nav_items: nav_items
      }
    }
  }
</script>
