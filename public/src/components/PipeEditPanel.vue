<template>
  <div>
    <div class="w-100 mb4" v-if="showHeader">
      <div class="flex flex-row items-start">
        <span class="flex-fill f4 lh-title">{{our_title}}</span>
        <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="$emit('close')"></i>
      </div>
    </div>

    <div>
      <el-form
        ref="form"
        class="el-form--compact el-form__label-tiny"
        :model="edit_pipe"
        :rules="rules"
      >
        <div class="flex flex-row">
          <el-form-item
            class="flex-fill mr3"
            key="short_description"
            prop="short_description"
            label="Short description"
          >
            <el-input
              placeholder="Enter short description"
              autocomplete="off"
              :autofocus="true"
              v-model="edit_pipe.short_description"
            />
          </el-form-item>

          <el-form-item
            class="flex-fill"
            key="alias"
            prop="alias"
            label="Alias"
          >
            <el-input
              placeholder="Enter alias"
              autocomplete="off"
              spellcheck="false"
              v-model="edit_pipe.alias"
            >
              <span
                slot="suffix"
                class="h-100 hint--bottom-left hint--large cursor-default"
                aria-label="Pipes can be referenced via an alias in the Flex.io command line interface (CLI), all SDKs as well as the REST API."
              >
                <i class="material-icons md-24 blue v-mid">info</i>
              </span>
            </el-input>
          </el-form-item>
        </div>

        <el-form-item
          key="description"
          prop="description"
          label="Description"
        >
          <el-input
            type="textarea"
            placeholder="Description"
            :rows="3"
            v-model="edit_pipe.description"
          />
        </el-form-item>
      </el-form>
    </div>

    <div class="mt4 w-100 flex flex-row justify-end" v-if="showFooter">
      <el-button
        class="ttu fw6"
        @click="$emit('cancel')"
      >
        Cancel
      </el-button>
      <el-button
        class="ttu fw6"
        type="primary"
        @click="submit"
        :disabled="has_errors"
      >
        {{submit_label}}
      </el-button>
    </div>
  </div>
</template>

<script>
  import { OBJECT_TYPE_PIPE } from '../constants/object-type'
  import Validation from '@comp/mixins/validation'

  const defaultAttrs = () => {
    return {
      eid: null,
      short_description: '',
      alias: '',
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
        default: () => { return {} }
      },
      'mode': {
        type: String,
        default: 'add'
      }
    },
    mixins: [Validation],
    watch: {
      pipe: {
        handler: 'initPipe',
        immediate: true,
        deep: true
      }
    },
    data() {
      return {
        edit_pipe: _.assign({}, defaultAttrs(), this.pipe),
        rules: {
          alias: [
            { validator: this.formValidateAlias }
          ]
        }
      }
    },
    computed: {
      eid() {
        return _.get(this, 'edit_pipe.eid', '')
      },
      our_title() {
        if (this.title.length > 0) {
          return this.title
        }

        return this.mode == 'edit'
          ? 'Edit "' + _.get(this.pipe, 'short_description') + '" Pipe'
          : 'New Pipe'
      },
      submit_label() {
        return this.mode == 'edit' ? 'Save changes' : 'Create pipe'
      },
      has_errors() {
        return false
      }
    },
    mounted() {
      this.$nextTick(() => {
        if (this.$refs.form) {
          this.$refs.form.validateField('alias')
        }
      })
    },
    methods: {
      submit() {
        this.$refs.form.validate((valid) => {
          if (!valid)
            return

          // there are no errors in the form; do the submit
          this.$emit('submit', this.edit_pipe)
        })
      },
      reset(attrs) {
        this.edit_pipe = _.assign({}, defaultAttrs(), attrs)
      },
      formValidateAlias(rule, value, callback) {
        if (value.length == 0) {
          callback()
          return
        }

        // we haven't changed the alias; trying to validate it will tell us it already exists
        if (this.mode == 'edit' && value == _.get(this.pipe, 'alias', '')) {
          callback()
          return
        }

        this.$_Validation_validateAlias(OBJECT_TYPE_PIPE, value, (response, errors) => {
          var message = _.get(errors, 'alias.message', '')
          if (message.length > 0) {
            callback(new Error(message))
          } else {
            callback()
          }
        })
      },
      initPipe() {
        var pipe = _.cloneDeep(this.pipe)

        // we have to do this to force watcher validation
        this.$nextTick(() => {
          this.edit_pipe = _.assign({}, pipe)
        })
      },
      updatePipe(attrs) {
        this.edit_pipe = _.assign({}, this.edit_pipe, attrs)
      }
    }
  }
</script>
