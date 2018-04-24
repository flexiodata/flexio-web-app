<template>
  <div>
    <div class="tl pb3">
      <h3 class="fw6 f3 mid-gray mt0 mb2">Choose value</h3>
    </div>
    <el-form
      ref="form"
      :model="form"
      :label-position="label_position"
      :label-width="label_width"
      v-show="is_active"
    >
      <el-form-item
        v-for="fi in form_items"
        :label="fi.label"
      >
        <el-date-picker
          type="date"
          :placeholder="fi.placeholder"
          v-model="form.form_input1"
          v-if="fi.element == 'input' && fi.type == 'date'"
        />
        <el-input
          type="textarea"
          :placeholder="fi.placeholder"
          v-model="form.form_input2"
          v-else-if="fi.element == 'input' && fi.type == 'textarea'"
        />
        <el-input
          :placeholder="fi.placeholder"
          v-model="form.form_input3"
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
      },
      form: {
        get() {
          return _.get(this.$store, 'state.builder.prompts[' + this.index + '].form')
        },
        set(form) {
          debugger
          this.$store.commit('BUILDER__UPDATE_ACTIVE_ITEM', { form })
        }
      }
    }
  }
</script>
