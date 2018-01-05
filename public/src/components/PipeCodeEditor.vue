<template>
  <div class="overflow-y-auto">
    <div class="mv4 pv3 ph4 center" style="max-width: 1440px">

      <!-- code editor -->
      <div class="relative bg-white b--white-box ba">
        <code-editor
          ref="code"
          lang="javascript"
          :val="edit_code"
          :options="{ minHeight: 300 }"
          @change="updateCode"
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
          <btn btn-sm class="b ttu blue mr2" @click="cancelEdit">Cancel</btn>
          <btn btn-sm class="b ttu white bg-blue" @click="saveChanges">Save Changes</btn>
        </div>
      </transition>

    </div>
  </div>
</template>

<script>
  import Btn from './Btn.vue'
  import CodeEditor from './CodeEditor.vue'
  import Flexio from 'flexio-sdk-js'

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
      Btn,
      CodeEditor
    },
    inject: ['pipeEid'],
    data() {
      return {
        edit_code: Flexio.pipe.toCode(this.tasks),
        syntax_msg: ''
      }
    },
    computed: {
      orig_code() {
        return Flexio.pipe.toCode(this.tasks)
      },
      is_changed() {
        return this.orig_code != this.edit_code
      }
    },
    methods: {
      updateCode(code) {
        this.edit_code = code
        this.syntax_msg = ''
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
          var fn = (Flexio, callback) => {
            return eval(this.edit_code)
          }

          // get access to pipe object
          var pipe = fn.call(this, Flexio)

          // get compiled pipe JSON
          var pipe_json = pipe.getJSON()

          // pull out task from pipe JSON
          var task = _.get(pipe_json, 'task', [])

          var eid = this.pipeEid
          var attrs = { task }

          this.syntax_msg = ''

          this.$store.dispatch('updatePipe', { eid, attrs }).then(response => {
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
        }
      }
    }
  }
</script>
