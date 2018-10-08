<template>
  <div>
    <div
      class="tl pb3"
      v-if="title.length > 0"
    >
      <h3 class="fw6 f3 mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 marked"
      v-html="description"
      v-show="show_description"
    >
    </div>
    <el-form
      class="el-form--cozy el-form__label-tiny"
      label-position="top"
      inline
      :model="edit_values"
    >
      <el-form-item
        key="remote_state"
        prop="remote_state"
        label="Would you like to execute an inline script or a remote script?"
      >
        <el-radio-group v-model="edit_values.remote_state">
          <el-radio-button
            :label="option.val"
            :key="option.val"
            v-for="option in remote_options"
          >
            {{option.label}}
          </el-radio-button>
        </el-radio-group>
      </el-form-item>
      <el-form-item
        key="lang"
        prop="lang"
        label="Language"
      >
        <el-select v-model="edit_values.lang">
          <el-option
            :label="option.label"
            :value="option.val"
            :key="option.val"
            v-for="option in lang_options"
          />
        </el-select>
      </el-form-item>
      <el-form-item
        class="w-100"
        key="path"
        prop="path"
        label="Remote script URL"
        v-show="edit_values.remote_state == 'remote'"
      >
        <el-input
          autocomplete="off"
          spellcheck="false"
          placeholder="URL"
          :autofocus="true"
          v-model="edit_values.path"
        />
      </el-form-item>
      <el-form-item
        class="w-100"
        key="code"
        prop="code"
        label="Code"
        v-show="edit_values.remote_state == 'inline'"
      >
        <CodeEditor
          class="bg-white ba b--black-10"
          style="line-height: 1.15; font-size: 13px"
          transpose="base64"
          :lang="code_editor_lang"
          :options="{ minRows: 8, maxRows: 32 }"
          v-model="edit_values.code"
        />
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
  import marked from 'marked'
  import util from '../utils'
  import CodeEditor from './CodeEditor.vue'

  const default_python = util.btoaUnicode(`# basic hello world example
def flex_handler(flex):
    flex.end("Hello, World.")
`)

  const default_javascript = util.btoaUnicode(`// basic hello world example
exports.flex_handler = function(flex) {
  flex.end("Hello, World.")
}
`)

  const getDefaultValues = () => {
    return {
      op: 'execute',
      lang: 'python',
      path: '',
      code: '',
      remote_state: 'inline'
    }
  }

  const remote_options = [
    { label: 'Inline script', val: 'inline' },
    { label: 'Remote script', val: 'remote' }
  ]

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
      item: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      },
      edit_values: {
        handler: 'onEditValuesChange',
        immediate: true,
        deep: true
      },
      'edit_values.lang': {
        handler: 'onLangChange'
      },
      is_changed: {
        handler: 'onChange'
      }
    },
    data() {
      return {
        remote_options,
        code_python: default_python,
        code_javascript: default_javascript,
        orig_values: _.assign({}, getDefaultValues()),
        edit_values: _.assign({}, getDefaultValues()),
        lang_options: [
          { label: 'Python',  val: 'python' },
          { label: 'Node.js', val: 'nodejs' }
          //{ label: 'Javascript', val: 'javascript' }
        ]
      }
    },
    computed: {
      show_description() {
        return this.description.length > 0
      },
      title() {
        return _.get(this.item, 'title', 'Execute')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      is_changed() {
        return !_.isEqual(this.edit_values, this.orig_values)
      },
      code_editor_lang() {
        switch (this.edit_values.lang) {
          case 'python':     return 'python'
          case 'nodejs':     return 'javascript'
          case 'javascript': return 'javascript'
        }

        return 'python'
      }
    },
    methods: {
      initSelf() {
        var form_values = _.get(this.item, 'form_values', {})
        form_values = _.assign({}, getDefaultValues(), form_values)
        form_values.remote_state = form_values.path.length == 0 ? 'inline' : 'remote'

        var has_no_code = _.get(form_values, 'code', '').length == 0
        var is_inline_script = form_values.remote_state == 'inline'

        if (has_no_code) {
          form_values.code = this.getCodeByLang(form_values.lang)

          // when creating a new execute task, make sure we fire an 'item-change'
          // event so that the pipe module knows about the default code if the user
          // attempts to save the pipe without editing the code at all
          //
          // NOTE: we cannot use $nextTick here because this call happens multiple times
          if (is_inline_script) {
            setTimeout(() => { this.onEditValuesChange() }, 1)
          }
        }

        switch (form_values.lang) {
          case 'python':
            this.code_python = form_values.code
            break
          case 'nodejs':
          case 'javascript':
            this.code_javascript = form_values.code
            break
        }

        this.orig_values = _.cloneDeep(form_values)
        this.edit_values = _.cloneDeep(form_values)
        this.$emit('update:isNextAllowed', true)
      },
      getCodeByLang(lang) {
        switch (lang) {
          case 'python':     return this.code_python
          case 'nodejs':     return this.code_javascript
          case 'javascript': return this.code_javascript
        }

        return ''
      },
      onChange(val) {
        if (val) {
          this.$emit('active-item-change', this.index)
        }
      },
      onLangChange(val) {
        this.edit_values.code = this.getCodeByLang(val)
      },
      onEditValuesChange() {
        var values = _.assign({}, this.edit_values)
        var is_local = values.remote_state == 'inline' ? true : false
        values = _.pick(values, ['op', 'lang', is_local ? 'code' : 'path'])
        this.$emit('item-change', values, this.index)
      }
    }
  }
</script>
