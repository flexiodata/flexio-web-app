<template>
  <div class="overflow-y-auto">
    <div class="mv4 pv3 ph4 center" style="max-width: 1440px">
      <div class="relative bg-white b--white-box ba">
        <code-editor
          ref="code"
          lang="javascript"
          :val="edit_code"
          :options="{ minHeight: 300 }"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import CodeEditor from './CodeEditor.vue'
  import FlexioSdkJs from 'flexio-sdk-js'

  export default {
    props: {
      'pipe-options': {
        type: Object,
        default: () => { return {} }
      },
      'tasks': {
        type: Array,
        required: true
      },
      'is-process-running': {
        type: Boolean
      }
    },
    components: {
      CodeEditor
    },
    data() {
      return {
        edit_code: FlexioSdkJs.pipe.toCode(this.tasks)
      }
    }
  }
</script>
