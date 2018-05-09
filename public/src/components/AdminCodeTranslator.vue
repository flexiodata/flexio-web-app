<template>
  <div class="flex flex-row items-stretch relative">
    <div class="flex-fill flex flex-column bl b--black-20">
      <div class="pa2 f7 silver ttu fw6 bb b--black-05 bg-nearer-white">SDK</div>
      <code-editor
        ref="sdk"
        class="flex-fill"
        lang="javascript"
        :val="sdk_str"
        @change="updateJSON"
      />
    </div>
    <div class="flex-fill flex flex-column bl b--black-20">
      <div class="pa2 f7 silver ttu fw6 bb b--black-05 bg-nearer-white">SDK to JSON</div>
      <code-editor
        ref="json"
        class="flex-fill"
        lang="application/json"
        :val="json_str"
        @change="updateReverseSdk"
      />
    </div>
    <div class="flex-fill flex flex-column bl b--black-20">
      <div class="pa2 f7 silver ttu fw6 bb b--black-05 bg-nearer-white">JSON to SDK</div>
      <code-editor
        ref="reverse-sdk"
        class="flex-fill"
        lang="javascript"
        :val="reverse_sdk_str"
      />
    </div>
  </div>
</template>

<script>
  import Flexio from 'flexio-sdk-js'
  import CodeEditor from './CodeEditor.vue'

  export default {
    components: {
      CodeEditor
    },
    data() {
      return {
        sdk_str: 'Flexio.pipe()\n  .request("https://httpbin.org/ip")',
        json_str: '',
        reverse_sdk_str: ''
      }
    },
    mounted() {
      this.updateJSON(this.sdk_str)
    },
    methods: {
      updateJSON(code) {
        try {
          // create a function to create the JS SDK code to call
          var fn = (Flexio, callback) => { return eval(code) }

          // get access to pipe object
          var pipe = fn.call(this, Flexio)

          // check pipe syntax
          if (!Flexio.util.isPipeObject(pipe))
            throw({ message: 'Invalid pipe syntax. Pipes must start with `Flexio.pipe()`.' })

          // get the pipe task JSON
          var task = _.get(pipe.getJSON(), 'task', { op: 'sequence', items: [] })

          // stringify JSON; indent 2 spaces
          var task_str = JSON.stringify(task, null, 2)

          this.json_str = task_str
          this.reverse_sdk_str = Flexio.pipe(task).toCode()
          this.$refs['json'].setValue(this.json_str)
          this.$refs['reverse-sdk'].setValue(this.reverse_sdk_str)
        }
        catch(e)
        {
          // TODO: add error handling
        }
      },
      updateReverseSdk(json) {
        try {
          this.reverse_sdk_str = Flexio.pipe(JSON.parse(json)).toCode()
          this.$refs['reverse-sdk'].setValue(this.reverse_sdk_str)
        }
        catch(e)
        {
          // TODO
        }
      }
    }
  }
</script>
