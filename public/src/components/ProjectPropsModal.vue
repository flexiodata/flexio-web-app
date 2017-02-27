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
        data-vv-scope="form-create-edit"
        @submit.prevent="submit"
        v-show="mode!='delete'"
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

      <form
        novalidate
        data-vv-scope="form-delete"
        @submit.prevent="submitDelete"
        v-show="mode=='delete'"
      >
        <p class="ma0 pb1 lh-copy">Are you sure you want to delete the <span class="b">{{original_project.name}}</span> project and all of its contents? To confirm and continue with this action, enter the project name below.</p>
        <input v-model="original_project.name" data-vv-name="project_name" name="project_name" type="hidden">
        <ui-textbox
          ref="js-input-confirm-name"
          name="confirm_name"
          autocomplete="off"
          label="Name"
          floating-label
          help=" "
          :error="errors.first('confirm_name')"
          :invalid="errors.has('confirm_name')"
          v-model="confirm_name"
          v-validate
          data-vv-as="name"
          data-vv-name="confirm_name"
          data-vv-value-path="confirm_name"
          data-vv-rules="required|confirmed:project_name"
        >
        </ui-textbox>
        <div class="ttu dark-red fw6">Warning: You can't undo this action!</div>
      </form>

    </div>

    <div slot="footer" class="flex-fill flex flex-row">
      <btn
        btn-md
        class="flex-none b dark-red mr2 hint--top"
        aria-label="Confirmation required"
        @click="showDeleteForm()"
        v-if="mode=='edit'"
      >
        <span class="ttu">Delete Project</span>
      </btn>
      <div class="flex-fill">&nbsp;</div>
      <btn btn-md class="flex-none b ttu blue mr2" @click="close()">Cancel</btn>
      <btn btn-md v-if="mode!='delete'" class="flex-none b ttu blue" @click="submit()">{{submit_label}}</btn>
      <btn btn-md v-else class="flex-none b ttu ba b--dark-red white bg-dark-red" @click="submitDelete()">Delete project</btn>
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
    props: ['open-from', 'close-to'],
    components: {
      Btn
    },
    data() {
      return {
        is_open: false,
        mode: 'edit', // 'edit' or 'delete'
        confirm_name: '',
        project: _.extend({}, DEFAULT_ATTRS),
        original_project: _.extend({}, DEFAULT_ATTRS)
      }
    },
    watch: {
      'project.name'(value) {
        this.$emit('input', value);
      }
    },
    computed: {
      has_eid() {
        return _.get(this.project, 'eid') !== null
      },
      title() {
        if (this.mode == 'delete')
          return 'Delete "' + _.get(this.original_project, 'name') + '" Project?'

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
      showDeleteForm() {
        var me = this
        this.mode = 'delete'

        this.$nextTick(() => {
          me.$refs['js-input-confirm-name'].$el.focus()
        })
      },
      submit() {
        this.$validator.validateAll('form-create-edit').then(success => {
          // handle error
          if (!success)
            return

          this.$emit('submit', this.project, this)
        })
      },
      submitDelete() {
        this.$validator.validateAll('form-delete').then(success => {
          // handle error
          if (!success)
            return

          this.$emit('submit-delete', this.project, this)
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
