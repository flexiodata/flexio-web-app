<template>
  <div
    class="bg-nearer-white ph4 overflow-y-scroll relative"
    style="padding-bottom: 8rem"
  >
    <div class="center" style="max-width: 36rem">
      <div
        class="mt4 mb3 relative z-7 bg-nearer-white"
      >
        <div
          class="flex flex-row items-center center tc"
          style="max-width: 1440px"
        >
          <h1 class="flex-fill mv0 pv3 fw6 mid-gray">Account Settings</h1>
        </div>
      </div>
      <div class="bg-white css-white-box br2">
        <el-tabs
          class="bg-white br2 ph4 pt3 pb4 el-tabs--allow-overflow"
          @tab-click="onTabClick"
          v-model="active_tab_name"
        >
          <el-tab-pane name="profile">
            <div slot="label" class="tc" style="min-width: 3rem">Profile</div>
            <AccountProfileForm class="mt3" />
          </el-tab-pane>

          <el-tab-pane name="region">
            <div slot="label" class="tc" style="min-width: 3rem">Region</div>
            <AccountRegionForm class="mt3" />
          </el-tab-pane>

          <el-tab-pane name="api">
            <div slot="label" class="tc" style="min-width: 3rem">API</div>
            <AccountApiForm class="mt3" v-if="has_user" />
          </el-tab-pane>

          <el-tab-pane name="password">
            <div slot="label" class="tc" style="min-width: 3rem">Password</div>
            <AccountPasswordForm class="mt3" />
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
