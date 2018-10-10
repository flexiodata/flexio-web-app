<template>
  <nav class="z-10">
    <div class="flex flex-row items-center bg-white pv1 ph2 ph3-ns" style="min-height: 56px">
      <div class="flex-fill flex flex-row items-center" style="letter-spacing: 0.03em">
        <router-link to="/pipes" class="mr3 dib link v-mid min-w3 hint--bottom" aria-label="Home">
          <img src="../assets/logo-flexio-navbar.png" class="dib" alt="Flex.io">
        </router-link>
        <router-link to="/pipes" class="fw6 f6 ttu link nav-link" style="margin: 0 10px" data-tour-step="pipe-onboarding-8">Pipes</router-link>
        <router-link to="/connections" class="fw6 f6 ttu link nav-link" style="margin: 0 10px">Connections</router-link>
        <router-link to="/storage" class="fw6 f6 ttu link nav-link" style="margin: 0 10px">Storage</router-link>
        <router-link to="/activity" class="fw6 f6 ttu link nav-link" style="margin: 0 10px">Activity</router-link>
      </div>
      <div v-if="user_fetching"></div>
      <div class="flex-none flex flex-row items-center" v-else-if="is_logged_in">
        <el-button
          type="primary"
          class="ttu b"
          @click="onNewPipeClick"
          v-if="is_route_pipes"
        >
          <div style="margin: -2px 0">New Pipe</div>
        </el-button>
        <el-button
          type="primary"
          class="ttu b"
          @click="onNewConnectionClick"
          v-if="is_route_connections"
        >
          <div style="margin: -2px 0">New Connection</div>
        </el-button>
        <UserDropdown class="ml4" />
      </div>
      <div v-else>
        <router-link to="/signin" class="link underline-hover dib f6 f6-ns ttu b black-60 ph2 pv1 mr1 mr2-ns">Sign in</router-link>
        <router-link to="/signup" class="link no-underline dib f6 f6-ns ttu b br1 white bg-orange darken-10 ph2 ph3-ns pv2 mv1">
          <span class="di dn-ns">Sign up</span>
          <span class="dn di-ns">Sign up for free</span>
        </router-link>
      </div>
    </div>

    <!-- connection create dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="51rem"
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_connection_new_dialog"
    >
      <ConnectionEditPanel
        @close="show_connection_new_dialog = false"
        @cancel="show_connection_new_dialog = false"
        @create-connection="show_connection_new_dialog = false"
        v-if="show_connection_new_dialog"
      />
    </el-dialog>
  </nav>
</template>


<script>
  import { mapState } from 'vuex'
  import {
    ROUTE_PIPES,
    ROUTE_HOME_PIPES,
    ROUTE_HOME_CONNECTIONS,
  } from '../constants/route'
  import UserDropdown from './UserDropdown.vue'
  import ConnectionEditPanel from './ConnectionEditPanel.vue'

  export default {
    components: {
      UserDropdown,
      ConnectionEditPanel
    },
    data() {
      return {
        show_connection_new_dialog: false
      }
    },
    computed: {
      ...mapState([
        'active_user_eid',
        'user_fetching'
      ]),
      route_name() {
        return _.get(this.$route, 'name', '')
      },
      is_logged_in() {
        return this.active_user_eid.length > 0
      },
      is_route_pipes() {
        return this.route_name == ROUTE_HOME_PIPES
      },
      is_route_connections() {
        return this.route_name == ROUTE_HOME_CONNECTIONS
      }
    },
    methods: {
      openPipe(eid) {
        this.$router.push({ name: ROUTE_PIPES, params: { eid } })
      },
      tryCreatePipe(attrs) {
        if (!_.isObject(attrs)) {
          attrs = { name: 'Untitled Pipe' }
        }

        this.$store.dispatch('createPipe', { attrs }).then(response => {
          if (response.ok) {
            var pipe = response.body
            var analytics_payload = _.pick(pipe, ['eid', 'name', 'alias', 'created'])
            this.$store.track('Created Pipe', analytics_payload)

            this.openPipe(response.body.eid)
          } else {
            this.$store.track('Created Pipe (Error)')
          }
        })
      },
      onNewPipeClick() {
        // when creating a new pipe, start out with a basic Python 'Hello World' script
        var attrs = {
          name: 'Untitled Pipe',
          task: {
            op: 'sequence',
            items: [{
              op: 'execute',
              lang: 'python',
              code: 'IyBiYXNpYyBoZWxsbyB3b3JsZCBleGFtcGxlCmRlZiBmbGV4X2hhbmRsZXIoZmxleCk6CiAgICBmbGV4LnJlcy5lbmQoIkhlbGxvLCBXb3JsZC4iKQo='
            }]
          }
        }

        this.tryCreatePipe(attrs)
      },
      onNewConnectionClick() {
        this.show_connection_new_dialog = true
      }
    }
  }
</script>

<style lang="stylus" scoped>
  @import '../stylesheets/variables.styl'

  .nav-link
    padding-top: 4px
    padding-bottom: 2px
    border-bottom: 2px solid transparent
    color: $body-color
    transition: border 0.3s ease-in-out

    &.router-link-active
    &:hover
      border-bottom-color: $blue
</style>
