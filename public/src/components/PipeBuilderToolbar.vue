<template>
  <div class="w-100">
    <div class="flex flex-row mh4">
      <div class="flex-fill"></div>
      <div class="flex-none flex flex-column flex-row-ns items-end items-center-ns">
        <btn
          btn-md
          btn-primary
          class="ttu b mr2"
          @click="openCopyPipeModal"
          v-if="is_superuser"
        >Get Template JSON</btn>
        <btn
          btn-md
          btn-primary
          class="ttu b"
          @click="cancelProcess"
          v-if="isPrompting || isProcessRunning"
        >Cancel</btn>
        <div
          class="hint--bottom-left"
          :aria-label="run_button_tooltip"
          v-else
        >
          <btn
            btn-md
            btn-primary
            class="ttu b"
            :disabled="!is_run_allowed"
            @click="runPipe"
          >Run</btn>
        </div>
      </div>

      <!-- copy pipe modal -->
      <copy-pipe-modal
        ref="modal-copy-pipe"
        @hide="show_copy_pipe_modal = false"
        v-if="show_copy_pipe_modal"
      ></copy-pipe-modal>

    </div>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex'
  import { PIPEHOME_VIEW_BUILDER } from '../constants/pipehome'
  import Btn from './Btn.vue'
  import CopyPipeModal from './CopyPipeModal.vue'

  export default {
    props: {
      'pipe-eid': {
        type: String,
        required: true
      },
      'pipe-view': {
        type: String
      },
      'is-prompting': {
        type: Boolean,
        default: false
      },
      'is-process-running': {
        type: Boolean
      }
    },
    components: {
      Btn,
      CopyPipeModal
    },
    data() {
      return {
        show_copy_pipe_modal: false,
        ss_errors: {}
      }
    },
    computed: {
      pipe() {
        return _.get(this.$store, 'state.objects.'+this.pipeEid, {})
      },
      tasks() {
        return _.get(this.pipe, 'task', [])
      },
      is_superuser() {
        // limit to @flex.io users for now
        var user_email = _.get(this.getActiveUser(), 'email', '')
        return _.includes(user_email, '@flex.io') && _.get(this.$route, 'query.su', false) !== false
      },
      is_run_allowed() {
        return this.tasks.length > 0
      },
      run_button_tooltip() {
        return ''
      }
    },
    methods: {
      ...mapGetters([
        'getActiveUser'
      ]),
      setPipeView(view) {
        this.$emit('set-pipe-view', view)
      },
      runPipe() {
        this.setPipeView(PIPEHOME_VIEW_BUILDER)
        this.$emit('run-pipe')
      },
      cancelProcess() {
        this.$emit('cancel-process')
      },
      openCopyPipeModal() {
        this.show_copy_pipe_modal = true
        this.$nextTick(() => { this.$refs['modal-copy-pipe'].open(this.pipe) })
      }
    }
  }
</script>
