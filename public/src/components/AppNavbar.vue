<template>
  <nav v-if="render_nav" v-show="show_nav" class="bb b--black-20">
    <div class="flex flex-row bg-white pa2 ph3-ns items-center">
      <div class="flex-fill flex flex-row items-center truncate">
        <router-link to="/home" class="dib v-mid link min-w3" title="Home">
          <img src="../assets/logo-flexio-navbar.png" class="dib" alt="Flex.io">
        </router-link>
        <router-link
          v-if="show_title"
          :to="project_link"
          class="link dib lh-title underline-hover truncate f5 f4-m f3-l fw6 br1 pv1 ph1 ph2-ns v-mid ml2 black-60"
          title="Back to project home"
        >
          {{project_name}}
        </router-link>
      </div>
      <div class="flex-none">
        <div v-if="user_fetching"></div>
        <div v-else-if="logged_in">
          <a
            class="f5 b dib pointer ml1 ml2-ns ph1 ph3-ns pv1 pv2-ns br1 blue hover-bg-light-gray popover-trigger"
            ref="helpdropdowntrigger"
            tabindex="0"
          ><i class="material-icons v-mid">help</i></a>

          <ui-popover
            trigger="helpdropdowntrigger"
            ref="helpdropdown"
            dropdown-position="bottom right"
          >
            <ui-menu
              contain-focus
              has-icons

              :options="[{
                id: 'docs',
                label: 'Help Docs',
                icon: 'import_contacts'
              },{
                id: 'support',
                label: 'Contact Support',
                icon: 'mail_outline'
              }]"

              @select="onHelpDropdownItemClick"
              @close="$refs.helpdropdown.close()"
            >
            </ui-menu>
          </ui-popover>

          <a
            class="no-underline f5 b dib pointer ml1 mr0 mr2-ns ph1 ph3-ns pv1 pv2-ns br1 black-60 hover-bg-light-gray popover-trigger"
            ref="userdropdowntrigger"
            tabindex="0"
          >
            <span class="v-mid">{{first_name}}</span>
            <i class="material-icons v-mid">arrow_drop_down</i>
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
                id: 'projects',
                label: 'Projects',
                icon: 'home'
              },{
                id: 'account',
                label: 'Account',
                icon: 'account_circle'
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
          <router-link to="/signup" class="link no-underline dib f6 f6-ns ttu b br1 white bg-orange darken-10 ph2 ph3-ns pv2">
            <span class="di dn-ns">Sign up</span>
            <span class="dn di-ns">Sign up for free</span>
          </router-link>
        </div>
      </div>
    </div>

    <!-- email support modal -->
    <email-support-modal
      open-from=".btn-contact-support"
      close-to=".btn-contact-support"
      ref="modal-email-support"
      @submit="trySendSupportEmail"
    ></email-support-modal>
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
  import { mapState, mapGetters } from 'vuex'
  import EmailSupportModal from './EmailSupportModal.vue'

  export default {
    components: {
      EmailSupportModal
    },
    computed: {
      ...mapState([
        'user_fetching',
        'active_project'
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
      show_title() {
        return this.active_project.length > 0
      },
      project_link() {
        return '/project/'+this.active_project
      },
      project_name() {
        return _.get(this.getActiveProject(), 'name', '')
      },
      user_eid() {
        return _.get(this.getActiveUser(), 'eid', '')
      },
      first_name() {
        return _.get(this.getActiveUser(), 'first_name', '')
      },
      user_name() {
        return _.get(this.getActiveUser(), 'user_name', '')
      },
      logged_in() {
        return this.user_eid.length > 0
      }
    },
    methods: {
      ...mapGetters([
        'getActiveProject',
        'getActiveUser'
      ]),
      openHelpDocs() {
        window.open('https://docs.flex.io', '_blank')
      },
      openEmailSupportModal() {
        this.$refs['modal-email-support'].open()
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
      trySendSupportEmail(attrs, modal) {
        this.$store.dispatch('sendEmailSupport', { attrs }).then(response => {
          if (response.ok)
          {
            modal.success()
          }
           else
          {
            // TODO: add error handling
          }
        })
      },
      onHelpDropdownItemClick(menu_item) {
        switch (menu_item.id)
        {
          case 'docs':    return this.openHelpDocs()
          case 'support': return this.openEmailSupportModal()
        }
      },
      onUserDropdownItemClick(menu_item) {
        switch (menu_item.id)
        {
          case 'projects': return this.gotoProjects()
          case 'account':  return this.gotoAccount()
          case 'sign-out': return this.signOut()
        }
      }
    }
  }
</script>
2
