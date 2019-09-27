<template>
  <div>
    <!-- header -->
    <HeaderBar
      class="mb4"
      :title="our_title"
      @close-click="onClose"
      v-show="showHeader"
    />

    <!-- body -->
    <div>
      <p>Enter the name of the connection. The connection name is how you will reference this connection in your functions (e.g. in the path of a lookup function).</p>
      <el-form
        ref="form"
        class="el-form--cozy el-form__label-tiny"
        label-position="top"
        :model="$data"
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
            v-model="name"
          />
        </el-form-item>
      </el-form>
    </div>

    <!-- footer -->
    <ButtonBar
      class="mt4"
      :submit-button-disabled="has_errors"
      :submit-button-text="submit_label"
      @cancel-click="onCancel"
      @submit-click="onSubmit"
      v-show="showFooter"
    />
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import { OBJECT_TYPE_CONNECTION } from '@/constants/object-type'
  import HeaderBar from '@/components/HeaderBar'
  import ButtonBar from '@/components/ButtonBar'
  import MixinValidation from '@/components/mixins/validation'

  const getDefaultState = (component) => {
    return {
      is_emitting_update: false,
      rules: {
        name: [
          { required: true, message: 'Please input a name', trigger: 'blur' },
          { validator: component.formValidateName }
        ]
      },
      form_errors: {},

      // edit values
      edit_connection: {},
      name: '',
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
      connection: {
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
      HeaderBar,
      ButtonBar
    },
    watch: {
      connection: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      },
      edit_connection: {
        handler: 'emitUpdate',
        deep: true
      },
      name: {
        handler: 'updateName',
      },
    },
    data() {
      return getDefaultState(this)
    },
    computed: {
      ...mapState({
        active_team_name: state => state.teams.active_team_name
      }),
      our_title() {
        if (this.title.length > 0) {
          return this.title
        }

        var cname = _.get(this.connection, 'name', '')
        return this.mode == 'edit' ? `Edit "${cname}" Connection` : 'New Connection'
      },
      submit_label() {
        return this.mode == 'edit' ? 'Save changes' : 'Create connection'
      },
      has_errors() {
        return _.keys(this.form_errors).length > 0
      }
    },
    methods: {
      initSelf() {
        if (this.is_emitting_update === true ) {
          return
        }

        // reset our local component data
        _.assign(this.$data, getDefaultState(this))

        // reset local objects
        this.edit_connection = _.assign({}, this.edit_connection, _.cloneDeep(this.connection))

        this.name = _.get(this.edit_connection, 'name', '')

        // reset the form
        if (this.$refs.form) {
          this.$refs.form.resetFields()
        }
      },
      emitUpdate() {
        this.is_emitting_update = true
        this.$emit('update:connection', this.edit_connection)
        this.$nextTick(() => { this.is_emitting_update = false })
      },
      updateEditConnection(attrs) {
        this.edit_connection = _.assign({}, this.edit_connection, attrs)
      },
      updateName() {
        var name = this.name
        this.updateEditConnection({ name })
      },
      onClose() {
        this.initSelf()
        this.$emit('close')
      },
      onCancel() {
        this.initSelf()
        this.$emit('cancel')
      },
      onSubmit() {
        this.$emit('submit', this.edit_connection)
      },
      formValidateName(rule, value, callback) {
        // we haven't changed the name; trying to validate it will tell us if it already exists
        if (value == _.get(this.connection, 'name', '')) {
          callback()
          return
        }

        this.$_Validation_validateName(this.active_team_name, OBJECT_TYPE_CONNECTION, value, (response, errors) => {
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
