<template>
  <div class="relative">
    <div
      class="z-6 absolute right-0 mt1 mr1"
      :style="json_view_toggle_style"
      v-if="is_json_kind && showJsonViewToggle"
    >
      <el-radio-group
        size="micro"
        :disabled="!enableJsonViewToggle"
        v-model="json_view"
      >
        <el-radio-button label="json"><span class="fw6">JSON</span></el-radio-button>
        <el-radio-button label="yaml"><span class="fw6">YAML</span></el-radio-button>
      </el-radio-group>
    </div>
    <CodeMirror
      ref="editor"
      class="h-100"
      :value="calc_value"
      :options="opts"
      @input="onCmChange"
      @update="onCmUpdate"
      v-bind="$attrs"
    />
  </div>
</template>

<script>
  // vue-codemirror includes
  import 'codemirror/lib/codemirror.css'
  import 'codemirror/addon/lint/lint.css'
  import { codemirror } from 'vue-codemirror'
  import {} from 'codemirror/mode/javascript/javascript'
  import {} from 'codemirror/mode/yaml/yaml'
  import {} from 'codemirror/mode/python/python'
  import {} from 'codemirror/mode/markdown/markdown'
  import {} from 'codemirror/addon/display/placeholder'
  import {} from 'codemirror/addon/lint/lint'
  import {} from 'codemirror/addon/lint/json-lint'
  import {} from 'codemirror/addon/lint/yaml-lint'
  //import {} from 'codemirror/mode/css/css'
  //import {} from 'codemirror/mode/xml/xml'
  //import {} from 'codemirror/mode/htmlmixed/htmlmixed'
  import { atobUnicode, btoaUnicode } from '@/utils'

  import yaml from 'js-yaml'

  export default {
    inheritAttrs: false,
    props: {
      value: {
        type: String,
        required: true
      },
      lang: {
        type: String,
        default: 'javascript' // 'javascript', 'python', 'yaml', 'json', 'html', 'markdown'
      },
      lint: {
        type: Boolean,
        default: false
      },
      transpose: {
        type: String,
        default: 'none' // 'none' or 'base64'
      },
      showJsonViewToggle: {
        type: Boolean,
        default: true
      },
      enableJsonViewToggle: {
        type: Boolean,
        default: true
      },
      options: {
        type: Object,
        default: () => {}
      }
    },
    components: {
      CodeMirror: codemirror
    },
    watch: {
      value: {
        handler: 'transposeValue',
        immediate: true
      },
      lang: {
        handler: 'updateJsonFromLang',
        immediate: true
      },
      json_view: {
        handler: 'onJsonViewChange'
      }
    },
    data() {
      return {
        calc_value: '',
        json_view: this.lang == 'yaml' ? 'yaml' : 'json',
        has_vertical_scrollbar: false,
        scrollbar_width: 0
      }
    },
    computed: {
      mode() {
        switch (this.lang) {
          case 'html':
            return 'htmlmixed'
          case 'json':
            return 'javascript'
        }
        return this.lang
      },
      is_json_kind() {
        return this.lang == 'json' || this.lang == 'yaml'
      },
      json_view_toggle_style() {
        var w = this.scrollbar_width
        return this.has_vertical_scrollbar ? 'padding-right: '+w+'px' : ''
      },
      default_opts() {
        return {
          minRows: 16,
          maxRows: -1,
          /*
          minHeight: number|string, // overrides minRows
          maxHeight: number|string, // overrides maxRows
          */
          lineNumbers: true,
          mode: this.mode,
          gutters: this.lint ? ['CodeMirror-lint-markers'] : [],
          lint: this.lint,
          indentUnit: this.lang == 'python' ? 4 : 2,
          extraKeys: {
            // indent with 4 spaces for python and 2 spaces for all other languages
            Tab: function(cm) {
              //var spaces = Array(cm.getOption('indentUnit') + 1).join(' ')
              var mode = cm.getOption('mode')
              var spaces = mode == 'python' ? '    ' : '  '
              cm.replaceSelection(spaces)
            }
          }
        }
      },
      opts() {
        return _.assign({}, this.default_opts, this.options)
      }
    },
    created() {
      // required for linting
      if (!window.jsyaml) {
        window.jsyaml = yaml
      }
    },
    mounted() {
      this.updateMinMaxHeight()
    },
    methods: {
      focus() {
        if (this.$refs.editor) {
          this.$refs.editor.codemirror.focus()
        }
      },
      getMinHeight() {
        // `minHeight` overrides `minRows`
        var min_h = this.opts.minHeight || (this.opts.minRows * 14) + 8

        // default to pixels if only a number is provided
        return _.isNumber(min_h) ? min_h + 'px' : min_h
      },
      getMaxHeight() {
        if (!_.isNil(this.opts.maxHeight) && this.opts.maxRows < 0)
          return undefined

        // `maxHeight` overrides `maxRows`
        var max_h = this.opts.maxHeight || (this.opts.maxRows * 14) + 8

        // default to pixels if only a number is provided
        return _.isNumber(max_h) ? max_h + 'px' : max_h
      },
      transposeValue() {
        try {
          this.calc_value = this.transpose == 'base64' ? atobUnicode(this.value) : this.value
        }
        catch (e) {
        }
      },
      updateMinMaxHeight() {
        var scroller = this.$refs.editor.codemirror.getScrollerElement()
        scroller.style.minHeight = this.getMinHeight()
        scroller.style.maxHeight = this.getMaxHeight()
      },
      updateJsonFromLang() {
        this.json_view = this.lang == 'yaml' ? 'yaml' : 'json'
      },
      onCmChange: _.debounce(function(value) {
        this.$emit('input', this.transpose == 'base64' ? btoaUnicode(value) : value)
      }, 50),
      onCmUpdate(cm) {
        var info = cm.getScrollInfo()
        this.has_vertical_scrollbar = info.height > info.clientHeight
        this.scrollbar_width = cm.display.nativeBarWidth
      },
      onJsonViewChange(value) {
        this.$emit('update:lang', value)
      }
    }
  }
</script>
