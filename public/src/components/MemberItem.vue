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
      <a
        class="f5 dib pointer pa1 mid-gray popover-trigger"
        ref="dropdownTrigger"
        tabindex="0"
        @click.stop
      ><span class="v-mid">{{rights_label}}</span> <i class="material-icons v-mid">expand_more</i></a>

      <ui-popover
        trigger="dropdownTrigger"
        ref="dropdown"
        dropdown-position="bottom right"
      >
        <ui-menu
          contain-focus
          has-icons
          :options="menu_items"
          @select="onDropdownItemClick"
          @close="$refs.dropdown.close()"
        ></ui-menu>
      </ui-popover>
    </div>
  </div>
</article>
</template>

<script>
  import * as types from '../constants/action-type'
  import * as actions from '../constants/action-info'

  export default {
    props: ['item'],
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
      rights_label() {
        if (_.includes(this.item_actions, types.ACTION_TYPE_DELETE))  { return 'Can Edit' }
        if (_.includes(this.item_actions, types.ACTION_TYPE_WRITE))   { return 'Can Edit' }
        if (_.includes(this.item_actions, types.ACTION_TYPE_EXECUTE)) { return 'Can View' }
        if (_.includes(this.item_actions, types.ACTION_TYPE_READ))    { return 'Can View' }
        return ''
      },
      menu_items() {
        return [{
          id: types.ACTION_TYPE_DELETE, // implicitly allow 'can write' as well
          label: 'Can Edit'
        },{
          id: types.ACTION_TYPE_EXECUTE, // implicitly allow 'can read' as well
          label: 'Can View'
        },{
          type: 'divider'
        },{
          id: 'remove',
          label: 'Remove'
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
