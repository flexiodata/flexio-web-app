<template>
  <article>
    <div class="flex flex-column bb b--black-10 pb3 mt3 hide-child">
      <div class="mh3 mh0-l relative">
        <span
          class="absolute top-0 right-0 pa1 br1 pointer child darken-10 hint--top"
          aria-label="Edit Project"
          @click.stop="onEditClick"
        >
          <i class="material-icons v-mid f4">edit</i>
        </span>
        <h1 class="f4 f3-ns fw6 lh-title mv0">
          <router-link :to="href" class="link underline-hover blue">{{item.name}}</router-link>
        </h1>
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
  </article>
</template>

<script>
  import moment from 'moment'
  import util from '../utils'

  export default {
    props: ['item'],
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
      onEditClick() {
        this.$emit('activate', this.item)
      }
    }
  }
</script>
