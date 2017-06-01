<template>
<article class="dt w-100 bb b--black-05 ph3 pb3 mt3">
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
      <span
        class="pointer f3 lh-solid b child black-30 hover-black-60 mr2 hint--left"
        :aria-label="remove_str"
        @click="deleteItem"
      >
        &times;
      </span>
    </div>
  </div>
</article>
</template>

<script>
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
        return this.is_pending ? this.item.email : this.item.first_name+' '+this.item.last_name
      },
      remove_str() {
        return 'Remove ' + this.title
      }
    },
    methods: {
      deleteItem() {
        this.$emit('delete', this.item)
      }
    }
  }
</script>
