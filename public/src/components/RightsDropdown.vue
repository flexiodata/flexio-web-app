<template>
  <div>
    <a
      class="pointer popover-trigger"
      ref="dropdownTrigger"
      tabindex="0"
      @click.stop
    ><span class="v-mid">{{rights_label}}</span><i class="material-icons v-mid" v-if="isEditable">expand_more</i></a>

    <ui-popover
      class="mw5 f6 mid-gray"
      ref="popover"
      trigger="dropdownTrigger"
      dropdown-position="bottom right"
    >
      <div class="flex flex-row pl2 pr3 pv3 darken-05 pointer bb b--black-05" @click="changeRights('can-edit')">
        <i class="material-icons md-18 ml1 mr2" :class="can_edit ? '' : 'transparent'">check</i>
        <div>
          <div class="fw6 lh-title mb1">Can Edit</div>
          <div class="f7 lh-copy light-silver" v-if="isEveryone">Everyone can view, run, edit or delete this pipe</div>
          <div class="f7 lh-copy light-silver" v-else>People can view, run, edit or delete this pipe</div>
        </div>
      </div>
      <div class="flex flex-row pl2 pr3 pv3 darken-05 pointer" @click="changeRights('can-view')">
        <i class="material-icons md-18 ml1 mr2" :class="!can_edit && can_view ? '' : 'transparent'">check</i>
        <div>
          <div class="fw6 lh-title mb1">Can View</div>
          <div class="f7 lh-copy light-silver" v-if="isEveryone">Everyone can view or run this pipe</div>
          <div class="f7 lh-copy light-silver" v-else>People can view or run this pipe</div>
        </div>
      </div>
      <div class="flex flex-row pl2 pr3 pv3 darken-05 pointer bt b--black-05" @click="removeRight" v-if="isEveryone">
        <i class="material-icons md-18 ml1 mr2" :class="!can_edit && !can_view ? '' : 'transparent'">check</i>
        <div>
          <div class="fw6 lh-title mb1">Can't View</div>
          <div class="f7 lh-copy light-silver">No one can view or run this pipe except members</div>
        </div>
      </div>
      <div class="flex flex-row pl2 pr3 pv3 darken-05 pointer bt b--black-05 dark-red" @click="removeRight" v-else>
        <i class="material-icons md-18 ml1 mr2 transparent">check</i>
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
      },
      'is-editable': {
        type: Boolean,
        default: true
      },
      'is-everyone': {
        type: Boolean,
        default: false
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
          this.can_view ? 'Can View' :
          "Can't View"
      }
    },
    methods: {
      changeRights(rights) {
        switch (rights)
        {
          case 'can-view':
            this.updateRights([
              types.ACTION_TYPE_READ,
              types.ACTION_TYPE_EXECUTE
            ])
            break
          case 'can-edit':
            this.updateRights([
              types.ACTION_TYPE_READ,
              types.ACTION_TYPE_EXECUTE,
              types.ACTION_TYPE_WRITE,
              types.ACTION_TYPE_DELETE
            ])
            break
        }

        this.$refs.popover.close()
      },
      updateRights(actions) {
        var eid = _.get(this.item, 'eid', '')
        var attrs = { actions }

        // we're on the 'ghost' everyone item in the rights list; use the 'createRights'
        // call to add the selected rights
        if (this.isEveryone && eid.length == 0)
        {
          var rights = [{
            object_eid: _.get(this.item, 'object_eid', ''),
            action_code: 'public',
            action_type: 'CAT',
            actions
          }]

          this.$store.dispatch('createRights', { attrs: { rights } }).then(response => {
            console.log(response)
          })

          return
        }

        // otherwise, update the right
        this.$store.dispatch('updateRight', { eid, attrs }).then(response => {
          console.log(response)
        })
      },
      removeRight() {
        this.$store.dispatch('deleteRight', { attrs: this.item })
        this.$refs.popover.close()
      }
    }
  }
</script>
