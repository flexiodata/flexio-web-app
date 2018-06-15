<template>
  <div>
    <div class="tl pb3">
      <h3 class="fw6 f3 mid-gray mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 mid-gray marked"
      v-html="description"
      v-show="show_description"
    >
    </div>

    <el-form
      ref="form"
      :class="item.cls"
      :model="form_values"
      :label-position="label_position"
      :label-width="label_width"
      v-if="show_controls"
    >
      <el-form-item
        :class="fi.cls"
        :label="fi.label"
        :label-width="fi.element == 'markdown' ? (fi.label_width || '0') : fi.label_width"
        :key="fi.variable"
        :prop="fi.variable"
        v-show="fi.type !== 'hidden'"
        v-for="fi in form_items"
      >
        <div
          v-html="getMarkdown(fi.value)"
          v-if="fi.element == 'markdown'"
        ></div>
        <CodeEditor
          class="bg-white ba b--black-10 overflow-y-auto"
          style="line-height: 1.15; font-size: 13px"
          :lang="fi.lang ? fi.lang : 'javascript'"
          :options="{ minRows: 8, maxRows: 20 }"
          v-model="form_values[fi.variable]"
          v-else-if="fi.element == 'code-editor'"
        />
        <el-switch
          v-model="form_values[fi.variable]"
          v-else-if="fi.element == 'switch'"
        />
        <el-checkbox
          :placeholder="fi.placeholder"
          v-model="form_values[fi.variable]"
          v-else-if="fi.element == 'checkbox'"
        />
        <el-checkbox-group
          :placeholder="fi.placeholder"
          v-model="form_values[fi.variable]"
          v-else-if="fi.element == 'checkbox-group'"
        >
          <el-checkbox
            :label="option.value"
            :key="option.value"
            :name="fi.variable"
            v-for="option in fi.options"
          >
            {{option.label}}
          </el-checkbox>
        </el-checkbox-group>
        <el-radio-group
          :placeholder="fi.placeholder"
          v-model="form_values[fi.variable]"
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
          v-model="form_values[fi.variable]"
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
          v-model="form_values[fi.variable]"
          v-else-if="fi.element == 'input' && isDatePickerType(fi.type)"
        />
        <el-input
          type="textarea"
          :placeholder="fi.placeholder"
          v-model="form_values[fi.variable]"
          v-else-if="fi.element == 'input' && fi.type == 'textarea'"
        />
        <el-input
          type="hidden"
          :placeholder="fi.placeholder"
          v-model="form_values[fi.variable]"
          v-else-if="fi.element == 'input' && fi.type == 'hidden'"
        />
        <el-input
          :placeholder="fi.placeholder"
          v-model="form_values[fi.variable]"
          v-else
        />
      </el-form-item>
    </el-form>

    <div v-else-if="show_summary">
      <div class="mb2 bt b--black-10"></div>
      <table>
        <tbody>
          <tr
            :key="key"
            v-for="(val, key) in form_values"
          >
            <td class="v-top pa1"><span class="fw6">{{key}}:</span></td>
            <td class="v-top pa1">{{ JSON.stringify(val, null, 2) }}</td>
          </tr>
        </tbody>
      </table>
      <div class="mt2 bt b--black-10"></div>
    </div>
  </div>
</template>

<script>
  import marked from 'marked'
  import { mapState } from 'vuex'
  import CodeEditor from './CodeEditor.vue'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number,
        required: true
      }
    },
    components: {
      CodeEditor
    },
    watch: {
      is_changed: {
        handler: 'onChange'
      },
      is_active: {
        handler: 'autoFocus',
        immediate: true
      },
      form_values: {
        handler: 'updateForm',
        immediate: true,
        deep: true
      }
    },
    data() {
      return {
        form_values: null,
        orig_form_values: null
      }
    },
    computed: {
      ...mapState({
        mode: state => state.builder.mode,
        active_prompt_idx: state => state.builder.active_prompt_idx
      }),
      builder__is_prompt_mode() {
        return this.mode == 'prompt'
      },
      is_active() {
        return this.index == this.active_prompt_idx
      },
      is_before_active() {
        return this.index < this.active_prompt_idx
      },
      is_changed() {
        return !_.isEqual(this.form_values, this.orig_form_values)
      },
      show_controls() {
        return !this.builder__is_prompt_mode || this.is_active
      },
      show_description() {
        return this.show_controls && this.description.length > 0
      },
      show_summary() {
        return this.builder__is_prompt_mode && this.is_before_active
      },
      title() {
        return _.get(this.item, 'title', 'Choose values')
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
      }
    },
    mounted() {
      this.autoFocus()
    },
    methods: {
      autoFocus() {
        if (!this.is_active)
          return

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
      getMarkdown(val) {
        return marked(val)
      },
      updateForm() {
        if (this.form_values === null) {
          var form_values = _.get(this.$store, 'state.builder.prompts[' + this.index + '].form_values')
          this.form_values = _.cloneDeep(form_values)
          this.orig_form_values = _.cloneDeep(form_values)
        } else {
          this.$store.commit('builder/UPDATE_ACTIVE_ITEM', { form_values: this.form_values })
        }
      },
      isDatePickerType(type) {
        return ['year','month','date','datetime','week','datetimerange','daterange'].indexOf(type) != -1
      },
      onChange(val) {
        if (!this.builder__is_prompt_mode && val === true) {
          this.$store.commit('builder/SET_ACTIVE_ITEM', this.index)
          this.$emit('item-change', this.form_values, this.index)
        }
      }
    }
  }
</script>
