<template>
  <div>
    <!-- header -->
    <HeaderBar
      class="mb4"
      :title="our_title"
      @close-click="onClose"
      v-show="showHeader"
    />

    <p v-if="our_desc.length > 0">{{our_desc}}</p>

    <!-- body -->
    <el-form
      ref="form"
      class="el-form--cozy el-form__label-tiny"
      label-position="top"
      :model="edit_pipe"
      :rules="rules"
      @validate="onValidateItem"
      @submit.prevent.native
    >
      <el-form-item
        key="name"
        prop="name"
        label="Name"
      >
        <el-input
          placeholder="Enter name"
          auto-complete="off"
          spellcheck="false"
          :autofocus="true"
          v-model="edit_pipe.name"
        />
      </el-form-item>
    </el-form>

    <!-- footer -->
    <ButtonBar
      class="mt4"
      :submit-button-disabled="has_errors"
      :submit-button-text="submit_label"
      @cancel-click="onCancel"
      @submit-click="submit"
      v-show="showFooter"
    />
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { OBJECT_TYPE_PIPE } from '@/constants/object-type'
  import CodeEditor from '@/components/CodeEditor'
  import HeaderBar from '@/components/HeaderBar'
  import ButtonBar from '@/components/ButtonBar'
  import MixinValidation from '@/components/mixins/validation'

  const getDefaultState = (component) => {
    return {
      rules: {
        name: [
          { required: true, message: 'Please input a name', trigger: 'blur' },
          { validator: component.formValidateName }
        ]
      },
      form_errors: {},

      // edit values
      edit_pipe: {
        name: ''
      },
    }
  }

  export default {
    props: {
      title: {
        type: String,
        default: ''
      },
      showHeader: {
        type: Boolean,
        default: true
      },
      showFooter: {
        type: Boolean,
        default: true
      },
      pipe: {
        type: Object,
        required: true
      },
      mode: {
        type: String,
        default: 'add' // 'add' or 'edit'
      }
    },
    mixins: [MixinValidation],
    components: {
      CodeEditor,
      HeaderBar,
      ButtonBar
    },
    watch: {
      pipe: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      }
    },
    data() {
      return getDefaultState(this)
    },
    computed: {
      ...mapState({
        active_team_name: state => state.teams.active_team_name
      }),
      fname() {
        return _.get(this.pipe, 'name', '')
      },
      fop() {
        return _.get(this.pipe, 'task.op', '')
      },
      our_title() {
        if (this.title.length > 0) {
          return this.title
        }

        if (this.mode == 'add') {
          switch (this.fop) {
            case 'execute': return 'New Execute Function'
            case 'extract': return 'New Extract Function'
            case 'lookup':  return 'New Lookup Function'
          }

          return 'New Function'
        }

        return `Edit "${this.fname}" Function`
      },
      our_desc() {
        if (this.mode == 'add') {
          switch (this.fop) {
            case 'execute': return 'Build a spreadsheet function using your own Python or Node.js code.'
            case 'extract': return 'Build a spreadsheet function that returns a data extract from a remote data table.'
            case 'lookup':  return 'Build a spreadsheet function that returns information to cell(s), based on a key value lookup from a remote data table.'
          }
        }

        return ''
      },
      submit_label() {
        return this.mode == 'edit' ? 'Save changes' : 'Create function'
      },
      has_errors() {
        return _.keys(this.form_errors).length > 0
      }
    },
    methods: {
      initSelf() {
        // reset our local component data
        _.assign(this.$data, getDefaultState(this))

        // reset local objects
        this.edit_pipe = _.assign({}, this.edit_pipe, _.cloneDeep(this.pipe))

        // reset the form
        if (this.$refs.form) {
          this.$refs.form.resetFields()
        }
      },
      onClose() {
        this.initSelf()
        this.$emit('close')
      },
      onCancel() {
        this.initSelf()
        this.$emit('cancel')
      },
      submit() {
        this.mode == 'edit' ? this.submitEditPipe() : this.submitCreatePipe()
      },
      submitCreatePipe() {
        var team_name = this.active_team_name
        var attrs = this.edit_pipe

        this.$store.dispatch('pipes/create', { team_name, attrs }).then(response => {
          this.$message({
            message: 'The function was created successfully.',
            type: 'success'
          })

          var analytics_payload = _.pick(response.data, ['eid', 'name', 'title', 'description', 'created'])
          this.$store.track('Created Function', analytics_payload)

          this.$emit('update-pipe', response.data)
        })
      },
      submitEditPipe() {
        var team_name = this.active_team_name
        var eid = _.get(this.edit_pipe, 'eid', '')
        var attrs = _.pick(this.edit_pipe, ['name'])

        this.$store.dispatch('pipes/update', { team_name, eid, attrs }).then(response => {
          this.$message({
            message: 'The function was updated successfully.',
            type: 'success'
          })

          this.$emit('update-pipe', response.data)
        })
      },
      formValidateName(rule, value, callback) {
        if (value.length == 0) {
          callback()
          return
        }

        // we haven't changed the name; trying to validate it will tell us if it already exists
        if (value == _.get(this.pipe, 'name', '')) {
          callback()
          return
        }

        this.$_Validation_validateName(this.active_team_name, OBJECT_TYPE_PIPE, value, (response, errors) => {
          var message = _.get(errors, 'name.message', '')
          if (message.length > 0) {
            callback(new Error(message))
          } else {
            callback()
          }
        })
      },
      onValidateItem(key, valid) {
        var errors = _.assign({}, this.form_errors)
        if (valid) {
          errors = _.omit(errors, [key])
        } else {
          errors[key] = true
        }
        this.form_errors = _.assign({}, errors)
      }
    }
  }
</script>
