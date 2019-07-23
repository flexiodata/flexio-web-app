<template>
  <div class="flex flex-column h-100">
    <div class="w-100 mb3" v-if="showHeader">
      <div class="flex flex-row items-start">
        <span class="flex-fill f4 lh-title">Configure Runtime for '{{pipe.short_description}}'</span>
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
          :show-json-view-toggle="false"
          :lang.sync="lang"
          style="font-size: 13px"
          v-model="edit_code"
        />
      </div>
      <multipane-resizer />
      <div
        class="pane bl b--black-20"
        :style="{
          flexGrow: 1,
          fontSize: '1rem'
        }"
      >
        <el-alert
          type="error"
          show-icon
          :title="error_msg"
          :closable="false"
          v-show="error_msg.length > 0"
        />
        <div class="h-100 bg-nearer-white pv5 ph4 overflow-y-auto" v-if="is_empty">
          <div class="center mw6 f5 lh-copy">
            <p>This is a Flex.io web interface to provide end-users with a runtime version of your pipe, without exposing all of your carefully crafted wiring.</p>

            <p>The web interface provides you with flexibility to request authentication credentials from a user (i.e., logging in with their credentials and running your function against their account) and/or enabling users to apply parameters to your function (i.e., selecting a drop-down menu or entering a value or a date range).</p>

            <p>The web interface is created using YML.  However, the syntax isn't fully documented yet, so please contact us using the chat button at the bottom right if you have any questions and we'll be happy to assist.</p>
          </div>
        </div>
        <BuilderDocument
          class="h-100"
          :class="error_msg.length > 0 ? 'o-40 no-pointer-events no-select' : ''"
          :definition="edit_json"
          v-else
        />
      </div>
    </multipane>

    <div class="mt3 w-100 flex flex-row justify-end" v-if="showFooter">
      <el-button
        class="ttu fw6"
        @click="onCancel"
      >
        Cancel
      </el-button>
      <el-button
        class="ttu fw6"
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
  import CodeEditor from '@comp/CodeEditor'
  import BuilderDocument from '@comp/BuilderDocument'

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
      pipe: {
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
        error_msg: ''
      }
    },
    computed: {
      has_errors() {
        return this.error_msg.length > 0
      },
      is_empty() {
        return this.edit_code == ''
      }
    },
    mounted() {
      this.initPipe()
    },
    methods: {
      onClose() {
        setTimeout(() => {
          this.revert()
          this.initPipe()
        }, 500)
        this.$emit('close')
      },
      onCancel() {
        setTimeout(() => {
          this.revert()
          this.initPipe()
        }, 500)
        this.$emit('cancel')
      },
      onSubmit() {
        var edit_pipe = _.cloneDeep(this.pipe)
        _.assign(edit_pipe, this.edit_json)
        this.$emit('submit', edit_pipe)
      },
      initPipe() {
        var ui_obj = _.get(this.pipe, 'ui', {})
        var code = yaml.safeDump(ui_obj)
        this.edit_code = _.isEmpty(ui_obj) ? '' : code
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
