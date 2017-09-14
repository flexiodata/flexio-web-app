

// API TO MINIMIZE LATENCY/BANDWIDTH: query for a subset of the data; cache query results for low latency
// Statisfies following usecase:
// * Bring down only a subset of the data that an API typically returns to minimize bandwidth and latency

// simple proxy; query the endpoint, passing on the params; limit the result and cache it
var t = Flexio.task()
.read('stdin')
.read({
    type: 'request',
    url: 'https://www.eventbriteapi.com/v3/users/me/owned_event_attendees/',
    method: 'GET',
    authentication: {
        type: 'header',
        connection: 'my-eventbrite'
    },
    params: '=stdin'
})
.query({
    // TODO: graphql query here?
    profile: null,
    status: null
})
.write('stdout');

Flexio.pipe()
.trigger({
    type: 'http',
    url: '/https://www.flexio.com/<flexiouser>/users/me/owned_event_attendees',
    method: 'GET'
})
.cache({
    time: 1000 // cache results for 1 second; results will be loaded from cache if concurrent requests are within 5 seconds
})
.task(t)
.save();
