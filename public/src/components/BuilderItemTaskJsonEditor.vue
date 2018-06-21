<template>
  <div>
    <div
      class="tl pb3"
      v-if="title.length > 0"
    >
      <h3 class="fw6 f3 mid-gray mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 mid-gray marked"
      v-html="description"
      v-show="show_description"
    >
    </div>

    <CodeEditor
      class="bg-white ba b--black-10 overflow-y-auto"
      :lang.sync="lang"
      :enable-json-view-toggle="!has_errors"
      :options="{ minRows: 8, maxRows: 20 }"
      v-model="edit_code"
    />
    <transition name="el-zoom-in-top">
      <div class="f8 dark-red pre overflow-y-hidden overflow-x-auto code mt1" v-if="has_errors">{{error_msg}}</div>
    </transition>
  </div>
</template>

<script>
  import yaml from 'js-yaml'
  import marked from 'marked'
  import CodeEditor from './CodeEditor.vue'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number,
        required: true
      },
      activeItemIdx: {
        type: Number,
        required: true
      },
      isNextAllowed: {
        type: Boolean,
        required: true
      }
    },
    components: {
      CodeEditor
    },
    watch: {
      is_changed: {
        handler: 'onChange'
      },
      edit_code: {
        handler: 'onEditJsonChange'
      },
      lang: {
        handler: 'onLangChange'
      },
      has_errors: {
        handler: 'onErrorChange'
      },
      'item.value': {
        handler: 'resetSelf',
        immediate: true,
        deep: true
      }
    },
    data() {
      return {
        task: this.item.value,
        orig_code: '',
        edit_code: '',
        error_msg: '',
        lang: 'json'
      }
    },
    computed: {
      show_description() {
        return this.description.length > 0
      },
      title() {
        return _.get(this.item, 'title', 'Task JSON')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      has_errors() {
        return this.error_msg.length > 0
      },
      is_changed() {
        return this.edit_code != this.orig_code
      }
    },
    methods: {
      resetSelf() {
        var task = this.task

        if (this.lang == 'yaml') {
          // YAML view; stringify JSON into YAML
          this.edit_code = yaml.safeDump(task)
        } else {
          // JSON view; stringify JSON and indent 2 spaces
          this.edit_code = JSON.stringify(task, null, 2)
        }

        this.orig_code = this.edit_code
        this.error_msg = ''
      },
      onErrorChange() {
        this.$emit('item-error-change', this.has_errors, this.index)
        this.$emit('update:isNextAllowed', !this.has_errors)
      },
      onLangChange() {
        this.resetSelf()
      },
      onChange(val) {
        if (val === true) {
          this.$emit('active-item-change', this.index)
        }
      },
      onEditJsonChange() {
        var task = null

        try {
          if (this.lang == 'yaml') {
            // YAML view
            task = yaml.safeLoad(this.edit_code)
          } else {
            // JSON view
            task = JSON.parse(this.edit_code)
          }

          this.task = task
          this.error_msg = ''
        }
        catch(e)
        {
          this.error_msg = 'Parse error: ' + e.message
        }


        if (_.isNil(task)) {
          this.$emit('item-change', {}, this.index)
          return
        }

        try {
          if (_.isNil(task.op)) {
            throw({ message: 'Tasks must have an `op` node.' })
          }

          this.$emit('item-change', task, this.index)
          this.error_msg = ''
        }
        catch (e) {
          this.$emit('item-change', {}, this.index)
          this.error_msg = e.message
        }
      }
    }
  }
</script>
