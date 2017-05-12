<template>
  <ui-modal
    ref="dialog"
    remove-close-button
    @hide="onHide"
  >
    <div slot="header" class="f4">{{title}}</div>

    <div v-if="is_open">
      <form
        novalidate
        @submit.prevent="submit"
      >
        <ui-textbox
          name="name"
          autocomplete="off"
          label="Name"
          floating-label
          help=" "
          :error="errors.first('name')"
          :invalid="errors.has('name')"
          v-model="project.name"
          v-validate
          data-vv-name="name"
          data-vv-value-path="project.name"
          data-vv-rules="required"
        >
        </ui-textbox>
        <ui-textbox
          label="Description"
          floating-label
          :multi-line="true"
          :rows="1"
          v-model="project.description"
        ></ui-textbox>
      </form>
    </div>

    <div slot="footer" class="flex-fill flex flex-row">
      <div class="flex-fill">&nbsp;</div>
      <btn btn-md class="flex-none b ttu blue mr2" @click="close()">Cancel</btn>
      <btn btn-md class="flex-none b ttu blue" @click="submit()">{{submit_label}}</btn>
    </div>
  </ui-modal>
</template>

<script>
  import Btn from './Btn.vue'

  const DEFAULT_ATTRS = {
    eid: null,
    name: '',
    description: ''
  }

  export default {
    components: {
      Btn
    },
    data() {
      return {
        is_open: false,
        mode: 'edit', // 'edit' or 'create'
        confirm_name: '',
        project: _.extend({}, DEFAULT_ATTRS),
        original_project: _.extend({}, DEFAULT_ATTRS)
      }
    },
    watch: {
      'project.name'(value) {
        this.$emit('input', value)
      }
    },
    computed: {
      has_eid() {
        return _.get(this.project, 'eid') !== null
      },
      title() {
        return this.has_eid
          ? 'Edit "' + _.get(this.original_project, 'name') + '" Project'
          : 'New Project'
      },
      submit_label() {
        return this.has_eid ? 'Save changes' : 'Create project'
      }
    },
    methods: {
      open(attrs) {
        this.reset(attrs)
        this.mode = this.has_eid ? 'edit' : 'create'
        this.is_open = true
        this.$refs['dialog'].open()
      },
      close() {
        this.$refs['dialog'].close()
      },
      submit() {
        this.$validator.validateAll().then(success => {
          // handle error
          if (!success)
            return

          this.$emit('submit', this.project, this)
        })
      },
      reset(attrs) {
        this.confirm_name = ''
        this.project = _.extend({}, DEFAULT_ATTRS, attrs)
        this.original_project = _.extend({}, DEFAULT_ATTRS, attrs)
      },
      onHide() {
        this.reset()
        this.is_open = false
      }
    }
  }
</script>
