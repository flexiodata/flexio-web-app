<template>
  <div>
    <textarea
      ref="textarea"
      class="w-100 pa1 ba b--black-30 resize-none input-reset lh-title focus-outline-transparent"
      autocomplete="off"
      rows="1"
      @keydown.esc="endEdit(false)"
      @keydown.enter="save"
      @blur="save"
      v-model="edit_val"
      v-show="is_editing"
      v-deferred-focus
    ></textarea>
    <div class="flex flex-row items-center hide-child hover-black" v-if="!is_editing">
      <div @click="startEdit" v-html="markdown_val" v-if="isMarkdown && markdown_val.length > 0"></div>
      <div @click="startEdit" v-else-if="!isMarkdown && edit_val.length > 0">{{edit_val}}</div>
      <div :class="placeholderCls" @click="startEdit" v-else>{{placeholder}}</div>
      <button
        class="ml1 pa0 br1 child"
        :class="editButtonTooltipCls"
        :aria-label="editButtonLabel"
        @click="startEdit"
        v-if="!is_editing && showEditButton"
      ><i class="db material-icons f6">edit</i>
      </button>
    </div>
  </div>
</template>

<script>
  import autosize from 'autosize'
  import marked from 'marked'

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
      'edit-button-label': {
        type: String,
        default: 'Click to edit'
      },
      'edit-button-tooltip-cls': {
        type: String,
        default: 'hint--top'
      },
      'show-edit-button': {
        type: Boolean,
        default: true
      },
      'autosize': {
        type: Boolean,
        default: true
      },
      'is-markdown': {
        type: Boolean,
        default: false
      }
    },
    watch: {
      val: function(val, old_val) {
        this.edit_val = val
      }
    },
    data() {
      return {
        edit_val: this.val,
        is_editing: false,
        autosize_initialized: false
      }
    },
    computed: {
      markdown_val() {
        // this eliminates the need to for the markdown to be compiled with every keystroke
        if (this.is_editing)
          return ''

        return marked(this.edit_val)
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
      startEdit() {
        this.is_editing = true

        this.$nextTick(() => {
          var ta = this.$refs['textarea']
          if (this.autosize_initialized)
            autosize.update(ta)
          ta.focus()
        })
      },
      endEdit(save) {
        if (save === false)
          this.edit_val = this.val

        this.is_editing = false
      }
    }
  }
</script>
