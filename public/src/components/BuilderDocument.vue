<template>
  <div class="bg-nearer-white pa4 overflow-y-auto relative" :id="doc_id">
    <div
      class="h-100 flex flex-row items-center justify-center"
      v-if="is_fetching"
    >
      <Spinner size="large" message="Loading..." />
    </div>
    <div
      class="center"
      style="max-width: 1440px; margin-bottom: 6rem"
      v-else-if="is_fetched"
    >
      <h1 class="db mv0 pb4 fw6 mid-gray tc">{{title}}</h1>
      <div class="flex flex-row">
        <BuilderList
          class="flex-fill"
          :container-id="doc_id"
          :show-insert-buttons="false"
        />
        <div
          class="dn db-l ml4 pa3 bg-white br2 css-dashboard-box sticky"
          style="max-height: 30rem; min-width: 20rem; max-width: 33%"
        >
          <div class="h-100 flex flex-column">
            <div class="flex flex-row items-center pb2 mb2 bb b--black-10">
              <div class="flex-fill fw6 gray">Output</div>
            </div>
            <CodeEditor
              class="flex-fill overflow-auto"
              lang="javascript"
              :options="{ lineNumbers: false, readOnly: true }"
              :update-on-val-change="true"
              :val="code"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'
  import stickybits from 'stickybits'
  import { mapState } from 'vuex'
  import Spinner from 'vue-simple-spinner'
  import BuilderList from './BuilderList.vue'
  import CodeEditor from './CodeEditor.vue'

  const test_def = {
    "title": "Test Prompts",
    "description": "",
    "keywords": [],
    "connections": [],
    "content": "# Test prompts\n\nExample template for rendering all components",
    "prompts": [
      {
        "element": "form",
        "title": "Standard form",
        "description": "This is the form description. Text is written in Markdown so it can be quite descriptive.",
        "label_position": "left",
        "label_width": "9rem",
        "form_items": [
          {
            "element": "input",
            "type": "text",
            "variable": "input_text",
            "label": "Text Input",
            "placeholder": "Text Input",
            "value": ""
          },
          {
            "element": "input",
            "type": "textarea",
            "variable": "input_textarea",
            "label": "Textarea",
            "placeholder": "Textarea",
            "value": ""
          },
          {
            "element": "input",
            "type": "date",
            "variable": "input_date",
            "label": "Date Input",
            "placeholder": "Date Input",
            "value": ""
          },
          {
            "element": "input",
            "type": "daterange",
            "variable": "input_daterange",
            "label": "Date Range Input",
            "placeholder": "Date Range Input",
            "value": ""
          },
          {
            "element": "select",
            "variable": "input_select",
            "label": "Select",
            "placeholder": "Select",
            "value": "IL",
            "options": [
              { "label": "California", "value": "CA" },
              { "label": "Illinois",   "value": "IL" },
              { "label": "Wyoming",    "value": "WY" }
            ]
          },
          {
            "element": "radio-group",
            "variable": "radio_group",
            "label": "Radio Group",
            "placeholder": "Radio Group",
            "value": [],
            "options": [
              { "label": "California", "value": "CA" },
              { "label": "Illinois",   "value": "IL" },
              { "label": "Wyoming",    "value": "WY" }
            ]
          },
          {
            "element": "checkbox-group",
            "variable": "checkbox_group",
            "label": "Checkbox Group",
            "placeholder": "Checkbox Group",
            "value": [],
            "options": [
              { "label": "California", "value": "CA" },
              { "label": "Illinois",   "value": "IL" },
              { "label": "Wyoming",    "value": "WY" }
            ]
          },
          {
            "element": "checkbox",
            "variable": "checkbox",
            "label": "Checkbox",
            "placeholder": "Checkbox",
            "value": false
          },
          {
            "element": "switch",
            "variable": "switch",
            "label": "Switch",
            "placeholder": "Switch",
            "value": false
          }
        ]
      },
      {
        "title": "Connection chooser with single connection type",
        "element": "connection-chooser",
        "variable": "connection_dropbox",
        "connection_type": "dropbox"
      },
      {
        "title": "Connection chooser with all connection",
        "element": "connection-chooser",
        "variable": "connection_any",
        "connection_type": ""
      },
      {
        "title": "Default file chooser",
        "description": "This is the default file chooser. Multiple files and folder can be selected at the same time.",
        "element": "file-chooser",
        "variable": "file_chooser_default",
        "connection": "connection_dropbox"
      },
      {
        "title": "File chooser with CSVs only",
        "description": "This file chooser only allows CSV files to be selected.",
        "element": "file-chooser",
        "variable": "file_chooser_default",
        "connection": "connection_dropbox",
        "filetype_filter": ['csv']
      },
      {
        "title": "File chooser with a connection alias",
        "description": "This file chooser uses a connection alias `my-foo-alias`.",
        "element": "file-chooser",
        "variable": "file_chooser_alias",
        "connection": "connection_dropbox",
        "connection_alias": "my-foo-alias"
      },
      {
        "title": "Single file selection file chooser",
        "description": "This file chooser only allows a single file to be selected.",
        "element": "file-chooser",
        "variable": "file_chooser_single_file",
        "connection": "connection_dropbox",
        "allow_multiple": false,
        "allow_folders": false
      },
      {
        "description": "Folder only file chooser",
        "element": "file-chooser",
        "variable": "folder_chooser_folders_only",
        "connection": "connection_dropbox",
        "folders_only": true
      },
      {
        "description": "Single folder selection file chooser",
        "element": "file-chooser",
        "variable": "folder_chooser_single_folder",
        "connection": "connection_dropbox",
        "folders_only": true,
        "allow_multiple": false
      }
    ],
    "pipe_language": "javascript",
    "pipe": ""
  }

  var pipe_arr = [ "Flexio.pipe()" ]

  var buildPipeCode = (arr) => {
    _.each(arr, p => {
      if (p.element == 'form') {
        buildPipeCode(p.form_items)
      } else if (p.variable) {
        var echo_str = "echo('" + p.variable + ": ${" + p.variable + "}')"
        pipe_arr.push(echo_str)
      }
    })
  }

  buildPipeCode(test_def.prompts)

  test_def.pipe = pipe_arr.join('\n  .')

  export default {
    components: {
      Spinner,
      BuilderList,
      CodeEditor
    },
    watch: {
      slug: {
        handler: 'loadTemplate',
        immediate: true
      },
      active_prompt: {
        handler: 'updateCode',
        immediate: true
      },
      is_fetched() {
        setTimeout(() => { stickybits('.sticky') }, 100)
      }
    },
    data() {
      return {
        doc_id: _.uniqueId('builder-doc-')
      }
    },
    computed: {
      ...mapState({
        is_fetching: state => state.builder.fetching,
        is_fetched: state => state.builder.fetched,
        active_prompt: state => state.builder.active_prompt,
        title: state => state.builder.def.title,
        code: state => state.builder.code
      }),
      slug() {
        return _.get(this.$route, 'params.template', undefined)
      }
    },
    mounted() {
      setTimeout(() => { stickybits('.sticky') }, 100)
    },
    methods: {
      loadTemplate() {
        this.$store.commit('builder/FETCHING_DEF', true)

        if (this.slug == 'test') {
          this.$store.commit('builder/INIT_DEF', test_def)
          this.$store.commit('builder/FETCHING_DEF', false)
        } else {
          axios.get('/def/templates/' + this.slug + '.json').then(response => {
            var def = response.data
            this.$store.commit('builder/INIT_DEF', def)
            this.$store.commit('builder/FETCHING_DEF', false)
            this.$store.track('Started Template', {
              title: def.title
            })
          }).catch(error => {
            this.$store.commit('builder/FETCHING_DEF', false)
          })
        }
      },
      updateCode() {
        this.$store.commit('builder/UPDATE_CODE')
      }
    }
  }
</script>
