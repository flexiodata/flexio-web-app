<template>
  <div class="bg-nearer-white overflow-y-scroll">
    <div class="ph4 pv5">
      <div
        class="w-100 center mw-doc"
        style="min-height: 20rem; margin-bottom: 10rem"
      >
        <el-tabs
          tab-position="left"
          @tab-click="onTabClick"
          v-model="active_tab_name"
        >
          <el-tab-pane name="profile">
            <div slot="label"><div style="min-width: 5rem">Profile</div></div>
            <div class="nt3">
              <div class="ma3 pa4 bg-white br2 css-white-box trans-mw">
                <h3 class="mt0 fw6 f3">Profile</h3>
                <AccountProfileForm style="max-width: 42rem" />
                <div class="h1"></div>
              </div>
            </div>
          </el-tab-pane>

          <el-tab-pane name="settings">
            <div slot="label"><div style="min-width: 5rem">Account</div></div>
            <div class="nt3">
              <div class="ma3 pa4 bg-white br2 css-white-box trans-mw">
                <h3 class="mt0 fw6 f3">Change Password</h3>
                <AccountPasswordForm style="max-width: 28rem" />
                <div class="h3"></div>
                <h3 class="mt0 fw6 f3">Regional Settings</h3>
                <AccountRegionForm style="max-width: 28rem" />
                <div class="h3"></div>
                <h3 class="mt0 fw6 f3 dark-red">Delete Account</h3>
                <p class="lh-copy f6">Once you delete your account, there's no going back. Please be sure you want to do this!</p>
                <el-button
                  class="ttu fw6"
                  type="danger"
                  @click="show_account_delete_dialog = true"
                >
                  Delete your account
                </el-button>
                <div class="h3"></div>
              </div>
            </div>
          </el-tab-pane>

          <el-tab-pane name="api">
            <div slot="label"><div style="min-width: 5rem">API keys</div></div>
            <div class="nt3" v-if="has_user">
              <div class="ma3 pa4 bg-white br2 css-white-box trans-mw">
                <h3 class="mt0 fw6 f3">API Keys</h3>
                <p class="lh-copy f6 mb3">This is a list of API keys associated with your account. Remove any keys that you do not recognize.</p>
                <AccountApiForm />
                <div class="h1"></div>
              </div>
            </div>
          </el-tab-pane>

          <el-tab-pane name="billing">
            <div slot="label"><div style="min-width: 5rem">Billing</div></div>
            <div class="nt3">
              <div class="ma3 pa4 bg-white br2 css-white-box trans-mw">
                <BillingEditPanel />
                <div class="h1"></div>
              </div>
            </div>
          </el-tab-pane>
        </el-tabs>
      </div>
    </div>

    <!-- account delete dialog -->
    <el-dialog
      custom-class="el-dialog--no-footer"
      width="32rem"
      top="4vh"
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
  import AccountProfileForm from '@/components/AccountProfileForm'
  import AccountRegionForm from '@/components/AccountRegionForm'
  import AccountApiForm from '@/components/AccountApiForm'
  import AccountInvoiceDetailsForm from '@/components/AccountInvoiceDetailsForm'
  import AccountPasswordForm from '@/components/AccountPasswordForm'
  import AccountDeleteForm from '@/components/AccountDeleteForm'
  import BillingEditPanel from '@/components/BillingEditPanel'

  export default {
    metaInfo() {
      return {
        title: _.get(this.action_label_lookup, [this.getSection()], 'My Account')
      }
    },
    components: {
      AccountProfileForm,
      AccountRegionForm,
      AccountApiForm,
      AccountInvoiceDetailsForm,
      AccountPasswordForm,
      AccountDeleteForm,
      BillingEditPanel,
    },
    watch: {
      route_action: {
        handler: 'onRouteActionChange',
        immediate: true
      }
    },
    data() {
      return {
        action_label_lookup: {
          profile: 'Your Profile',
          activity: 'Your Activity',
          settings: 'Account Settings',
          api: 'API Keys',
          billing: 'Billing'
        },
        active_tab_name: this.getSection(),
        show_account_delete_dialog: false
      }
    },
    computed: {
      ...mapState({
        active_user_eid: state => state.users.active_user_eid,
      }),
      has_user() {
        return this.active_user_eid.length > 0
      },
      route_action() {
        return _.get(this.$route, 'params.action', 'profile')
      },
    },
    mounted() {
      this.$nextTick(() => {
        this.setRoute(this.getSection())
      })
    },
    methods: {
      getSection() {
        return _.get(this.$route, 'params.action', 'profile')
      },
      setRoute(action) {
        // update the route
        var current_action = _.get(this.$route, 'params.action', '')
        var new_route = _.pick(this.$route, ['name', 'meta', 'params', 'path'])
        new_route.params = _.assign({}, new_route.params, { action })
        this.$router[current_action.length == 0 ? 'replace' : 'push'](new_route)
      },
      onTabClick(tab) {
        this.setRoute(tab.name)
      },
      onRouteActionChange(val) {
        this.active_tab_name = val
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .trans-mw
    transition: max-width .3s ease-in-out
</style>
