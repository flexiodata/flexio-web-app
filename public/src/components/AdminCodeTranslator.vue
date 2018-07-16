<template>
  <div class="flex flex-row items-stretch relative">
    <div class="flex-fill flex flex-column bl b--black-20">
      <div class="pa2 f7 silver ttu fw6 bb b--black-05 bg-nearer-white">SDK</div>
      <CodeEditor
        class="flex-fill"
        lang="javascript"
        v-model="sdk_str"
      />
    </div>
    <div class="flex-fill flex flex-column bl b--black-20">
      <div class="pa2 f7 silver ttu fw6 bb b--black-05 bg-nearer-white">JSON</div>
      <CodeEditor
        class="flex-fill"
        lang="application/json"
        v-model="json_str"
      />
    </div>
    <div class="flex-fill flex flex-column bl b--black-20">
      <div class="pa2 f7 silver ttu fw6 bb b--black-05 bg-nearer-white">YAML</div>
      <CodeEditor
        class="flex-fill"
        lang="yaml"
        :show-json-view-toggle="false"
        v-model="yaml_str"
      />
    </div>
    <div class="flex-fill flex flex-column bl b--black-20" v-show="false">
      <div class="pa2 f7 silver ttu fw6 bb b--black-05 bg-nearer-white">JSON to SDK</div>
      <CodeEditor
        class="flex-fill"
        lang="javascript"
        v-model="reverse_sdk_str"
      />
    </div>
  </div>
</template>

<script>
  import yaml from 'js-yaml'
  import Flexio from 'flexio-sdk-js'
  import utilSdkJs from '../utils/sdk-js'
  import CodeEditor from './CodeEditor.vue'

  export default {
    components: {
      CodeEditor
    },
    watch: {
      sdk_str: {
        handler: 'updateJSON',
        immediate: true
      },
      json_str: {
        handler: 'updateOutput',
        immediate: true
      }
    },
    data() {
      return {
        sdk_str: 'Flexio.pipe()\n  .request("https://httpbin.org/ip")',
        json_str: '',
        yaml_str: '',
        reverse_sdk_str: ''
      }
    },
    methods: {
      updateJSON(code) {
        try {
          // get the pipe task JSON
          var task = utilSdkJs.getTaskJSON(code)

          // stringify JSON; indent 2 spaces
          var task_str = JSON.stringify(task, null, 2)

          this.json_str = task_str

          // do this until we fix the object ref issue in the Flex.io JS SDK
          var task_obj = _.cloneDeep(task)
          this.reverse_sdk_str = Flexio.pipe(task_obj).toCode()
        }
        catch(e)
        {
          // TODO: add error handling
        }
      },
      updateOutput(json) {
        this.updateYAML(json)
        this.updateReverseSDK(json)
      },
      updateYAML(json) {
        this.yaml_str = yaml.safeDump(JSON.parse(json))
      },
      updateReverseSDK(json) {
        try {
          this.reverse_sdk_str = Flexio.pipe(JSON.parse(json)).toCode()
        }
        catch(e)
        {
          // TODO
        }
      }
    }
  }
</script>
