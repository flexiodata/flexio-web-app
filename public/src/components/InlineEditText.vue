<template>
  <div>
    <textarea
      ref="textarea"
      class="w-100 pa1 ba b--black-10 resize-none"
      autocomplete="off"
      rows="1"
      @keydown.esc="endEdit(false)"
      @keydown.enter="save"
      @blur="save"
      v-model="edit_val"
      v-show="editing"
      v-focus
    ></textarea>
    <div class="flex flex-row items-center hide-child hover-black" v-if="!editing">
      <div @click="startEdit" v-if="edit_val.length > 0">{{edit_val}}</div>
      <div :class="placeholderCls" @click="startEdit" v-else>{{placeholder}}</div>
      <button
        class="ml1 pa0 br1 child"
        :class="editButtonTooltipCls"
        :aria-label="editButtonLabel"
        @click="startEdit"
        v-if="!editing && showEditButton"
      ><i class="db material-icons f6">edit</i>
      </button>
    </div>
  </div>
</template>

<script>
  import autosize from 'autosize'

  export default {
    props: {
      'input-key': {
        required: true,
        type: String
      },
      'val': {
        default: '',
        type: String
      },
      'placeholder': {
        default: 'Click here to edit',
        type: String
      },
      'placeholder-cls': {
        default: '',
        type: String
      },
      'edit-button-label': {
        default: 'Click to edit',
        type: String
      },
      'edit-button-tooltip-cls': {
        default: 'hint--top',
        type: String
      },
      'show-edit-button': {
        default: true,
        type: Boolean
      },
      'autosize': {
        default: true,
        type: Boolean
      }
    },
    data() {
      return {
        edit_val: this.val,
        editing: false,
        autosize_initialized: false
      }
    },
    watch: {
      val(val, old_val) {
        this.edit_val = val
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
        this.editing = true

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

        this.editing = false
      }
    }
  }
</script>
