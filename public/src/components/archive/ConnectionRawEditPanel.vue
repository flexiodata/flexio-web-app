<template>
  <div class="flex flex-column">
    <div class="flex-none flex flex-row items-center w-100 pa2 bb b--light-gray bg-nearer-white">
      <div><span class="f4 mid-gray">{{connection.name}}</span> <span class="moon-gray">({{connection.eid}})</span></div>
      <div class="flex-fill">&nbsp;</div>
      <btn btn-md class="b ttu blue mr2" @click="cancel">Cancel</btn>
      <btn btn-md class="b ttu blue" @click="submit">Save</btn>
    </div>
    <code-editor
      ref="code"
      class="flex-fill overflow-y-auto"
      lang="application/json"
      :val="json"
      @change="update"
    />
  </div>
</template>
<script>
  import Btn from './Btn.vue'
  import CodeEditor from './CodeEditor.vue'

  export default {
    props: {
      'connection': {
        type: Object,
        required: true
      }
    },
    components: {
      Btn,
      CodeEditor
    },
    watch: {
      connection(val, old_val) {
        this.json = JSON.stringify(_.get(val, 'connection_info', {}), null, 2)
        this.$nextTick(() => { this.$refs.code.reset() })
      }
    },
    data() {
      return {
        json: JSON.stringify(_.get(this.connection, 'connection_info', {}), null, 2)
      }
    },
    computed: {
      info() {
        var obj = {}
        try { obj = JSON.parse(this.json) } catch (e) { }
        return obj
      }
    },
    methods: {
      update(val) {
        this.json = val
      },
      cancel() {
        this.json = JSON.stringify(_.get(this.connection, 'connection_info', {}), null, 2)
        this.$nextTick(() => { this.$refs.code.reset() })
        this.$emit('cancel', this.connection)
      },
      submit() {
        this.$emit('submit', this.connection, this.info)
      }
    }
  }
</script>
