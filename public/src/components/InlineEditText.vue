<template>
  <div>
    <textarea
      class="pa1 ba b--black-10"
      autocomplete="off"
      rows="1"
      @keydown.esc="endEdit(false)"
      @keydown.enter="trySave"
      v-model="edit_value"
      v-if="editing"
      v-focus
    ></textarea>
    <div class="hide-child hover-black" v-else>
      <div class="dib" @click="editing = true" v-if="edit_value.length > 0">{{edit_value}}</div>
      <div class="dib pointer" :class="placeholderCls" @click="editing = true" v-else>{{placeholder}}</div>
      <button
        class="pa0 br1 hint--top child"
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
      'show-edit-button': {
        default: true,
        type: Boolean
      }
    },
    data() {
      return {
        edit_value: this.val,
        editing: false
      }
    },
    methods: {
      trySave() {
        this.$emit('save', { [this.inputKey]: this.val }, this)
      },
      endEdit(save) {
        if (save === false)
          this.edit_value = this.val

        this.editing = false
      }
    }
  }
</script>
