<template>
  <div>
    <div
      class="tl pb3"
      v-if="showTitle"
    >
      <h3 class="fw6 f3 mid-gray mt0 mb2" v-if="title.length > 0">{{title}}</h3>
    </div>
    <p class="mt0" v-if="message.length > 0">{{message}}</p>
    <div class="flex flex-row flex-wrap items-center nl1">
      <div
        class="flex flex-column justify-center items-center"
        :class="'f6 fw6 ttu br2 ma1 pv3 w4 pointer silver hover-blue bg-near-white darken-05'"
        :key="item.op"
        @click="itemClick(item)"
        v-for="(item, index) in items"
      >
        <i class="material-icons md-48">{{item.icon}}</i>
        <div class="mt2">{{item.name}}</div>
      </div>
    </div>
  </div>
</template>

<script>
  const tasks = [
    {
      op: 'read',
      name: 'Read',
      icon: 'input'
    },{
      op: 'echo',
      name: 'Echo',
      icon: 'settings_remote'
    },{
      op: 'execute',
      name: 'Execute',
      icon: 'code'
    },{
      op: 'email',
      name: 'Email',
      icon: 'mail_outline'
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
      title: {
        type: String,
        default: ''
      },
      message: {
        type: String,
        default: ''
      },
      showTitle: {
        type: Boolean,
        default: false
      }
    },
    computed: {
      items() {
        return tasks
      }
    },
    methods: {
      itemClick(item) {
        this.$emit('task-chooser-item-click', item)
      }
    }
  }
</script>
