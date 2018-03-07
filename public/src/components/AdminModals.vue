<template>
  <div class="pa4 overflow-y-auto">
    <div class="mw8 center">
      <div class="pa1 w-100 mb3 mt4 bb b--black-10 fw6">Common Modals</div>
      <el-button type="primary" class="ttu b" :disabled="true" @click="openAlertModal">Alert Modal</el-button>
      <el-button type="primary" class="ttu b" :disabled="true" @click="openConfirmModal">Confirm Modal</el-button>
      <el-button type="primary" class="ttu b" :disabled="true" @click="openEmailSupportModal">Email Support Modal</el-button>
      <div class="pa1 w-100 mb3 mt4 bb b--black-10 fw6">Project Modals</div>
      <el-button type="primary" class="ttu b" :disabled="true" @click="openNewProjectPropsModal">New Project Modal</el-button>
      <el-button type="primary" class="ttu b" :disabled="true" @click="openEditProjectPropsModal">Edit Project Modal</el-button>
      <el-button type="primary" class="ttu b" :disabled="true" @click="openDeleteProjectPropsModal">Delete Project Modal</el-button>
      <el-button type="primary" class="ttu b" :disabled="true" @click="openMemberAddModal">Member Add Modal</el-button>
      <div class="pa1 w-100 mb3 mt4 bb b--black-10 fw6">Connection Modals</div>
      <el-button type="primary" class="ttu b" :disabled="true" @click="openNewConnectionModal">New Connection Modal</el-button>
      <el-button type="primary" class="ttu b" :disabled="true" @click="openEditConnectionModal">Edit Connection Modal</el-button>
      <div class="pa1 w-100 mb3 mt4 bb b--black-10 fw6">Pipe Modals</div>
      <el-button type="primary" class="ttu b" :disabled="true" @click="openNewPipeDialog">New Pipe Modal</el-button>
      <el-button type="primary" class="ttu b" :disabled="true" @click="openEditPipeDialog">Edit Pipe Modal</el-button>
      <el-button type="primary" class="ttu b" :disabled="true" @click="openPipeScheduleModal">Pipe Schedule Modal</el-button>
      <el-button type="primary" class="ttu b" :disabled="true" @click="openPipeShareModal">Pipe Share Modal</el-button>
      <div class="pa1 w-100 mb3 mt4 bb b--black-10 fw6">Other Modals</div>
      <el-button type="primary" class="ttu b" :disabled="true" @click="openTallModal">Tall Modal</el-button>
      <el-button type="primary" class="ttu b" @click="openFlexioModal">Flexio Modal</el-button>
      <el-button type="primary" class="ttu b" @click="openFlexioSignUpModal">Flexio Sign Up Modal</el-button>
    </div>

    <flexio-modal
      @cancel="show_flexio_modal = false"
      v-if="show_flexio_modal"
    >
      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem iusto veniam, accusantium voluptates eum, aliquid sequi molestiae corporis maxime incidunt dolores reiciendis et laborum quidem exercitationem quos perferendis error necessitatibus.</p>
      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem iusto veniam, accusantium voluptates eum, aliquid sequi molestiae corporis maxime incidunt dolores reiciendis et laborum quidem exercitationem quos perferendis error necessitatibus.</p>
      <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolorem iusto veniam, accusantium voluptates eum, aliquid sequi molestiae corporis maxime incidunt dolores reiciendis et laborum quidem exercitationem quos perferendis error necessitatibus.</p>
    </flexio-modal>

    <sign-up-modal
      ref="modal-sign-up"
      container-cls="relative"
      container-style="width: 32rem"
      @cancel="show_flexio_signup_modal = false"
      v-if="show_flexio_signup_modal"
    />

  </div>
</template>

<script>
  import { CONNECTION_TYPE_MYSQL } from '../constants/connection-type'
  import FlexioModal from './FlexioModal.vue'
  import SignUpModal from './SignUpModal.vue'

  export default {
    components: {
      FlexioModal,
      SignUpModal
    },
    data() {
      return {
        show_flexio_modal: false,
        show_flexio_signup_modal: false
      }
    },
    methods: {
      openFlexioModal() {
        this.show_flexio_modal = true
      },

      openFlexioSignUpModal() {
        this.show_flexio_signup_modal = true
      }
    }
  }
</script>
