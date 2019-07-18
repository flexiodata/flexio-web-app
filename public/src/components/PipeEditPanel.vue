<template>
  <div>
    <div class="w-100 mb4" v-if="showHeader">
      <div class="flex flex-row items-center">
        <span class="flex-fill f4 lh-title">{{our_title}}</span>
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
        label="Name"
      >
        <el-input
          placeholder="Enter name"
          :autofocus="true"
          v-model="edit_pipe.name"
        />
      </el-form-item>

      <el-form-item
        key="short_description"
        prop="short_description"
        label="Short description"
      >
        <el-input
          placeholder="Enter short description"
          v-model="edit_pipe.short_description"
        />
      </el-form-item>

      <div>
        <label class="el-form-item__label">Description</label>
        <CodeEditor
          class="bg-white ba b--black-10"
          style="font-size: 13px"
          :options="{
            minRows: 12,
            maxRows: 24,
            lineNumbers: false
          }"
          v-model="edit_pipe.description"
        />
      </div>
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
        {{submit_label}}
      </el-button>
    </div>
  </div>
</template>

<script>
  import randomstring from 'randomstring'
  import { OBJECT_TYPE_PIPE } from '../constants/object-type'
  import CodeEditor from '@comp/CodeEditor'
  import MixinValidation from '@comp/mixins/validation'

  const getNameSuffix = (length) => {
    return randomstring.generate({
      length,
      charset: 'alphabetic',
      capitalization: 'lowercase'
    })
  }

  const defaultAttrs = () => {
    var suffix = getNameSuffix(4)

    return {
      name: `pipe-${suffix}`,
      short_description: '',
      description: ''
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
        default: () => { return defaultAttrs() }
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
        orig_pipe: _.assign({}, defaultAttrs(), this.pipe),
        edit_pipe: _.assign({}, defaultAttrs(), this.pipe),
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
      pname() {
        return _.get(this.orig_pipe, 'name', '')
      },
      our_title() {
        if (this.title.length > 0) {
          return this.title
        }

        return this.mode == 'edit' ? `Edit "${this.pname}" Pipe` : 'New Pipe'
      },
      submit_label() {
        return this.mode == 'edit' ? 'Save changes' : 'Create pipe'
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
        var pipe = _.cloneDeep(this.pipe)

        // we have to do this to force watcher validation
        this.$nextTick(() => {
          this.orig_pipe = _.cloneDeep(pipe)
          this.edit_pipe = _.cloneDeep(pipe)
        })
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

        // we haven't changed the name; trying to validate it will tell us it already exists
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
