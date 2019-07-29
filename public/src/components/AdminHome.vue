<template>
  <div class="flex flex-column flex-row-l items-stretch bg-nearer-white">
    <PageNotFound class="flex-fill" v-if="!is_superuser" />
    <template v-else>
      <div class="flex flex-row flex-column-l flex-none items-stretch bb bb-0-l br-l b--black-05 pv0 pt4-l ph1-l overflow-auto trans-pm">
        <router-link to="/pipes" class="mt1 ph1 pl3-l pr5-l pv2">
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
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
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
    },{
      route: 'prototype',
      name: 'Prototype',
      icon: 'featured_play_list'
    },
  ]

  export default {
    components: {
      PageNotFound
    },
    data() {
      return {
        nav_items
      }
    },
    computed: {
      is_superuser() {
        debugger
        // limit to @flex.io users for now
        var user_email = this.getActiveUserEmail()
        return _.includes(user_email, '@flex.io')
      }
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUserEmail': 'getActiveUserEmail()'
      })
    }
  }
</script>
