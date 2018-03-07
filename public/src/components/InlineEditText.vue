<template>
  <div>
    <textarea
      ref="textarea"
      autocomplete="off"
      rows="1"
      class="w-100 pa1 ba b--black-10 resize-none input-reset lh-title focus-outline-transparent"
      :class="isBlock ? 'db' : ''"
      :placeholder="placeholder"
      @keydown.esc="endEdit(false)"
      @keydown.enter.ctrl="save"
      @keydown.enter="onEnterKeydown"
      @blur="onBlur"
      v-model="edit_val"
      v-show="is_editing"
      v-deferred-focus
    ></textarea>
    <div
      class="flex flex-row items-center hide-child"
      :class="[
        allowEdit ? staticCls : '',
        allowEdit ? 'hover-black' : '',
        !showEditButton && tooltip.length > 0 ? tooltipCls : ''
      ]"
      :aria-label="!showEditButton && tooltip.length > 0 ? tooltip : null"
      v-if="!is_editing"
    >
      <div
        class="marked"
        :class="{ 'flex-fill': isBlock }"
        @click="startEdit('click')"
        v-html="markdown_val"
        v-if="isMarkdown && markdown_val.length > 0">
      </div>
      <div
        :class="isBlock ? 'flex-fill' : ''"
        @click="startEdit('click')"
        v-else-if="!isMarkdown && edit_val.length > 0"
      >
        {{edit_val}}
      </div>
      <div
        :class="[placeholderCls, isBlock ? 'flex-fill' : '']"
        @click="startEdit('click')"
        v-else
      >
        {{placeholder}}
      </div>
      <button
        class="br1 child"
        :class="[tooltipCls, isBlock ? 'ma1 pa1 self-start' : '']"
        :aria-label="tooltip"
        @click="startEdit('edit-button')"
        v-if="!is_editing && showEditButton && allowEdit"
      ><i class="db material-icons f6">edit</i>
      </button>
    </div>
    <transition name="slide-fade">
      <div class="flex flex-row items-start mt2" v-show="show_buttons">
        <div class="flex-fill"></div>
        <btn btn-sm class="ttu b blue mr2" @click="endEdit(false)">Cancel</btn>
        <btn btn-sm class="ttu b white bg-blue" @click="save">Save Changes</btn>
      </div>
    </transition>
  </div>
</template>

<script>
  import autosize from 'autosize'
  import marked from 'marked'
  import Btn from './Btn.vue'

  export default {
    props: {
      'input-key': {
        type: String,
        required: true
      },
      'val': {
        type: String,
        default: ''
      },
      'placeholder': {
        type: String,
        default: 'Click here to edit'
      },
      'placeholder-cls': {
        type: String,
        default: ''
      },
      'tooltip': {
        type: String,
        default: 'Click to edit'
      },
      'tooltip-cls': {
        type: String,
        default: 'hint--top'
      },
      'show-edit-button': {
        type: Boolean,
        default: true
      },
      'show-save-cancel-buttons': {
        type: Boolean,
        default: false
      },
      'static-cls': {
        type: String,
        default: ''
      },
      'autosize': {
        type: Boolean,
        default: true
      },
      'allow-edit': {
        type: Boolean,
        default: true
      },
      'edit-on': {
        type: Array,
        default: () => { return ['click', 'edit-button'] }
      },
      'is-markdown': {
        type: Boolean,
        default: false
      },
      'is-multiline': {
        type: Boolean,
        default: false
      },
      'is-block': {
        type: Boolean,
        default: false
      }
    },
    components: {
      Btn
    },
    watch: {
      val: function(val, old_val) {
        this.before_edit_val = val
        this.edit_val = val
      },
      edit_val: function(val, old_val) {
        this.$emit('change', val, old_val)
      }
    },
    data() {
      return {
        before_edit_val: this.val,
        edit_val: this.val,
        is_editing: false,
        // this allows us to let the hide transition finish
        // before we swap out the edit text for the static text
        keep_buttons_alive: false,
        autosize_initialized: false
      }
    },
    computed: {
      markdown_val() {
        return marked(this.edit_val)
      },
      show_buttons() {
        return this.allowEdit && this.is_editing && this.keep_buttons_alive && (this.showSaveCancelButtons || this.isMarkdown)
      }
    },
    mounted() {
      if (this.autosize)
      {
        autosize(this.$refs['textarea'])
        this.autosize_initialized = true
      }
    },
    beforeDestroy() {
      if (this.autosize_initialized)
        autosize.destroy(this.$refs['textarea'])
    },
    methods: {
      save() {
        this.$emit('save', { [this.inputKey]: this.edit_val }, this)
      },
      startEdit(from) {
        if (!this.allowEdit)
          return

        // allow ability to choose how an edit is started
        if (!_.includes(this.editOn, from))
          return

        this.is_editing = true
        this.keep_buttons_alive = true

        this.$nextTick(() => {
          var ta = this.$refs['textarea']
          if (this.autosize_initialized)
            autosize.update(ta)
          ta.focus()
        })
      },
      endEdit(save) {
        this.keep_buttons_alive = false

        if (save === false)
          this.edit_val = this.before_edit_val
           else
          this.before_edit_val = this.edit_val

        // allow time for transition to finish
        if (this.showSaveCancelButtons || this.isMarkdown)
          setTimeout(() => { this.is_editing = false }, 450)
           else
          this.is_editing = false

        this.$emit('cancel')
      },
      onEnterKeydown(evt) {
        if (!this.isMarkdown && !this.isMultiline)
        {
          this.save()
          evt.preventDefault()
        }
      },
      onBlur() {
        if (this.is_editing && !this.show_buttons)
          this.save()
      }
    }
  }
</script>
