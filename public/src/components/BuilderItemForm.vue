<template>
  <div v-show="visible">
    <div
      class="tl pb3"
      v-show="title.length > 0"
    >
      <h3 class="fw6 f3 ma0">{{title}}</h3>
    </div>
    <div
      class="pb3 markdown"
      v-html="description"
      v-show="description.length > 0"
    >
    </div>
    <el-form
      ref="form"
      :class="item.class"
      :model="edit_values"
      :label-position="label_position"
      :label-width="label_width"
      :rules="rules"
      @validate="onValidateItem"
    >
      <el-form-item
        :class="fi.class"
        :label="fi.label"
        :label-width="fi.element == 'markdown' ? (fi.label_width || '0') : fi.label_width"
        :key="fi.name"
        :prop="fi.name"
        v-show="fi.type !== 'hidden'"
        v-for="fi in form_items"
      >
        <div
          v-html="getMarkdown(fi.value)"
          v-if="fi.element == 'markdown'"
        ></div>
        <CodeEditor
          class="bg-white"
          style="line-height: 1.15; font-size: 13px; padding: 4px; border: 1px solid #dcdfe6; border-radius: 4px"
          :lang="fi.lang"
          :transpose="fi.transpose || 'none'"
          :options="{ minRows: 8, maxRows: 20, lineNumbers: false }"
          v-model="edit_values[fi.name]"
          v-else-if="fi.element == 'code-editor'"
        />
        <el-switch
          v-model="edit_values[fi.name]"
          v-else-if="fi.element == 'switch'"
        />
        <el-checkbox
          :placeholder="fi.placeholder"
          v-model="edit_values[fi.name]"
          v-else-if="fi.element == 'checkbox'"
        />
        <el-checkbox-group
          :placeholder="fi.placeholder"
          v-model="edit_values[fi.name]"
          v-else-if="fi.element == 'checkbox-group'"
        >
          <el-checkbox
            :label="option.value"
            :key="option.value"
            :name="fi.name"
            v-for="option in fi.options"
          >
            {{option.label}}
          </el-checkbox>
        </el-checkbox-group>
        <el-radio-group
          :placeholder="fi.placeholder"
          v-model="edit_values[fi.name]"
          v-else-if="fi.element == 'radio-group'"
        >
          <el-radio
            :label="option.value"
            :key="option.value"
            v-for="option in fi.options"
          >
            {{option.label}}
          </el-radio>
        </el-radio-group>
        <el-select
          :placeholder="fi.placeholder"
          v-model="edit_values[fi.name]"
          v-else-if="fi.element == 'select'"
        >
          <el-option
            :label="option.label"
            :value="option.value"
            :key="option.value"
            v-for="option in fi.options"
          />
        </el-select>
        <el-date-picker
          format="MM/dd/yyyy"
          :type="fi.type"
          :editable="false"
          :placeholder="fi.placeholder"
          v-model="edit_values[fi.name]"
          v-else-if="fi.element == 'input' && isDatePickerType(fi.type)"
        />
        <el-input
          type="textarea"
          :placeholder="fi.placeholder"
          v-model="edit_values[fi.name]"
          v-else-if="fi.element == 'input' && fi.type == 'textarea'"
        />
        <el-input
          type="number"
          :placeholder="fi.placeholder"
          v-model.number="edit_values[fi.name]"
          v-else-if="fi.element == 'input' && fi.type == 'number'"
        />
        <el-input
          type="hidden"
          :placeholder="fi.placeholder"
          v-model="edit_values[fi.name]"
          v-else-if="fi.element == 'input' && fi.type == 'hidden'"
        />
        <el-input
          :placeholder="fi.placeholder"
          v-model="edit_values[fi.name]"
          v-else
        />
      </el-form-item>
    </el-form>
    <ButtonBar
      class="mt4"
      :submit-button-disabled="has_errors"
      @cancel-click="onCancelClick"
      @submit-click="onSubmitClick"
      v-bind="$attrs"
    />
  </div>
</template>

<script>
  import marked from 'marked'
  import CodeEditor from '@/components/CodeEditor'
  import ButtonBar from '@/components/ButtonBar'

  const getDefaultState = () => {
    return {
      edit_values: {},
      form_errors: {},
      rules: {},
    }
  }

  // make sure 'gfm' and 'breaks' are both set to true
  // to have Markdown render the same as it does on GitHub
  marked.setOptions({
    gfm: true,
    breaks: true,
    pedantic: false,
    sanitize: false,
    smartLists: true,
    smartypants: true
  })

  export default {
    inheritAttrs: false,
    props: {
      item: {
        type: Object,
        required: true
      },
      visible: {
        type: Boolean,
        default: true
      },
      formErrors: {
        type: Object,
        default: () => {}
      },
    },
    components: {
      CodeEditor,
      ButtonBar
    },
    watch: {
      item: {
        handler: 'initSelf',
        immediate: true,
        deep: true
      },
      edit_values: {
        handler: 'emitFormValues',
        immediate: true,
        deep: true
      },
      form_errors: {
        handler: 'emitFormErrors',
        immediate: true,
        deep: true
      }
    },
    data() {
      return getDefaultState()
    },
    computed: {
      title() {
        return _.get(this.item, 'title', '')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      label_position() {
        return _.get(this.item, 'label_position', 'top')
      },
      label_width() {
        return _.get(this.item, 'label_width', '10rem')
      },
      form_items() {
        return _.get(this.item, 'form_items', [])
      },
      form_rules() {
        return _.cloneDeep(this.item.rules || {})
      },
      form_values() {
        var obj = {}
        _.each(this.form_items, item => {
          var val = item.value
          val = _.isArray(val) || _.isObject(val) ? _.cloneDeep(val) : val
          obj[item.name] = val
        })
        return obj
      },
      has_errors() {
        return _.keys(this.form_errors).length > 0
      },
    },
    methods: {
      initSelf() {
        // reset our local component data
        var edit_values = this.form_values
        var rules = this.form_rules
        _.assign(this.$data, getDefaultState(), { edit_values, rules })

        this.autoFocus()
      },
      autoFocus() {
        this.$nextTick(() => {
          var form = this.$refs['form']
          if (form) {
            var first_comp = _.get(form, '$children[0].$children[0]', null)
            if (first_comp && first_comp.focus) {
              first_comp.focus()
            }
          }
        })
      },
      emitFormValues() {
        this.$emit('update:formValues', this.edit_values)
      },
      emitFormErrors() {
        this.$emit('update:formErrors', this.form_errors)
      },
      isDatePickerType(type) {
        return ['year','month','date','datetime','week','datetimerange','daterange'].indexOf(type) != -1
      },
      getMarkdown(val) {
        return marked(val)
      },
      validateForm(clear) {
        this.$nextTick(() => {
          if (this.$refs.form) {
            this.$refs.form.validate((valid, errors) => {
              if (clear === true) {
                this.$refs.form.clearValidate()
                this.form_errors = {}
              } else {
                this.form_errors = _.assign({}, errors)
              }
            })
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
      },
      onCancelClick() {
        this.$emit('cancel-click', this.edit_values)
      },
      onSubmitClick() {
        this.validateForm()
        this.$nextTick(() => {
          if (!this.has_errors) {
            this.$emit('submit-click', this.edit_values)
          }
        })
      },
    }
  }
</script>