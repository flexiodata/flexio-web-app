<template>
  <div>
    <div class="tl pb3" v-show="is_after_active">
      <h3 class="fw6 f3 mt0 mb2">Summary</h3>
    </div>
    <div class="tc" v-show="is_active || is_before_active">
      <div class="dib mb2">
        <i class="el-icon-success v-mid dark-green f2"></i>
      </div>
      <h3 class="fw6 f3 mt0 mb2">You're all set!</h3>
      <p class="mv4 f4">Your pipe is configured and ready to be run.</p>
      <div class="mt4">
        <el-button
          class="ttu b"
          type="primary"
          size="large"
          @click="openPipe"
        >
          View pipe
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
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
      is_active() {
        return this.index == this.activeItemIdx
      },
      is_before_active() {
        return this.index < this.activeItemIdx
      },
      is_after_active() {
        return this.index > this.activeItemIdx
      }
    },
    methods: {
      openPipe() {
        this.$emit('open-pipe')
      }
    }
  }
</script>
