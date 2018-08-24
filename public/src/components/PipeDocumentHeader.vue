<template>
  <div class="flex flex-row items-center">
    <div class="flex-fill">
      <h1 class="mv0 fw6">{{title}}</h1>
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
        style="margin-left: 1rem"
        aria-label="Schedule"
        v-if="false"
      >
        <i class="material-icons">date_range</i>
      </el-button>
      <el-button
        plain
        class="btn-header hint--bottom"
        aria-label="Properties"
        v-if="false"
      >
        <i class="material-icons">edit</i>
      </el-button>
      <LabelSwitch
        class="hint--bottom"
        active-color="#13ce66"
        aria-label="Turn pipe on"
        v-model="is_pipe_mode_run"
      />
      <el-button
        class="ttu b"
        style="min-width: 5rem; margin-left: 1rem"
        type="primary"
        size="small"
        :disabled="!allowRun"
        @click="$emit('run-click')"
        v-if="!isModeRun"
      >
        Test
      </el-button>
      <el-dropdown
        class="ml3"
        trigger="click"
        @command="onCommand"
        v-if="false"
      >
        <span class="el-dropdown-link dib pointer hover-blue">
          <i class="material-icons">more_vert</i>
        </span>
        <el-dropdown-menu style="min-width: 10rem" slot="dropdown">
          <el-dropdown-item class="flex flex-row items-center ph2" command="schedule"><i class="material-icons mr3">date_range</i> Schedule</el-dropdown-item>
          <el-dropdown-item class="flex flex-row items-center ph2" command="deploy"><i class="material-icons mr3">archive</i> Deploy</el-dropdown-item>
          <el-dropdown-item divided></el-dropdown-item>
          <el-dropdown-item class="flex flex-row items-center ph2" command="properties"><i class="material-icons mr3">edit</i> Properties</el-dropdown-item>
        </el-dropdown-menu>
      </el-dropdown>
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
    margin: 0 8px
</style>
