<template>
  <div class="flex flex-column h-100">
    <div class="w-100 mb3" v-if="showHeader">
      <div class="flex flex-row items-start">
        <span class="flex-fill f4 lh-title">Configure Runtime for '{{pipe.name}}'</span>
        <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="onClose"></i>
      </div>
    </div>

    <multipane
      class="flex-fill vertical-panes ba b--black-10"
      layout="vertical"
    >
      <div
        class="pane"
        :style="{
          maxWidth: '50%',
          minWidth: '200px',
          width: '20%',
          marginLeft: '0',
          opacity: '1'
        }"
      >
        <CodeEditor
          class="h-100"
          :class="!code_expanded ? 'o-40 no-pointer-events no-select' : ''"
          :show-json-view-toggle="false"
          :lang.sync="lang"
          style="font-size: 13px"
          v-show="code_expanded"
          v-model="edit_code"
        />
      </div>
      <multipane-resizer />
      <div
        class="pane bl b--black-20"
        :style="{ flexGrow: 1 }"
      >
        <el-alert
          type="error"
          show-icon
          :title="error_msg"
          :closable="false"
          v-show="error_msg.length > 0"
        />
        <BuilderDocument
          class="h-100"
          :class="error_msg.length > 0 ? 'o-40 no-pointer-events no-select' : ''"
          :definition="edit_json"
        />
      </div>
    </multipane>

    <div class="mt3 w-100 flex flex-row justify-end" v-if="showFooter">
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
  import yaml from 'js-yaml'
  import { Multipane, MultipaneResizer } from 'vue-multipane'
  import CodeEditor from './CodeEditor.vue'
  import BuilderDocument from './BuilderDocument.vue'

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
    components: {
      Multipane,
      MultipaneResizer,
      CodeEditor,
      BuilderDocument
    },
    watch: {
      pipe: {
        handler: 'initPipe',
        immediate: true,
        deep: true
      },
      edit_code: {
        handler: 'updateJSON',
        immediate: true
      }
    },
    data() {
      return {
        lang: 'yaml',
        edit_code: '',
        edit_json: {},
        error_msg: '',
        code_expanded: true
      }
    },
    computed: {
      has_errors() {
        return this.error_msg.length > 0
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
        var edit_pipe = _.cloneDeep(this.pipe)
        _.assign(edit_pipe, this.edit_json)
        this.$emit('submit', edit_pipe)
      },
      initPipe() {
        this.edit_code = yaml.safeDump(_.get(this.pipe, 'ui', {}))
      },
      revert() {
        // TODO: need to do anything?
      },
      updateJSON() {
        var res = ''
        try {
          if (this.lang == 'yaml') {
            // YAML view
            res = yaml.safeLoad(this.edit_code)
          } else {
            // JSON view
            res = JSON.parse(this.edit_code)
          }

          this.error_msg = ''
          this.edit_json = { ui: res }
        }
        catch(e)
        {
          this.error_msg = 'Parse error: ' + e.message
        }
      }
    }
  }
</script>

<style lang="stylus" scoped>
  .vertical-panes
    width: 100%
    height: 100%

  .vertical-panes > .pane ~ .pane
    border-left: 1px solid #ddd
</style>
