<template>
  <div class="flex flex-row items-stretch relative">
    <div class="w-third-ns flex flex-column bl b--black-20" style="min-width: 200px">
      <div class="pa2 f7 silver ttu fw6 bb b--black-05 bg-nearer-white">YAML</div>
      <CodeEditor
        class="flex-fill"
        :show-json-view-toggle="false"
        :lang.sync="lang"
        v-model="edit_code"
      />
    </div>
    <div class="flex-fill flex flex-column bl b--black-20">
      <div class="pa2 f7 silver ttu fw6 bb b--black-05 bg-nearer-white">Builder</div>
      <div class="flex-fill bg-nearer-white pa4 overflow-y-auto" :id="doc_id">
        <BuilderList
        class="center"
          style="max-width: 880px"
          builder-mode="wizard"
          :items="items"
          :container-id="doc_id"
          :active-item-idx.sync="active_prompt_idx"
          :show-numbers="true"
          :show-icons="false"
          :show-insert-buttons="false"
          :show-edit-buttons="false"
          :show-delete-buttons="false"
          @item-prev="active_prompt_idx--"
          @item-next="active_prompt_idx++"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import yaml from 'js-yaml'
  import CodeEditor from './CodeEditor.vue'
  import BuilderList from './BuilderList.vue'

  export default {
    components: {
      CodeEditor,
      BuilderList
    },
    data() {
      return {
        lang: 'yaml',
        edit_code: '',
        active_prompt_idx: 0,
        doc_id: _.uniqueId('admin-builder-')
      }
    },
    computed: {
      edit_json() {
        var res = ''
        try {
          if (this.lang == 'yaml') {
            // YAML view
            res = yaml.safeLoad(this.edit_code)
          } else {
            // JSON view
            res = JSON.parse(this.edit_code)
          }

          return res
        }
        catch(e)
        {
          return { error: true, message: 'Parse error: ' + e.message }
        }
      },
      items() {
        return _.get(this.edit_json, 'prompts', [])
      }
    }
  }
</script>
