// make all elements the same height

export default {
  methods: {
    sameHeights: function(selector) {
      var selector = selector || '[data-key="sameheights"]'
      var query = document.querySelectorAll(selector)
      var elements = query.length
      var max = 0

      if (elements)
      {
        while (elements--)
        {
          var element = query[elements]
          if (element.clientHeight > max)
            max = element.clientHeight
        }

        elements = query.length

        while (elements--)
        {
          var element = query[elements]
          element.style.height = max+'px'
        }
      }
    }
  }
}
