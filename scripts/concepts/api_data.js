

// API FROM DATA: create a new API from data
// Satisfies following usecases:
// * Create a new API from data
// * Cache a set of data from one or more APIs so it can be queried/used in alternate ways
// (in the case of this example, we'd read from the API and put it in local storage, then access
// it with another API function)

// create an API from data loaded into a local store
var t = Flexio.task()
.read('stdin')
.validate({
    year: {type: 'datetime'}
})
.read('local/chicago-crime')
.filter('date = ${year}')
.query({
    // TODO: graphql query here?
    id: null,
    type: null,
    date: null,
    lat: null,
    long: null
})
.write('stdout');

Flexio.pipe()
.trigger({
    type: 'http',
    url: '/crimes',
    method: 'GET'
})
.trigger({
    type: 'email',
    url: 'crimes'
})
.cache({
    time: 5000 // cache results for 5 seconds; results will be loaded from cache if concurrent requests are within 5 seconds
})
.task(t)
.save();

// create an API from a remote csv table
var t = Flexio.task()
.read('stdin')
.validate({
    year: {type: 'datetime'}
})
.read({
    url: 'https://www.github.com/flexio-data/someplace/chicago-crime.csv',
    method: 'GET',
})
.filter('date = ${year}')
.query({
    // TODO: graphql query here?
    id: null,
    type: null,
    date: null,
    lat: null,
    long: null
})
.write('stdout');

Flexio.pipe()
.trigger({
    type: 'http',
    url: '/crimes',
    method: 'GET'
})
.cache({
    time: 5000 // cache results for 5 seconds; results will be loaded from cache if concurrent requests are within 5 seconds
})
.task(t)
.save();

