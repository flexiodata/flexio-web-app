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

    <div class="flex flex-row flex-wrap items-stretch">
      <div
        class="step-chooser-item hint--top hint--medium-large"
        :key="item.op"
        :aria-label="item.description"
        @click="itemClick(item)"
        v-for="(item, index) in items"
      >
        <div class="flex flex-column justify-center items-center">
          <i class="material-icons md-72">{{item.icon}}</i>
          <div class="step-chooser-item-title">{{item.name}}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import marked from 'marked'
  import * as tasks from '@/constants/task-info'

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
        default: 'Insert new step'
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
        this.$emit('task-chooser-select-task', _.omit(item, ['name', 'icon', 'bg_color']), this.index)
      }
    }
  }
</script>

<style lang="stylus" scoped>
  @import '../stylesheets/variables.styl'

  .step-chooser-item
    border: 2px dashed rgba(0,0,0,0.075)
    border-radius: 4px
    color: #bbb
    cursor: pointer
    margin: 2%
    min-width: calc( (100% / 3) - (4%) )
    max-width: calc( (100% / 3) - (4%) )
    padding: 32px 32px 42px
    transition: all 0.15s ease

    &:hover
      background-color: $nearer-white
      border: 2px solid $blue
      color: $blue

  .step-chooser-item-title
    margin-top: 1rem
    font-size: 1rem
    font-weight: 600
    text-transform: uppercase
</style>
