<template>
  <div>
    <div class="w-100 mb4" v-if="showHeader">
      <div class="flex flex-row items-center" v-if="showHeader">
        <span class="flex-fill f4">Configure Runtime for '{{pipe.name}}'</span>
        <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="onClose"></i>
      </div>
    </div>

    <div>Pipe Runtime Configure Panel</div>

    <div class="mt4 w-100 flex flex-row justify-end" v-if="showFooter">
      <el-button
        class="ttu b"
        @click="onCancel"
      >
        Cancel
      </el-button>
      <el-button
        class="ttu b"
        type="primary"
        :disabled="has_errors"
        @click="onSubmit"
      >
        Save changes
      </el-button>
    </div>
  </div>
</template>

<script>
  const defaultAttrs = () => {
    return {
      name: '',
      alias: '',
      description: '',
      ui: {}
    }
  }

  export default {
    props: {
      'title': {
        type: String,
        default: ''
      },
      'show-header': {
        type: Boolean,
        default: true
      },
      'show-footer': {
        type: Boolean,
        default: true
      },
      'pipe': {
        type: Object,
        default: () => { return defaultAttrs() }
      }
    },
    watch: {
      pipe: {
        handler: 'initPipe',
        immediate: true,
        deep: true
      },
      edit_pipe: {
        handler: 'updatePipe',
        deep: true
      }
    },
    data() {
      return {
        edit_pipe: defaultAttrs()
      }
    },
    computed: {
      has_errors() {
        return false
      }
    },
    methods: {
      onClose() {
        this.revert()
        this.initPipe()
        this.$emit('close')
      },
      onCancel() {
        this.revert()
        this.initPipe()
        this.$emit('cancel')
      },
      onSubmit() {
        this.$emit('submit', this.edit_pipe)
      },
      initPipe() {
        this.edit_pipe = _.cloneDeep(this.pipe)
      },
      updatePipe() {
        this.$emit('change', this.edit_pipe)
      },
      revert() {
        // TODO
      }
    }
  }
</script>
