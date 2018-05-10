<template>
  <el-form
    class="el-form-compact"
    label-width="7rem"
    size="small"
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
      label="Path"
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
            size="small"
            :data-clipboard-text="path"
          ><span class="ttu b">Copy</span></el-button>
        </template>
      </el-input>
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
      custom-class="no-header no-footer"
      width="56rem"
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
  </el-form>
</template>

<script>
  import { mapState } from 'vuex'
  import {
    SCHEDULE_STATUS_ACTIVE,
    SCHEDULE_STATUS_INACTIVE
  } from '../constants/schedule'
  import PipeSchedulePanel from './PipeSchedulePanel.vue'

  export default {
    components: {
      PipeSchedulePanel
    },
    watch: {
      form_values: {
        handler: 'updateForm',
        immediate: true,
        deep: true
      }
    },
    data() {
      return {
        show_pipe_schedule_dialog: false,
        form_values: null,
        rules: {
          name: [
            { required: true, message: 'Please input a name', trigger: 'blur' }
          ]
        }
      }
    },
    computed: {
      ...mapState({
        edit_pipe: state => state.pipe.edit_pipe
      }),
      path() {
        return 'https://api.flex.io/v1/me/pipes/' + this.form_values.alias
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
    methods: {
      updateForm() {
        if (this.form_values === null) {
          this.resetForm()
        } else {
          this.$store.commit('pipe/UPDATE_EDIT_PIPE', this.form_values)
        }
      },
      resetForm() {
        var form_values = _.get(this.$store, 'state.pipe.edit_pipe')
        this.form_values = _.cloneDeep(form_values)
      },
      updatePipeSchedule(attrs) {
        attrs = _.pick(attrs, ['schedule', 'schedule_status'])

        var pipe = _.cloneDeep(this.edit_pipe)
        _.assign(pipe, attrs)
        this.$store.commit('pipe/UPDATE_EDIT_PIPE', pipe)

        this.show_pipe_schedule_dialog = false
      }
    }
  }
</script>
