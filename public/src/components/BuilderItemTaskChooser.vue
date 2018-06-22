<template>
  <div>
    <div
      class="tl pb3"
      v-if="showTitle && title.length > 0"
    >
      <h3 class="fw6 f3 mid-gray mt0 mb2">{{title}}</h3>
    </div>
    <div
      class="pb3 mid-gray marked"
      v-html="marked_description"
      v-show="show_description"
    >
    </div>

    <div class="flex flex-row flex-wrap items-center nl2">
      <div
        class="flex flex-column justify-center items-center"
        :class="'f6 fw6 ttu br2 ma2 pv3 w4 pointer silver hover-blue bg-near-white darken-05'"
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
  import marked from 'marked'

  const tasks = [
    {
      op: 'connect',
      name: 'Connect',
      icon: 'repeat'
    },{
      op: 'request',
      name: 'Request',
      icon: 'http'
    },{
      op: 'read',
      name: 'Read',
      icon: 'input'
    },{
      op: 'write',
      name: 'Write',
      icon: 'input'
    },{
      op: 'copy',
      name: 'Copy',
      icon: 'content_copy'
    },{
      op: 'convert',
      name: 'Convert',
      icon: 'settings'
    },{
      op: 'execute',
      name: 'Execute',
      icon: 'code'
    },{
      op: 'echo',
      name: 'Echo',
      icon: 'settings_remote'
    },{
      op: 'email',
      name: 'Email',
      icon: 'mail_outline'
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
        default: false
      },
      title: {
        type: String,
        default: ''
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
        return _.isArray(this.ops) ? _.filter(this.tasks, (t) => { return this.ops.indexOf(t.op) != -1 }) : tasks
      }
    },
    methods: {
      itemClick(item) {
        this.$emit('task-chooser-select-task', _.omit(item, ['name', 'icon']), this.index)
      }
    }
  }
</script>
