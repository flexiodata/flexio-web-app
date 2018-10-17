<template>
  <div>
    <div class="w-100 mb4" v-if="showHeader">
      <div class="flex flex-row items-start">
        <span class="flex-fill f4 lh-title" v-if="has_process">Details for '{{pipe_name}}'</span>
        <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="onClose"></i>
      </div>
    </div>

    <JsonDetailsPanel :json="process_trimmed" v-if="process" />

    <h4 class="f8 fw6 ttu moon-gray mb1 mt4 pb1">Process Content</h4>
    <ProcessContent :processEid="processEid" />

    <div class="mt4 w-100 flex flex-row justify-end" v-if="showFooter">
      <el-button
        class="ttu b"
        type="primary"
        @click="onClose"
      >
        Close
      </el-button>
    </div>
  </div>
</template>

<script>
  import JsonDetailsPanel from './JsonDetailsPanel.vue'
  import ProcessContent from './ProcessContent.vue'

  export default {
    props: {
      title: {
        type: String,
        default: ''
      },
      showHeader: {
        type: Boolean,
        default: true
      },
      showFooter: {
        type: Boolean,
        default: true
      },
      processEid: {
        type: String,
        required: true
      }
    },
    components: {
      JsonDetailsPanel,
      ProcessContent
    },
    computed: {
      has_process() {
        return true
      },
      process() {
        return _.get(this.$store, 'state.objects.' + this.processEid)
      },
      process_trimmed() {
        return _.omit(this.process, ['is_fetched', 'is_fetching'])
      },
      pipe_name() {
        return _.get(this.process, 'parent.name', 'Anonymous Pipe')
      }
    },
    mounted() {
      this.$store.dispatch('fetchProcess', { eid: this.processEid })
    },
    methods: {
      onClose() {
        this.$emit('close')
      }
    }
  }
</script>
