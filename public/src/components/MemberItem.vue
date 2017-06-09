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
      <div class="dib f6 bg-white ba b--black-10 ph2 ph3-ns pv1 black-60 cursor-default">Owner</div>
    </div>
    <div v-else class="w-100 tr">
      <rights-dropdown :item="item"></rights-dropdown>
    </div>
  </div>
</article>
</template>

<script>
  import api from '../api'
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
      email_hash() {
        return _.get(this.item, 'email_hash', '')
      },
      profile_src() {
        return 'https://secure.gravatar.com/avatar/'+this.email_hash+'?d=mm&s=64'
      },
      is_owner() {
        return _.get(this.item, 'access_code') == this.ownerEid
      },
      is_pending() {
        return _.get(this.item, 'first_name', '').length == 0 && _.get(this.item, 'last_name', '').length == 0
      },
      title() {
        return _.get(this.item, 'access_code')
        //return this.is_pending ? this.item.email : this.item.first_name+' '+this.item.last_name
      }
    }
  }
</script>
