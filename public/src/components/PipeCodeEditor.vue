<template>
  <div>
    <!-- code editor -->
    <div class="relative bg-white ba b--black-10 lh-title">
      <CodeEditor
        lang="javascript"
        v-model="edit_code"
      />
    </div>

    <!-- syntax error message and cancel/save buttons -->
    <transition name="slide-fade">
      <div class="flex flex-row items-start mt2" v-if="is_changed">
        <div class="flex-fill mr4">
          <transition name="slide-fade">
            <div class="f7 dark-red pre overflow-y-hidden overflow-x-auto code" v-if="syntax_msg.length > 0">{{syntax_msg}}</div>
          </transition>
        </div>
        <el-button size="mini" type="plain" class="ttu b" @click="cancelEdit">Cancel</el-button>
        <el-button size="mini" type="primary" class="ttu b" @click="saveChanges">Save Changes</el-button>
      </div>
    </transition>

    <!-- preview -->
    <PipeContent
      class="mt3 relative"
      :stream-eid="last_stream_eid"
      v-if="last_stream_eid.length > 0 && !is_process_failed"
    />
  </div>
</template>

<script>
  import { PROCESS_STATUS_FAILED } from '../constants/process'
  import Flexio from 'flexio-sdk-js'
  import CodeEditor from './CodeEditor.vue'
  import PipeContent from './PipeContent.vue'

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
      'active-process': {
        type: Object
      },
      'is-process-running': {
        type: Boolean
      }
    },
    components: {
      CodeEditor,
      PipeContent
    },
    inject: ['pipeEid'],
    watch: {
      edit_code() {
        this.syntax_msg = ''
      }
    },
    data() {
      return {
        edit_code: Flexio.pipe({ op: 'sequence', params: { items: this.tasks } }).toCode(),
        syntax_msg: ''
      }
    },
    computed: {
      orig_code() {
        return Flexio.pipe({ op: 'sequence', params: { items: this.tasks } }).toCode()
      },
      is_changed() {
        return this.orig_code != this.edit_code
      },
      // find the active subprocess by finding this task eid in the subprocess array
      last_subprocess() {
        return _
          .chain(this.activeProcess)
          .get('log')
          .last()
          .value()
      },
      last_stream_eid() {
        return _.get(this.last_subprocess, 'output.stdout.eid', '')
      },
      active_process_status() {
        return _.get(this.activeProcess, 'process_status', '')
      },
      is_process_failed() {
        return this.active_process_status == PROCESS_STATUS_FAILED
      },
      save_code() {
        // TODO: we need to give this code editor quite a bit of love...
        // this is a bit of a kludge that allows pipe code to be pasted with a .run()
        var code = this.edit_code
        var idx = code.indexOf('.run')
        if (idx >= 0)
          code = code.substring(0, idx)
        return code.trim()
      }
    },
    methods: {
      getEditCode() {
        return this.edit_code
      },
      cancelEdit() {
        // reset the edit json
        this.edit_code = this.orig_code

        this.$nextTick(() => {
          // reset the code in the code editor
          var code_editor = this.$refs['code']
          if (!_.isNil(code_editor))
            code_editor.reset()

          // reset the edit json
          this.updateCode(this.orig_code)
        })
      },
      saveChanges() {
        try {
          // create a function to create the JS SDK code to call
          var fn = (Flexio, callback) => { return eval(this.save_code) }

          // get access to pipe object
          var pipe = fn.call(this, Flexio)

          if (!Flexio.util.isPipeObject(pipe))
            throw({ message: 'Invalid pipe syntax. Pipes must start with `Flexio.pipe()`.' })

          // get the pipe task JSON
          var task = _.get(pipe.getJSON(), 'task', { op: 'sequence', params: {} })

          var eid = this.pipeEid
          var attrs = { task }

          this.syntax_msg = ''

          return this.$store.dispatch('updatePipe', { eid, attrs }).then(response => {
            if (response.ok)
            {
              this.edit_code = this.orig_code
            }
             else
            {
              // TODO: add error handling
            }
          })
        }
        catch(e)
        {
          this.syntax_msg = e.message
          return null
        }
      }
    }
  }
</script>
