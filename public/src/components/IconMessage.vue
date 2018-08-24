<template>
  <div>
    <div class="dib mb3 pv1">
      <i :class="icon_cls" :style="icon_style"></i>
    </div>
    <h3 class="fw6 f3 mt0 mb4" v-if="title.length > 0">{{title}}</h3>
    <p class="mv4 lh-copy center mw7"><slot></slot></p>
  </div>
</template>

<script>
  export default {
    props: {
      icon: {
        type: String,
        default: 'el-icon-warning'
      },
      iconColor: {
        type: String,
        default: '#ec7713'
      },
      title: {
        type: String,
        default: ''
      }
    },
    computed: {
      icon_cls() {
        return this.icon + ' v-mid f1'
      },
      icon_style() {
        return 'color: ' + this.iconColor
      }
    }
  }
</script>
