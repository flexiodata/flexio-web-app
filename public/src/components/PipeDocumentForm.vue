<template>
  <el-form
    ref="form"
    class="el-form--cozy"
    label-width="9rem"
    :model="form_values"
    :rules="rules"
    v-if="form_values"
  >
    <el-form-item
      key="name"
      label="Name"
      prop="name"
    >
      <el-input
        placeholder="Enter name"
        style="max-width: 36rem"
        :autofocus="true"
        v-model="form_values.name"
      />
    </el-form-item>
    <el-form-item
      key="alias"
      label="API Endpoint"
      prop="alias"
    >
      <el-input
        placeholder="Enter alias"
        style="max-width: 36rem"
        v-model="form_values.alias"
      >
        <template slot="prepend">https://api.flex.io/v1/me/pipes/</template>
        <template slot="append">
          <el-button
            slot="append"
            class="hint--top"
            aria-label="Copy to Clipboard"
            :data-clipboard-text="path"
          ><span class="ttu b">Copy</span></el-button>
        </template>
      </el-input>
      <span class="ml1">
        <el-button
          type="text"
          @click="show_pipe_deploy_dialog = true"
        >
          How do I deploy this pipe?
        </el-button>
      </span>
    </el-form-item>
    <el-form-item
      label="Scheduled"
    >
      <el-switch
        active-color="#009900"
        v-model="is_scheduled"
      />
      <span class="ml1">
        <el-button
          type="text"
          @click="show_pipe_schedule_dialog = true"
        >
          Options...
        </el-button>
      </span>
    </el-form-item>
    <el-form-item
      key="description"
      label="Description"
      prop="description"
    >
      <el-input
        type="textarea"
        placeholder="Enter description"
        style="max-width: 48rem"
        :rows="3"
        v-model="form_values.description"
      />
    </el-form-item>

    <!-- pipe schedule dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="42rem"
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_pipe_schedule_dialog"
    >
      <PipeSchedulePanel
        :pipe="edit_pipe"
        @close="show_pipe_schedule_dialog = false"
        @cancel="show_pipe_schedule_dialog = false"
        @submit="updatePipeSchedule"
      />
    </el-dialog>

    <!-- pipe deploy dialog -->
    <el-dialog
      custom-class="el-dialog--no-header el-dialog--no-footer"
      width="56rem"
      top="8vh"
      :modal-append-to-body="false"
      :visible.sync="show_pipe_deploy_dialog"
    >
      <PipeDeployPanel
        :pipe="orig_pipe"
        @close="show_pipe_deploy_dialog = false"
      />
    </el-dialog>

  </el-form>
</template>

<script>
  import { mapState } from 'vuex'
  import { SCHEDULE_STATUS_ACTIVE, SCHEDULE_STATUS_INACTIVE } from '../constants/schedule'
  import { OBJECT_TYPE_PIPE } from '../constants/object-type'
  import PipeSchedulePanel from './PipeSchedulePanel.vue'
  import PipeDeployPanel from './PipeDeployPanel.vue'
  import Validation from './mixins/validation'

  export default {
    mixins: [Validation],
    components: {
      PipeSchedulePanel,
      PipeDeployPanel
    },
    watch: {
      orig_pipe: {
        handler: 'resetForm',
        immedate: true,
        deep: true
      },
      form_values: {
        handler: 'updateStore',
        deep: true
      }
    },
    data() {
      return {
        show_pipe_schedule_dialog: false,
        show_pipe_deploy_dialog: false,
        form_values: null,
        rules: {
          name: [
            { required: true, message: 'Please input a name', trigger: 'blur' }
          ],
          alias: [
            { validator: this.formValidateAlias }
          ]
        }
      }
    },
    computed: {
      ...mapState({
        orig_pipe: state => state.pipe.orig_pipe,
        edit_pipe: state => state.pipe.edit_pipe
      }),
      identifier() {
        var alias = this.form_values.alias
        return alias.length > 0 ? alias : _.get(this.edit_pipe, 'eid', '')
      },
      path() {
        return 'https://api.flex.io/v1/me/pipes/' + this.identifier
      },
      path_tooltip() {
        return 'This is the API endpoint for this pipe. To run this pipe via our REST API, append "/run" to this endpoint.'
      },
      is_scheduled: {
        get() {
          return _.get(this.edit_pipe, 'schedule_status') == SCHEDULE_STATUS_ACTIVE ? true : false
        },
        set() {
          var status = this.is_scheduled ? SCHEDULE_STATUS_INACTIVE : SCHEDULE_STATUS_ACTIVE
          _.set(this.form_values, 'schedule_status', status)
        }
      }
    },
    mounted() {
      this.resetForm(this.orig_pipe)
    },
    methods: {
      validate(callback) {
        this.$refs.form.validate(callback)
      },
      updateStore() {
        this.$store.commit('pipe/UPDATE_EDIT_PIPE', this.form_values)
      },
      resetForm(form_values) {
        this.form_values = _.cloneDeep(form_values)
      },
      updatePipeSchedule(attrs) {
        attrs = _.pick(attrs, ['schedule', 'schedule_status'])

        var pipe = _.cloneDeep(this.edit_pipe)
        _.assign(pipe, attrs)
        this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)

        this.show_pipe_schedule_dialog = false
      },
      formValidateAlias(rule, value, callback) {
        if (value.length == 0) {
          callback()
          return
        }

        if (value == _.get(this.orig_pipe, 'alias', '')) {
          callback()
          return
        }

        this.$_Validation_validateAlias(OBJECT_TYPE_PIPE, value, (response, errors) => {
          var message = _.get(errors, 'alias.message', '')
          if (message.length > 0) {
            callback(new Error(message))
          } else {
            callback()
          }
        })
      }
    }
  }
</script>
