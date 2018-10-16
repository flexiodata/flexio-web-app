<template>
  <div>
    <div class="w-100 mb4" v-if="showHeader">
      <div class="flex flex-row items-start">
        <span class="flex-fill f4 lh-title" v-if="has_process">Details for '{{processEid}}'</span>
        <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="onClose"></i>
      </div>
    </div>

    <div class="ph4">
      <div class="flex flex-row items-start mb1" v-for="(val, key) in trimmed_process">
        <div class="ttu fw6 moon-gray" style="width: 150px">{{key}}</div>
        <pre class="flex-fill mt0 code f7 overflow-x-auto">{{val}}&nbsp;</pre>
      </div>
    </div>

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
    computed: {
      has_process() {
        return true
      },
      process() {
        return _.get(this.$store, 'state.objects.' + this.processEid)
      },
      trimmed_process() {
        return _.omit(this.process, ['is_fetched', 'is_fetching'])
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
