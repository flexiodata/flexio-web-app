<template>
  <nav v-if="render_nav" v-show="show_nav" class="bb b--black-20">
    <div class="flex flex-row bg-white pa1 pl3-ns pr2-ns items-center">
      <div class="flex-fill flex flex-row items-center truncate">
        <router-link to="/home" class="dib link v-mid min-w3" title="Home">
          <img src="../assets/logo-flexio-navbar.png" class="dib" alt="Flex.io">
        </router-link>
        <app-breadcrumbs class="flex flex-row items-center lh-title f6 fw6 f4-ns fw4-ns"></app-breadcrumbs>
      </div>
      <div class="flex-none">
        <div v-if="user_fetching"></div>
        <div v-else-if="logged_in">
          <a
            class="no-underline f5 b dib pointer ml1 mr0 mr2-ns pv1 ph1 ph2-ns br1 hover-bg-light-gray popover-trigger"
            ref="userdropdowntrigger"
            tabindex="0"
          >
            <img :src="user_profile_src" class="dib v-mid ba b--black-10 db br-100"/>
            <i class="material-icons v-mid black-20" style="margin: 0 -6px">arrow_drop_down</i>
          </a>

          <ui-popover
            trigger="userdropdowntrigger"
            ref="userdropdown"
            dropdown-position="bottom right"
          >
            <ui-menu
              contain-focus
              has-icons

              :options="[{
                id: 'account',
                label: 'Account',
                icon: 'account_circle'
              },{
                id: 'docs',
                label: 'Documentation',
                icon: 'import_contacts'
              },{
                type: 'divider'
              },{
                id: 'sign-out',
                label: 'Sign out',
                icon: 'forward'
              }]"

              @select="onUserDropdownItemClick"
              @close="$refs.userdropdown.close()"
            >
            </ui-menu>
          </ui-popover>
        </div>
        <div v-else>
          <router-link to="/signin" class="link underline-hover dib f6 f6-ns ttu b black-60 ph2 pv1 mr1 mr2-ns">Sign in</router-link>
          <router-link to="/signup" class="link no-underline dib f6 f6-ns ttu b br1 white bg-orange darken-10 ph2 ph3-ns pv2 mv1">
            <span class="di dn-ns">Sign up</span>
            <span class="dn di-ns">Sign up for free</span>
          </router-link>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
  import {
    ROUTE_ACCOUNT,
    ROUTE_EMBEDHOME,
    ROUTE_HOME,
    ROUTE_SIGNIN,
    ROUTE_SIGNUP,
    ROUTE_FORGOTPASSWORD,
    ROUTE_RESETPASSWORD
  } from '../constants/route'
  import { HOSTNAME } from '../constants/common'
  import { mapState, mapGetters } from 'vuex'
  import AppBreadcrumbs from './AppBreadcrumbs.vue'

  export default {
    components: {
      AppBreadcrumbs
    },
    computed: {
      ...mapState([
        'user_fetching'
      ]),
      render_nav() {
        switch (this.$route.name)
        {
          case ROUTE_EMBEDHOME:
            return false

          default:
            return true
        }
      },
      show_nav() {
        switch (this.$route.name)
        {
          case ROUTE_SIGNIN:
          case ROUTE_SIGNUP:
          case ROUTE_FORGOTPASSWORD:
          case ROUTE_RESETPASSWORD:
            return false

          default:
            return true
        }
      },
      user_eid() {
        return _.get(this.getActiveUser(), 'eid', '')
      },
       user_email_hash() {
        return _.get(this.getActiveUser(), 'email_hash', '')
      },
      user_profile_src() {
        return 'https://secure.gravatar.com/avatar/'+this.user_email_hash+'?d=mm&s=32'
      },
      logged_in() {
        return this.user_eid.length > 0
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ]),
      openHelpDocs() {
        window.open('https://'+HOSTNAME+'/docs/web-app/', '_blank')
      },
      gotoProjects() {
        this.$router.push({ name: ROUTE_HOME })
      },
      gotoAccount() {
        this.$router.push({ name: ROUTE_ACCOUNT })
      },
      signOut() {
        this.$store.dispatch('signOut').then(response => {
          if (response.ok)
          {
            this.$router.push({ name: ROUTE_SIGNIN })
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      onUserDropdownItemClick(menu_item) {
        switch (menu_item.id)
        {
          case 'projects': return this.gotoProjects()
          case 'account':  return this.gotoAccount()
          case 'docs':     return this.openHelpDocs()
          case 'sign-out': return this.signOut()
        }
      }
    }
  }
</script>
