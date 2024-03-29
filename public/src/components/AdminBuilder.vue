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
      <AdminBuilderUi
        class="flex-fill pa5 overflow-y-scroll"
        :prompts="calc_prompts"
      />
    </div>
  </div>
</template>

<script>
  import yaml from 'js-yaml'
  import { mapGetters } from 'vuex'
  import CodeEditor from '@/components/CodeEditor'
  import AdminBuilderUi from '@/components/AdminBuilderUi'

  import test_def from '../data/builder/docusign-example.yml'
  // easy way to get rid of a bunch of elements for quick testing
  //test_def.prompts = _.filter(test_def.prompts, { element: 'form' })

  const edit_code = yaml.safeDump(test_def)

  export default {
    metaInfo: {
      title: '[Admin] Builder'
    },
    components: {
      CodeEditor,
      AdminBuilderUi
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
    computed: {
      calc_prompts() {
        return _.get(this.edit_json, 'prompts', [])
      }
    },
    mounted() {
      // we need this for the API call that creates the pending connection in BuilderItemFileChooser
      var team_name = this.getActiveUsername()
      this.$store.dispatch('teams/changeActiveTeam', { team_name })
    },
    methods: {
      ...mapGetters('users', {
        'getActiveUsername': 'getActiveUsername',
      }),
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
