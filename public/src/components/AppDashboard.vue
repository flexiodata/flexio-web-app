<template>
  <div class="overflow-y-auto bg-nearer-white">
    <!-- control bar -->
    <div class="pa3 pa4-l pb3-l bb bb-0-l b--black-10">
      <div class="flex flex-row">
        <div class="flex-fill flex flex-row items-center">
          <div class="f2 dn db-ns mr3">Dashboard</div>
        </div>
        <div class="flex-none flex flex-row items-center">
          <el-button type="primary" class="ttu b" @click="onNewPipeClick">New pipe</el-button>
        </div>
      </div>
    </div>

    <div class="ma4">
      <div class="flex flex-row items-center pv2">
        <div class="flex-fill fw6">Getting Started</div>
        <!-- placeholder for consistent spacing -->
        <i class="invisible material-icons">chevron_right</i>
      </div>
      <div class="bg-white br2 css-white-box">
        <div class="pa3">
          <div class="dib">
            <help-items
              help-message="I need help getting started with Flex.io..."
              :items="['quick-start', 'sdk-and-cli', 'api-docs', 'templates', 'help']"
              :item-cls="'f6 fw6 ttu br2 ma1 pv3 w4 pointer silver hover-blue bg-white hover-bg-nearer-white'"
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
      <div class="bg-white br2 css-white-box">
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
      <div class="bg-white br2 css-white-box">
        <div class="pa3">
          <stats-pipes-run :chart-height="280"></stats-pipes-run>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import { ROUTE_PIPES } from '../constants/route'
  import StatsPipesRun from './StatsPipesRun.vue'
  import AccountApiForm from './AccountApiForm.vue'
  import HelpItems from './HelpItems.vue'

  export default {
    components: {
      StatsPipesRun,
      AccountApiForm,
      HelpItems
    },
    mounted() {
      this.$store.track('Visited Dashboard Page')
    },
    methods: {
      openPipe(eid) {
        this.$router.push({ name: ROUTE_PIPES, params: { eid } })
      },
      tryCreatePipe(attrs) {
        if (!_.isObject(attrs))
          attrs = { name: 'Untitled Pipe' }

        this.$store.dispatch('createPipe', { attrs }).then(response => {
          if (response.ok)
          {
            var pipe = response.body
            var analytics_payload = _.pick(pipe, ['eid', 'name', 'description', 'alias', 'created'])
            this.$store.track('Created Pipe', analytics_payload)

            this.openPipe(response.body.eid)
          }
           else
          {
            this.$store.track('Created Pipe (Error)')
          }
        })
      },
      onNewPipeClick() {
        this.tryCreatePipe()
      }
    }
  }
</script>
