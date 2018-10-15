<template>
  <div>
    <div
      class="tl pb3"
      v-if="showTitle && title.length > 0"
    >
      <h3 class="fw6 f3 mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 marked"
      v-html="marked_description"
      v-show="show_description"
    >
    </div>

    <div class="flex flex-row flex-wrap items-center nl2" style="max-width: 40rem">
      <div
        class="br2 ma2 pv3 w4 pointer silver hover-blue ba css-list-item hint--top hint--medium-large"
        :key="item.op"
        :aria-label="item.description"
        @click="itemClick(item)"
        v-for="(item, index) in items"
      >
        <div class="flex flex-column justify-center items-center">
          <i class="material-icons md-48">{{item.icon}}</i>
          <div class="mt2 f6 fw6 ttu">{{item.name}}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import marked from 'marked'
  import * as tasks from '../constants/task-info'

  export default {
    props: {
      item: {
        type: Object
      },
      index: {
        type: Number
      },
      showTitle: {
        type: Boolean,
        default: true
      },
      title: {
        type: String,
        default: 'Insert new task'
      },
      description: {
        type: String,
        default: ''
      },
      ops: {
        type: Array,
        required: false
      }
    },
    data() {
      return {
        tasks
      }
    },
    computed: {
      show_description() {
        return this.marked_description.length > 0
      },
      marked_description() {
        return marked(this.description)
      },
      items() {
        return _.isArray(this.ops) ? _.filter(this.tasks, (t) => { return this.ops.indexOf(t.op) != -1 }) : this.tasks
      }
    },
    methods: {
      itemClick(item) {
        this.$emit('task-chooser-select-task', _.omit(item, ['name', 'icon']), this.index)
      }
    }
  }
</script>
