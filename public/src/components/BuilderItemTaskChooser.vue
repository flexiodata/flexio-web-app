<template>
  <div class="flex flex-row flex-wrap items-center">
    <div
      style="min-width: 9rem"
      class="flex flex-column justify-center items-center"
      :class="'f6 fw6 ttu br2 ma1 pa3 pointer silver hover-blue bg-white hover-bg-near-white'"
      :key="item.op"
      v-for="(item, index) in items"
    >
      <i class="material-icons md-48">{{item.icon}}</i>
      <div class="mt2">{{item.name}}</div>
    </div>
  </div>
</template>

<script>
  const tasks = [
    {
      op: 'read',
      name: 'Read files',
      icon: 'input'
    },{
      op: 'request',
      name: 'Web Request',
      icon: 'http'
    },{
      op: 'execute',
      name: 'Execute code',
      icon: 'code'
    },{
      op: 'echo',
      name: 'Echo text',
      icon: 'settings_remote'
    }
  ]

  /*
  const tasks = [
    {
      op: 'connect',
      name: 'Connect',
      icon: 'repeat'
    },{
      op: 'convert',
      name: 'Convert',
      icon: 'settings'
    },{
      op: 'copy',
      name: 'Copy',
      icon: 'content_copy'
    },{
      op: 'create',
      name: 'Create',
      icon: 'photo_filter'
    },{
      op: 'delete',
      name: 'Delete',
      icon: 'delete'
    },{
      op: 'echo',
      name: 'Echo',
      icon: 'settings_remote'
    },{
      op: 'email',
      name: 'Email',
      icon: 'mail_outline'
    },{
      op: 'execute',
      name: 'Execute',
      icon: 'code'
    },{
      op: 'exit',
      name: 'Exit',
      icon: 'cancel'
    },{
      op: 'filter',
      name: 'Filter',
      icon: 'filter_list'
    },{
      op: 'list',
      name: 'List',
      icon: 'list'
    },{
      op: 'mkdir',
      name: 'Mkdir',
      icon: 'folder'
    },{
      op: 'read',
      name: 'Read',
      icon: 'input'
    },{
      op: 'render',
      name: 'Render',
      icon: 'photo'
    },{
      op: 'request',
      name: 'Request',
      icon: 'http'
    },{
      op: 'select',
      name: 'Select',
      icon: 'view_carousel'
    },{
      op: 'write',
      name: 'Write',
      icon: 'input'
    }
  ]
  */

  export default {
    props: {
      starter: {
        type: Boolean,
        default: false
      }
    },
    computed: {
      items() {
        if (this.starter) {
          return tasks
        } else {
          return tasks
        }
      }
    }
  }
</script>
