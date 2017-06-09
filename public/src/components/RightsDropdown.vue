<template>
  <div>
    <a
      class="f5 pointer pa1 mid-gray popover-trigger"
      ref="dropdownTrigger"
      tabindex="0"
      @click.stop
    ><span class="v-mid">Can Edit</span> <i class="material-icons v-mid">expand_more</i></a>

    <ui-popover
      class="mw5 f6 mid-gray"
      trigger="dropdownTrigger"
      dropdown-position="bottom right"
    >
      <div class="flex flex-row pl2 pr3 pv3 darken-05 pointer bb b--black-05">
        <i class="material-icons md-18 ml1 mr2">check</i>
        <div>
          <div class="fw6 mb1">Can Edit</div>
          <div class="f7 lh-copy light-silver">People can view, run, edit or delete this pipe</div>
        </div>
      </div>
      <div class="flex flex-row pl2 pr3 pv3 darken-05 pointer bb b--black-05">
        <i class="material-icons md-18 ml1 mr2" style="color: transparent">check</i>
        <div>
          <div class="fw6 mb1">Can View</div>
          <div class="f7 lh-copy light-silver">People can view or run this pipe</div>
        </div>
      </div>
      <div class="flex flex-row pl2 pr3 pv3 darken-05 pointer dark-red">
        <i class="material-icons md-18 ml1 mr2" style="color: transparent">check</i>
        <div>Remove</div>
      </div>
    </ui-popover>
  </div>
</template>
