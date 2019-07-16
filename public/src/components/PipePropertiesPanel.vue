<template>
  <div>
    <div class="w-100 mb4" v-if="showHeader">
      <div class="flex flex-row items-start">
        <span class="flex-fill f4 lh-title">Properties for '{{pipe.name}}'</span>
        <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="onClose"></i>
      </div>
    </div>

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
        label="API Endpoint"
      >
        <el-input
          placeholder="Enter name"
          v-model="edit_pipe.name"
        >
          <template slot="prepend"><div class="nl2 nr2">https://api.flex.io/v1/me/pipes/</div></template>
          <template slot="append">
            <el-button
              class="hint--top"
              aria-label="Copy to Clipboard"
              :data-clipboard-text="path"
            ><i class="material-icons md-18 nl2 nr2">assignment</i></el-button>
          </template>
        </el-input>
      </el-form-item>

      <el-form-item
        key="short_description"
        prop="short_description"
        label="Short description"
      >
        <el-input
          placeholder="Enter short description"
          :autofocus="true"
          v-model="edit_pipe.short_description"
        />
      </el-form-item>

      <el-form-item
        key="description"
        prop="description"
        label="Description"
      >
        <el-input
          type="textarea"
          placeholder="Enter description"
          :rows="3"
          v-model="edit_pipe.description"
        />
      </el-form-item>
    </el-form>

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
        Save changes
      </el-button>
    </div>
  </div>
</template>

<script>
  import { OBJECT_TYPE_PIPE } from '../constants/object-type'
  import MixinValidation from '@comp/mixins/validation'

  const defaultAttrs = () => {
    return {
      name: '',
      short_description: '',
      description: ''
    }
  }

  export default {
    props: {
      'title': {
        type: String,
        default: ''
      },
      'show-header': {
        type: Boolean,
        default: true
      },
      'show-footer': {
        type: Boolean,
        default: true
      },
      'pipe': {
        type: Object,
        default: () => { return defaultAttrs() }
      }
    },
    mixins: [MixinValidation],
    watch: {
      pipe: {
        handler: 'initPipe',
        immediate: true,
        deep: true
      },
      edit_pipe: {
        handler: 'updatePipe',
        deep: true
      }
    },
    data() {
      return {
        edit_pipe: defaultAttrs(),
        rules: {
          name: [
            { required: true, message: 'Please input a name', trigger: 'blur' },
            { validator: this.formValidateName }
          ]
        },
        form_errors: {}
      }
    },
    computed: {
      identifier() {
        var pname = this.edit_pipe.name
        return pname.length > 0 ? pname : _.get(this.edit_pipe, 'eid', '')
      },
      path() {
        return 'https://api.flex.io/v1/me/pipes/' + this.identifier
      },
      has_errors() {
        return _.keys(this.form_errors).length > 0
      }
    },

    methods: {
      onClose() {
        this.revert()
        this.initPipe()
        this.$emit('close')
      },
      onCancel() {
        this.revert()
        this.initPipe()
        this.$emit('cancel')
      },
      onSubmit() {
        this.$emit('submit', this.edit_pipe)
      },
      initPipe() {
        this.edit_pipe = _.cloneDeep(this.pipe)
      },
      updatePipe() {
        this.$emit('change', this.edit_pipe)
      },
      revert() {
        if (this.$refs.form) {
          this.$refs.form.resetFields()
        }
        this.form_errors = {}
      },
      validate(callback) {
        this.$refs.form.validate(callback)
      },
      formValidateName(rule, value, callback) {
        if (value.length == 0) {
          callback()
          return
        }

        if (value == _.get(this.pipe, 'name', '')) {
          callback()
          return
        }

        this.$_Validation_validateName(OBJECT_TYPE_PIPE, value, (response, errors) => {
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
