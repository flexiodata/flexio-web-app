<template>
  <div
    class="bg-nearer-white ph4 overflow-y-scroll relative"
    style="padding-bottom: 8rem"
  >
    <div class="center" style="max-width: 42rem">
      <div class="mt4 mt5-ns bg-white css-white-box br2">
        <el-tabs
          class="bg-white br2 pv4 pl2"
          tab-position="left"
          @tab-click="onTabClick"
          v-model="active_tab_name"
        >
          <el-tab-pane name="profile">
            <div slot="label">Profile</div>
            <div class="ml3 mr4">
              <h3 class="mt0 mb3 pb2 mid-gray fw6 bb b--black-10">Profile</h3>
              <AccountProfileForm />
            </div>
          </el-tab-pane>

          <el-tab-pane name="account">
            <div slot="label">Account</div>
            <div class="ml3 mr4">
              <h3 class="mt0 mb3 pb2 mid-gray fw6 bb b--black-10">Change password</h3>
              <AccountPasswordForm />
              <div class="h3"></div>
              <h3 class="mt0 mb3 pb2 mid-gray fw6 bb b--black-10">Regional settings</h3>
              <AccountRegionForm />
            </div>
          </el-tab-pane>

          <el-tab-pane name="api">
            <div slot="label">API</div>
            <div class="ml3 mr4" v-if="has_user">
              <h3 class="mt0 mb3 pb2 mid-gray fw6 bb b--black-10">API keys</h3>
              <p class="lh-copy f6">This is a list of API keys associated with your account. Remove any keys that you do not recognize.</p>
              <AccountApiForm class="pa3 ba b--black-10 br2" />
            </div>
          </el-tab-pane>
        </el-tabs>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import AccountProfileForm from './AccountProfileForm.vue'
  import AccountRegionForm from './AccountRegionForm.vue'
  import AccountApiForm from './AccountApiForm.vue'
  import AccountPasswordForm from './AccountPasswordForm.vue'

  export default {
    components: {
      AccountProfileForm,
      AccountRegionForm,
      AccountApiForm,
      AccountPasswordForm
    },
    data() {
      return {
        active_tab_name: this.getTabName()
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
        this.setHash(this.getTabName())
      })
    },
    methods: {
      setHash(tab_name) {
        window.location.hash = '#'+tab_name
      },
      getTabName() {
        var hash = window.location.hash
        var tab_name = hash.substring(1)
        return tab_name.length > 0 ? tab_name : 'profile'
      },
      onTabClick(tab) {
        this.setHash(tab.name)
      }
    }
  }
</script>
