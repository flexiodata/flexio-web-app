<template>
  <div class="flex flex-row items-center justify-center" :style="styl">
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
      bgColor: {
        type: String,
        default: '#555'
      }
    },
    computed: {
      styl() {
        return {
          color: this.color,
          backgroundColor: this.bgColor
        }
      }
    }
  }
</script>
