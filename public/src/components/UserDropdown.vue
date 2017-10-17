<template>
  <div>
    <a
      class="no-underline f5 b dib pointer pv1 ph1 ph2-ns br1 hover-bg-light-gray popover-trigger"
      ref="dropdown-trigger"
      tabindex="0"
    >
      <img :src="user_profile_src" class="dib v-mid ba b--black-10 db br-100"/>
      <i class="material-icons v-mid black-20" style="margin: 0 -6px">arrow_drop_down</i>
    </a>

    <ui-popover
      trigger="dropdown-trigger"
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
</template>

<script>
  import {
    ROUTE_ACCOUNT,
    ROUTE_HOME,
    ROUTE_SIGNIN
  } from '../constants/route'
  import { HOSTNAME } from '../constants/common'
  import { mapGetters } from 'vuex'

  export default {
    computed: {
      user_email_hash() {
        return _.get(this.getActiveUser(), 'email_hash', '')
      },
      user_profile_src() {
        return 'https://secure.gravatar.com/avatar/'+this.user_email_hash+'?d=mm&s=32'
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
