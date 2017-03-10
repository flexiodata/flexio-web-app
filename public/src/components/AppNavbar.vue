<template>
  <nav v-if="render_nav" v-show="show_nav" class="bb b--black-20">
    <div class="flex flex-row bg-white pa1 pl3-ns pr2-ns items-center">
      <div class="flex-fill flex flex-row items-center truncate">
        <router-link to="/home" class="dib link v-mid min-w3" title="Home">
          <img src="../assets/logo-flexio-navbar.png" class="dib" alt="Flex.io">
        </router-link>
        <div class="flex flex-row items-center lh-title f6 f4-ns fw6">
          <router-link
            v-if="show_project_title"
            to="/home"
            title="Project List"
            class="flex flex-row items-center ml2 ml3-ns pl2 pl3-ns link b--black-20 bl black-60 hover-black"
          ><i class="material-icons">home</i>
          </router-link>
          <i v-if="show_project_title" class="material-icons md-24 black-60 fa-rotate-270">arrow_drop_down</i>
          <router-link
            v-if="show_project_title && show_document_title"
            :to="project_link"
            title="Project Overview"
            class="link black-60 hover-black truncate"
          >{{project_name}}
          </router-link>
          <div
            v-else
            class="link black-60 truncate"
          >{{project_name}}
          </div>
          <i v-if="show_document_title" class="material-icons md-24 black-60 fa-rotate-270">arrow_drop_down</i>
          <div
            v-if="show_document_title"
            class="dib black-60 truncate"
          >{{document_name}}
          </div>
        </div>
      </div>
      <div class="flex-none">
        <div v-if="user_fetching"></div>
        <div v-else-if="logged_in">
          <a
            class="b dib pointer ml1 ml2-ns pv2 ph1 ph2-ns br1 blue hover-bg-light-gray popover-trigger"
            ref="helpdropdowntrigger"
            tabindex="0"
          ><i class="material-icons md-24 v-mid">help</i></a>

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
            class="no-underline f5 b dib pointer ml1 mr0 mr2-ns pv2 ph1 ph2-ns br1 black-60 hover-bg-light-gray popover-trigger"
            ref="userdropdowntrigger"
            tabindex="0"
          >
            <img :src="user_profile_src" class="dib v-mid ba b--black-10 db br-100"/>
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
  import { HOSTNAME } from '../constants/common'
  import { mapState, mapGetters } from 'vuex'
  import EmailSupportModal from './EmailSupportModal.vue'

  export default {
    components: {
      EmailSupportModal
    },
    computed: {
      ...mapState([
        'user_fetching',
        'active_project',
        'active_document'
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
      show_project_title() {
        return this.active_project.length > 0
      },
      show_document_title() {
        return this.show_project_title && this.active_project != this.active_document
      },
      home_link() {
        return '/home'
      },
      project_link() {
        return '/project/'+this.active_project
      },
      project_name() {
        return _.get(this.getActiveProject(), 'name', '')
      },
      document_name() {
        return _.get(this.getActiveDocument(), 'name', '')
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
      user_email_hash() {
        return _.get(this.getActiveUser(), 'email_hash', '')
      },
      user_profile_src() {
        return 'https://secure.gravatar.com/avatar/'+this.user_email_hash+'?d=mm&s=24'
      },
      logged_in() {
        return this.user_eid.length > 0
      }
    },
    methods: {
      ...mapGetters([
        'getActiveProject',
        'getActiveDocument',
        'getActiveUser'
      ]),
      openHelpDocs() {
        window.open('https://'+HOSTNAME+'/docs/web-app/', '_blank')
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
