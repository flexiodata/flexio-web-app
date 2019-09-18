<template>
  <div class="w-100 flex flex-row">
    <el-button
      :class="utilityButtonClass"
      :type="utilityButtonType"
      :disabled="utilityButtonDisabled"
      @click="$emit('utility-click')"
      v-show="utilityButtonVisible"
      v-bind="utilityButtonOptions"
    >
      {{utilityButtonText}}
    </el-button>
    <div class="flex-fill"></div>
    <el-button
      :class="cancelButtonClass"
      :type="cancelButtonType"
      :disabled="cancelButtonDisabled"
      @click="$emit('cancel-click')"
      v-show="cancelButtonVisible"
      v-bind="cancelButtonOptions"
    >
      {{cancelButtonText}}
    </el-button>
    <el-button
      :class="submitButtonClass"
      :type="submitButtonType"
      :disabled="submitButtonDisabled"
      @click="$emit('submit-click')"
      v-show="submitButtonVisible"
      v-bind="submitButtonOptions"
    >
      {{submitButtonText}}
    </el-button>
  </div>
</template>

<script>
  export default {
    props: {
      utilityButtonText: {
        type: String,
        default: 'Utility'
      },
      utilityButtonClass: {
        type: String,
        default: 'ttu fw6'
      },
      utilityButtonDisabled: {
        type: Boolean,
        default: false
      },
      utilityButtonVisible: {
        type: Boolean,
        default: false
      },
      utilityButtonType: {
        type: String,
        default: ''
      },
      utilityButtonOptions: {
        type: Object,
        default: () => {}
      },
      cancelButtonText: {
        type: String,
        default: 'Cancel'
      },
      cancelButtonClass: {
        type: String,
        default: 'ttu fw6'
      },
      cancelButtonVisible: {
        type: Boolean,
        default: true
      },
      cancelButtonDisabled: {
        type: Boolean,
        default: false
      },
      cancelButtonType: {
        type: String,
        default: ''
      },
      cancelButtonOptions: {
        type: Object,
        default: () => {}
      },
      submitButtonText: {
        type: String,
        default: 'Submit'
      },
      submitButtonClass: {
        type: String,
        default: 'ttu fw6'
      },
      submitButtonVisible: {
        type: Boolean,
        default: true
      },
      submitButtonDisabled: {
        type: Boolean,
        default: false
      },
      submitButtonType: {
        type: String,
        default: 'primary'
      },
      submitButtonOptions: {
        type: Object,
        default: () => {}
      },
    }
  }
</script>
