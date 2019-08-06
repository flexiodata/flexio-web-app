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
        key="description"
        prop="description"
        label="Description"
      >
        <el-input
          type="textarea"
          placeholder="Enter description"
          :rows="6"
          v-model="edit_pipe.description"
        />
      </el-form-item>

      <!-- we're revisit using JSDoc nomenclature in the future -->
      <div v-if="false">
        <label class="el-form-item__label">
          <span class="flex flex-row items-center">
            <span>Description</span>
            <el-button
              type="text"
              class="el-form-item__label"
              style="border: 0; padding: 0; margin-left: 8px"
              @click="autofillDescription"
            >
              Autofill with example descripion
            </el-button>
          </span>
        </label>
        <CodeEditor
          class="pa1 bg-white ba b--black-10 br2"
          style="font-size: 13px"
          :options="{
            minRows: 12,
            maxRows: 24,
            lineNumbers: false,
            placeholder: description_placeholder
          }"
          v-model="edit_pipe.description"
        />
      </div>

      <div class="mt3" style="width: 300px">
        <label class="el-form-item__label">Preview</label>
        <div class="pa3 bg-white ba b--black-10 br2">
          <div class="code f7 b" v-html="spreadsheet_command_syntax"></div>
          <p class="mb0 f6" v-if="pdesc.length > 0">{{pdesc}}</p>
        </div>
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
  import { mapState } from 'vuex'
  import { getJsDocObject, getSpreadsheetSyntaxStr } from '@/utils/pipe'
  import { OBJECT_TYPE_PIPE } from '@/constants/object-type'
  import CodeEditor from '@/components/CodeEditor'
  import MixinValidation from '@/components/mixins/validation'

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
      title: '',
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
        form_errors: {},
        description_placeholder: `Example:

Add two numbers

@customfunction
@param {number} first First number
@param {number} second Second number
@returns {number} The sum of the two numbers.
`
      }
    },
    computed: {
      ...mapState({
        active_team_name: state => state.teams.active_team_name
      }),
      pname() {
        return _.get(this.orig_pipe, 'name', '')
      },
      our_title() {
        if (this.title.length > 0) {
          return this.title
        }

        return this.mode == 'edit' ? `Edit "${this.pname}" Function` : 'New Function'
      },
      pdesc() {
        var jsdoc_obj = getJsDocObject(this.edit_pipe)
        return _.get(jsdoc_obj, 'description', '')
      },
      spreadsheet_command_syntax() {
        return getSpreadsheetSyntaxStr(this.edit_pipe, true)
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
      autofillDescription() {
        var desc = this.description_placeholder
        desc = desc.substring(desc.indexOf('\n'))
        desc = desc.trim()
        this.edit_pipe.description = desc
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
