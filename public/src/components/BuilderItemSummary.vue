<template>
  <div>
    <div class="tl pb3" v-show="is_after_active">
      <h3 class="fw6 f3 mt0 mb2">{{title}}</h3>
    </div>
    <div class="tc" v-show="is_active || is_before_active">
      <div class="dib mv3 pv1">
        <i class="el-icon-success v-mid f1" style="color: #13ce66"></i>
      </div>
      <div
        class="pb3 marked"
        v-html="description"
        v-show="show_description"
      >
      </div>
      <div>
        <el-button
          class="ttu fw6"
          type="primary"
          @click="onActionClick"
        >
          {{action_btn_label}}
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
  import marked from 'marked'
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
        required: true
      }
    },
    watch: {
      is_active() {
        if (this.is_active) {
          this.$emit('create-pipe')
        }
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
      is_after_active() {
        return this.index > this.activeItemIdx
      },
      show_description() {
        return this.description.length > 0
      },
      title() {
        return _.get(this.item, 'title', 'Success')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      action_btn_label() {
        return _.get(this.item, 'action_button.label', 'View pipe')
      },
      action_btn_href() {
        return _.get(this.item, 'action_button.href', '')
      }
    },
    methods: {
      openPipe() {
        this.$emit('open-pipe')
      },
      onActionClick() {
        var href = this.action_btn_href

        if (href.length > 0) {
          if (href.indexOf('%%') == 0) {
            href = href.replace('%%', '')
            href = href.replace('%%', '')
            href = _.get(this, href, '#')
          }
          window.location.href = href
        } else {
          this.openPipe()
        }
      }
    }
  }
</script>
