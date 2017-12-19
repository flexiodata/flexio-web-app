<template>
  <div class="flex flex-row relative hide-child">
    <div class="flex flex-column relative cursor-default ba bw2 mh1 pv1 square-6 square-7-ns trans-wh"
      :class="[
        isGhost?'css-task-ghost black-40 b--transparent fw6':'white',
        (isDisabled && !is_insert_item)?'css-disabled o-50':'darken-10',
        ((is_active && !isDisabled) || is_insert_item)?'b--black-40 mh2':'',
        bg_color
      ]"
      @click="onClick"
    >
      <div v-if="isGhost" class="css-task-inner-border absolute absolute--fill ba bw1 b--black-10 b--dashed"></div>
      <div v-if="is_active" class="absolute absolute--fill ba b--white-80"></div>
      <span v-if="allow_delete" @click.stop="onDeleteClick" class="absolute top-0 right-0 pointer f3 lh-solid b pr1 child hint--bottom-left" aria-label="Delete Task">&times;</span>
      <span v-if="is_insert_item" @click.stop="onCancelInsertClick" class="absolute top-0 right-0 pointer f3 lh-solid b pr1 child hint--bottom-left" aria-label="Cancel Insert">&times;</span>
      <div class="flex flex-column flex-fill tc" :class="[showName?'pb1 justify-end':'justify-center']">
        <slot name="icon">
          <i class="material-icons f3 f2-ns">{{task_icon}}</i>
        </slot>
      </div>
      <div v-if="showName" class="flex flex-column justify-center tc ph1 pb2" style="flex: 0 0 2.25rem">
        <span class="f7">
          <slot name="name">{{task_name}}</slot>
        </span>
      </div>
    </div>
    <div
      v-show="allow_insert"
      @click.stop="onInsertClick"
      class="css-task-insert flex flex-column child pointer justify-center bg-black-20 ph1 mv1 white darken-10"
    >
      <i class="material-icons md-24">add</i>
    </div>
  </div>
</template>

<script>
  import * as types from '../constants/task-type'
  import * as tasks from '../constants/task-info'

  export default {
    props: {
      'item': {},
      'active-item-eid': {},
      'last-task-item': {},
      'is-disabled': { default: false },
      'is-task-add': {},
      'is-ghost': {},
      'show-name': { default: true }
    },
    computed: {
      is_item() {
        return this.isTaskAdd || this.isGhost ? false : true
      },
      is_active() {
        return !this.is_item ? false : this.activeItemEid == _.get(this.item, 'eid')
      },
      is_insert_item() {
        return !this.is_item ? false : _.get(this.item, 'inserting') === true
      },
      is_last_task() {
        return !this.is_item ? false : _.get(this.lastTaskItem, 'eid') == _.get(this.item, 'eid')
      },
      allow_delete() {
        return !this.isDisabled && !this.isTaskAdd && !this.isGhost
      },
      allow_insert() {
        return !this.isDisabled && !this.isTaskAdd && !this.isGhost && !this.is_last_task
      },
      task_name() {
        var mname = _.get(this.item, 'metadata.name', '')

        return this.name
          ? this.name : mname.length > 0
          ? mname : _.result(this, 'tinfo.name', 'Task')
      },
      task_icon() {
        return this.icon ? this.icon : _.result(this, 'tinfo.icon', 'build')
      },
      bg_color() {
        if (this.isGhost)
          return 'bg-white'

        if (this.isTaskAdd)
          return 'bg-black-20'

        switch (_.get(this.item, 'type')) {
          // blue tiles
          case types.TASK_OP_INPUT:
          case types.TASK_OP_CONVERT:
          case types.TASK_OP_EMAIL_SEND:
          case types.TASK_OP_OUTPUT:
          case types.TASK_OP_PROMPT:
            return 'bg-task-blue'

          case types.TASK_OP_EXECUTE:
            return 'bg-task-purple'

          // green tiles
          case types.TASK_OP_CALC:
          case types.TASK_OP_DISTINCT:
          case types.TASK_OP_DUPLICATE:
          case types.TASK_OP_FILTER:
          case types.TASK_OP_GROUP:
          case types.TASK_OP_LIMIT:
          case types.TASK_OP_MERGE:
          case types.TASK_OP_SEARCH:
          case types.TASK_OP_SORT:
            return 'bg-task-green'

          // orange tiles
          case types.TASK_OP_COPY:
          case types.TASK_OP_CUSTOM:
          case types.TASK_OP_FIND_REPLACE:
          case types.TASK_OP_NOP:
          case types.TASK_OP_RENAME:
          case types.TASK_OP_SELECT:
          case types.TASK_OP_TRANSFORM:
            return 'bg-task-orange'
        }

        // default
        return 'bg-task-gray'
      }
    },
    methods: {
      tinfo() {
        return _.find(tasks, { type: _.get(this.item, 'type') })
      },

      onClick() {
        if (!this.isDisabled)
          this.$emit('activate', this.item)
      },

      onDeleteClick() {
        if (!this.isDisabled)
          this.$emit('delete', this.item)
      },

      onInsertClick() {
        if (!this.isDisabled)
          this.$emit('insert', this.item)
      },

      onCancelInsertClick() {
        this.$emit('cancel-insert', this.item)
      }
    }
  }
</script>

<style lang="less">
  .hide-child .child.css-task-insert {
    margin-left: -2rem;
    transition: margin-left .15s ease-in;
  }
  .hide-child:hover .child.css-task-insert,
  .hide-child:focus .child.css-task-insert,
  .hide-child:active .child.css-task-insert {
    margin-left: 0;
    transition: margin-left .15s ease-in;
  }

  .css-task-ghost:not(.css-disabled) {
    &:hover,
    &:focus,
    &:active {
      color: #000;

      .css-task-inner-border {
        border-style: solid;
      }
    }
  }

  /*
  // -- active task caret --

  @s: 15px;

  .css-caret {
    position: relative;
    left: -1 * @s;
  }

  .css-caret:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
  }

  .css-caret:after {
    content: '';
    position: absolute;
    left: 1px;
    top: 1px;
  }

  .css-caret:before {
    border-bottom: (@s + 1) solid rgba(0, 0, 0, 0.2);
    border-left: (@s + 1) solid transparent;
    border-right: (@s + 1) solid transparent;
  }

  .css-caret:after {
    border-bottom: @s solid #fff;
    border-left: @s solid transparent;
    border-right: @s solid transparent;
  }
  */
</style>
