<template>
  <div>
    <div
      class="tl pb3"
      v-if="title.length > 0"
    >
      <h3 class="fw6 f3 mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 marked"
      v-html="description"
      v-show="show_description"
    >
    </div>
    <el-form
      ref="form"
      class="flex-fill el-form--compact el-form__label-tiny"
      label-position="top"
      :model="edit_values"
      :rules="rules"
      @validate="onValidateItem"
    >
      <el-form-item
        key="to"
        label="To"
        prop="to"
      >
        <el-input
          placeholder="Enter email address"
          autocomplete="off"
          style="max-width: 30rem"
          v-model="edit_values.to"
        />
        <el-select
          class="w-100"
          multiple
          filterable
          allow-create
          default-first-option
          popper-class="dn"
          placeholder="Enter email addresses"
          v-model="edit_values.to"
          v-if="false"
        />
      </el-form-item>
      <el-form-item
        key="subject"
        label="Subject"
        prop="subject"
      >
        <el-input
          placeholder="Enter subject"
          autocomplete="off"
          v-model="edit_values.subject"
        />
      </el-form-item>
      <el-form-item
        key="body"
        label="Message"
        prop="body"
      >
        <el-input
          type="textarea"
          placeholder="Enter message"
          autocomplete="off"
          :autosize="{ minRows: 3, maxRows: 8}"
          v-model="edit_values.body"
        />
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
  import marked from 'marked'

  const getDefaultValues = () => {
    return {
      to: '',
      subject: '',
      body: '',
      attachments: []
    }
  }

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number,
        required: true
      },
      activeItemIdx: {
        type: Number,
        required: true
      },
      isNextAllowed: {
        type: Boolean,
        required: true
      }
    },
    watch: {
      item: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      },
      is_changed: {
        handler: 'onChange'
      },
      edit_values: {
        handler: 'onEditValuesChange',
        immediate: true,
        deep: true
      },
      form_errors(val) {
        this.$emit('update:isNextAllowed', _.keys(val).length == 0)
      }
    },
    data() {
      return {
        orig_values: getDefaultValues(),
        edit_values: getDefaultValues(),
        form_errors: {},
        rules: {
          to: [
            { required: true, message: 'Please input an email address' },
            { type: 'email', message: 'Please input a valid email address', trigger: 'blur' }
          ],
          subject: [
            { required: true, message: 'Please input a subject' }
          ],
          body: [
            { required: true, message: 'Please input a message' }
          ]
        }
      }
    },
    computed: {
      show_description() {
        return this.description.length > 0
      },
      title() {
        return _.get(this.item, 'title', 'Connect')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      is_changed() {
        return !_.isEqual(this.edit_values, this.orig_values)
      }
    },
    methods: {
      initSelf() {
        var form_values = _.get(this.item, 'form_values', {})
        this.orig_values = _.assign({}, getDefaultValues(), form_values)
        this.edit_values = _.assign({}, getDefaultValues(), form_values)
        this.$nextTick(() => { this.validateForm(true) })
      },
      validateForm(clear) {
        if (this.$refs.form) {
          this.$refs.form.validate((valid) => {
            this.$emit('update:isNextAllowed', valid)
            if (clear === true) {
              this.$refs.form.clearValidate()
            }
          })
        }
      },
      onValidateItem(key, valid) {
        var errors = _.assign({}, this.form_errors)
        if (valid) {
          errors = _.omit(errors, [key])
        } else {
          errors[key] = true
        }
        this.form_errors = _.assign({}, errors)
      },
      onChange(val) {
        if (val) {
          this.$nextTick(() => { this.validateForm(true) })
          this.$emit('active-item-change', this.index)
        }
      },
      onEditValuesChange() {
        this.$emit('item-change', this.edit_values, this.index)
      }
    }
  }
</script>
