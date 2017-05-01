<template>
  <div class="overflow-y-auto" @scroll="onScroll">
    <div class="pa4 ml4 ml0-l mr4 bg-white ba b--white-box br2 tc" v-if="tasks.length == 0">
      <div class="lh-copy mid-gray mb3 i">There are no steps in this pipe.</div>
      <div class="mt3">
        <btn
          btn-md
          btn-primary
          class="ttu b"
          @click="insertNewTask(-1)"
        >
          Add a step
        </btn>
      </div>
    </div>
    <div class="pb3 ml2-m ml3-l" v-else>
      <pipe-builder-item
        v-for="(task, index) in tasks"
        :pipe-eid="pipeEid"
        :item="task"
        :index="index"
        :tasks="tasks"
        :active-prompt-idx="activePromptIdx"
        :first-prompt-idx="first_prompt_idx"
        :last-prompt-idx="last_prompt_idx"
        :is-prompting="isPrompting"
        :is-scrolling="is_scrolling"
        :active-process="activeProcess"
        :show-preview="show_all_previews"
        @insert-task="insertNewTask"
        @toggle-preview="togglePreview"
        @prompt-value-change="onPromptValueChange"
        @go-prev-prompt="$emit('go-prev-prompt')"
        @go-next-prompt="$emit('go-next-prompt')"
      ></pipe-builder-item>
    </div>
  </div>
</template>

<script>
  import { TASK_TYPE_INPUT, TASK_TYPE_OUTPUT } from '../constants/task-type'
  import Btn from './Btn.vue'
  import PipeBuilderItem from './PipeBuilderItem.vue'

  export default {
    props: {
      'pipe-eid': {
        type: String,
        required: true
      },
      'tasks': {
        type: Array,
        required: true
      },
      'active-prompt-idx': {
        type: Number,
        default: 0
      },
      'is-prompting': {
        type: Boolean,
        default: false
      },
      'active-process': {
        type: Object
      },
      'project-connections': {
        type: Array,
        default: () => { return [] }
      }
    },
    components: {
      Btn,
      PipeBuilderItem
    },
    data() {
      return {
        is_scrolling: false,
        show_all_previews: true
      }
    },
    computed: {
      first_prompt_idx() {
        return _.findIndex(this.tasks, { has_variable: true })
      },
      last_prompt_idx() {
        return _.findLastIndex(this.tasks, { has_variable: true })
      }
    },
    methods: {
      insertNewTask(idx) {
        var eid = this.pipeEid
        var attrs = {
          index: _.defaultTo(idx, -1),
          params: {}
        }
        this.$store.dispatch('createPipeTask', { eid, attrs })
      },

      togglePreview(show, toggle_all) {
        if (toggle_all)
          this.show_all_previews = show
      },

      onPromptValueChange(val, set_key) {
        this.$emit('prompt-value-change', val, set_key)
      },

      resetScroll: _.debounce(function() {
        this.is_scrolling = false
      }, 200),

      onScroll: _.debounce(function() {
        this.is_scrolling = true
        this.resetScroll()
      }, 50, { 'leading': true, 'trailing': false })
    }
  }
</script>

<style>
  .css-corner-title {
    top: 0.5rem;
    left: 0.625rem;
  }
</style>
