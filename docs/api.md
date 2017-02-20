


#Introduction

The Flex.io API allows users to run pipe, configure and manage the running pipe
process, and read the resulting pipe process output.  Users can set inputs and
read outputs so that files can be easily processed with the pipe logic at runtime.

#Getting Started

1. Create an account
2. Get your API key
   - go to the account area
   - click on the API tab
   - click on "Create API Key"
3. Create your first API request:
   - create an HTTP GET Request for "https://www.flex.io/api/v1/status"
   - set the Authorization header to your API key: "Authorization: Bearer [[app:Authorization]]"
   - set the content type to JSON: "Content-Type: application/json; charset=utf-8"

#Example

To return basic status information about the current user, enter the following:

[block:code]
curl -X GET 'https://www.flex.io/api/v1/status'
-H "Authorization: Bearer [[app:Authorization]]"
-H "Content-Type: application/json; charset=utf-8"
[/block]

You'll get back something like the following:

[block:code]
{
    "user_loggedin": true,
    "user_name": "john",
    "user_eid": "ktzsnpqgvwjm"
}
[/block]


#Overview

All API calls use the following root: https://www.flex.io/api/v1/

#Authentication

Most API calls must be authenticated. API calls are authenticated by passing the API
key in the Authorization HTTP header:
Authorization: Bearer [[app:Authorization]]

#Available Calls

The API endpoints are:

GET	 /status
GET	 /user/:eid
GET  /projects/
GET  /projects/:eid/pipes
GET  /pipes/:eid
GET  /pipes/:eid/processes
GET  /processes/:eid
POST /processes/:eid/input
POST /processes/:eid/run

Following are specifics for each one.



#Summary

Status Information

#Request

GET /status

#Description

Returns information about the currently logged in user.

#Responses

[block:code]
{
  "codes": [
    {
      "code": "{\n      \"user_loggedin\": true,\n      \"user_name\": \"aaron\",\n      \"user_eid\": \":eid\"\n  }",
      "language": "json",
      "name": "200 Success"
    }
  ]
}
[/block]

#Attributes
[block:parameters]
{
  "data": {

    "h-0": "Name",
    "h-1": "Type",
    "h-2": "Description",

    "0-0": "user_loggedin",
    "0-1": "boolean",
    "0-2": "True if a user is logged in, and false otherwise.",

    "1-0": "user_name",
    "1-1": "string",
    "1-2": "The username of the user that's currently logged in.",

    "2-0": "user_eid",
    "2-1": "string",
    "2-2": "The id of the user that's currently logged in."

  },
  "cols": 3,
  "rows": 3
}
[/block]



#Summary

Project List

#Request

GET /projects

#Description

Returns an array of information about the projects that a user owns and is following.

#Responses

[block:code]
{
  "codes": [
    {
      "code": "{}",
      "language": "json",
      "name": "200 Success"
    }
  ]
}
[/block]

#Attributes
[block:parameters]
{
  "data": {

    "h-0": "Name",
    "h-1": "Type",
    "h-2": "Description",

    "0-0": "eid",
    "0-1": "string",
    "0-2": "The id of the project.",

    "1-0": "eid_type",
    "1-1": "string",
    "1-2": "The type of object.",

    "2-0": "eid_status",
    "2-1": "string",
    "2-2": "The current status of an object.",

    "3-0": "name",
    "3-1": "string",
    "3-2": "The name of the project.",

    "4-0": "description",
    "4-1": "string",
    "4-2": "A description of the project.",

    "5-0": "follower_count",
    "5-1": "integer",
    "5-2": "The number of users following the project.",

    "6-0": "pipe_count",
    "6-1": "integer",
    "6-2": "The number of pipes in the project.",

    "7-0": "owned_by",
    "7-1": "object",
    "7-2": "Information about who owns the project.",

    "8-0": "created_by",
    "8-1": "object",
    "8-2": "Information about who created the project.",

    "9-0": "created",
    "9-1": "datetime",
    "9-2": "When the project was created.",

    "10-0": "created",
    "10-1": "datetime",
    "10-2": "When the project was last updated."

  },
  "cols": 3,
  "rows": 11
}
[/block]



#Summary

Project Information

#Request

GET /projects/:eid

#Description

Returns information about the projects that a user owns and is following.

#Responses

[block:code]
{
  "codes": [
    {
      "code": "",
      "language": "json",
      "name": "200 Success"
    }
  ]
}
[/block]

#Attributes
[block:parameters]
{
  "data": {

    "h-0": "Name",
    "h-1": "Type",
    "h-2": "Description",

    "0-0": "eid",
    "0-1": "string",
    "0-2": "The id of the project.",

    "1-0": "eid_type",
    "1-1": "string",
    "1-2": "The type of object.",

    "2-0": "eid_status",
    "2-1": "string",
    "2-2": "The current status of an object.",

    "3-0": "name",
    "3-1": "string",
    "3-2": "The name of the project.",

    "4-0": "description",
    "4-1": "string",
    "4-2": "A description of the project.",

    "5-0": "follower_count",
    "5-1": "integer",
    "5-2": "The number of users following the project.",

    "6-0": "pipe_count",
    "6-1": "integer",
    "6-2": "The number of pipes in the project.",

    "7-0": "owned_by",
    "7-1": "object",
    "7-2": "Information about who owns the project.",

    "8-0": "created_by",
    "8-1": "object",
    "8-2": "Information about who created the project.",

    "9-0": "created",
    "9-1": "datetime",
    "9-2": "When the project was created.",

    "10-0": "created",
    "10-1": "datetime",
    "10-2": "When the project was last updated."

  },
  "cols": 3,
  "rows": 11
}
[/block]



#Summary

Pipe List

#Request

GET /projects/:eid/pipes

#Description

Returns an array of information about the pipes that are in a project.

#Responses

[block:code]
{
  "codes": [
    {
      "code": "",
      "language": "json",
      "name": "200 Success"
    }
  ]
}
[/block]

#Attributes

[block:parameters]
{
  "data": {

    "h-0": "Name",
    "h-1": "Type",
    "h-2": "Description",

    "0-0": "eid",
    "0-1": "string",
    "0-2": "The id of the pipe.",

    "1-0": "eid_type",
    "1-1": "string",
    "1-2": "The type of object.",

    "2-0": "eid_status",
    "2-1": "string",
    "2-2": "The current status of an object.",

    "3-0": "name",
    "3-1": "string",
    "3-2": "The name of the pipe.",

    "4-0": "description",
    "4-1": "string",
    "4-2": "A description of the pipe.",

    "5-0": "project",
    "5-1": "object",
    "5-2": "Information about the project that a pipe is a member of.",

    "6-0": "owned_by",
    "6-1": "object",
    "6-2": "Information about who owns the project.",

    "7-0": "created_by",
    "7-1": "object",
    "7-2": "Information about who created the project.",

    "8-0": "created",
    "8-1": "datetime",
    "8-2": "When the project was created.",

    "9-0": "created",
    "9-1": "datetime",
    "9-2": "When the project was last updated."

  },
  "cols": 3,
  "rows": 10
}
[/block]



#Summary

Pipe Information

#Request

GET /pipes/:eid

#Description

Returns information about a specific pipe that is in a project.

#Responses

[block:code]
{
  "codes": [
    {
      "code": "",
      "language": "json",
      "name": "200 Success"
    }
  ]
}
[/block]

#Attributes

[block:parameters]
{
  "data": {

    "h-0": "Name",
    "h-1": "Type",
    "h-2": "Description",

    "0-0": "eid",
    "0-1": "string",
    "0-2": "The id of the pipe.",

    "1-0": "eid_type",
    "1-1": "string",
    "1-2": "The type of object.",

    "2-0": "eid_status",
    "2-1": "string",
    "2-2": "The current status of an object.",

    "3-0": "name",
    "3-1": "string",
    "3-2": "The name of the pipe.",

    "4-0": "description",
    "4-1": "string",
    "4-2": "A description of the pipe.",

    "5-0": "project",
    "5-1": "object",
    "5-2": "Information about the project that a pipe is a member of.",

    "6-0": "owned_by",
    "6-1": "object",
    "6-2": "Information about who owns the project.",

    "7-0": "created_by",
    "7-1": "object",
    "7-2": "Information about who created the project.",

    "8-0": "created",
    "8-1": "datetime",
    "8-2": "When the project was created.",

    "9-0": "created",
    "9-1": "datetime",
    "9-2": "When the project was last updated."

  },
  "cols": 3,
  "rows": 10
}
[/block]


