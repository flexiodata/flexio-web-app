<template>
  <div>
    <div class="w-100 mb4" v-if="showHeader">
      <div class="flex flex-row items-start">
        <span class="flex-fill f4 lh-title">Process info for '{{pipe_name}}'</span>
        <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="onClose"></i>
      </div>
    </div>

    <el-tabs
      class="el-tabs--flush-header"
      type="card"
      v-model="active_tab_name"
    >
      <el-tab-pane name="process" label="Process info">
        <JsonDetailsPanel
          class="ba b--black-10 pa3 overflow-y-auto"
          style="margin-top: -1px; max-height: 540px"
          :json="process_trimmed"
          v-if="process"
        />
      </el-tab-pane>
      <el-tab-pane name="user" label="User info" v-if="showUser">
        <JsonDetailsPanel
          class="ba b--black-10 pa3 overflow-y-auto"
          style="margin-top: -1px; max-height: 540px"
          :json="user_trimmed"
          v-if="user"
        />
      </el-tab-pane>
      <el-tab-pane name="output" label="Output">
        <ProcessContent
          style="margin-top: -1px"
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
      showUser: {
        type: Boolean,
        default: false
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
      processEid: {
        handler: 'fetchProcess',
        immediate: true
      }
    },
    data() {
      return {
        active_tab_name: 'process'
      }
    },
    computed: {
      process() {
        return _.get(this.$store, 'state.objects.' + this.processEid)
      },
      process_trimmed() {
        return _.omit(this.process, ['is_fetched', 'is_fetching', 'eid_type', 'eid_status', 'started_by', 'log'])
      },
      user() {
        var user_eid = _.get(this.process, 'owned_by.eid', '')
        if (user_eid.length > 0) {
          return _.get(this.$store, 'state.objects.' + user_eid)
        }
        return null
      },
      user_trimmed() {
        return _.omit(this.user, ['is_fetched', 'is_fetching', 'eid_type', 'eid_status', 'email_hash', 'config', 'owned_by'])
      },
      pipe_name() {
        var eid = _.get(this.process, 'parent.eid', '')
        var name = _.get(this.process, 'parent.name', '')
        return eid.length == 0 ? '(Anonymous Pipe)' : name.length > 0 ? name : '(No name)'
      }
    },
    methods: {
      fetchProcess() {
        this.active_tab_name = 'process'

        // query the owner if we have their eid
        var process = _.get(this.$store, 'state.objects.' + this.processEid)
        var user_eid = _.get(process, 'owned_by.eid', '')
        if (this.showUser && user_eid.length > 0) {
          this.$store.dispatch('fetchUser', { eid: user_eid })
        }

        this.$store.dispatch('fetchProcess', { eid: this.processEid })
      },
      onClose() {
        this.$emit('close')
      }
    }
  }
</script>
