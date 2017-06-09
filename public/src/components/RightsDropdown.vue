<template>
  <div>
    <a
      class="f5 pointer pa1 mid-gray popover-trigger"
      ref="dropdownTrigger"
      tabindex="0"
      @click.stop
    ><span class="v-mid">{{rights_label}}</span> <i class="material-icons v-mid">expand_more</i></a>

    <ui-popover
      class="mw5 f6 mid-gray"
      trigger="dropdownTrigger"
      dropdown-position="bottom right"
    >
      <div class="flex flex-row pl2 pr3 pv3 darken-05 pointer bb b--black-05" @click="$emit('can-edit')">
        <i class="material-icons md-18 ml1 mr2" :class="can_edit ? '' : 'transparent'">check</i>
        <div>
          <div class="fw6 lh-title mb1">Can Edit</div>
          <div class="f7 lh-copy light-silver">People can view, run, edit or delete this pipe</div>
        </div>
      </div>
      <div class="flex flex-row pl2 pr3 pv3 darken-05 pointer bb b--black-05" @click="$emit('can-view')">
        <i class="material-icons md-18 ml1 mr2" :class="!can_edit && can_view ? '' : 'transparent'">check</i>
        <div>
          <div class="fw6 lh-title mb1">Can View</div>
          <div class="f7 lh-copy light-silver">People can view or run this pipe</div>
        </div>
      </div>
      <div class="flex flex-row pl2 pr3 pv3 darken-05 pointer dark-red" @click="$emit('remove')">
        <i class="material-icons md-18 ml1 mr2 transparent" :class="can_edit ? '' : 'transparent'">check</i>
        <div>Remove</div>
      </div>
    </ui-popover>
  </div>
</template>

<script>
  import * as types from '../constants/action-type'
  import * as actions from '../constants/action-info'

  export default {
    props: {
      'item': {
        type: Object,
        required: true
      }
    },
    computed: {
      item_actions() {
        return _.get(this.item, 'actions', [])
      },
      can_edit() {
        if (_.includes(this.item_actions, types.ACTION_TYPE_DELETE) ||
            _.includes(this.item_actions, types.ACTION_TYPE_WRITE))
        {
          return true
        }

        return false
      },
      can_view() {
        if (_.includes(this.item_actions, types.ACTION_TYPE_EXECUTE) ||
            _.includes(this.item_actions, types.ACTION_TYPE_READ))
        {
          return true
        }

        return false
      },
      rights_label() {
        return this.can_edit ? 'Can Edit' :
          this.can_view ? 'Can View' : ''
      }
    }
  }
</script>
