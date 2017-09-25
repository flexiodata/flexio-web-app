


// EXAMPLE 1; SAVE UPLOADED FILES TO DIFFERENT LOCATIONS
// DESCRIPTION: route uploaded files to different destinations based on content
Flexio.pipe()
.params({
    "upload" : {"type": "file"}
})
.tag("upload", function(input, output, context) {
    switch(input.upload.mime_type)
    {
        default: output.upload.tag.append("unknown"); return;
        case 'text/csv': output.upload.tag.append('csv'); return;
        case 'text/plain': output.upload.tag.append('txt'); return;
        case 'image/png': output.upload.tag.append('image'); return;
        case 'image/jpeg': output.upload.tag.append('image'); return;
    }
})
.save("upload", "dropbox/data", {tagged: ["csv", "txt"]})  // save content tagged 'csv' or 'txt' to the 'data' folder
.save("upload", "dropbox/images", {tagged: ["image"]})     // save content tagged 'image' to the 'images' folder
.save("upload", "dropbox/review", {tagged: ["unknown"]})   // save unknown content to the 'review' folder
.save("upload", "s3")                                      // archive everything on s3
.email({                                                   // email when something goes wrong; TODO: attempt at trigger/tag-based email logic
    "to": ["aaron@flex.io"],
    "message": "Need to review uploaded data",
    "when": {"tagged": "unkown"}
});



// EXAMPLE 2A: FILE UPLOAD VALIDATION
// DESCRIPTION: get an uploaded file from a form upload, make sure it's a CSV and save it to a filestore
Flexio.pipe()
.input('upload', { as: 'file' }) // concept showing input using existing 'input' rather than 'params'
.assert({
  'test': { 'mime_type': 'text/csv' }, // what to test for; check to make sure the file is a CSV
  'response': {
    'success': false,
    'message': 'The file is not a CSV file.'
  }
})
.convert('csv', 'table') // convert file to CSV
.output('dropbox', 'fxtest103-dropbox', '/test_data') // save a copy of the file to Dropbox (or some other connection handle)
.return({
  'success': true,
  'message': 'The file is a CSV file and has been uploaded successfully.'
});



// EXAMPLE 2B; FILE UPLOAD VALIDATION
// DESCRIPTION: get an uploaded file from a form upload, make sure it's a CSV with certain fields, and save it to a filestore
Flexio.pipe()
.params({
    "upload" : {"type": "file", "mime_type": "text/csv"}                                 // look for a file with a text/csv mime_type, posted to the 'upload' form parameter
})
.convert("upload", {"to": "table", "header": true, "delimiter": ",", "qualifier": "\""}) // convert file stored in 'upload' to a table
.assert({
    // what to test for; check to make sure the table has the following columns
    'test': {
      'columns': [
        { 'name': 'id', 'type': 'integer' },
        { 'name': 'email', 'type': 'string' },
        { 'name': 'event_name', 'type': 'string' },
        { 'name': 'event_date', 'type': 'date' }
      ]
    },
    // response to return in event of failure
    'response': {
      'success': false,
      'message': 'The file structure does not match the required structure. Please make sure the file includes the following columns: `id`, `email`, `event_name` and `event_date`.'
    }
})
.save("upload", "local")   // save a copy of the file to local storage; could be 'dropbox' or some other connection handle
.return({
    "success": true,
    "path": "upload.path" // get the path associated with the upload variable; treat variables as objects with a base value, but with other properties?
});



// EXAMPLE 3; FORM INPUT VALIDATION
// DESCRIPTION: take input from a webform, make sure the address is valid and save valid data to an API endpoint
Flexio.pipe()
.params({
    "name": {"type": "string"},
    "address": {"type": "string"},
    "city": {"type": "string"},
    "state": {"type": "string"},
    "zip": {"type": "string"}
})
.request("maprequest", {  // check the input parameters against the mapzen API; store results in 'maprequest' variable
    "method": "GET",
    "url": "https://search.mapzen.com/v1/search?text=${address},${city},{$state}" // TODO: variables probably should be referenceable as properties of the pipe, allowing variables to be queried and changed; so Flexio.pipe().properities to get a list of variables?
})
.assert("maprequest", {  // make sure the result returned from the maprequest contains 'features', which means a valid address was found
    "callback": function(input, output, context) { // use a callback function to demonstrate how we might check for more complex features using an inline code snippet
        if (input.features.length > 0)
          return true;
            else
          return false;
    }
})
.request('crm-update', { // take the validated parameters and output them to the contacts api endpoint
    "method": "POST",
    "url": "https://api.crm.com/v1/contacts",
    "params": {
        "name": "${name}",
        "address": "${address}",
        "city": "${city}",
        "state": "${state}",
        "zip": "${zip}"
    }
})
.return({
    "success": true // TODO: we'll want to return the result of crm-update; if the API indicates success, then we'd return true here; otherwise false
});



// EXAMPLE 4; FORM INPUT VALIDATION
// DESCRIPTION: take input from a webform, make sure the email is valid based on a table; register new users with an API and save the email so it can't be used again
Flexio.pipe()
.params({
    "name": {"type": "string"},
    "email": {"type": "string"}
})
.find("lookup-result", "google-drive/attendees", pipe.params.email) // see if the email is located in the list of attendees; note: experiment with different variable schema; note: local key/value storage would give better performance than gdrive
.assert(pipe.params.lookup-result, false)
.append("google-drive/attendees", {"email" : pipe.params.email})
.request('event-update', { // take the validated parameters and output them to the attendees api endpoint
    "method": "POST",
    "url": "https://api.events.com/v1/attendees",
    "params": {
        "email": pipe.params.email
    }
})
.return({
    "success": pipe.params.crm-updated.success
});



// EXAMPLE 5; FILTER/FORMAT FILE-BASED DATA FOR DISPLAY
// DESCRIPTION: take data from a CSV somewhere (or uploaded data in a local store); filter and format it for display on a map (chicago crime example)
var year = ''; // get year directly from form
Flexio.pipe()
.input('https://raw.githubusercontent.com/flexiodata/examples/master/chicago-crime/chicago-homicides-2001-2016.csv')
.convert('csv', 'table')
.select('date', 'year', 'block', 'description', 'location description', 'latitude', 'longitude')
.filter("year = '"+year+"'") // year here comes from the form; this code is inline in the client, so don't have to route the variable through the params
.convert('table', 'json')
.cache(3600) // cache results for an hour
.return();   // TODO: in the past we always echoed the output; what 'handle' do we use for to denote that we want to echo the result of the pipe?



// EXAMPLE 6; AGGREGATE/FILTER API DATA FOR DISPLAY
// DESCRIPTION: take data from an API that's paginated, read through all the entries, add up the entires per day, and output the result in JSON for display in a time chart
Flexio.pipe()
.request({   // make a request to the events API using the auth method and credentials stored in "my-events"
    "method": "GET",
    "url": "https://api.events.com/events",
    "connection": "my-events",
    "pagination": "cursor"  // TODO: obviously, we need to decide how to handle multiple request with pagination; just trying to show the idea that we're reading through multiple requests
})
.javascript(function(input, output) {
    var totals = array();
    for (i in input.result)
        totals[input.result.date]++;
    output.totals;
})
.return(output.totals);



// EXAMPLE 7; ENRICH A LIST OF ADDRESSES WITH INFO FROM AN API
// DESCRIPTION: here's an example of a pipe that reads through a predetermined list of addresses and enriches
// it with county info; in this example, we explore two possibilities (not necessarily exclusive) for querying
// and enriching a list of items from JSON (could use a CSV from the web or a connection with a simple convert step):
// 1) in the first select step, we take the location list, read through each item in the location list, and
// output the requested fields to the 'temp' location (memory or temp file until some type of 'saveas' call?);
// in the case of the 'county' variable, we use the current values of the row we're on to do a lookup and return
// the county info from the result;
// 2) in the second select step, we take the result of the first select stored in 'temp' and we also iterate over
// each row; however, in this case, we output each row using the results of a javascript callback function; this
// callback function takes the parameters from the input row, does a series of requests on them and for each input
// row, writes out an output row (we could also use this to filter)
var mapzen_api_key = 'abc';
Flexio.pipe()
    .params({ // pipe input; no parameters are defined for this pipe
    })
    .define('location', 'https://www.api.com/whole-foods/addresses.json') // define an alias for the locations
    .define('mapinfo', 'https://search.mapzen.com/v1/search')             // define an alias for the mapinfo search
    .select('temp', 'location', {                                         // reads through each item in 'location' and outputs constructed object into 'temp'
        "address": "location.address",
        "city": "location.city",
        "state": "location.state",
        "zip": "location.zip",
        "county": {                                                       // get the county by looking up the info from the mapinfo alias with the appropriate variables
            "lookup": "mapinfo?text=:location.address,:location.city,:location.state&api_key=" + mapzen_api_key, // get api key from a variable; could use connection alias info
            "return": "features[0].properties.county"
        }
    })
    .select('result', 'temp', function(input, output, context) {          // read through each item in temp and use the callback function for writing each output row
        output = input; // copy all the input values the output
        var geo = context.request('https://api.datausa.io/attrs/search/?q=' + input.county + 'county,' + input.state + '&kind=geo');
        geo = _.get(geo, 'data[0][0]', '');
        output.pop = context.request('https://api.datausa.io/api/?show=geo&year=latest&sumlevel=county&required=pop&geo=' + geo);
        output.age = context.request('https://api.datausa.io/api/?show=geo&year=latest&sumlevel=county&required=age&geo=' + geo);
        output.poverty = context.request('https://api.datausa.io/api/?show=geo&year=latest&sumlevel=county&required=income_below_poverty&geo=' + geo);
    })
    .cache('result', 60) // cache 'result' for 60 seconds; subsequent calls to running pipe will return cached result if previously executed in 60 seconds
    .return('result')    // return 'result'
    .run();



// EXAMPLE 8; TAKE INPUT FROM A FORM, ENRICH IT AND ECHO IT BACK FOR DISPLAY
// DESCRIPTION: here's an example of a pipe that takes an address as a parameter and returns county information;
// this is similar to example #1, except that we're focused on getting a single result set; this could be used
// for building a static client with a UI similar to datausa; the main difference between this example and the
// previous example is that we don't have any list to iterate over, so a select doesn't make any sense (although
// we could potentially use a null input); as an alternative we explore a create function with a callback; this
// create function takes the parameters from the pipe, then invokes the callback function, which creates a new
// variable named 'temp' containing the result of the callback; the pipe then returns this value
var mapzen_api_key = 'abc';
Flexio.pipe()
    .params({
        "address" : {"type" : "string"}
    })
    create('result', function(input, output, context) {          // create a new result without any initial input

        var location = context.request('https://search.mapzen.com/v1/search?text=' + input.params.address + '&api_key=' + mapzen_api_key)
        location = JSON.parse(location);

        output.address = location.address;
        output.city = location.city;
        output.state = location.state;
        output.county = location.county;

        var geo = context.request('https://api.datausa.io/attrs/search/?q=' + output.county + 'county,' + output.state + '&kind=geo');
        geo = _.get(geo, 'data[0][0]', '');

        output.pop = context.request('https://api.datausa.io/api/?show=geo&year=latest&sumlevel=county&required=pop&geo=' + geo);
        output.age = context.request('https://api.datausa.io/api/?show=geo&year=latest&sumlevel=county&required=age&geo=' + geo);
        output.poverty = context.request('https://api.datausa.io/api/?show=geo&year=latest&sumlevel=county&required=income_below_poverty&geo=' + geo);
    })
    .cache('result', 60) // cache 'result' for 60 seconds; subsequent calls to running pipe will return cached result if previously executed in 60 seconds
    .return('result')    // return 'result'
    .run();


