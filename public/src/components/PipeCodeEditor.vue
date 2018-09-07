<template>
  <div>
    <CodeEditor
      :class="editorCls"
      :lang.sync="lang"
      :lint="true"
      :enable-json-view-toggle="!has_errors"
      :show-json-view-toggle="false"
      v-bind="$attrs"
      v-model="edit_code"
    />
    <transition name="el-zoom-in-top">
      <div class="f8 dark-red pre overflow-y-hidden overflow-x-auto code mt1" v-if="has_errors && false">{{error_msg}}</div>
    </transition>
  </div>
</template>

<script>
  import yaml from 'js-yaml'
  import Flexio from 'flexio-sdk-js'
  import utilSdkJs from '../utils/sdk-js'
  import CodeEditor from './CodeEditor.vue'

  // TODO: remove 'omitDeep' once we get rid of task eids
  const omitDeep = (collection, excludeKeys) => {
    function omitFn(val) {
      if (val && typeof val === 'object') {
        excludeKeys.forEach((key) => {
          delete val[key]
        })
      }
    }

    return _.cloneDeepWith(collection, omitFn)
  }

  export default {
    inheritAttrs: false,
    props: {
      value: {
        type: Object,
        required: true
      },
      type: {
        type: String,
        default: 'sdk-js' // 'sdk-js', 'json', 'yaml'
      },
      editorCls: {
        type: String,
        default: 'bg-white ba b--black-10'
      },
      taskOnly: {
        type: Boolean,
        default: true
      },
      hasErrors: {
        type: Boolean,
        required: true
      }
    },
    components: {
      CodeEditor
    },
    watch: {
      value: {
        handler: 'initFromPipeTask',
        immediate: true,
        deep: true
      },
      type: {
        handler: 'onTypeChange'
      },
      lang: {
        handler: 'onLangChange'
      },
      edit_code: {
        handler: 'onEditCodeChange'
      },
      has_errors: {
        handler: 'onErrorChange'
      }
    },
    data() {
      return {
        is_inited: false,
        is_editing: false,
        orig_code: '',
        edit_code: '',
        lang: this.type == 'sdk-js' ? 'javascript' : this.type,
        error_msg: ''
      }
    },
    computed: {
      has_errors() {
        return this.error_msg.length > 0
      }
    },
    methods: {
      initFromPipeTask(force) {
        if (this.is_editing && force != true) {
          return
        }

        this.is_inited = false

        try {
          // TODO: remove 'omitDeep' once we get rid of task eids
          var task = omitDeep(this.value, ['eid'])

          switch (this.type) {
            case 'json':
            case 'yaml':
              if (this.lang == 'yaml') {
                // YAML view; stringify JSON into YAML
                this.edit_code = yaml.safeDump(task)
              } else {
                // JSON view; stringify JSON and indent 2 spaces
                this.edit_code = JSON.stringify(task, null, 2)
              }
              break

            case 'sdk-js':
              // Flex.io JS SDK view
              this.edit_code = Flexio.pipe(task).toCode()
              break
          }

          // set original code so we know if we've edited it or not
          this.orig_code = this.edit_code
          this.error_msg = ''
        }
        catch (e) {
          this.error_msg = e.message
        }

        this.is_editing = false
        this.$nextTick(() => { this.is_inited = true })
      },
      revert() {
        this.initFromPipeTask(true)
      },
      onErrorChange() {
        this.$emit('update:hasErrors', this.has_errors ? true : false)
      },
      onTypeChange() {
        this.lang = this.type == 'sdk-js' ? 'javascript' : this.type
        this.initFromPipeTask(true)
      },
      onLangChange() {
        this.initFromPipeTask(true)
      },
      onEditCodeChange() {
        // avoid infinite loop (we emit a value change in this function which
        // will cause the value watcher to call `initFromPipeTask`, etc.)
        if (!this.is_inited) {
          return
        }

        this.is_editing = true

        var obj = null
        var task = null
        var pipe = null

        switch (this.type) {
          case 'json':
          case 'yaml':
            try {
              if (this.lang == 'yaml') {
                // YAML view
                obj = yaml.safeLoad(this.edit_code)
              } else {
                // JSON view
                obj = JSON.parse(this.edit_code)
              }

              this.error_msg = ''
            }
            catch (e)
            {
              this.error_msg = 'Parse error: ' + e.message
            }
            break

          case 'sdk-js':
            try {
              // get the pipe task JSON
              obj = utilSdkJs.getTaskJSON(this.edit_code)
              this.error_msg = ''
            }
            catch(e)
            {
              this.error_msg = 'Syntax error: ' + e.message
            }
            break
        }

        if (this.taskOnly) {
          // we're only dealing with the task JSON
          task = _.cloneDeep(obj)

          if (_.isNil(task)) {
            this.$emit('input', { op: 'sequence', items: [] })
            return
          }

          try {
            if (_.isNil(task.op)) {
              throw({ message: 'Pipes must have an `op` node' })
            } else if (task.op != 'sequence') {
              throw({ message: 'The `op` node must be "sequence"' })
            } else if (_.isNil(task.items)) {
              throw({ message: 'Pipes must have an `items` node' })
            } else if (!_.isArray(task.items)) {
              throw({ message: 'The `items` node must be an array' })
            }

            this.$emit('input', { op: 'sequence', items: task.items })
            this.error_msg = ''
          }
          catch (e) {
            this.$emit('input', { op: 'sequence', items: [] })
            this.error_msg = e.message
          }
        } else {
          // we're dealing with the full pipe JSON
          pipe = _.cloneDeep(obj)

          // TODO: add try/catch error checking here

          this.$emit('input', pipe)
        }
      }
    }
  }
</script>
