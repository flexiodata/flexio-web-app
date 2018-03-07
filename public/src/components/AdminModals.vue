<template>
  <div class="pa4 overflow-y-auto">
    <div class="mw8 center">
      <div class="pa1 w-100 mb3 mt4 bb b--black-10 fw6">Common Modals</div>
      <el-button type="primary" class="ttu b" @click="openAlertModal">Alert Modal</el-button>
      <el-button type="primary" class="ttu b" @click="openConfirmModal">Confirm Modal</el-button>
      <el-button type="primary" class="ttu b" @click="openEmailSupportModal">Email Support Modal</el-button>
      <div class="pa1 w-100 mb3 mt4 bb b--black-10 fw6">Project Modals</div>
      <el-button type="primary" class="ttu b" @click="openNewProjectPropsModal">New Project Modal</el-button>
      <el-button type="primary" class="ttu b" @click="openEditProjectPropsModal">Edit Project Modal</el-button>
      <el-button type="primary" class="ttu b" @click="openDeleteProjectPropsModal">Delete Project Modal</el-button>
      <el-button type="primary" class="ttu b" @click="openMemberAddModal">Member Add Modal</el-button>
      <div class="pa1 w-100 mb3 mt4 bb b--black-10 fw6">Connection Modals</div>
      <el-button type="primary" class="ttu b" @click="openNewConnectionModal">New Connection Modal</el-button>
      <el-button type="primary" class="ttu b" @click="openEditConnectionModal">Edit Connection Modal</el-button>
      <div class="pa1 w-100 mb3 mt4 bb b--black-10 fw6">Pipe Modals</div>
      <el-button type="primary" class="ttu b" @click="openNewPipeDialog">New Pipe Modal</el-button>
      <el-button type="primary" class="ttu b" @click="openEditPipeDialog">Edit Pipe Modal</el-button>
      <el-button type="primary" class="ttu b" @click="openPipeScheduleModal">Pipe Schedule Modal</el-button>
      <el-button type="primary" class="ttu b" @click="openPipeShareModal">Pipe Share Modal</el-button>
      <div class="pa1 w-100 mb3 mt4 bb b--black-10 fw6">Other Modals</div>
      <el-button type="primary" class="ttu b" @click="openTallModal">Tall Modal</el-button>
      <el-button type="primary" class="ttu b" @click="openFlexioModal">Flexio Modal</el-button>
      <el-button type="primary" class="ttu b" @click="openFlexioSignUpModal">Flexio Sign Up Modal</el-button>
    </div>

    <alert-modal
      ref="modal-alert"
      title="Error"
      @hide="show_alert_modal = false"
      v-if="show_alert_modal"
    >
      <div class="lh-copy">There was an error with something you entered. Please fix the error to continue.</div>
    </alert-modal>

    <confirm-modal
      ref="modal-confirm"
      title="Confirm?"
      @hide="show_confirm_modal = false"
      v-if="show_confirm_modal"
    >
      <div class="lh-copy">Doing this might be a bad idea. Are you sure you want to continue?</div>
    </confirm-modal>

    <ui-modal
      ref="modal-tall"
      @hide="show_tall_modal = false"
      v-if="show_tall_modal"
    >
      <div slot="header" class="f4">Tall modal</div>

      <p>Lorem ipsum dolor sit amet, ut etiam erat, vehicula condimentum taciti fringilla pulvinar, iaculis mollis eget interdum duis non posuere, commodi tristique porttitor nunc orci, sed mi. Mauris varius sed justo turpis, wisi placerat amet, sed turpis vitae hendrerit nec, ad elementum vestibulum commodo eget ultricies sodales. In nostrum hendrerit platea, mi cursus velit sit blandit vestibulum, amet convallis nulla amet massa at orci, aenean vestibulum vestibulum. Nulla libero orci in eget, dui nulla, ligula lobortis. Imperdiet aenean risus dictum dapibus dolor enim.</p>

      <p>Vel taciti cras, ullamcorper ut, ac ullamcorper a velit ligula semper. Consequat volutpat. Porta enim eget dolor. Nunc nullam enim, id metus, placerat curabitur eros arcu netus lacinia aliquam, consequatur quis neque morbi libero, erat rhoncus quisque felis commodo quis. Non eget mi aliquam, sem sit vel dolor, quisque vel non. Eget in, in felis aliquam scelerisque et, mauris iure morbi. Neque fermentum nonummy pharetra interdum aliquam leo.</p>

      <p>Euismod maecenas vitae hymenaeos arcu, sit nam wisi mauris nullam et. At vivamus vitae, vitae amet wisi corrupti a nulla gravida, praesent ac quam sapien vulputate non, integer dui quod mattis, quis pede velit. Suscipit dui. Turpis imperdiet ornare curabitur risus varius, quis eleifend eros. Aliquam augue arcu, volutpat interdum, mus odio leo integer nonummy, exercitationem eget elit lorem. In neque consectetuer nibh elementum, ac pellentesque, wisi vestibulum varius, lectus sed elit vitae, libero tristique platea purus eget arcu. Iaculis laoreet lectus nisl hendrerit, faucibus nibh, purus massa nulla bibendum aliquam nec mi, ante volutpat. Diam aliquam, eu nulla rutrum ornare eros. Nec molestie, purus aliquam lorem quis, non pellentesque diam, vestibulum integer facilisis, fermentum ac congue non dolor a ipsum. Iaculis facilis tortor sit nulla id porta, in lacus scelerisque enim a, quis pharetra sed ligula mus diam eu.</p>

      <p>A erat eu, fringilla vivamus ante fringilla. Velit mattis dictum, at ac mauris, varius consequat leo dignissim ipsum vitae maecenas. Rutrum orci curabitur, nec metus elit. Leo velit dolor nam ultrices sapien luctus, ut nibh, tincidunt quis lacus sed, elit eros ultrices egestas eget ut, tristique sem in suspendisse mauris elit. In urna mauris non ut neque cras, ultricies aliquam, laoreet ante proin parturient justo quam, vel risus augue. Mauris ante.</p>

      <p>Et donec justo pellentesque fermentum cras, adipisci tempus arcu varius orci, tincidunt leo sit. Eu nisl at imperdiet in non erat, dapibus est, lorem at parturient erat tempus, et porttitor lorem at lobortis. Vestibulum quam ut aliquam, at mattis est tortor in lectus lorem, urna magna blandit, tincidunt in ipsum, nascetur blandit fames. At sagittis sed ac nisl tristique vel, sociosqu vestibulum sollicitudin erat a ante, convallis velit elementum viverra vestibulum rutrum aliquam, at turpis eros et donec fusce mauris. Aenean elit suscipit condimentum a, quis sapien vivamus sed pulvinar quam sed. Blandit auctor pulvinar, blandit dignissim vitae neque morbi excepteur. Mi non massa. Volutpat lacinia ultricies est elit. Maecenas quam, dictum urna ac ut, convallis ex molestiae semper donec pellentesque odio, lacinia vulputate at. Accumsan tellus sapien laoreet laoreet consectetuer blandit, interdum amet dictumst integer lectus, nonummy mattis, lorem tempus elit at neque wisi, dapibus laoreet nostra ultricies gravida in elit. Felis vestibulum sed laoreet duis donec, nunc accusamus malesuada, quis viverra facilisis dolores elementum nec odio, dolore vitae.</p>

      <p>Lobortis luctus nisl ut ipsum, aliquam sed orci rhoncus et vestibulum habitant, enim elit sem mi. Ante cursus wisi turpis, purus lacinia orci cursus, diam ac pharetra. Scelerisque non fringilla non adipiscing, vel elit ante massa auctor quam, ante eros vitae justo varius curabitur donec. Tellus consequat nunc sollicitudin vestibulum, nunc tortor proin integer orci pede, risus vitae, dictumst nibh aenean donec malesuada a pellentesque, cum suspendisse semper suspendisse mauris aliquam sed. Adipiscing non eget elit enim arcu, sed ut massa non vitae, leo aenean maecenas at recusandae nulla fringilla, ad iaculis justo in suspendisse, quis tellus nulla. Adipiscing magna cursus, in morbi feugiat vitae eu, faucibus amet, ut pellentesque ultrices et aliquam mauris, vel pariatur dolor placerat consequat. Justo suscipit dis vitae lectus, class ante non mauris fringilla tristique.</p>

      <div slot="footer" class="flex flex-row items-end">
        <el-button type="plain" class="ttu b" @click="show_tall_modal = false">Cancel</el-button>
        <el-button type="primary" class="ttu b" @click="show_tall_modal = false">Ok</el-button>
      </div>
    </ui-modal>

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
  import AlertModal from './AlertModal.vue'
  import ConfirmModal from './ConfirmModal.vue'
  import FlexioModal from './FlexioModal.vue'
  import SignUpModal from './SignUpModal.vue'

  var exampleProject = () => {
    return {
      name: 'Test Project',
      eid: 'test-project'
    }
  }

  var exampleConnection = () => {
    return {
      name: 'Test Connection',
      eid: 'test-connection',
      connection_type: CONNECTION_TYPE_MYSQL
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
      AlertModal,
      ConfirmModal,
      FlexioModal,
      SignUpModal
    },
    data() {
      return {
        show_alert_modal: false,
        show_confirm_modal: false,
        show_project_props_modal: false,
        show_project_delete_modal: false,
        show_connection_props_modal: false,
        show_pipe_props_modal: false,
        show_pipe_schedule_modal: false,
        show_pipe_share_modal: false,
        show_email_support_modal: false,
        show_member_add_modal: false,
        show_tall_modal: false,
        show_flexio_modal: false,
        show_flexio_signup_modal: false
      }
    },
    methods: {
      openAlertModal() {
        this.show_alert_modal = true
        this.$nextTick(() => { this.$refs['modal-alert'].open() })
      },

      openConfirmModal() {
        this.show_confirm_modal = true
        this.$nextTick(() => { this.$refs['modal-confirm'].open() })
      },

      openNewProjectPropsModal() {
        this.show_project_props_modal = true
        this.$nextTick(() => { this.$refs['modal-project-props'].open() })
      },

      openEditProjectPropsModal() {
        this.show_project_props_modal = true
        this.$nextTick(() => { this.$refs['modal-project-props'].open(exampleProject()) })
      },

      openDeleteProjectPropsModal() {
        this.show_project_delete_modal = true
        this.$nextTick(() => { this.$refs['modal-project-delete'].open(exampleProject()) })
      },

      openNewConnectionModal() {
        this.show_connection_props_modal = true
        this.$nextTick(() => { this.$refs['modal-connection-props'].open() })
      },

      openEditConnectionModal() {
        this.show_connection_props_modal = true
        this.$nextTick(() => { this.$refs['modal-connection-props'].open(exampleConnection()) })
      },

      openNewPipeDialog() {
        this.show_pipe_props_modal = true
        this.$nextTick(() => { this.$refs['modal-pipe-props'].open() })
      },

      openEditPipeDialog() {
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
      },

      openTallModal() {
        this.show_tall_modal = true
        this.$nextTick(() => { this.$refs['modal-tall'].open() })
      },

      openFlexioModal() {
        this.show_flexio_modal = true
      },

      openFlexioSignUpModal() {
        this.show_flexio_signup_modal = true
      }
    }
  }
</script>
