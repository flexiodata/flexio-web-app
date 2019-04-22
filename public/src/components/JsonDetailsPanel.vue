<template>
  <div>
    <div class="mb2">
      <el-radio-group size="small" v-model="pretty_state">
        <el-radio-button label="pretty">Pretty</el-radio-button>
        <el-radio-button label="raw">Raw</el-radio-button>
      </el-radio-group>
    </div>
    <div
      class="overflow-auto"
      v-if="pretty_state == 'pretty'"
    >
      <template v-for="(val, key) in json">
        <h4 class="f8 fw6 ttu moon-gray bb b--black-05 mb1 mt3 pb1">{{key}}</h4>
        <pre class="overflow-x-auto mb0 tl lh-title f7">{{val}}</pre>
      </template>
    </div>
    <CodeEditor
      class="bg-white ba b--black-10"
      style="font-size: 13px"
      lang="json"
      :show-json-view-toggle="false"
      :options="{
        minRows: 12,
        maxRows: 24,
        lineNumbers: false,
        readOnly: true
      }"
      v-model="json_str"
      v-else
    />
  </div>
</template>

<script>
  import CodeEditor from '@comp/CodeEditor'

  export default {
    props: {
      json: {
        type: Object,
        required: true
      }
    },
    components: {
      CodeEditor
    },
    data() {
      return {
        pretty_state: 'pretty'
      }
    },
    computed: {
      json_str() {
        return JSON.stringify(this.json, null, 2)
      }
    }
  }
</script>
