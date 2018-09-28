<template>
  <article
    class="mv3-l bb ba-l br2-l pointer no-select trans-pm css-list-item"
    @click="openPipe"
  >
    <div class="flex flex-row items-center">
      <div class="flex-fill mr2 fw6 f6 f5-ns">
        <router-link class="link" :to="pipe_route">
          <div class="pa3">
            <div class="flex-ns flex-row items-center">
              <h3 class="f6 f5-ns fw6 lh-title dark-gray mv0 mr2 css-list-title">{{item.name}}</h3>
              <div
                class="dib f8 black-40 mt1 mt0-ns pa1 bg-nearer-white ba b--black-05 br2"
                v-if="item.alias"
              >{{item.alias}}</div>
            </div>
            <div class="dn db-l mw7" v-if="has_description">
              <h4 class="f6 fw4 mt1 mb0 lh-copy light-silver">{{item.description}}</h4>
            </div>
          </div>
        </router-link>
      </div>
      <div class="flex-none nt3 nb3">
        <div class="pv3" @click.stop>
          <LabelSwitch
            class="dib ml2 hint--bottom"
            active-color="#13ce66"
            :aria-label="is_deployed ? 'Turn pipe off' : 'Turn pipe on'"
            v-model="is_deployed"
          />
        </div>
      </div>
      <div class="flex-none pl2" @click.stop>
        <el-dropdown trigger="click" @command="onCommand">
          <span class="el-dropdown-link dib pointer pa3 black-30 hover-black">
            <i class="material-icons v-mid">expand_more</i>
          </span>
          <el-dropdown-menu style="min-width: 10rem; margin-top: -0.5rem" slot="dropdown">
            <el-dropdown-item class="flex flex-row items-center ph2" command="open"><i class="material-icons mr3">edit</i> Edit</el-dropdown-item>
            <el-dropdown-item class="flex flex-row items-center ph2" command="duplicate"><i class="material-icons mr3">content_copy</i> Duplicate</el-dropdown-item>
            <div class="mv2 bt b--black-10"></div>
            <el-dropdown-item class="flex flex-row items-center ph2" command="delete"><i class="material-icons mr3">delete</i> Delete</el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
    </div>
  </article>
</template>

<script>
  import { ROUTE_PIPES } from '../constants/route'
  import { SCHEDULE_STATUS_ACTIVE } from '../constants/schedule'
  import LabelSwitch from './LabelSwitch.vue'

  const PIPE_MODE_UNDEFINED = ''
  const PIPE_MODE_BUILD     = 'B'
  const PIPE_MODE_RUN       = 'R'

  export default {
    props: {
      'item': {
        type: Object,
        required: true
      }
    },
    components: {
      LabelSwitch
    },
    computed: {
      input_type() {
        return ''
      },
      output_type() {
        return ''
      },
      has_description() {
        return _.get(this.item, 'description', '').length > 0
      },
      /*
      is_scheduled() {
        return _.get(this.item, 'schedule_status') == SCHEDULE_STATUS_ACTIVE ? true : false
      },
      */
      is_deployed: {
        get() {
          return _.get(this.item, 'pipe_mode') == PIPE_MODE_RUN ? true : false
        },
        set(value) {
          var doSet = () => {
            var pipe_mode = value === false ? PIPE_MODE_BUILD : PIPE_MODE_RUN

            var attrs = {
              pipe_mode
            }

            this.$store.dispatch('updatePipe', { eid: this.item.eid, attrs })
          }

          if (value === false) {
            this.$confirm('This pipe is turned on and is possibly being used in a production environment. Are you sure you want to continue?', 'Really turn pipe off?', {
              confirmButtonText: 'TURN PIPE OFF',
              cancelButtonText: 'CANCEL',
              type: 'warning'
            }).then(() => {
              doSet()
              this.$store.track("Turned Pipe Off")
            }).catch(() => {
              this.$store.track("Turned Pipe Off (Canceled)")
            })
          } else {
            doSet()
            this.$store.track("Turned Pipe On")
          }
        }
      },
      pipe_route() {
        return { name: ROUTE_PIPES, params: { eid: this.item.eid } }
      }
    },
    methods: {
      openPipe() {
        this.$store.track('Opened Pipe', this.getAnalyticsPayload(this.item))
        this.$router.push(this.pipe_route)
      },
      getAnalyticsPayload(pipe) {
        var analytics_payload = _.pick(pipe, ['eid', 'name', 'alias'])
        _.assign(analytics_payload, { title: pipe.name })

        return analytics_payload
      },
      onCommand(cmd) {
        switch (cmd) {
          case 'open':      return this.openPipe()
          case 'duplicate': return this.$emit('duplicate', this.item)
          case 'delete':    return this.$emit('delete', this.item)
        }
      }
    }
  }
</script>
