<template>
  <div>
    <div class="w-100 mb4" v-if="showHeader">
      <div class="flex flex-row items-start">
        <span class="flex-fill f4 lh-title" v-if="has_process">Process info for '{{pipe_name}}'</span>
        <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="onClose"></i>
      </div>
    </div>

    <el-tabs
      type="border-card"
      v-model="active_tab_name"
    >
      <el-tab-pane name="details" label="Process info">
        <JsonDetailsPanel
          class="pa4 overflow-y-auto"
          style="margin: -15px; max-height: 598px"
          :json="process_trimmed"
          v-if="process"
        />
      </el-tab-pane>
      <el-tab-pane name="output" label="Output">
        <ProcessContent
          style="margin: -16px"
          :processEid="processEid"
        />
      </el-tab-pane>
    </el-tabs>

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
    watch: {
      processEid() {
        this.active_tab_name = 'details'
        this.$store.dispatch('fetchProcess', { eid: this.processEid })
      }
    },
    data() {
      return {
        active_tab_name: 'details'
      }
    },
    computed: {
      has_process() {
        return true
      },
      process() {
        return _.get(this.$store, 'state.objects.' + this.processEid)
      },
      process_trimmed() {
        return _.omit(this.process, ['is_fetched', 'is_fetching', 'eid_type', 'eid_status', 'started_by', 'log'])
      },
      pipe_name() {
        var eid = _.get(this.process, 'parent.eid', '')
        var name = _.get(this.process, 'parent.name', '')
        return eid.length == 0 ? '(Anonymous Pipe)' : name.length > 0 ? name : '(No name)'
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
