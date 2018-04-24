<template>
  <div>
    <div class="tl pb3">
      <h3 class="fw6 f3 mid-gray mt0 mb2">Choose values</h3>
    </div>
    <el-form
      :model="form_values"
      :label-position="label_position"
      :label-width="label_width"
      v-show="is_active"
    >
      <el-form-item
        v-for="fi in form_items"
        :label="fi.label"
        :prop="fi.variable"
      >
        <el-switch
          v-model="form_values[fi.variable]"
          v-if="fi.element == 'switch'"
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
            :name="fi.variable"
            v-for="option in fi.options"
          >
            {{option.label}}
          </el-checkbox>
        </el-checkbox-group>
        <el-select
          :placeholder="fi.placeholder"
          v-model="form_values[fi.variable]"
          v-else-if="fi.element == 'select'"
        >
          <el-option
            :label="option.label"
            :value="option.value"
            v-for="option in fi.options"
          />
        </el-select>
        <el-date-picker
          type="date"
          :placeholder="fi.placeholder"
          v-model="form_values[fi.variable]"
          v-else-if="fi.element == 'input' && fi.type == 'date'"
        />
        <el-input
          type="textarea"
          :placeholder="fi.placeholder"
          v-model="form_values[fi.variable]"
          v-else-if="fi.element == 'input' && fi.type == 'textarea'"
        />
        <el-input
          :placeholder="fi.placeholder"
          v-model="form_values[fi.variable]"
          v-else
        />
      </el-form-item>
    </el-form>
    <div v-show="is_before_active">
      TODO
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

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
    watch: {
      form_values: {
        handler: 'updateForm',
        immediate: true,
        deep: true
      }
    },
    data() {
      return {
        form_values: null
      }
    },
    computed: {
      ...mapState({
        active_prompt_idx: state => state.builder.active_prompt_idx
      }),
      is_active() {
        return this.index == this.active_prompt_idx
      },
      is_before_active() {
        return this.index < this.active_prompt_idx
      },
      prompt() {
        return _.get(this.$store, 'state.builder.prompts[' + this.index + ']')
      },
      label_position() {
        return _.get(this.prompt, 'label_position', 'top')
      },
      label_width() {
        return _.get(this.prompt, 'label_width', '10rem')
      },
      form_items() {
        return _.get(this.prompt, 'form_items', [])
      }
    },
    methods: {
      updateForm() {
        if (this.form_values === null) {
          var form_values = _.get(this.$store, 'state.builder.prompts[' + this.index + '].form_values')
          this.form_values = _.cloneDeep(form_values)
        } else {
          this.$store.commit('BUILDER__UPDATE_ACTIVE_ITEM', { form_values: this.form_values })
        }
      }
    }
  }
</script>
