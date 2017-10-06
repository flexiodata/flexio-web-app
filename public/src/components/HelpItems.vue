<template>
  <div class="flex flex-row flex-wrap items-center justify-center center">
    <div
      class="flex flex-column justify-center items-center f6 fw6 ttu br2 ma2 pa2 h4 w4 pointer moon-gray bg-white hover-blue css-help-item"
      @click="helpItemClick(item)"
      v-for="(item, index) in filtered_items"
    >
      <i class="material-icons md-48">{{item.icon}}</i>
      <div class="mt2">{{item.label}}</div>
    </div>
  </div>
</template>

<script>
  const help_items = [{
    id: 'quick-start',
    icon: 'launch',
    label: 'Quick Start',
    href: 'https://www.flex.io/docs/getting-started'
  },{
    id: 'api-docs',
    icon: 'help',
    label: 'API Docs',
    href: 'https://www.flex.io/docs/api'
  },{
    id: 'command-bar-docs',
    icon: 'help',
    label: 'Docs',
    href: 'https://www.flex.io/docs/web-app/#command-bar-operations'
  },{
    id: 'templates',
    icon: 'now_widgets',
    label: 'Templates',
    href: 'https://www.flex.io/templates'
  },{
    id: 'help',
    icon: 'chat',
    label: 'Get Help',
    action: 'open-intercom'
  }]

  export default {
    props: {
      'items': {
        type: Array,
        default: () => { return ['command-bar-docs', 'templates', 'help'] }
      },
      'help-message': {
        type: String,
        default: 'I have a question about how to use the pipe builder.'
      }
    },
    computed: {
      filtered_items() {
        return _.filter(help_items, (item) => {
          return _.includes(this.items, item.id)
        })
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
          if (!_.isNil(window.Intercom) && item.action == 'open-intercom')
          {
            window.Intercom('showNewMessage', this.helpMessage)
          }
           else
          {
            var mailto_link = 'mailto:support@flex.io?subject=' + this.helpMessage
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
