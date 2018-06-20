<template>
  <div :value="value">
    <CodeEditor
      class="bg-white ba b--black-10 overflow-y-auto"
      :lang="lang"
      v-bind="$attrs"
      v-model="code"
    />
    <transition name="el-zoom-in-top">
      <div class="f8 dark-red pre overflow-y-hidden overflow-x-auto code mt1" v-if="error_msg.length > 0">{{errorPrefix}}{{error_msg}}</div>
    </transition>
  </div>
</template>

<script>
  import yaml from 'js-yaml'
  import Flexio from 'flexio-sdk-js'
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
      },
      errorPrefix: {
        type: String,
        default: ''
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
        handler: 'onTypeChange',
        immediate: true,
        deep: true
      },
      code: {
        handler: 'onChange'
      },
      error_msg: {
        handler: 'onErrorChange'
      }
    },
    data() {
      return {
        orig_code: '',
        code: '',
        error_msg: ''
      }
    },
    computed: {
      lang() {
        return this.type == 'yaml' ? 'yaml' : 'javascript'
      },
      is_editing() {
        return this.code != this.orig_code
      }
    },
    methods: {
      initFromPipeTask(force) {
        if (this.is_editing && force != true) {
          return
        }

        // TODO: remove 'omitDeep' once we get rid of task eids
        var task = omitDeep(this.value, ['eid'])

        switch (this.type) {
          case 'json':
            // stringify JSON; indent 2 spaces
            this.code = JSON.stringify(task, null, 2)
            break

          case 'yaml':
            // stringify JSON into YAML
            this.code = yaml.safeDump(task)
            break
        }

        // set original code so we know if we've edited it or not
        this.orig_code = this.code
      },
      revert() {
        this.initFromPipeTask(true)
      },
      onErrorChange() {
        this.$emit('update:hasErrors', this.error_msg.length > 0 ? true : false)
      },
      onTypeChange() {
        this.initFromPipeTask(true)
      },
      onChange() {
        var task = null

        switch (this.type) {
          case 'json':
            try {
              task = JSON.parse(this.code)
              this.error_msg = ''
            }
            catch (e)
            {
              this.error_msg = e.message
            }
            break

          case 'yaml':
            try {
              task = yaml.safeLoad(this.code)
              this.error_msg = ''
            }
            catch(e)
            {
              this.error_msg = e.message
            }
            break
        }

        if (_.isNil(task)) {
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
          this.error_msg = e.message
        }
      }
    }
  }
</script>
