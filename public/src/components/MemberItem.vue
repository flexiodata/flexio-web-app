<template>
<article class="dt w-100 darken-05 ph3 pv3">
  <div class="dtc w2 v-mid">
    <img :src="profile_src" class="ba b--black-10 db br-100 w2 h2">
  </div>
  <div class="dtc v-mid pl2">
    <h1 class="f6 fw6 lh-title black mv0">{{title}}</h1>
    <h2 v-if="!is_pending" class="f7 fw4 mt0 mb0 black-60">@{{item.user_name}}</h2>
  </div>
  <div class="dtc v-mid">
    <div v-if="is_owner" class="w-100 tr">
      <div class="dib f6 bg-white ba b--black-10 ph2 ph3-ns pv1 black-60 cursor-default">{{owner}}</div>
    </div>
    <div v-else class="w-100 tr">
      <rights-dropdown></rights-dropdown>
    </div>
  </div>
</article>
</template>

<script>
  import * as types from '../constants/action-type'
  import * as actions from '../constants/action-info'
  import RightsDropdown from './RightsDropdown.vue'

  export default {
    props: ['item'],
    components: {
      RightsDropdown
    },
    computed: {
      email_hash() {
        return _.get(this.item, 'email_hash', '')
      },
      profile_src() {
        return 'https://secure.gravatar.com/avatar/'+this.email_hash+'?d=mm&s=64'
      },
      is_owner() {
        return _.toLower(_.get(this.item, 'user_group')) == 'owner'
      },
      is_pending() {
        return _.get(this.item, 'first_name', '').length == 0 && _.get(this.item, 'last_name', '').length == 0
      },
      owner() {
        return this.is_owner ? 'Owner' : ''
      },
      title() {
        return _.get(this.item, 'access_code')
        return this.is_pending ? this.item.email : this.item.first_name+' '+this.item.last_name
      },
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
      },
      menu_items() {
        return [{
          id: types.ACTION_TYPE_DELETE, // implicitly allow 'can write' as well
          label: 'Can Edit',
          icon: this.can_edit ? 'check' : 'check_box_outline_blank'
        },{
          id: types.ACTION_TYPE_EXECUTE, // implicitly allow 'can read' as well
          label: 'Can View',
          icon: !this.can_edit && this.can_view ? 'check' : 'check_box_outline_blank'
        },{
          type: 'divider'
        },{
          id: 'remove',
          label: 'Remove',
          icon: ''
        }]
      }
    },
    methods: {
      onDropdownItemClick(menu_item) {
        switch (menu_item.id)
        {
          case types.ACTION_TYPE_READ:
          case types.ACTION_TYPE_WRITE:
          case types.ACTION_TYPE_DELETE:
          case types.ACTION_TYPE_EXECUTE:
            return this.$emit('edit', this.item, menu_item.id)
          case 'remove':
            return this.$emit('delete', this.item)
        }
      }
    }
  }
</script>
