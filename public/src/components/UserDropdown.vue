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
      <el-dropdown-item class="flex flex-row items-center" command="account"><i class="material-icons mr3">account_circle</i> My Account</el-dropdown-item>
      <el-dropdown-item divided></el-dropdown-item>
      <div class="mv2" style="padding: 0 20px">
        <h5 class="ma0">Help</h5>
      </div>
      <el-dropdown-item class="flex flex-row items-center" command="docs"><i class="material-icons mr3">help</i>Online Docs</el-dropdown-item>
      <el-dropdown-item class="flex flex-row items-center" command="tour"><i class="material-icons mr3">school</i>Run Intro Tour</el-dropdown-item>
      <el-dropdown-item divided></el-dropdown-item>
      <el-dropdown-item class="flex flex-row items-center" command="sign-out"><i class="material-icons mr3">forward</i> Sign out</el-dropdown-item>
    </el-dropdown-menu>
  </el-dropdown>
</template>

<script>
  import {
    ROUTE_ACCOUNT,
    ROUTE_HOME,
    ROUTE_PIPES,
    ROUTE_SIGNIN
  } from '../constants/route'
  import { HOSTNAME } from '../constants/common'
  import { mapGetters } from 'vuex'
  import MixinConfig from './mixins/config'

  export default {
    mixins: [MixinConfig],
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
      restartOnboardingTour() {
        var attrs = {
          "eid_type": "PIP",
          "name": "Hacker News Feed",
          "description": "Get the latest articles from Hacker News",
          "task": {
            "op": "sequence",
            "items": [
                {
                  "op": "execute",
                  "lang": "python",
                  "code": "aW1wb3J0IHJlcXVlc3RzCgojIGhhbmRsZXIgdG8gY29tbXVuaWNhdGUgd2l0aCBGbGV4LmlvIGNhbGxpbmcgY29udGV4dApkZWYgZmxleGlvX2hhbmRsZXIoY29udGV4dCk6CiAgCiAgICByZXN1bHRzID0gJycKICAKICAgICMgZ2V0IDUgbmV3IHN0b3JpZXMgZnJvbSBIYWNrZXJOZXdzIChodHRwczovL2dpdGh1Yi5jb20vSGFja2VyTmV3cy9BUEkpCiAgICByZXNwb25zZSA9IHJlcXVlc3RzLmdldCgiaHR0cHM6Ly9oYWNrZXItbmV3cy5maXJlYmFzZWlvLmNvbS92MC9uZXdzdG9yaWVzLmpzb24iKQogICAgc3RvcmllcyA9IHJlc3BvbnNlLmpzb24oKVs6NV0KICAgIAogICAgIyBnZXQgbmV3IHN0b3J5IGluZm8KICAgIGZvciBpIGluIHN0b3JpZXM6CiAgICAgICAgcmVzcG9uc2UgPSByZXF1ZXN0cy5nZXQoImh0dHBzOi8vaGFja2VyLW5ld3MuZmlyZWJhc2Vpby5jb20vdjAvaXRlbS8iICsgc3RyKGkpICsgIi5qc29uIikKICAgICAgICBzdG9yeSA9IHJlc3BvbnNlLmpzb24oKQogICAgICAgIHJlc3VsdHMgKz0gc3RvcnkuZ2V0KCd0aXRsZScsJycpICsgIlxuIgogICAgICAgIHJlc3VsdHMgKz0gc3RvcnkuZ2V0KCd1cmwnLCcnKSArICJcblxuIgogICAgICAgIAogICAgIyByZXR1cm4gdGhlIGxpc3Qgb2YgbmV3IHN0b3JpZXMKICAgIGNvbnRleHQub3V0cHV0LndyaXRlKHJlc3VsdHMpCgogICAgICA="
                }
            ]
          }
        }

        var cfg_path = 'app.prompt.onboarding.pipeDocument.build.shown'
        this.$_Config_reset(cfg_path)

        this.$store.dispatch('createPipe', { attrs }).then(response => {
          if (response.ok) {
            var pipe = response.body
            var eid = pipe.eid
            this.$router.push({ name: ROUTE_PIPES, params: { eid } })
          }
        })
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
          case 'tour':     return this.restartOnboardingTour()
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
