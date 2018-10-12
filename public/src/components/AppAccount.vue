<template>
  <div
    class="bg-nearer-white ph4 overflow-y-scroll relative"
    style="padding-bottom: 8rem"
  >
    <div class="center" style="max-width: 42rem">
      <div class="mt4 mt5-ns bg-white br2 css-white-box">
        <el-tabs
          class="bg-white br2 pv4 pl3"
          tab-position="left"
          @tab-click="onTabClick"
          v-model="active_tab_name"
        >
          <el-tab-pane name="profile">
            <div slot="label">Profile</div>
            <div class="ml3 mr4">
              <h3 class="mt0 mb3 pb2 fw6 bb b--black-10">Profile</h3>
              <AccountProfileForm />
            </div>
          </el-tab-pane>

          <el-tab-pane name="settings">
            <div slot="label">Account</div>
            <div class="ml3 mr4">
              <h3 class="mt0 mb3 pb2 fw6 bb b--black-10">Change password</h3>
              <AccountPasswordForm />
              <div class="h3"></div>
              <h3 class="mt0 mb3 pb2 fw6 bb b--black-10">Regional settings</h3>
              <AccountRegionForm />
              <div class="h3"></div>
              <h3 class="mt0 mb3 pb2 dark-red fw6 bb b--black-10">Delete account</h3>
              <p class="lh-copy f6">Once you delete your account, there's no going back. Please be sure you want to do this!</p>
              <el-button
                class="ttu b"
                type="danger"
                @click="show_account_delete_dialog = true"
              >
                Delete your account
              </el-button>
              <div class="h2"></div>
            </div>
          </el-tab-pane>

          <el-tab-pane name="api">
            <div slot="label">API</div>
            <div class="ml3 mr4" v-if="has_user">
              <h3 class="mt0 mb3 pb2 fw6 bb b--black-10">API keys</h3>
              <p class="lh-copy f6 mb3">This is a list of API keys associated with your account. Remove any keys that you do not recognize.</p>
              <AccountApiForm class="pa3 ba b--black-10 br3" />
            </div>
          </el-tab-pane>
        </el-tabs>
      </div>
    </div>

    <!-- account delete dialog -->
    <el-dialog
      custom-class="el-dialog--no-footer"
      width="32rem"
      top="8vh"
      title="Really delete your account?"
      :modal-append-to-body="false"
      :visible.sync="show_account_delete_dialog"
    >
      <AccountDeleteForm
        class="nt3"
        v-if="show_account_delete_dialog"
      />
    </el-dialog>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import AccountProfileForm from './AccountProfileForm.vue'
  import AccountRegionForm from './AccountRegionForm.vue'
  import AccountApiForm from './AccountApiForm.vue'
  import AccountPasswordForm from './AccountPasswordForm.vue'
  import AccountDeleteForm from './AccountDeleteForm.vue'

  export default {
    metaInfo: {
      title: 'My Account'
    },
    components: {
      AccountProfileForm,
      AccountRegionForm,
      AccountApiForm,
      AccountPasswordForm,
      AccountDeleteForm
    },
    data() {
      return {
        active_tab_name: this.getTabName(),
        show_account_delete_dialog: false
      }
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      has_user() {
        return this.active_user_eid.length > 0
      }
    },
    mounted() {
      this.$store.track('Visited Account Page')

      this.$nextTick(() => {
        this.setRoute(this.getTabName())
      })
    },
    methods: {
      setRoute(tab_name) {
        // update the route
        var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path'])
        _.set(new_route, 'params.section', tab_name)
        this.$router.replace(new_route)
      },
      getTabName() {
        return _.get(this.$route, 'params.section', 'profile')
      },
      onTabClick(tab) {
        this.setRoute(tab.name)
      }
    }
  }
</script>
