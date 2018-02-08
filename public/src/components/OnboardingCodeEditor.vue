<template>
  <div :class="cls" v-show="is_inited">
    <div class="flex flex-row absolute top-0 right-0" style="z-index: 10" v-if="showButtons">
      <button
        type="button"
        class="btn border-box no-select pointer f6 pv1 lh-copy ph3 white bg-black-20 hover-bg-blue darken-10"
        @click="$emit('save')"
        v-if="show_save_button"
      ><span class="f6 ttu b">Save &amp; Deploy</span></button>
      <button
        type="button"
        aria-label="Copy to Clipboard"
        class="btn border-box no-select pointer f6 pv1 lh-copy ph3 white bg-black-20 hover-bg-blue darken-10 hint--top clipboardjs"
        :class="show_run_button || show_see_it_work_button ? '' : 'br2 br--top br--right'"
        :data-clipboard-target="'#'+textarea_id"
        @click="copy"
        v-if="show_copy_button"
      ><span class="f6 ttu b">Copy</span></button>
      <button
        type="button"
        class="border-box no-select pointer f6 pv1 lh-copy ph3 white bg-blue darken-10"
        :class="show_see_it_work_button ? '' : 'br2 br--top br--right'"
        @click="run"
        v-if="show_run_button"
      ><span class="f6 ttu b">Run</span></button>
      <a
        class="border-box no-select pointer f6 pv1 lh-copy ph3 br2 br--top br--right white bg-blue darken-10"
        target="_blank"
        :href="externalLink"
        v-if="show_see_it_work_button"
      ><span class="f6 ttu b">See It Work</span></a>
    </div>
    <h4 class="mt0" v-if="title.length > 0">{{title}}</h4>
    <div v-if="description.length > 0">
      <pre><code class="db ph3">{{description}}</code></pre>
      <div class="mv3 bb b--black-10"></div>
    </div>
    <div class="relative bg-white b--white-box ba lh-title" style="box-shadow: 0 1px 4px rgba(0,0,0,0.125)" v-if="isEditable">
      <code-editor
        ref="code"
        lang="javascript"
        :val="edit_code"
        :options="{ minHeight: 300 }"
        @change="updateCode"
        v-if="is_inited"
      />
    </div>
    <div class="relative pv2 ph3 bg-near-white br2 overflow-x-auto" style="padding-top: 24px" v-else>
      <pre class="f7 lh-title"><code class="db" style="white-space: pre-wrap" spellcheck="false">{{edit_code}}</code></pre>
    </div>
    <div class="mt3 pa3 bg-white ba b--black-10" v-if="is_loading">
      <div class="v-mid fw6 dark-gray"><span class="fa fa-spin fa-spinner"></span> Running...</div>
    </div>
    <div class="mt3 pa3 bg-white ba b--black-10" v-else-if="has_text_result || has_img_src || has_pdf_src">
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
    <textarea :id="textarea_id" v-show="false">{{copy_code}}</textarea>
  </div>
</template>

<script>
  import Flexio from 'flexio-sdk-js'
  import CodeEditor from './CodeEditor.vue'

  export default {
    props: {
      'title': {
        type: String,
        default: ''
      },
      'description': {
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
          if (window.location.hostname == 'www.flex.io')
            return {}

          return { baseUrl: 'https://test.flex.io/api/v1' }
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
      code(val, old_val) {
        this.updateCode(val)
        this.updateEditor(val)
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

        this.updateEditor(this.edit_code)
      })
    },
    methods: {
      toggleOutput() {
        this.show_output = !this.show_output
      },
      updateCode(code) {
        this.edit_code = code
        this.syntax_msg = ''
      },
      updateEditor(code) {
        // set the code in the code editor
        var code_editor = this.$refs['code']
        if (!_.isNil(code_editor))
          code_editor.setValue(code)
      },
      getTaskJSON() {
        try {
          var fn = (Flexio, callback) => {
            return eval(this.save_code)
          }

          // get access to pipe object
          var pipe = fn.call(this, Flexio)

          // get compiled pipe JSON
          var pipe_json = pipe.getJSON()

          // pull out task from pipe JSON
          var task = _.get(pipe_json, 'task', { op: 'sequence', params: {} })

          return task
        } catch(e) {
          return { op: 'sequence', params: {} }
        }
      },
      copy() {
        if (window.analytics)
          window.analytics.track('Copied Code Example', { label: window.location.pathname })
      },
      run() {
        if (typeof this.run_fn == 'function')
        {
          Flexio.setup(this.apiKey, this.sdkOptions)

          if (window.analytics)
            window.analytics.track('Ran Code Example', { label: window.location.pathname })

          this.is_loading = true

          this.run_fn.call(this, Flexio, (err, response) => {
            this.text_result = ''
            this.img_src = ''
            this.pdf_src = ''

            var content_type = ''

            // check the content type of our response; if the content type
            // exists, it's a Pipe API response, otherwise, it's simply a
            // JSON response (patched through from the Flex.io backend)
            if (typeof response == 'object' && typeof response.contentType == 'string')
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
