<template>
  <div>
    <div class="tl pb3">
      <h3 class="fw6 f3 mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 marked"
      v-html="description"
      v-show="show_description"
    >
    </div>
    <PipeSchedulePanel
      :pipe="orig_pipe"
      :show-header="false"
      :show-footer="false"
      @change="updatePipe"
    />
  </div>
</template>

<script>
  import marked from 'marked'
  import PipeSchedulePanel from './PipeSchedulePanel.vue'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number,
        required: true
      },
      activeItemIdx: {
        type: Number,
        required: true
      },
      isNextAllowed: {
        type: Boolean,
        required: false
      }
    },
    components: {
      PipeSchedulePanel
    },
    watch: {
      edit_pipe: {
        handler: 'emitItemChange',
        deep: true
      }
    },
    data() {
      return {
        orig_pipe: {},
        edit_pipe: {}
      }
    },
    computed: {
      is_active() {
        return this.index == this.activeItemIdx
      },
      is_before_active() {
        return this.index < this.activeItemIdx
      },
      show_description() {
        return this.description.length > 0
      },
      title() {
        return _.get(this.item, 'title', 'Schedule your pipe')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      }
    },
    methods: {
      updatePipe(pipe) {
        this.edit_pipe = _.assign({}, pipe)
      },
      emitItemChange() {
        this.$emit('item-change', { pipe: this.edit_pipe }, this.index)
      }
    }
  }
</script>
