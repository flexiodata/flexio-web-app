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
        data-vv-scope="form-delete"
        @submit.prevent="submit"
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
      <div class="flex-fill">&nbsp;</div>
      <btn btn-md class="flex-none b ttu blue mr2" @click="close()">Cancel</btn>
      <btn btn-md class="flex-none b ttu ba b--dark-red white bg-dark-red" @click="submit()">Delete project</btn>
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
        return 'Delete "' + _.get(this.original_project, 'name') + '" Project?'
      }
    },
    methods: {
      open(attrs) {
        this.reset(attrs)
        this.is_open = true
        this.$refs['dialog'].open()
      },
      close() {
        this.$refs['dialog'].close()
      },
      submit() {
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
