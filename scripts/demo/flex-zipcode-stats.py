
# ---
# name: flex-zipcode-stats
# deployed: true
# title: Flex Zipcode Stats
# description: Returns zipcode statistic information
# task:
#   op: redirect
#   path: https://api.flex.io/v1/integration-getting-started/pipes/flex-zipcode-stats/run
# params:
#   - name: properties
#     type: string
#     description: The properties to return, given as a string or array; defaults to all properties; see "Returns" for available properties
#     required: false
#   - name: search
#     type: string
#     description: Search query to determine the rows to return, given as a string or array
#     required: false
# returns:
#   - name: zipcode
#     type: string
#     description: The zip code number
#   - name: city
#     type: string
#     description: The city associated with the zip code
#   - name: state
#     type: string
#     description: The state identifier where the zip code is located
#   - name: state_name
#     type: string
#     description: The state name where the zip code is located
#   - name: latitude
#     type: number
#     description: The latitude for the zip code
#   - name: longitude
#     type: number
#     description: The longitude for the zip code
#   - name: population_2010
#     type: integer
#     description: The population in 2010 for the zip code
#   - name: land_sq_miles
#     type: number
#     description: The land area in square miles for the zip code
#   - name: density_per_sq_mile
#     type: number
#     description: The population density per square mile for the zip code
# examples:
#   - '""'
#   - '"zipcode, population_2010"'
#   - '"", "CA"'
# ---
