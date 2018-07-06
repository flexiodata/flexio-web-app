<template>
  <div>
    <div class="flex flex-row items-center justify-center mb3">
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
    <div class="marked" v-html="description"></div>
  </div>
</template>

<script>
  import marked from 'marked'
  import ServiceIcon from './ServiceIcon.vue'
  import TaskIcon from './TaskIcon.vue'

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
      description() {
        return marked(_.get(this.item, 'description', ''))
      },
      connections() {
        var items = _.get(this.item, 'connections', [])
        return items.join(',separator,').split(',')
      }
    }
  }
</script>
