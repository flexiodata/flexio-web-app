<template>
  <article
    :class="cls"
    :style="itemStyle"
    @click="onClick"
  >
    <div class="flex flex-row items-center cursor-default">
      <div class="flex-fill">
        <div class="flex flex-row items-center">
          <div
            class="br-100 mr2"
            style="width: 8px; height: 8px"
            :style="is_deployed ? 'background-color: #13ce66' : 'background-color: #dcdfe6'"
          ></div>
          <div class="flex-fill f5 fw6 cursor-default mr1 lh-title truncate">{{pname}}</div>
        </div>
      </div>
      <div
        class="flex-none ml2"
        @click.stop
        v-require-rights:pipe.update.hidden
      >
          <el-dropdown trigger="click" @command="onCommand">
            <span class="el-dropdown-link dib pointer pa1 black-30 hover-black">
              <i class="material-icons v-mid">expand_more</i>
            </span>
            <el-dropdown-menu style="min-width: 10rem" slot="dropdown">
              <el-dropdown-item class="flex flex-row items-center ph2" command="delete" :item="pipe"><i class="material-icons mr3">delete</i> Delete</el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
      </div>
    </div>
  </article>
</template>

<script>
  const DEPLOY_MODE_RUN = 'R'

  export default {
    props: {
      item: {
        type: Object,
        required: true
      },
      itemCls: {
        type: String,
        default: ''
      },
      itemStyle: {
        type: String,
        default: ''
      },
      selectedItem: {
        type: Object,
        default: () => {}
      },
      selectedCls: {
        type: String,
        default: 'relative bg-nearer-white'
      },
      showDropdown: {
        type: Boolean,
        default: false
      }
    },
    computed: {
      eid() {
        return _.get(this.item, 'eid', '')
      },
      pname() {
        return _.get(this.item, 'name', '')
      },
      is_selected() {
        return _.get(this.selectedItem, 'eid') === this.eid
      },
      is_deployed() {
        return _.get(this.item, 'deploy_mode') == DEPLOY_MODE_RUN ? true : false
      },
      cls() {
        var sel_cls = this.is_selected ? this.selectedCls : ''

        if (this.itemCls.length > 0) {
          return this.itemCls + ' ' + sel_cls
        } else {
          return 'min-w5 pa3 bb b--black-05 bg-white hover-bg-nearer-white ' + sel_cls
        }
      }
    },
    methods: {
      onCommand(cmd) {
        switch (cmd) {
          case 'edit':      return this.$emit('edit', this.item)
          case 'delete':    return this.$emit('delete', this.item)
        }
      },
      onClick: _.debounce(function() {
        this.$emit('activate', this.item)
      }, 500, { 'leading': true, 'trailing': false })
    }
  }
</script>
