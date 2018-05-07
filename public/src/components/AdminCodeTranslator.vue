<template>
  <div class="flex flex-row items-stretch relative">
    <div class="flex-fill flex flex-column bl b--black-20">
      <div class="pa2 f7 silver ttu fw6 bb b--black-05 bg-nearer-white">SDK</div>
      <CodeEditor2
        class="flex-fill"
        v-model="sdk_str"
        @change="updateJSON"
      />
    </div>
    <div class="flex-fill flex flex-column bl b--black-20">
      <div class="pa2 f7 silver ttu fw6 bb b--black-05 bg-nearer-white">SDK to JSON</div>
      <CodeEditor2
        class="flex-fill"
        :options="{ mode: 'application/json' }"
        v-model="json_str"
      />
    </div>
    <div class="flex-fill flex flex-column bl b--black-20">
      <div class="pa2 f7 silver ttu fw6 bb b--black-05 bg-nearer-white">JSON to SDK</div>
      <CodeEditor2
        class="flex-fill"
        v-model="reverse_sdk_str"
      />
    </div>
  </div>
</template>

<script>
  import Flexio from 'flexio-sdk-js'
  import CodeEditor2 from './CodeEditor2.vue'

  export default {
    components: {
      CodeEditor2
    },
    watch: {
      sdk_str: {
        handler: 'updateJSON',
        immediate: true
      },
      json_str: {
        handler: 'updateReverseSDK',
        immediate: true
      }
    },
    data() {
      return {
        sdk_str: 'Flexio.pipe()\n  .request("https://httpbin.org/ip")',
        json_str: '',
        reverse_sdk_str: ''
      }
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
          var task = _.get(pipe.getJSON(), 'task', { op: 'sequence', params: {} })

          // stringify JSON; indent 2 spaces
          var task_str = JSON.stringify(task, null, 2)

          this.json_str = task_str
          this.reverse_sdk_str = Flexio.pipe(task).toCode()
        }
        catch(e)
        {
          // TODO: add error handling
        }
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
