<template>
  <div class="flex flex-column items-stretch">
    <h1 v-if="false" class="flex-none f3 f2-l fw6 ph2 pv2 pv3-m pv4-l ma0 truncate white bg-blue tc">Account</h1>
    <h1 class="flex-none f3 f2-l fw6 ph2 pv2 pv3-m pv4-l ma0 truncate mid-gray tc">Account</h1>
    <ui-tabs class="flex-fill flex flex-column ui-tabs--centered ui-tabs--fill">
      <ui-tab title="Profile">
        <div class="mw6 center mt3">
          <account-profile-form></account-profile-form>
        </div>
      </ui-tab>

      <ui-tab title="Region">
        <div class="mw6 center mt3">
          <account-region-form></account-region-form>
        </div>
      </ui-tab>

      <ui-tab title="API">
        <div class="mw6 center mt3">
          <account-api-form v-if="has_user"></account-api-form>
        </div>
      </ui-tab>

      <ui-tab title="Password">
        <div class="mw6 center mt3">
          <account-password-form></account-password-form>
        </div>
      </ui-tab>
    </ui-tabs>
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
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      has_user() {
        return this.active_user_eid.length > 0
      }
    },
    mounted() {
      analytics.track('Visited Account Page')
    }
  }
</script>
