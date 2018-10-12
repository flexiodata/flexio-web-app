<template>
  <div class="flex flex-row items-stretch relative">
    <div
      class="flex flex-column trans-w"
      :style="code_expanded ? 'width: 40%' : 'width: 30px'"
    >
      <div class="flex flex-row items-center pa2 f7 silver ttu fw6 bb b--black-05 bg-nearer-white">
        <div class="flex-fill" v-show="code_expanded">YAML</div>
        <el-button
          class="ttu fw6"
          type="text"
          :title="code_expanded ? 'Hide YAML' : 'Show YAML'"
          :icon="code_expanded ? 'el-icon-caret-left' : 'el-icon-caret-right'"
          style="margin: -4px; padding: 5px 4px 4px; border: 0; font-size: 13px"
          @click="code_expanded = !code_expanded"
        />
      </div>
      <CodeEditor
        class="flex-fill"
        :class="!code_expanded ? 'o-40 no-pointer-events no-select' : ''"
        :show-json-view-toggle="false"
        :lang.sync="lang"
        v-show="code_expanded"
        v-model="edit_code"
      />
    </div>
    <div class="flex-fill flex flex-column bl b--black-05">
      <div class="pa2 f7 silver ttu fw6 bb b--black-05 bg-nearer-white">Builder</div>
      <el-alert
        type="error"
        show-icon
        :title="error_msg"
        :closable="false"
        v-show="error_msg.length > 0"
      />
      <BuilderDocument
        class="h-100"
        :class="error_msg.length > 0 ? 'o-40 no-pointer-events no-select' : ''"
        :definition="edit_json"
      />
    </div>
  </div>
</template>

<script>
  import yaml from 'js-yaml'
  import CodeEditor from './CodeEditor.vue'
  import BuilderDocument from './BuilderDocument.vue'

  import test_def from '../data/builder/test-def.yml'
  // easy way to get rid of a bunch of elements for quick testing
  //test_def.prompts = _.filter(test_def.prompts, { element: 'form' })

  const edit_code = yaml.safeDump(test_def)

  export default {
    components: {
      CodeEditor,
      BuilderDocument
    },
    watch: {
      edit_code: {
        handler: 'updateJSON',
        immediate: true
      }
    },
    data() {
      return {
        lang: 'yaml',
        edit_code,
        edit_json: {},
        error_msg: '',
        code_expanded: true
      }
    },
    methods: {
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
          this.edit_json = res
        }
        catch(e)
        {
          this.error_msg = 'Parse error: ' + e.message
        }
      }
    }
  }
</script>
