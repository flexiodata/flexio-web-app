<template>
  <div>
    <h3
      class="fw6 f3 mt0"
      v-if="title.length > 0"
    >
      {{title}}
    </h3>
    <div v-if="is_active">
      <Spinner size="large" :message="spinner_msg" v-if="!isNextAllowed" />
      <div class="tc f3" v-else>Done!</div>
    </div>
    <div
      class="marked"
      v-html="description"
      v-if="is_active"
    ></div>
  </div>
</template>

<script>
  import marked from 'marked'
  import Spinner from 'vue-simple-spinner'
  import { mapState } from 'vuex'

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
      Spinner,
    },
    watch: {
      is_active: {
        handler: 'updateAllowNext',
        immediate: true
      },
      process_result: {
        handler: 'updateAllowNext',
        immediate: true
      }
    },
    computed: {
      ...mapState({
        process_result: state => state.builder.process_result
      }),
      is_active() {
        return this.index == this.activeItemIdx
      },
      is_before_active() {
        return this.index < this.activeItemIdx
      },
      title() {
        return _.get(this.item, 'title', 'Running Pipe')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      spinner_msg() {
        return _.get(this.item, 'spinner_message', 'Running...')
      }
    },
    methods: {
      updateAllowNext() {
        var allow = !_.isEmpty(this.process_result)
        this.$emit('update:isNextAllowed', allow)
      }
    }
  }
</script>
