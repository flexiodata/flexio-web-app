<template>
  <div>
    <div
      class="tl pb3"
      v-if="showTitle && title.length > 0"
    >
      <h3 class="fw6 f3 mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 marked"
      v-html="marked_description"
      v-show="show_description"
    >
    </div>

    <div class="flex flex-row flex-wrap items-center nl2">
      <div
        class="br2 ma2 pv3 w4 pointer silver hover-blue ba css-list-item hint--top hint--medium-large"
        :key="item.op"
        :aria-label="item.tooltip"
        @click="itemClick(item)"
        v-for="(item, index) in items"
      >
        <div class="flex flex-column justify-center items-center">
          <i class="material-icons md-48">{{item.icon}}</i>
          <div class="mt2 f6 fw6 ttu">{{item.name}}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import marked from 'marked'

  const tasks = [
    {
      op: 'execute',
      name: 'Execute',
      icon: 'code',
      tooltip: 'Execute an inline or remote Python or Node.js function.'
    },{
      op: 'request',
      name: 'Request',
      icon: 'http',
      tooltip: 'Make an HTTP request; output will be sent to the next step of your pipe.'
    },{
      op: 'convert',
      name: 'Convert',
      icon: 'settings',
      tooltip: 'Convert the output of the previous step in your pipe to a different format (e.g., JSON to CSV)'
    },{
      op: 'email',
      name: 'Email',
      icon: 'mail_outline',
      tooltip: 'Email a notification, variable and/or attachments using Flex.io or your own SMTP email service.'
    },{
      op: 'echo',
      name: 'Echo',
      icon: 'settings_remote',
      tooltip: 'Echo a message or variable to the next step in your pipe.'
    },{
      op: 'oauth',
      name: 'OAuth',
      icon: 'security',
      tooltip: 'Set up an OAuth-type connection and output an execute step that returns your OAuth token.'
    },{
      op: 'connect',
      name: 'Connect',
      icon: 'repeat',
      tooltip: 'Create or add an external connection (OAuth, token, etc.) to call from your function.'
    },{
      op: 'read',
      name: 'Read',
      icon: 'input',
      tooltip: 'Read files from storage-type connections (e.g., Dropbox, MySQL, GitHub).'
    },{
      op: 'write',
      name: 'Write',
      icon: 'input',
      tooltip: 'Write files to storage-type connections (e.g., Dropbox, MySQL, GitHub).'
    },{
      op: 'copy',
      name: 'Copy',
      icon: 'content_copy',
      tooltip: 'Copy files or directories from one storage-type connection to a different storage-type connection.'
    }
  ]

  /*
  const tasks = [
    {
      op: 'create',
      name: 'Create',
      icon: 'photo_filter'
    },{
      op: 'delete',
      name: 'Delete',
      icon: 'delete'
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
      op: 'render',
      name: 'Render',
      icon: 'photo'
    },{
      op: 'select',
      name: 'Select',
      icon: 'view_carousel'
    }
  ]
  */

  export default {
    props: {
      item: {
        type: Object
      },
      index: {
        type: Number
      },
      showTitle: {
        type: Boolean,
        default: true
      },
      title: {
        type: String,
        default: 'Insert new task'
      },
      description: {
        type: String,
        default: ''
      },
      ops: {
        type: Array,
        required: false
      }
    },
    data() {
      return {
        tasks
      }
    },
    computed: {
      show_description() {
        return this.marked_description.length > 0
      },
      marked_description() {
        return marked(this.description)
      },
      items() {
        return _.isArray(this.ops) ? _.filter(this.tasks, (t) => { return this.ops.indexOf(t.op) != -1 }) : this.tasks
      }
    },
    methods: {
      itemClick(item) {
        this.$emit('task-chooser-select-task', _.omit(item, ['name', 'icon']), this.index)
      }
    }
  }
</script>
