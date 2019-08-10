<template>
  <div>
    <div class="tl pb3">
      <h3 class="fw6 f3 mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 marked"
      v-html="description"
      v-show="description.length > 0"
    >
    </div>
    <ProcessContent
      :process-eid="process_eid"
      v-if="process_eid.length > 0"
    />
  </div>
</template>

<script>
  import marked from 'marked'
  import ProcessContent from '@/components/ProcessContent'

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
      ProcessContent
    },
    computed: {
      is_active() {
        return this.index == this.activeItemIdx
      },
      is_before_active() {
        return this.index < this.activeItemIdx
      },
      title() {
        return _.get(this.item, 'title', 'Results')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      process_eid() {
        return _.get(this.item, 'process_eid', '')
      }
    }
  }
</script>
