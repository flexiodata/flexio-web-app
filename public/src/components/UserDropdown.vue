<template>
  <el-dropdown trigger="click" @command="onCommand">
    <span class="el-dropdown-link pointer dim-less">
      <img :src="user_profile_src" class="dib v-mid ba b--black-10 db br-100"/>
      <i class="material-icons v-mid black-30 nl1 md-18">expand_more</i>
    </span>
    <el-dropdown-menu style="min-width: 12rem" slot="dropdown">
      <div class="mv2" style="padding: 0 20px">
        <h5 class="ma0">{{full_name}}</h5>
        <div class="mt1 silver f8">{{email}}</div>
      </div>
      <el-dropdown-item class="flex flex-row items-center ph2" command="account"><i class="material-icons mr3">account_circle</i> My Account</el-dropdown-item>
      <el-dropdown-item class="flex flex-row items-center ph2" command="docs"><i class="material-icons mr3">help</i> Docs</el-dropdown-item>
      <div class="mv2 bt b--black-10"></div>
      <el-dropdown-item class="flex flex-row items-center ph2" command="sign-out"><i class="material-icons mr3">forward</i> Sign out</el-dropdown-item>
    </el-dropdown-menu>
  </el-dropdown>
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
      email() {
        return _.get(this.getActiveUser(), 'email', '')
      },
      email_hash() {
        return _.get(this.getActiveUser(), 'email_hash', '')
      },
      full_name() {
        var first_name = _.get(this.getActiveUser(), 'first_name', '')
        var last_name = _.get(this.getActiveUser(), 'last_name', '')
        return first_name + ' ' + last_name
      },
      user_profile_src() {
        return 'https://secure.gravatar.com/avatar/'+this.email_hash+'?d=mm&s=32'
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ]),
      openHelpDocs() {
        window.open('https://'+HOSTNAME+'/docs', '_blank')
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

<style scoped>
  .dim-less { opacity: 1; transition: opacity .15s ease-in; }
  .dim-less:hover, .dim-less:focus { opacity: .7; transition: opacity .15s ease-in; }
  .dim-less:active { opacity: .85; transition: opacity .15s ease-out; }
</style>
