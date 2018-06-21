<template>
  <div
    :value="value"
  >
    <CodeEditor
      class="bg-white ba b--black-10 overflow-y-auto"
      :lang.sync="lang"
      :enable-json-view-toggle="!has_errors"
      v-bind="$attrs"
      v-model="edit_code"
    />
    <transition name="el-zoom-in-top">
      <div class="f8 dark-red pre overflow-y-hidden overflow-x-auto code mt1" v-if="has_errors">{{error_msg}}</div>
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
        orig_code: '',
        edit_code: '',
        lang: this.type == 'sdk-js' ? 'javascript' : this.type,
        error_msg: ''
      }
    },
    computed: {
      is_editing() {
        return this.edit_code != this.orig_code
      },
      has_errors() {
        return this.error_msg.length > 0
      }
    },
    methods: {
      initFromPipeTask(force) {
        if (this.is_editing && force != true) {
          return
        }

        try {
          // TODO: remove 'omitDeep' once we get rid of task eids
          var task = omitDeep(this.value, ['eid'])

          switch (this.type) {
            case 'json':
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
        var task = null

        switch (this.type) {
          case 'json':
            try {
              if (this.lang == 'yaml') {
                // YAML view
                task = yaml.safeLoad(this.edit_code)
              } else {
                // JSON view
                task = JSON.parse(this.edit_code)
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
              task = utilSdkJs.getTaskJSON(this.edit_code)
              this.error_msg = ''
            }
            catch(e)
            {
              this.error_msg = 'Syntax error: ' + e.message
            }
            break
        }

        if (_.isNil(task)) {
          this.$emit('input', { op: 'sequence', items: [] })
          return
        }

        try {
          if (_.isNil(task.op)) {
            throw({ message: 'Pipes must have an `op` node.' })
          } else if (task.op != 'sequence') {
            throw({ message: 'The `op` node must be "sequence".' })
          } else if (_.isNil(task.items)) {
            throw({ message: 'Pipes must have an `items` node.' })
          } else if (!_.isArray(task.items)) {
            throw({ message: 'The `items` node must be an array.' })
          }

          this.$emit('input', { op: 'sequence', items: task.items })
          this.error_msg = ''
        }
        catch (e) {
          this.$emit('input', { op: 'sequence', items: [] })
          this.error_msg = e.message
        }
      }
    }
  }
</script>
