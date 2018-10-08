<template>
  <div class="flex flex-row items-center justify-center" :class="cls" :style="styl">
    <span class="material-icons">{{icon}}</span>
  </div>
</template>

<script>
  export default {
    props: {
      icon: {
        type: String,
        default: 'settings'
      },
      color: {
        type: String,
        default: '#fff'
      },
      bgColorCls: {
        type: String,
        default: ''
      },
      bgColor: {
        type: String
      }
    },
    computed: {
      cls() {
        var arr = []
        if (this.bgColorCls.length > 0) { arr.push(this.bgColorCls) }
        return arr.join(' ')
      },
      styl() {
        var bg_color
        if (this.bgColor || this.bgColorCls.length == 0) {
          bg_color = this.bgColor || '#555'
        }

        return {
          color: this.color,
          backgroundColor: bg_color
        }
      }
    }
  }
</script>
