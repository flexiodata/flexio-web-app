<template>
  <div class="flex flex-row flex-wrap items-center justify-center center">
    <div
      class="flex flex-column justify-center items-center f6 fw6 ttu br2 mh2 pa2 h4 w4 pointer moon-gray bg-white hover-blue css-help-item"
      @click="helpItemClick(item)"
      v-for="(item, index) in help_items"
    >
      <i class="material-icons md-48">{{item.icon}}</i>
      <div class="mt2">{{item.label}}</div>
    </div>
  </div>
</template>

<script>
  export default {
    computed: {
      help_items() {
        return [{
          id: 'docs',
          icon: 'help',
          label: 'Docs',
          href: 'https://www.flex.io/docs/web-app/#command-bar-operations'
        },{
          id: 'templates',
          icon: 'now_widgets',
          label: 'Templates',
          href: 'https://www.flex.io/templates/'
        },{
          id: 'chat',
          icon: 'chat',
          label: 'Get Help',
          action: 'open-intercom'
        }]
      }
    },
    methods: {
      helpItemClick(item) {
        if (_.isString(item.href))
        {
          window.open(item.href, '_blank')
        }
         else
        {
          var msg = 'I have a question about how to use the pipe builder.'

          if (!_.isNil(window.Intercom) && item.action == 'open-intercom')
          {
            window.Intercom('showNewMessage', msg)
          }
           else
          {
            var mailto_link = 'mailto:support@flex.io?subject='+msg
            window.open(mailto_link)
          }
        }
      }
    }
  }
</script>

<style lang="less">
  .css-help-item {
    box-shadow: inset 1px 1px 0 rgba(0,0,0,0.05), inset -1px 0 0 rgba(0,0,0,0.05), 0 2px 1px -1px rgba(0,0,0,0.25);
  }
</style>
