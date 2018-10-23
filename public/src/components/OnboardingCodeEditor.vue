<template>
  <div :class="cls" v-show="is_inited">
    <div class="absolute top-0 left-0 fw6 moon-gray ph2 pv1 no-select" style="z-index: 10; font-size: 10px" v-if="label.length > 0">{{label}}</div>
    <div class="absolute top-0 right-0 flex flex-row" style="z-index: 10; overflow: hidden" v-if="showButtons">
      <button
        type="button"
        class="btn border-box no-select pointer lh-copy white bg-black-20 hover-bg-blue darken-10"
        style="padding: 1px 12px 2px"
        @click="$emit('save')"
        v-if="show_save_button"
      ><span class="f6 ttu fw6">Save &amp; Deploy</span></button>
      <button
        type="button"
        aria-label="Copy to Clipboard"
        class="btn border-box no-select pointer lh-copy white bg-black-20 hover-bg-blue darken-10 hint--top clipboardjs"
        style="padding: 1px 12px 2px"
        :class="show_run_button || show_see_it_work_button ? '' : 'br2 br--top br--right'"
        :data-clipboard-target="'#'+textarea_id"
        @click="copy"
        v-if="show_copy_button"
      ><span class="f6 ttu fw6">Copy</span></button>
      <button
        type="button"
        class="border-box no-select pointer lh-copy white bg-blue darken-10"
        style="padding: 1px 12px 2px"
        :class="show_see_it_work_button ? '' : 'br2 br--top br--right'"
        @click="run"
        v-if="show_run_button"
      ><span class="f6 ttu fw6">Run</span></button>
      <a
        class="border-box no-select pointer lh-copy br2 br--top br--right white bg-blue darken-10"
        style="padding: 1px 12px 2px"
        target="_blank"
        :href="externalLink"
        v-if="show_see_it_work_button"
      ><span class="f6 ttu fw6">See It Work</span></a>
    </div>
    <h4 class="mt0" v-if="showTitle && title.length > 0">{{title}}</h4>
    <div
      class="relative bg-white css-white-box lh-title"
      v-if="isEditable"
    >
      <CodeEditor
        lang="javascript"
        v-model="edit_code"
        v-if="is_inited"
      />
    </div>
    <div
      class="relative pv2 ph3 bg-near-white br2 overflow-x-auto"
      :style="label.length > 0 ? 'padding-top: 20px' : ''"
      v-else
    >
      <pre class="f7 lh-title"><code class="db" style="white-space: pre-wrap" spellcheck="false">{{edit_code}}</code></pre>
    </div>
    <div class="mt3 pa3 bg-white css-white-box" v-if="is_loading">
      <div class="v-mid fw6 dark-gray"><span class="fa fa-spin fa-spinner"></span> Running...</div>
    </div>
    <div class="mt3 pa3 bg-white css-white-box" v-else-if="has_text_result || has_img_src || has_pdf_src">
      <div class="flex flex-row mb2">
        <h4 class="flex-fill mv0">Output</h4>
        <div>
          <div
            class="pointer moon-gray hover-blue hover-b--blue"
            @click="toggleOutput"
          >
            <i class="db material-icons no-select trans-t" :class="show_output?'rotate-90':'rotate-270'">chevron_right</i>
          </div>
        </div>
      </div>
      <div v-show="show_output">
        <pre class="f6" v-if="has_text_result"><code ref="output" class="db overflow-auto language-javascript" spellcheck="false" style="max-height: 25rem">{{text_result}}</code></pre>
        <img :src="img_src" v-if="has_img_src" />
        <div v-else-if="has_pdf_src" class="bg-white relative overflow-hidden" style="height: 25rem">
          <iframe :src="pdf_src" class="absolute top-0 left-0 w-100 h-100" height="100%" width="100%" frameborder="0" allowfullscreen></iframe>
        </div>
      </div>
    </div>
    <textarea
      class="fixed"
      style="left: 9999px; top: 9999px"
      :id="textarea_id"
      v-model="copy_code"
      v-if="show_copy_button"
    >
    </textarea>
  </div>
</template>

<script>
  import Flexio from 'flexio-sdk-js'
  import utilSdkJs from '../utils/sdk-js'
  import CodeEditor from './CodeEditor.vue'

  export default {
    props: {
      'title': {
        type: String,
        default: ''
      },
      'label': {
        type: String,
        default: ''
      },
      'cls': {
        type: String,
        default: 'relative pa3 bg-near-white br2 box-shadow'
      },
      'code': {
        type: String,
        default: ''
      },
      'fn': {
        type: Function,
        required: false // eval(code) if not provided
      },
      'api-key': {
        type: String,
        default: () => {
          // example@flex.io on https://www.flex.io
          if (window.location.hostname == 'www.flex.io')
            return 'bbphhkgvrfkgdskssrcb'

          // example@flex.io on https://test.flex.io
          return 'sqdmfxskjwxkgddfpxrd'
        }
      },
      'sdk-options': {
        type: Object,
        default: () => {
          switch (window.location.hostname) {
            case 'localhost':    return { host: 'localhost', insecure: true }
            case 'test.flex.io': return { host: 'test.flex.io' }
            case 'www.flex.io':  return { host: 'www.flex.io' }
          }

          return {}
        }
      },
      'response-type': {
        type: String,
        default: 'text'
      },
      'external-link': {
        type: String,
        default: 'https://www.flex.io'
      },
      'show-title': {
        type: Boolean,
        default: false
      },
      'show-description': {
        type: Boolean,
        default: false
      },
      'show-buttons': {
        type: Boolean,
        default: true
      },
      'is-editable': {
        type: Boolean,
        default: true
      },
      'buttons': {
        type: Array,
        default: () => { return ['copy', 'run'] }
      },
      'copy-prefix': {
        type: String,
        default: "\/\/ insert your API key here to use the Flex.io JS SDK\n\/\/ Flexio.setup('YOUR_API_KEY')\n\n"
      },
      'copy-suffix': {
        type: String,
        default: ''
      }
    },
    components: {
      CodeEditor
    },
    watch: {
      code(val) {
        this.edit_code = val
        this.syntax_msg = ''
      }
    },
    data() {
      return {
        edit_code: '',
        syntax_msg: '',
        text_result: '',
        img_src: '',
        pdf_src: '',
        textarea_id: _.uniqueId('textarea-'),
        show_output: true,
        is_loading: false,
        is_inited: false
      }
    },
    computed: {
      has_text_result() {
        return this.text_result.length > 0
      },
      has_img_src() {
        return this.img_src.length > 0
      },
      has_pdf_src() {
        return this.pdf_src.length > 0
      },
      show_save_button() {
        return this.buttons.indexOf('save') != -1
      },
      show_copy_button() {
        return this.buttons.indexOf('copy') != -1
      },
      show_run_button() {
        return this.buttons.indexOf('run') != -1
      },
      show_see_it_work_button() {
        return this.buttons.indexOf('see-it-work') != -1
      },
      save_code() {
        // TODO: we need to give this code editor quite a bit of love...
        // this is a bit of a kludge that allows pipe code to be pasted with a .run()
        var code = this.edit_code
        var idx = code.indexOf('.run')
        if (idx >= 0)
          code = code.substring(0, idx)
        return code.trim()
      },
      run_code() {
        var code = this.edit_code
        var idx = code.indexOf('.run')
        if (idx >= 0)
          code = code.substring(0, idx)
        return code + '.run(callback)'
      },
      run_fn() {
        if (typeof this.fn == 'function')
        {
          return this.fn
        }
         else
        {
          return (Flexio, callback) => {
            eval(this.run_code)
          }
        }
      },
      code_to_show() {
        var code = this.code.trim()
        var replace_str = ''

        switch (this.responseType)
        {
          default:
          case 'text':
            replace_str = "function(err, response) {\n    console.log(response.text)\n  })"
            break

          case 'list':
            replace_str = "function(err, list) {\n    console.log(list)\n  })"
            break

          case 'image':
            replace_str = "function(err, response) {\n    // image returned as 'response.blob' or 'response.buffer' on node.js\n  })"
            break

          case 'pdf':
            replace_str = "function(err, response) {\n    // pdf returned as 'response.blob' or 'response.buffer' on node.js\n  })"
            break
        }

        return code.replace('callback)', replace_str)
      },
      copy_code() {
        return this.copyPrefix + this.edit_code + this.copySuffix
      }
    },
    mounted() {
      this.$nextTick(() => {
        this.is_inited = true
        this.edit_code = this.code_to_show
      })
    },
    methods: {
      toggleOutput() {
        this.show_output = !this.show_output
      },
      getEditCode() {
        return this.edit_code
      },
      getTaskJSON() {
        try {
          // get the pipe task JSON
          var task = utilSdkJs.getTaskJSON(this.save_code)
          return task
        }
        catch(e)
        {
          return { op: 'sequence', items: [] }
        }
      },
      copy() {
        //this.$store.track('Copied Code Example', {
        //  title: this.title,
        //  code: this.edit_code
        //})
      },
      run() {
        if (typeof this.run_fn == 'function')
        {
          Flexio.setup(this.apiKey, this.sdkOptions)

          //this.$store.track('Ran Code Example', {
          //  title: this.title,
          //  code: this.edit_code
          //})

          this.is_loading = true

          this.run_fn.call(this, Flexio, (err, response) => {
            this.text_result = ''
            this.img_src = ''
            this.pdf_src = ''

            var content_type = ''

            // check the content type of our response; if the content type
            // exists, it's a Pipe API response, otherwise, it's simply a
            // JSON response (patched through from the Flex.io backend)
            if (_.isPlainObject(response) && typeof response.contentType == 'string')
              content_type = response.contentType

            if (content_type.substr(0,6) == 'image/') {
              // response is an image blob
              var url_creator = window.URL || window.webkitURL
              this.img_src = url_creator.createObjectURL(response.blob)
            } else if (content_type == 'application/pdf') {
              // response is a pdf blob
              var url_creator = window.URL || window.webkitURL
              this.pdf_src = url_creator.createObjectURL(response.blob)
            } else if (content_type == 'application/json') {
              // response is json
              this.text_result = JSON.stringify(response.data, null, 2)
            } else if (content_type.length > 0 && response.hasOwnProperty('buffer')) {
              // echo text by default
              this.text_result = response.text
            } else {
              // response is JSON
              this.text_result = JSON.stringify(response, null, 2)
            }

            this.is_loading = false
          }, (response) => {
            this.is_loading = false
          })
        }
      }
    }
  }
</script>
