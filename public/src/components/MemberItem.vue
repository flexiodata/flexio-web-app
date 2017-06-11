<template>
<article class="dt w-100 darken-05 ph3 pv3">
  <div class="dtc w2 v-mid">
    <img :src="profile_src" class="ba b--black-10 db br-100 w2 h2">
  </div>
  <div class="dtc v-mid pl2">
    <h1 class="f6 fw6 lh-title black mv0">{{title}}</h1>
    <h2 v-if="!is_pending" class="f7 fw4 lh-copy ma0 light-silver">@{{username}}</h2>
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
        :item="item"
      ></rights-dropdown>
    </div>
  </div>
</article>
</template>

<script>
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
      is_editable() {
        return this.ownerEid == this.active_user_eid
      },
      is_pending() {
        return _.get(this.item, 'user.first_name', '').length == 0 && _.get(this.item, 'user.last_name', '').length == 0
      },
      title() {
        return this.is_pending ? _.get(this.item, 'user.email', '') : _.get(this.item, 'user.first_name', '')+' '+_.get(this.item, 'user.last_name', '')
      },
      username() {
        return _.get(this.item, 'user.user_name', '')
      }
    }
  }
</script>
