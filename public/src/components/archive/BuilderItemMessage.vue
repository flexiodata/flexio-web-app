<template>
  <div>
    <div
      class="flex flex-row items-center justify-center"
      style="margin-bottom: 1.5rem"
      v-if="connections.length > 0"
    >
      <template
        v-for="type in connections"
      >
        <i
          class="material-icons moon-gray ph2"
          v-if="type == 'separator'"
        >
          play_arrow
        </i>
        <ServiceIcon
          class="br1 square-4"
          :type="type"
          v-else
        />
      </template>
    </div>
    <h3
      class="fw6 f3 mt0"
      v-if="title.length > 0"
    >
      {{title}}
    </h3>
    <div
      class="marked"
      v-html="description"
      v-if="is_active"
    ></div>
  </div>
</template>

<script>
  import marked from 'marked'
  import ServiceIcon from '@/components/ServiceIcon'
  import TaskIcon from '@/components/TaskIcon'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      index: {
        type: Number,
        required: true
      },
      activeItemIdx: {
        type: Number,
        required: true
      },
      isNextAllowed: {
        type: Boolean,
        required: false
      }
    },
    components: {
      ServiceIcon,
      TaskIcon
    },
    computed: {
      is_active() {
        return this.index == this.activeItemIdx
      },
      is_before_active() {
        return this.index < this.activeItemIdx
      },
      title() {
        return _.get(this.item, 'title', '')
      },
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      connections() {
        var items = _.get(this.item, 'connections', [])
        return items.length > 0 ? items.join(',separator,').split(',') : []
      }
    }
  }
</script>
