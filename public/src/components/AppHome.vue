<template>
  <div class="flex flex-column flex-row-l items-stretch">
    <div class="flex flex-row flex-column-l flex-none items-stretch bg-nearer-white bb bb-0-l br-l b--black-05 nt1-l pv0 pt4-l ph1-l overflow-auto trans-pm">
      <router-link v-for="item in nav_items" :to="item.route" class="flex-auto flex-none-l no-underline truncate tc tl-l link f5 fw6 mt1 mb2-l ph1 pl3-l pr5-l pv2 bb bb-0-l bw1 bw2-l css-nav-item">
        <div>
          <i class="material-icons md-24 v-mid dib" :class="item.class">{{item.icon}}</i>
          <span class="dn dib-ns v-mid">{{item.name}}</span>
        </div>
        <div class="mt1 ml4" v-if="item.subitems">
          <router-link v-for="subitem in item.subitems" :to="subitem.route" class="db truncate tl-l link f7 pt2 mt1 ml1 css-nav-item">
            &ndash; {{subitem.name}}
          </router-link>
        </div>
      </router-link>
    </div>
    <router-view class="flex-fill relative"></router-view>
  </div>
</template>

<script>
  const nav_items = [
    {
      route: '/dashboard',
      name: 'Dashboard',
      icon: 'dashboard',
      subitems: [
        {
          route: '/dashboard/overview',
          name: 'Overview'
        },
        {
          route: '/dashboard/learn',
          name: 'Learn'
        },
        {
          route: '/dashboard/docs',
          name: 'Documentation'
        }
      ]
    },
    {
      route: '/pipes',
      name: 'Pipes',
      icon: 'storage'
    },
    {
      route: '/connections',
      name: 'Connections',
      icon: 'repeat'
    },
    {
      route: '/storage',
      name: 'Storage',
      icon: 'layers'
    },/*
    {
      route: '/trash',
      name: 'Trash',
      icon: 'delete'
    }*/
  ]

  export default {
    data() {
      return {
        nav_items: nav_items
      }
    }
  }
</script>
