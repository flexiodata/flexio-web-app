

// API ENRICH: enrich a list of email addresses with event information from Eventbrite;
// the following cases follow a similar pattern; here's an example for one of them:
// * (Enrich/supplement data from an API)
// * 3.1 Compile a set of location-related data from a list of addresses
// * 3.3 Enrich a list of email addresses with event information from Eventbrite

// enrich a list of email addresses with event information from Eventbrite
var t = Flexio.task()
.read({
    type: 'request',
    name: 'attendees',
    url: 'https://www.eventbriteapi.com/v3/users/me/owned_event_attendees/',
    method: 'GET',
    authentication: {
        type: 'header',
        connection: 'my-eventbrite'
    },
    while: {
        paginator: {
        // TODO: specify pagination information for getting recursive requests;
        // see internal implementation for API wrappers for possible solution
    }}
})
.read({
    type: 'connection',
    name: 'emails',
    path: 'flexio://connection/emails/chicago-list'
})
.assoc({
    name: 'email-info',
    ref1: 'attendees.profile.email',
    ref2: 'email'
})
.query({
    // TODO: graphql query here?
    id: null,
    email: '=emails.email',
    name: '=email_info.name',
    city: '=email_info.city'
})
.write('stdout');

Flexio.pipe()
.trigger({
    type: 'http',
    url: '/events/watchlist',
    method: 'GET'
})
.cache({
    time: 5000 // cache results for 5 seconds; results will be loaded from cache if concurrent requests are within 5 seconds
})
.task(t)
.save();

