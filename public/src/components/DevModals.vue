<template>
  <div class="pa3">
    <div class="flex flex-column w-25">
      <div class="pa2">
        <btn btn-md btn-block btn-primary class="ttu b v-mid" @click="openConfirmModal">Open Confirm Modal</btn>
      </div>
      <div class="pa2">
        <btn btn-md btn-block btn-primary class="ttu b v-mid" @click="openProjectPropsModal">Open Project Properties Modal</btn>
      </div>
      <div class="pa2">
        <btn btn-md btn-block btn-primary class="ttu b v-mid" @click="openConnectionPropsModal">Open Connection Properties Modal</btn>
      </div>
      <div class="pa2">
        <btn btn-md btn-block btn-primary class="ttu b v-mid" @click="openNewPropsModal">Open New Pipe Modal</btn>
      </div>
      <div class="pa2">
        <btn btn-md btn-block btn-primary class="ttu b v-mid" @click="openEditPropsModal">Open Edit Pipe Modal</btn>
      </div>
      <div class="pa2">
        <btn btn-md btn-block btn-primary class="ttu b v-mid" @click="openPipeScheduleModal">Open Pipe Schedule Modal</btn>
      </div>
      <div class="pa2">
        <btn btn-md btn-block btn-primary class="ttu b v-mid" @click="openPipeShareModal">Open Pipe Share Modal</btn>
      </div>
      <div class="pa2">
        <btn btn-md btn-block btn-primary class="ttu b v-mid" @click="openEmailSupportModal">Open Email Support Modal</btn>
      </div>
      <div class="pa2">
        <btn btn-md btn-block btn-primary class="ttu b v-mid" @click="openMemberAddModal">Open Member Add Modal</btn>
      </div>
    </div>

    <confirm-modal
      ref="modal-confirm"
      @hide="show_confirm_modal = false"
      v-if="show_confirm_modal"
    ></confirm-modal>

    <project-props-modal
      ref="modal-project-props"
      @hide="show_project_props_modal = false"
      v-if="show_project_props_modal"
    ></project-props-modal>

    <connection-props-modal
      ref="modal-connection-props"
      @hide="show_connection_props_modal = false"
      v-if="show_connection_props_modal"
    ></connection-props-modal>

    <pipe-props-modal
      ref="modal-pipe-props"
      @hide="show_pipe_props_modal = false"
      v-if="show_pipe_props_modal"
    ></pipe-props-modal>

    <pipe-schedule-modal
      ref="modal-pipe-schedule"
      @hide="show_pipe_schedule_modal = false"
      v-if="show_pipe_schedule_modal"
    ></pipe-schedule-modal>

    <pipe-share-modal
      ref="modal-pipe-share"
      @hide="show_pipe_share_modal = false"
      v-if="show_pipe_share_modal"
    ></pipe-share-modal>

    <email-support-modal
      ref="modal-email-support"
      @hide="show_email_support_modal = false"
      v-if="show_email_support_modal"
    ></email-support-modal>

    <member-add-modal
      ref="modal-member-add"
      @hide="show_member_add_modal = false"
      v-if="show_member_add_modal"
    ></member-add-modal>
  </div>
</template>

<script>
  import Btn from './Btn.vue'
  import ConfirmModal from './ConfirmModal.vue'
  import ProjectPropsModal from './ProjectPropsModal.vue'
  import ConnectionPropsModal from './ConnectionPropsModal.vue'
  import PipePropsModal from './PipePropsModal.vue'
  import PipeScheduleModal from './PipeScheduleModal.vue'
  import PipeShareModal from './PipeShareModal.vue'
  import EmailSupportModal from './EmailSupportModal.vue'
  import MemberAddModal from './MemberAddModal.vue'

  var exampleProject = () => {
    return {
      name: 'Test Project',
      eid: 'test-project'
    }
  }

  var exampleConnection = () => {
    return {
      name: 'Test Connection',
      eid: 'test-connection'
    }
  }

  var examplePipe = () => {
    return {
      name: 'Test Pipe',
      eid: 'test-pipe'
    }
  }

  export default {
    components: {
      Btn,
      ConfirmModal,
      ProjectPropsModal,
      ConnectionPropsModal,
      PipePropsModal,
      PipeScheduleModal,
      PipeShareModal,
      EmailSupportModal,
      MemberAddModal
    },
    data() {
      return {
        show_confirm_modal: false,
        show_project_props_modal: false,
        show_connection_props_modal: false,
        show_pipe_props_modal: false,
        show_pipe_schedule_modal: false,
        show_pipe_share_modal: false,
        show_email_support_modal: false,
        show_member_add_modal: false
      }
    },
    methods: {
      openConfirmModal() {
        this.show_confirm_modal = true
        this.$nextTick(() => { this.$refs['modal-confirm'].open() })
      },

      openProjectPropsModal() {
        this.show_project_props_modal = true
        this.$nextTick(() => { this.$refs['modal-project-props'].open(exampleProject()) })
      },

      openConnectionPropsModal() {
        this.show_connection_props_modal = true
        this.$nextTick(() => { this.$refs['modal-connection-props'].open(exampleConnection()) })
      },

      openNewPropsModal() {
        this.show_pipe_props_modal = true
        this.$nextTick(() => { this.$refs['modal-pipe-props'].open() })
      },

      openEditPropsModal() {
        this.show_pipe_props_modal = true
        this.$nextTick(() => { this.$refs['modal-pipe-props'].open(examplePipe()) })
      },

      openPipeScheduleModal() {
        this.show_pipe_schedule_modal = true
        this.$nextTick(() => { this.$refs['modal-pipe-schedule'].open(examplePipe()) })
      },

      openPipeShareModal() {
        this.show_pipe_share_modal = true
        this.$nextTick(() => { this.$refs['modal-pipe-share'].open(examplePipe()) })
      },

      openEmailSupportModal() {
        this.show_email_support_modal = true
        this.$nextTick(() => { this.$refs['modal-email-support'].open() })
      },

      openMemberAddModal() {
        this.show_member_add_modal = true
        this.$nextTick(() => { this.$refs['modal-member-add'].open() })
      }
    }
  }
</script>
