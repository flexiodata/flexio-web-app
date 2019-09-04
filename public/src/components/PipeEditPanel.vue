<template>
  <div>
    <!-- header -->
    <div class="w-100 mb4" v-if="showHeader">
      <div class="flex flex-row items-center">
        <span class="flex-fill f4 lh-title">{{our_title}}</span>
        <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="onClose"></i>
      </div>
    </div>

    <!-- body -->
    <el-form
      ref="form"
      class="el-form--cozy el-form__label-tiny"
      label-position="top"
      :model="edit_pipe"
      :rules="rules"
      @validate="onValidateItem"
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
    <div class="mt4 w-100 flex flex-row justify-end" v-if="showFooter">
      <el-button
        class="ttu fw6"
        @click="onCancel"
      >
        Cancel
      </el-button>
      <el-button
        class="ttu fw6"
        type="primary"
        :disabled="has_errors"
        @click="onSubmit"
      >
        {{submit_label}}
      </el-button>
    </div>
  </div>
</template>

<script>
  import randomstring from 'randomstring'
  import { mapState } from 'vuex'
  import { OBJECT_TYPE_PIPE } from '@/constants/object-type'
  import { getSyntaxStr } from '@/utils/pipe'
  import CodeEditor from '@/components/CodeEditor'
  import MixinValidation from '@/components/mixins/validation'

  const getNameSuffix = (length) => {
    return randomstring.generate({
      length,
      charset: 'alphabetic',
      capitalization: 'lowercase'
    })
  }

  const getDefaultState = (component) => {
    var suffix = getNameSuffix(4)

    return {
      rules: {
        name: [
          { required: true, message: 'Please input a name', trigger: 'blur' },
          { validator: component.formValidateName }
        ]
      },
      form_errors: {},

      // pipe values
      edit_pipe: {
        name: `func-${suffix}`
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
      CodeEditor
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
      pname() {
        return _.get(this.pipe, 'name', '')
      },
      our_title() {
        if (this.title.length > 0) {
          return this.title
        }

        return this.mode == 'edit' ? `Edit "${this.pname}" Function` : 'New Function'
      },
      submit_label() {
        return this.mode == 'edit' ? 'Save changes' : 'Create function'
      },
      has_errors() {
        return _.keys(this.form_errors).length > 0
      }
    },
    methods: {
      onClose() {
        this.initSelf()
        this.$emit('close')
      },
      onCancel() {
        this.initSelf()
        this.$emit('cancel')
      },
      onSubmit() {
        this.$emit('submit', this.edit_pipe)
      },
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
