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
    ROUTE_APP_HOME,
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
          "name": "Example: Email Results of a Python Function",
          "description": "Get the top 5 stories from the Firebase Hacker News Feed and deliver via email",
          "task": {
            "op": "sequence",
            "items": [
              {
                "op": "execute",
                "lang": "python",
                "code": "aW1wb3J0IHJlcXVlc3RzCgojIGhhbmRsZXIgdG8gY29tbXVuaWNhdGUgd2l0aCBGbGV4LmlvIGNhbGxpbmcgY29udGV4dApkZWYgZmxleGlvX2hhbmRsZXIoY29udGV4dCk6CiAgCiAgICAjIGdldCB0aGUgbnVtYmVyIG9mIHN0b3JpZXMgdG8gcmV0dXJuIGZyb20gdGhlIGlucHV0IHBhcmFtcwogICAgIyByZXR1cm4gNSBzdG9yaWVzIGJ5IGRlZmF1bHQKICAgIHN0b3J5X2NvdW50ID0gNSAgCiAgICBmb3Iga2V5LCB2YWx1ZSBpbiBjb250ZXh0LmZvcm0uaXRlbXMoKToKICAgICAgICBpZiAoa2V5ICE9ICdjb3VudCcpOgogICAgICAgICAgICBjb250aW51ZQogICAgICAgIHRyeToKICAgICAgICAgICAgc3RvcnlfY291bnQgPSBpbnQodmFsdWUpCiAgICAgICAgICAgIHN0b3J5X2NvdW50ID0gbWF4KDEsIG1pbihzdG9yeV9jb3VudCwgMTAwKSkgICAgICAgICAgICAKICAgICAgICBleGNlcHQgVmFsdWVFcnJvcjoKICAgICAgICAgICAgcGFzcwogIAogICAgIyBnZXQgbmV3IHN0b3JpZXMgZnJvbSBIYWNrZXIgTmV3cyAoaHR0cHM6Ly9naXRodWIuY29tL0hhY2tlck5ld3MvQVBJKQogICAgcmVzcG9uc2UgPSByZXF1ZXN0cy5nZXQoImh0dHBzOi8vaGFja2VyLW5ld3MuZmlyZWJhc2Vpby5jb20vdjAvbmV3c3Rvcmllcy5qc29uIikKICAgIHN0b3JpZXMgPSByZXNwb25zZS5qc29uKClbOnN0b3J5X2NvdW50XQogICAgCiAgICAjIGdldCBuZXcgc3RvcnkgaW5mbwogICAgcmVzdWx0cyA9ICcnCiAgICBmb3IgaSBpbiBzdG9yaWVzOgogICAgICAgIHJlc3BvbnNlID0gcmVxdWVzdHMuZ2V0KCJodHRwczovL2hhY2tlci1uZXdzLmZpcmViYXNlaW8uY29tL3YwL2l0ZW0vIiArIHN0cihpKSArICIuanNvbiIpCiAgICAgICAgc3RvcnkgPSByZXNwb25zZS5qc29uKCkKICAgICAgICBpZiAoc3RvcnkgaXMgbm90IE5vbmUpOgogICAgICAgICAgICByZXN1bHRzICs9IHN0b3J5LmdldCgndGl0bGUnLCcobm8gdGl0bGUpJykgKyAiXG4iCiAgICAgICAgICAgIHJlc3VsdHMgKz0gc3RvcnkuZ2V0KCd1cmwnLCcobm8gdXJsKScpICsgIlxuXG4iCiAgICAgICAgCiAgICAjIHJldHVybiB0aGUgbGlzdCBvZiBuZXcgc3RvcmllcwogICAgY29udGV4dC5vdXRwdXQud3JpdGUocmVzdWx0cykKCiAgICAgIA=="
              }
            ]
          }
        }

        var cfg_path = 'app.prompt.onboarding.pipeDocument.build.shown'
        this.$_Config_reset(cfg_path)

        this.$store.dispatch('v2_action_createPipe', { attrs }).then(response => {
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
          if (response.ok) {
            this.$router.push({ name: ROUTE_SIGNIN })
          } else {
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
