<template>
  <div>
    <div class="pa2 bg-black-05 bb b--black-05">
      <div class="f6 fw6 ttu silver">My Connections</div>
    </div>
    <div class="relative">
      <connection-chooser-list
        list-type="output"
        item-layout="list"
        :project-eid="projectEid"
        :show-add="false"
        :show-blank-pipe="false"
        @item-activate="chooseConnection"
      ></connection-chooser-list>
    </div>
    <div class="pa2 bg-black-05 bb b--black-05">
      <div class="f6 fw6 ttu silver">Available Connections</div>
    </div>
    <service-list
      item-layout="list"
      @item-activate="setupConnection"
    ></service-list>
  </div>
</template>

<script>
  import ConnectionChooserList from './ConnectionChooserList.vue'
  import ServiceList from './ServiceList.vue'

  export default {
    props: ['project-eid'],
    components: {
      ConnectionChooserList,
      ServiceList
    },
    methods: {
      chooseConnection() {
        alert('My connection item clicked!')
      },
      setupConnection() {
        alert('Available connection item clicked!')
      }
    }
  }
</script>
