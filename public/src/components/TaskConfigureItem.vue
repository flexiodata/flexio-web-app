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

    <div class="flex flex-row items-center mt3" v-if="isActivePromptTask">
      <div class="flex-fill"></div>
      <btn btn-md class="b ttu blue mr2" @click="$emit('go-prev-prompt')" v-if="index != firstPromptIdx">Back</btn>
      <btn btn-md class="b ttu white bg-blue" @click="$emit('go-next-prompt')" v-if="index != lastPromptIdx">Next</btn>
      <btn btn-md class="b ttu white bg-blue mr2" @click="$emit('run-once-with-values')" v-if="index == lastPromptIdx">Run Once With These Values</btn>
      <btn btn-md class="b ttu white bg-blue" @click="$emit('save-values-and-run')" v-if="index == lastPromptIdx">Save Values & Run</btn>
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
      'index': {
        type: Number,
        required: true
      },
      'variables': {
        type: Array,
        required: true
      },
      'active-prompt-idx': {
        type: Number,
        required: true
      },
      'first-prompt-idx': {
        type: Number,
        required: true
      },
      'last-prompt-idx': {
        type: Number,
        required: true
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
      onValueChange(val, variable_set_key) {
        this.$emit('prompt-value-change', val, variable_set_key)
      }
    }
  }
</script>
