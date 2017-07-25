<script>
  // CommitChart.js
  import { Bar } from 'vue-chartjs'

  const default_config = {
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    datasets: [
      {
        backgroundColor: '#2196f3',
        data: [40, 20, 12, 39, 10, 40, 39, 80, 40, 20, 12, 11]
      }
    ]
  }

  const default_options = {
    animation: {
      duration: 0
    }
  }

  export default Bar.extend({
    props: {
      'config': {
        type: Object,
        default: () => { return {} }
      },
      'options': {
        type: Object,
        default: () => { return {} }
      }
    },
    mounted() {
      this.renderChart(
        _.assign({}, default_config, this.config),
        _.assign({}, default_options, this.options)
      )
    }
  })
</script>
