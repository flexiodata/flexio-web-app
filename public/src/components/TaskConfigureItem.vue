<template>
  <div>
    <task-configure-variable-item
      class="mb3"
      :item="v"
      :index="index"
      :task-item="item"
      @value-change="onValueChange"
      v-for="(v, index) in variables"
    ></task-configure-variable-item>

    <div class="flex flex-row items-center mt2" v-if="isActivePromptTask">
      <btn btn-md class="b ttu blue mr2" @click="$emit('go-prev-prompt')">Back</btn>
      <btn btn-md class="b ttu white bg-blue" @click="$emit('go-next-prompt')">Next</btn>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import Btn from './Btn.vue'
  import TaskConfigureVariableItem from './TaskConfigureVariableItem.vue'

  export default {
    props: {
      'item': {
        type: Object,
        required: true
      },
      'variables': {
        type: Array,
        required: true
      },
      'active-prompt-idx': {
        type: Number,
        default: 0
      },
      'is-active-prompt-task': {
        type: Boolean,
        default: false
      }
    },
    components: {
      Btn,
      TaskConfigureVariableItem
    },
    methods: {
      onValueChange(val, set_key) {
        this.$emit('prompt-value-change', val, set_key)
      }
    }
  }
</script>
