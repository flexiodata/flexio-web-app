<template>
  <div class="relative">
    <el-switch
      :value="value"
      :width="text_width"
      @input="onChange"
      v-bind="$attrs"
      v-if="text_width"
    />
    <transition name="el-zoom-in-center" mode="out-in">
      <span
        class="absolute top-0 ttu b pointer"
        :style="text_style"
        @click.stop="onChange"
        v-bind:key="value"
      >
        {{value ? activeLabel : inactiveLabel}}
      </span>
    </transition>
    <div class="absolute no-pointer-events dib ttu b invisible" style="left: 1000px; top: 1000px" ref="active-text">{{activeLabel}}</div>
    <div class="absolute no-pointer-events dib ttu b invisible" style="left: 1000px; top: 1000px" ref="inactive-text">{{inactiveLabel}}</div>
  </div>
</template>

<script>
  export default {
    props: {
      value: {
        type: Boolean,
        required: true
      },
      activeLabel: {
        type: String,
        default: 'On'
      },
      inactiveLabel: {
        type: String,
        default: 'Off'
      },
      activeLabelColor: {
        type: String,
        default: '#fff'
      },
      inactiveLabelColor: {
        type: String,
        default: '#fff'
      }
    },
    watch: {
      activeLabel: {
        handler: 'measureText',
        immediate: true
      },
      inactiveLabel: {
        handler: 'measureText',
        immediate: true
      }
    },
    data() {
      return {
        text_width: 0
      }
    },
    computed: {
      text_style() {
        var styles = []
        styles.push(this.value ? 'color: ' + this.activeLabelColor : 'color: ' + this.inactiveLabelColor)
        styles.push('top: 3px')
        styles.push(this.value ? 'left: 8px' : 'right: 8px')
        styles.push('font-size: 14px')
        return styles.join('; ')
      }
    },
    methods: {
      onChange() {
        this.$emit('input', !this.value)
      },
      measureText() {
        this.$nextTick(() => {
          var w1 = this.$refs['active-text'].clientWidth
          var w2 = this.$refs['inactive-text'].clientWidth
          this.text_width = Math.max(w1, w2) + 26
        })
      }
    }
  }
</script>
