<template>
  <div class="flex flex-row items-center">
    <div class="flex-fill">
      <h1 class="mv0 fw6 f3">{{title}}</h1>
    </div>
    <div
      class="flex-none flex flex-row items-center pv2"
      :class="{
        'invisible': is_pipe_mode_run
      }"
    >
      <el-button
        plain
        class="btn-header hint--bottom"
        aria-label="Schedule"
        @click="$emit('schedule-click')"
      >
        <i class="material-icons">date_range</i>
      </el-button>
      <el-button
        plain
        class="btn-header hint--bottom"
        aria-label="Properties"
        @click="$emit('properties-click')"
      >
        <i class="material-icons">edit</i>
      </el-button>
      <el-button
        class="ttu b"
        style="min-width: 5rem; margin: 0 1rem"
        type="primary"
        size="small"
        :disabled="!allowRun"
        @click="$emit('run-click')"
        v-if="!isModeRun"
      >
        Test
      </el-button>
      <div class="flex flex-row items-center justify-center">
        <span class="ttu f6 fw6">Your pipe is</span>
        <LabelSwitch
          class="dib ml2 hint--bottom"
          active-color="#13ce66"
          aria-label="Turn pipe on"
          v-model="is_pipe_mode_run"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import LabelSwitch from './LabelSwitch.vue'

  export default {
    props: {
      title: {
        type: String,
        required: true
      },
      isModeRun: {
        type: Boolean,
        required: true
      },
      allowRun: {
        type: Boolean,
        default: true
      }
    },
    components: {
      LabelSwitch
    },
    computed: {
      is_pipe_mode_run: {
        get() {
          return this.isModeRun
        },
        set(value) {
          this.$emit('update:isModeRun', value)
        }
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .btn-header
    background: transparent
    border: 0
    padding: 0
    margin: 0 0 0 1rem
</style>
