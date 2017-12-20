<script>
  // CommitChart.js
  import { Line } from 'vue-chartjs'

  const default_colors = [
    '#3366CC',
    '#DC3912',
    '#FF9900',
    '#109618',
    '#990099',
    '#3B3EAC',
    '#0099C6',
    '#DD4477',
    '#66AA00',
    '#B82E2E',
    '#316395',
    '#994499',
    '#22AA99',
    '#AAAA11',
    '#6633CC',
    '#E67300',
    '#8B0707',
    '#329262',
    '#5574A6',
    '#3B3EAC'
  ]

  const default_config = {}
  /*
  const default_config = {
    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    datasets: [{
      label: 'Dataset 1',
      backgroundColor: '#2196f3',
      borderColor: '#2196f3',
      fill: false,
      data: [40, 20, 12, 39, 10, 40, 39, 80, 40, 20, 12, 11]
    }]
  }
  */

  const default_options = {
    maintainAspectRatio: false,
    animation: {
      duration: 0
    },
    legend: {
      position: 'bottom'
    }
  }

  export default {
    extends: Line,
    props: {
      'labels': {
        type: Array,
        default: () => { return [] },
      },
      'datasets': {
        type: Array,
        default: () => { return [] },
      },
      'config': {
        type: Object,
        default: () => { return {} }
      },
      'options': {
        type: Object,
        default: () => { return {} }
      }
    },
    watch: {
      datasets: function(val, old_val) {
        this.doRender()
      }
    },
    mounted() {
      this.doRender()
    },
    methods: {
      doRender() {
        var cfg = {}

        if (this.labels.length > 0 && this.datasets.length > 0)
        {
          // auto-generate colors
          var datasets = _.map(this.datasets, (dataset, idx) => {
            return _.assign(dataset, {
              fill: false,
              backgroundColor: default_colors[idx],
              borderColor: default_colors[idx]
            })
          })

          cfg = {
            labels: this.labels,
            datasets: datasets
          }
        }

        this.renderChart(
          _.assign({}, default_config, this.config, cfg),
          _.assign({}, default_options, this.options)
        )
      }
    }
  }
</script>
