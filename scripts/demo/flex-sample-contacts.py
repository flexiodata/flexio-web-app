
# ---
# name: flex-sample-contacts
# deployed: true
# title: Flex Sample Contacts
# description: Returns sample contact information
# task:
#   op: redirect
#   path: https://api.flex.io/v1/integration-getting-started/pipes/flex-sample-contacts/run
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
#   - name: id
#     type: integer
#     description: The id of the contact
#   - name: first_name
#     type: string
#     description: The first name of the contact
#   - name: last_name
#     type: string
#     description: The last name of the contact
#   - name: street_address
#     type: string
#     description: The street address of the contact
#   - name: city
#     type: string
#     description: The city where the contact is located
#   - name: state
#     type: string
#     description: The state where the contact is located
#   - name: zipcode
#     type: string
#     description: The zipcode where the contact is located
#   - name: phone
#     type: string
#     description: The phone number of the contact
#   - name: email
#     type: string
#     description: The email of the contact
#   - name: birthday
#     type: string
#     description: The birthday of the contact
# examples:
#   - '""'
#   - '"first_name, last_name, email"'
#   - '"first_name, city, state, zipcode", "CA"'
# ---
