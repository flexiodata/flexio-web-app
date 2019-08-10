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
    <Spinner size="large" :message="progress_message" />
  </div>
</template>

<script>
  import marked from 'marked'
  import Spinner from 'vue-simple-spinner'

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
      Spinner
    },
    computed: {
      is_active() {
        return this.index == this.activeItemIdx
      },
      is_before_active() {
        return this.index < this.activeItemIdx
      },
      title() {
        return _.get(this.item, 'title', 'Processing')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      progress_message() {
        return _.get(this.item, 'progress_message', 'Settings things up...')
      },
      process_eid() {
        return _.get(this.item, 'process_eid', '')
      }
    }
  }
</script>
