<template>
  <div>
    <p>
      This example was started
      <b>
        <span v-if="hours > 0">
          <span>{{hours}}</span> <span v-if="hours == 1">hour</span><span v-else>hours</span>,
        </span>
        <span v-if="minutes > 0">
          <span>{{minutes}}</span> <span v-if="minutes == 1">minute</span><span v-else>minutes</span> and
        </span>
        <span>
          <span>{{seconds}}</span> seconds
        </span>
      </b>
      ago.
    </p>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        elapsed: 0
      }
    },
    computed: {
      seconds() {
        var seconds = this.elapsed / 1000;
        return (seconds % 60).toFixed(2);
      },
      minutes() {
        var minutes = this.elapsed / 60000;
        return Math.floor(minutes % 60);
      },
      hours() {
        var hours = this.elapsed / 3600000;
        return Math.floor(hours % 60);
      }
    },
    mounted() {
      this.start = new Date();
      this.timer = setInterval(this.tick, 47);
    },
    beforeDestroy() {
      clearInterval(this.timer);
    },
    methods: {
      tick() {
        this.elapsed = new Date() - this.start;
      }
    }
  }
</script>
