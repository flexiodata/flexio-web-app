<template>
  <div
    class="flex flex-column bg-nearer-white overflow-y-scroll"
  >
    <div class="ph4 pv5">
      <div
        class="w-100 center pa4 bg-white br2 css-white-box overflow-hidden"
        style="max-width: 1280px; min-height: 20rem; margin-bottom: 15rem"
      >
        <ProcessActivity>
          <div slot="title">
            <h3 class="mv0 fw6 f3">Activity</h3>
          </div>
        </ProcessActivity>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import ProcessActivity from '@/components/ProcessActivity'

  export default {
    metaInfo() {
      return {
        title: 'Activity',
        titleTemplate: (chunk) => {
          return chunk ? `${chunk} | ${this.getActiveTeamLabel()}` : 'Flex.io'
        }
      }
    },
    components: {
      ProcessActivity
    },
    methods: {
      ...mapGetters('teams', {
        'getActiveTeamLabel': 'getActiveTeamLabel'
      }),
    }
  }
</script>
