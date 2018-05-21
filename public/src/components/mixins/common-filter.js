// allows any component filter an array of objects,
// optionally specifying the keys they want to filter on

export default {
  methods: {
    $_Filter_filter: function(arr, filter, keys) {
      // not an array; bail out
      if (!_.isArray(arr))
        return []

      // no filter; we're done
      if (_.isNil(filter) || filter.length == 0)
        return arr

      // make sure we do case-insensitive string compare below
      filter = _.toLower(filter)

      // filter the array
      return _.filter(arr, function(obj) {
        // if filter keys were provided,
        // pare down the object to only these keys
        if (_.isArray(keys))
          obj = _.pick(obj, keys)

        // iterate through the corresponding value for each of the above keys
        // and return true if we find the filter string in the value
        return _
          .chain(obj)
          .values()
          .find(function(s) { return _.includes(_.toLower(s), filter) })
          .value() !== undefined
      })
    }
  }
}
