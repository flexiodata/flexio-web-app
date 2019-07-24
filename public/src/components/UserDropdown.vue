<template>
  <el-dropdown trigger="click" @command="onCommand">
    <span class="el-dropdown-link pointer dim-less">
      <img :src="user_profile_src" class="dib v-mid ba b--black-10 db br-100"/>
      <i class="material-icons v-mid black-30 nl1 md-18">expand_more</i>
    </span>
    <el-dropdown-menu style="min-width: 12rem" slot="dropdown">
      <div style="padding: 0 20px; margin: 8px 0">
        <FreeTrialNotice
          class="f8 br2"
          style="padding: 12px; margin: -10px -10px 10px -10px; color: #66b1ff; background-color: #ecf5ff"
          :show-upgrade="true"
        />
        <h5 class="ma0">{{full_name}}</h5>
        <div class="mt1 silver f8">{{email}}</div>
      </div>
      <el-dropdown-item class="flex flex-row items-center" command="account"><i class="material-icons mr3">account_circle</i> My Account</el-dropdown-item>
      <el-dropdown-item class="flex flex-row items-center" command="docs"><i class="material-icons mr3">help</i>Online Docs</el-dropdown-item>
      <el-dropdown-item divided></el-dropdown-item>
      <el-dropdown-item class="flex flex-row items-center" command="sign-out"><i class="material-icons mr3">forward</i> Sign out</el-dropdown-item>
    </el-dropdown-menu>
  </el-dropdown>
</template>

<script>
  import { ROUTE_APP_ACCOUNT, ROUTE_SIGNIN_PAGE } from '../constants/route'
  import { HOSTNAME } from '../constants/common'
  import { mapGetters } from 'vuex'
  import FreeTrialNotice from '@/components/FreeTrialNotice'

  export default {
    components: {
      FreeTrialNotice
    },
    computed: {
      email() {
        return _.get(this.getActiveUser(), 'email', '')
      },
      email_hash() {
        return _.get(this.getActiveUser(), 'email_hash', '')
      },
      full_name() {
        var user = this.getActiveUser()
        var first_name = _.get(user, 'first_name', '')
        var last_name = _.get(user, 'last_name', '')
        return `${first_name} ${last_name}`
      },
      user_profile_src() {
        return 'https://secure.gravatar.com/avatar/' + this.email_hash + '?d=mm&s=32'
      }
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUser': 'getActiveUser'
      }),
      openHelpDocs() {
        window.open('https://' + HOSTNAME + '/docs', '_blank')
      },
      gotoAccount() {
        this.$router.push({ name: ROUTE_APP_ACCOUNT })
      },
      signOut() {
        this.$store.dispatch('users/signOut').then(response => {
          this.$router.push({ name: ROUTE_SIGNIN_PAGE })
        })
      },
      onCommand(cmd) {
        switch (cmd)
        {
          case 'account':  return this.gotoAccount()
          case 'docs':     return this.openHelpDocs()
          case 'sign-out': return this.signOut()
        }
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .dim-less
    opacity: 1; transition: opacity .15s ease-in

  .dim-less:hover, .dim-less:focus
    opacity: .7; transition: opacity .15s ease-in

  .dim-less:active
    opacity: .85
</style>
