<template>
  <article>
    <div class="flex flex-column bb b--black-10 pb3 mt3">
      <div class="mh3 mh0-l relative">
        <div class="flex flex-row items-center">
          <h1 class="f4 f3-ns fw6 lh-title mv0">
            <router-link :to="href" class="link underline-hover blue">{{item.name}}</router-link>
          </h1>
          <div class="flex-fill"></div>
        <div class="flex-none ml2">
          <a
            class="f5 b dib pointer pa2 black-60 popover-trigger"
            ref="dropdownTrigger"
            tabindex="0"
          ><i class="material-icons v-mid b">expand_more</i></a>

          <ui-popover
            trigger="dropdownTrigger"
            ref="dropdown"
            dropdown-position="bottom right"
          >
            <ui-menu
              contain-focus
              has-icons

              :options="[{
                id: 'open',
                label: 'Open',
                icon: 'file_upload'
              },{
                id: 'edit',
                label: 'Edit',
                icon: 'edit'
              },{
                id: 'leave',
                label: 'Leave this project',
                icon: 'exit_to_app'
              },{
                type: 'divider'
              },{
                id: 'delete',
                label: 'Delete',
                icon: 'delete'
              }]"

              @select="onDropdownItemClick"
              @close="$refs.dropdown.close()"
            ></ui-menu>
          </ui-popover>
        </div>
      </div>
        </div>
        <h2 class="f6 fw4 mt2 mb0 lh-copy">{{item.description}}</h2>
        <div class="flex flex-row mt2 f6 black-60">
          <div class="flex-fill">
            <span class="hint--top" :aria-label="fullname">{{owner}}</span>
            <span class="ml1">&middot;</span>
            <span class="ml1 hint--top" :aria-label="members"><i class="cursor-default no-select material-icons f4 v-mid">person</i> {{item.follower_count}}</span>
            <span class="ml1">&middot;</span>
            <span class="ml1 hint--top" :aria-label="pipes"><i class="cursor-default no-select material-icons f4 v-mid">storage</i> {{item.pipe_count}}</span>
          </div>
          <div class="flex-none dn dib-ns">
            {{created}}
          </div>
        </div>
      </div>
    </div>

    <!-- leave confirm modal -->
    <confirm-modal
      ref="modal-leave-confirm"
      title="Leave this project?"
      submit-label="Leave project"
      cancel-label="Cancel"
      @confirm="onLeaveConfirmModalClose"
      @hide="show_leave_confirm_modal = false"
      v-if="show_leave_confirm_modal"
    >
      <div class="lh-copy mb3">Are you sure you want to leave the <span class="b">{{item.name}}</span> project?</div>
    </confirm-modal>
  </article>
</template>

<script>
  import moment from 'moment'
  import util from '../utils'
  import ConfirmModal from './ConfirmModal.vue'

  export default {
    props: ['item'],
    components: {
      ConfirmModal
    },
    data() {
      return {
        show_leave_confirm_modal: false
      }
    },
    computed: {
      href() {
        return '/project/'+this.item.eid
      },
      fullname() {
        return this.item.owned_by.first_name+' '+this.item.owned_by.last_name
      },
      owner() {
        return '@'+this.item.owned_by.user_name
      },
      is_owner() {
        return _.toLower(_.get(this.item, 'user_group')) == 'owner'
      },
      members() {
        var cnt = this.item.follower_count
        return util.pluralize(cnt, cnt+' '+'members', cnt+' '+'member')
      },
      pipes() {
        var cnt = this.item.pipe_count
        return util.pluralize(cnt, cnt+' '+'pipes', cnt+' '+'pipe')
      },
      created() {
        return moment(this.item.created).format('LL')
      }
    },
    methods: {
      onDropdownItemClick(menu_item) {
        switch (menu_item.id)
        {
          case 'open':      return this.$router.push({ path: this.href })
          case 'edit':      return this.$emit('edit', this.item)
          case 'delete':    return this.$emit('delete', this.item)
          case 'leave':     return this.openProjectLeaveModal()
        }
      },
      openProjectLeaveModal() {
        this.show_leave_confirm_modal = true
        this.$nextTick(() => { this.$refs['modal-leave-confirm'].open() })
      },
      onLeaveConfirmModalClose(modal) {
        /*
        TODO...
        */
      }
    }
  }
</script>
