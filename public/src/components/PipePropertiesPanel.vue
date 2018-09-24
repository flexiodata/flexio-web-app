<template>
  <div>
    <div class="w-100 mb4" v-if="showHeader">
      <div class="flex flex-row items-center" v-if="showHeader">
        <span class="flex-fill f4">Properties for '{{pipe.name}}'</span>
        <i class="el-icon-close pointer f3 black-30 hover-black-60" @click="onClose"></i>
      </div>
    </div>

    <el-form
      ref="form"
      class="el-form--cozy el-form__label-tiny"
      label-position="top"
      :model="edit_pipe"
      :rules="rules"
    >
      <el-form-item
        key="name"
        label="Name"
        prop="name"
      >
        <el-input
          placeholder="Enter name"
          style="max-width: 36rem"
          :autofocus="true"
          v-model="edit_pipe.name"
        />
      </el-form-item>
      <el-form-item
        key="alias"
        label="API Endpoint"
        prop="alias"
      >
        <el-input
          placeholder="Enter alias"
          class="mr1"
          style="max-width: 36rem"
          v-model="edit_pipe.alias"
        >
          <template slot="prepend">https://api.flex.io/v1/me/pipes/</template>
          <template slot="append">
            <el-button
              class="hint--top"
              aria-label="Copy to Clipboard"
              :data-clipboard-text="path"
            ><span class="ttu b">Copy</span></el-button>
          </template>
        </el-input>
      </el-form-item>
      <el-form-item
        key="description"
        label="Description"
        prop="description"
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
        class="ttu b"
        @click="onCancel"
      >
        Cancel
      </el-button>
      <el-button
        class="ttu b"
        type="primary"
        @click="onSubmit"
      >
        Save changes
      </el-button>
    </div>
  </div>
</template>

<script>
  import { OBJECT_TYPE_PIPE } from '../constants/object-type'
  import MixinValidation from './mixins/validation'

  const defaultAttrs = () => {
    return {
      name: '',
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
            { required: true, message: 'Please input a name', trigger: 'blur' }
          ],
          alias: [
            { validator: this.formValidateAlias }
          ]
        }
      }
    },
    computed: {
      identifier() {
        var alias = this.edit_pipe.alias
        return alias.length > 0 ? alias : _.get(this.edit_pipe, 'eid', '')
      },
      path() {
        return 'https://api.flex.io/v1/me/pipes/' + this.identifier
      }
    },

    methods: {
      onClose() {
        this.initPipe()
        this.$emit('close')
      },
      onCancel() {
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
      validate(callback) {
        this.$refs.form.validate(callback)
      },
      formValidateAlias(rule, value, callback) {
        if (value.length == 0) {
          callback()
          return
        }

        if (value == _.get(this.pipe, 'alias', '')) {
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
      }
    }
  }
</script>
