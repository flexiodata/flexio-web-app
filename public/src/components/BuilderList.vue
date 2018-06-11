<template>
  <div>
    <BuilderItem
      :item="item"
      :index="index"
      :key="item.id"
      v-bind="$attrs"
      v-on="$listeners"
      v-for="(item, index) in prompts"
    />
    <div v-if="prompts.length == 0">
      <p class="ttu fw6 f7 moon-gray">Start your pipe with one of the following tasks</p>
      <BuilderItemTaskChooser :starter="true" @task-chooser-item-click="chooseTask" />
      <p class="ttu fw6 f7 moon-gray">&mdash; or &mdash;</p>
      <div class="mt3">
        <el-button
          class="ttu b"
        >
          Get started with a pipe template
        </el-button>
      </div>
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'
  import BuilderItem from './BuilderItem.vue'
  import BuilderItemTaskChooser from './BuilderItemTaskChooser.vue'

  export default {
    inheritAttrs: false,
    props: {
      containerId: {
        type: String
      }
    },
    components: {
      BuilderItem,
      BuilderItemTaskChooser
    },
    watch: {
      active_prompt_idx: {
        handler: 'scrollToActive',
        immediate: true
      }
    },
    data() {
      return {
        do_initial_scroll: false
      }
    },
    computed: {
      ...mapState({
        prompts: state => state.builder.prompts,
        active_prompt: state => state.builder.active_prompt,
        active_prompt_idx: state => state.builder.active_prompt_idx
      })
    },
    mounted() {
      setTimeout(() => { this.do_initial_scroll = true }, 500)
    },
    methods: {
      scrollToItem(item_id) {
        if (_.isString(item_id) && _.isString(this.containerId)) {
          setTimeout(() => {
            this.$scrollTo('#'+item_id, {
                container: '#'+this.containerId,
                duration: 400,
                offset: this.active_prompt_idx == 0 ? -100 : -32
            })
          }, 10)
        }
      },
      scrollToActive() {
        if (!this.do_initial_scroll)
          return

        var item_id = _.get(this.active_prompt, 'id', null)
        this.scrollToItem(item_id)
      },
      chooseTask(item) {
        switch (item.op) {
          case 'read':
            alert(item.op)
            break
          case 'request':
            alert(item.op)
            break
          case 'execute':
            alert(item.op)
            break
          case 'echo':
            alert(item.op)
            break
        }
      }
    }
  }
</script>
