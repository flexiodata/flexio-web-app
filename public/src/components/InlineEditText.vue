<template>
  <div>
    <textarea
      class="w-100 pa1 ba b--black-10"
      autocomplete="off"
      rows="1"
      @keydown.esc="endEdit(false)"
      @keydown.enter="save"
      @blur="save"
      v-model="edit_val"
      v-if="editing"
      v-focus
    ></textarea>
    <div class="flex flex-row items-center hide-child hover-black" v-else>
      <div @click="editing = true" v-if="edit_val.length > 0">{{edit_val}}</div>
      <div :class="placeholderCls" @click="editing = true" v-else>{{placeholder}}</div>
      <button
        class="ml1 pa0 br1 child"
        :class="editButtonTooltipCls"
        :aria-label="editButtonLabel"
        @click="editing = true"
        v-if="!editing && showEditButton"
      ><i class="db material-icons f6">edit</i>
      </button>
    </div>
  </div>
</template>

<script>
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
      }
    },
    data() {
      return {
        edit_val: this.val,
        editing: false
      }
    },
    watch: {
      val(val, old_val) {
        this.edit_val = val
      }
    },
    methods: {
      save() {
        this.$emit('save', { [this.inputKey]: this.edit_val }, this)
      },
      endEdit(save) {
        if (save === false)
          this.edit_val = this.val

        this.editing = false
      }
    }
  }
</script>
