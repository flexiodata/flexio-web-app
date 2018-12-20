<template>
  <el-select
    :value="value"
    @input="emitInput"
    v-bind="$attrs"
  >
    <el-option
      :label="option.label"
      :value="option.val"
      :key="option.val"
      v-for="option in options"
    >
      <div class="flex flex-row items-center">
        <i class="el-icon-success dark-green" v-if="option.val == 'C'"></i>
        <i class="el-icon-warning dark-red" v-else-if="option.val == 'F'"></i>
        <i class="el-icon-error dark-red" v-else-if="option.val == 'X'"></i>
        <i class="el-icon-loading blue" v-else-if="option.val == 'R'"></i>
        <i class="el-icon-info blue" v-else></i>
        <span class="ml2">{{option.label}}</span>
      </div>
    </el-option>
  </el-select>
</template>

<script>
  import * as ps from '../constants/process'

  const fmtProcessStatus = (status) => {
    switch (status) {
      case ps.PROCESS_STATUS_PENDING   : return 'Pending'
      case ps.PROCESS_STATUS_WAITING   : return 'Waiting'
      case ps.PROCESS_STATUS_RUNNING   : return 'Running'
      case ps.PROCESS_STATUS_CANCELLED : return 'Canceled'
      case ps.PROCESS_STATUS_PAUSED    : return 'Paused'
      case ps.PROCESS_STATUS_FAILED    : return 'Failed'
      case ps.PROCESS_STATUS_COMPLETED : return 'Completed'
    }

    return ''
  }

  const statuses = [
    ps.PROCESS_STATUS_COMPLETED,
    ps.PROCESS_STATUS_FAILED,
    ps.PROCESS_STATUS_PENDING,
    ps.PROCESS_STATUS_RUNNING,
    ps.PROCESS_STATUS_CANCELLED
  ]

  const options = _.map(statuses, (val) => {
    return { label: fmtProcessStatus(val), val }
  })

  export default {
    props: {
      value: {
        type: String
      }
    },
    data() {
      return {
        options
      }
    },
    methods: {
      emitInput(val) {
        this.$emit('input', val)
      }
    }
  }
</script>
