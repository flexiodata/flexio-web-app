<template>
  <article class="dt w-100 darken-05 ph3 pv3">
    <div class="dtc w2 v-mid" v-if="is_everyone">
      <div class="db br-100 w2 h2">
        <i class="material-icons relative black-40" style="font-size: 32px">{{icon}}</i>
      </div>
    </div>
    <div class="dtc w2 v-mid" v-else>
      <img :src="profile_src" class="ba b--black-10 db br-100 w2 h2">
    </div>
    <div class="dtc v-mid pl2">
      <div class="f6 fw6 lh-title black">{{title}}</div>
      <div class="f7 lh-copy light-silver" v-if="is_everyone">{{description}}</div>
      <div class="f7 lh-copy light-silver" v-else-if="!is_pending">@{{username}}</div>
    </div>
    <div class="dtc v-mid">
      <div v-if="is_owner" class="w-100 tr">
        <div class="f6 fw6 black-60 cursor-default">Owner</div>
      </div>
      <div v-else class="w-100 tr">
        <rights-dropdown
          class="f6 fw6 black-60"
          :class="is_editable ? '' : 'o-40 no-pointer-events'"
          :is-editable="is_editable"
          :is-everyone="is_everyone"
          :item="item"
        ></rights-dropdown>
      </div>
    </div>
  </article>
</template>

<script>
  import * as types from '../constants/action-type'
  import { mapState } from 'vuex'
  import RightsDropdown from './RightsDropdown.vue'

  export default {
    props: {
      'item': {
        type: Object,
        required: true
      },
      'owner-eid': {
        type: String,
        default: ''
      }
    },
    components: {
      RightsDropdown
    },
    computed: {
      ...mapState([
        'active_user_eid'
      ]),
      email_hash() {
        return _.get(this.item, 'user.email_hash', '')
      },
      profile_src() {
        return 'https://secure.gravatar.com/avatar/'+this.email_hash+'?d=mm&s=64'
      },
      is_owner() {
        return _.get(this.item, 'access_code') == this.ownerEid
      },
      is_everyone() {
        return _.get(this.item, 'access_code', '') == 'public'
      },
      is_editable() {
        return this.ownerEid == this.active_user_eid
      },
      is_pending() {
        return _.get(this.item, 'user.first_name', '').length == 0 && _.get(this.item, 'user.last_name', '').length == 0
      },
      item_actions() {
        return _.get(this.item, 'actions', [])
      },
      can_read() {
        return _.includes(this.item_actions, types.ACTION_TYPE_READ)
      },
      icon() {
        if (this.is_everyone)
          return this.can_read ? 'people' : 'lock'

        return ''
      },
      title() {
        var email = _.get(this.item, 'user.email', '')
        var first_name = _.get(this.item, 'user.first_name', '')
        var last_name = _.get(this.item, 'user.last_name', '')

        if (this.is_everyone)
          return this.can_read ? 'Public' : 'Private'

        return this.is_pending ? email : first_name+' '+last_name
      },
      description() {
        if (this.is_everyone)
          return this.can_read ? 'Anyone on the Internet can view this pipe' : 'Only the members below can access this pipe'
      },
      username() {
        return _.get(this.item, 'user.user_name', '')
      }
    }
  }
</script>
