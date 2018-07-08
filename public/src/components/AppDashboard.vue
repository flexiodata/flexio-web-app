<template>
  <div class="overflow-y-auto bg-nearer-white">
    <div class="ma4">
      <div class="f3 f2-ns">Dashboard</div>
    </div>

    <div class="ma4">
      <div class="flex flex-row items-center pv2">
        <div class="flex-fill fw6">Getting Started</div>
        <!-- placeholder for consistent spacing -->
        <i class="invisible material-icons">chevron_right</i>
      </div>
      <div class="bg-white css-white-box">
        <div class="pa3">
          <div class="dib">
            <help-items
              help-message="I need help getting started with Flex.io..."
              :items="['quick-start', 'sdk-and-cli', 'api-docs', 'templates', 'help']"
              :item-cls="'f6 fw6 ttu br2 ma1 pv3 w4 pointer silver hover-blue bg-white hover-bg-near-white'"
            ></help-items>
          </div>
        </div>
      </div>
    </div>

    <div class="ma4">
      <div class="flex flex-row items-center pv2">
        <div class="flex-fill fw6">API Keys</div>
        <router-link
          to="/account#api"
          class="flex flex-row items-center ml2 ml3-ns pl2 pl3-ns f6 fw6 link blue dim"
        >
          Manage API Keys <i class="material-icons">chevron_right</i>
        </router-link>
      </div>
      <div class="bg-white css-white-box">
        <div class="pa3">
          <div class="dib">
            <account-api-form
              ref="account-api-form"
              :show-create-button="false"
              :show-only-one="true"
            ></account-api-form>
          </div>
        </div>
      </div>
    </div>

    <div class="ma4">
      <div class="flex flex-row items-center pv2">
        <div class="flex-fill fw6">Pipe Activity</div>
        <!-- placeholder for consistent spacing -->
        <i class="invisible material-icons">chevron_right</i>
        <router-link
          to="/dashboard"
          class="flex flex-row items-center ml2 ml3-ns pl2 pl3-ns f6 fw6 link blue dim"
          v-if="false"
        >
          View Activity <i class="material-icons">chevron_right</i>
        </router-link>
      </div>
      <div class="bg-white css-white-box">
        <div class="pa3">
          <stats-pipes-run :chart-height="280"></stats-pipes-run>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import StatsPipesRun from './StatsPipesRun.vue'
  import AccountApiForm from './AccountApiForm.vue'
  import HelpItems from './HelpItems.vue'

  export default {
    components: {
      StatsPipesRun,
      AccountApiForm,
      HelpItems
    }
  }
</script>
