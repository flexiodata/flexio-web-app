import {
  LoginResource,
  LogoutResource,
  SignupResource,
  HelpResource,
  UserResource,
  UserTokenResource,
  ProjectResource,
  ConnectionResource,
  PipeResource,
  ProcessResource,
  RightsResource,
  AdminResource,
  StreamResource,
  TestResource,
  TrashResource,
  ValidateResource
} from './resources'

/*
  // global Vue object
  Vue.http.get('/someUrl', [options]).then(successCallback, errorCallback);
  Vue.http.post('/someUrl', [body], [options]).then(successCallback, errorCallback);

  // in a Vue instance
  this.$http.get('/someUrl', [options]).then(successCallback, errorCallback);
  this.$http.post('/someUrl', [body], [options]).then(successCallback, errorCallback);

  // resource default actions
  get: {method: 'GET'},
  save: {method: 'POST'},
  query: {method: 'GET'},
  update: {method: 'PUT'},
  remove: {method: 'DELETE'},
  delete: {method: 'DELETE'}
*/

var GET = 'get'
var POS = 'save'
var DEL = 'delete'

export default {
  // auth
  login:                          function({ attrs })                   { return LoginResource[POS] ({}, attrs)                                                           },
  logout:                         function()                            { return LogoutResource[POS] ()                                                                   },
  signUp:                         function()                            { return SignupResource[POS] ({}, attrs)                                                          },
  checkSignup:                    function({ attrs })                   { return SignupResource[POS] ({ p1: 'check' }, attrs)                                             },

  // validation
  validate:                       function({ attrs })                   { return ValidateResource[POS] ({}, attrs)                                                        },

  // rights
  fetchRights:                    function({ attrs })                   { return RightsResource[GET] (attrs)                                                              },
  createRights:                   function({ attrs })                   { return RightsResource[POS] ({}, attrs)                                                          },
  updateRight:                    function({ eid, attrs })              { return RightsResource[POS] ({ eid }, attrs)                                                     },
  deleteRight:                    function({ eid })                     { return RightsResource[DEL] ({ eid })                                                            },

  // help
  sendEmailSupport:               function({ attrs })                   { return HelpResource[POS] ({}, attrs)                                                            },

  // user
  fetchUser:                      function({ eid })                     { return UserResource[GET] ({ eid })                                                              },
  fetchUserStatistics:            function({ eid })                     { return UserResource[GET] ({ eid, p1: 'statistics' })                                            },
  createUser:                     function({ attrs })                   { return UserResource[POS] ({}, attrs)                                                            },
  updateUser:                     function({ eid, attrs })              { return UserResource[POS] ({ eid }, attrs)                                                       },
  signUp:                         function({ attrs })                   { return UserResource[POS] ({}, attrs)                                                            },
  changePassword:                 function({ eid, attrs })              { return UserResource[POS] ({ eid, p1: 'changepassword'       }, attrs)                           },
  requestPasswordReset:           function({ attrs })                   { return UserResource[POS] ({      p1: 'requestpasswordreset' }, attrs)                           },
  resetPassword:                  function({ attrs })                   { return UserResource[POS] ({      p1: 'resetpassword'        }, attrs)                           },

  // token
  fetchUserTokens:                function({ eid })                     { return UserResource[GET] ({ eid, p1: 'tokens' })                                                },
  createUserToken:                function({ eid, attrs })              { return UserResource[POS] ({ eid, p1: 'tokens' }, attrs)                                         },
  deleteUserToken:                function({ eid, token_eid })          { return UserResource[DEL] ({ eid, p1: 'tokens', p2: token_eid })                                 },

  // project
  fetchProjects:                  function()                            { return ProjectResource[GET] ()                                                                  },
  fetchProject:                   function({ eid })                     { return ProjectResource[GET] ({ eid })                                                           },
  createProject:                  function({ attrs })                   { return ProjectResource[POS] ({}, attrs)                                                         },
  updateProject:                  function({ eid, attrs })              { return ProjectResource[POS] ({ eid }, attrs)                                                    },
  deleteProject:                  function({ eid })                     { return ProjectResource[DEL] ({ eid })                                                           },

  // project (member)
  fetchProjectMembers:            function({ eid })                     { return ProjectResource[GET] ({ eid, p1: 'followers' })                                          },
  createProjectMembers:           function({ eid, attrs })              { return ProjectResource[POS] ({ eid, p1: 'followers' }, attrs)                                   },
  deleteProjectMember:            function({ eid, member_eid })         { return ProjectResource[DEL] ({ eid, p1: 'followers', p2: member_eid })                          },

  // project (other)
  fetchProjectPipes:              function({ eid })                     { return ProjectResource[GET] ({ eid, p1: 'pipes' })                                              },
  fetchProjectConnections:        function({ eid })                     { return ProjectResource[GET] ({ eid, p1: 'connections' })                                        },
  fetchProjectTrash:              function({ eid })                     { return ProjectResource[GET] ({ eid, p1: 'trash' })                                              },
  bulkDeleteProjectItems:         function({ eid, attrs })              { return ProjectResource[DEL] ({ eid, p1: 'trash' }, attrs)                                       },

  // connection
  fetchConnections:               function()                            { return ConnectionResource[GET] ()                                                               },
  fetchConnection:                function({ eid })                     { return ConnectionResource[GET] ({ eid })                                                        },
  createConnection:               function({ attrs })                   { return ConnectionResource[POS] ({}, attrs)                                                      },
  updateConnection:               function({ eid, attrs })              { return ConnectionResource[POS] ({ eid }, attrs)                                                 },
  deleteConnection:               function({ eid })                     { return ConnectionResource[DEL] ({ eid })                                                        },
  describeConnection:             function({ eid, path })               { return ConnectionResource[GET] ({ eid, p1: 'describe', q: path })                               },
  testConnection:                 function({ eid, attrs })              { return ConnectionResource[POS] ({ eid, p1: 'connect' }, attrs)                                  },
  disconnectConnection:           function({ eid, attrs })              { return ConnectionResource[POS] ({ eid, p1: 'disconnect' }, attrs)                               },

  // pipe
  fetchPipes:                     function()                            { return PipeResource[GET] ()                                                                     },
  fetchPipe:                      function({ eid })                     { return PipeResource[GET] ({ eid })                                                              },
  createPipe:                     function({ attrs })                   { return PipeResource[POS] ({}, attrs)                                                            },
  updatePipe:                     function({ eid, attrs })              { return PipeResource[POS] ({ eid }, attrs)                                                       },
  deletePipe:                     function({ eid })                     { return PipeResource[DEL] ({ eid })                                                              },

  // pipe (other)
  fetchPipeProcesses: function({ eid, attrs }) {
    var start = attrs.start
    var limit = attrs.limit
    var order = attrs.order
    return PipeResource[GET] ({ eid, p1: 'processes', start, limit, order } )
  },

  // trash
  fetchTrash:                     function()                            { return TrashResource[GET] ()                                                                    },
  emptyTrash:                     function({ attrs })                   { return TrashResource[DEL] ({}, attrs)                                                           },

  // task
  createPipeTask:                 function({ eid, attrs })              { return PipeResource[POS] ({ eid, p1: 'tasks' }, attrs)                                          },
  updatePipeTask:                 function({ eid, task_eid, attrs })    { return PipeResource[POS] ({ eid, p1: 'tasks', p2: task_eid }, attrs)                            },
  deletePipeTask:                 function({ eid, task_eid })           { return PipeResource[DEL] ({ eid, p1: 'tasks', p2: task_eid })                                   },

  // process
  fetchProcess:                   function({ eid })                     { return ProcessResource[GET] ({ eid })                                                           },
  createProcess:                  function({ attrs })                   { return ProcessResource[POS] ({}, attrs)                                                         },
  cancelProcess:                  function({ eid })                     { return ProcessResource[POS] ({ eid, p1: 'cancel' }, {})                                         },

  // process (other)
  fetchProcessTaskInputInfo:      function({ eid, task_eid })           { return ProcessResource[GET] ({ eid, p1:'tasks', p2: task_eid, p3: 'input', p4: 'info' })        },
  fetchProcessTaskOutputInfo:     function({ eid, task_eid })           { return ProcessResource[GET] ({ eid, p1:'tasks', p2: task_eid, p3: 'output', p4: 'info' })       },

  // statistics
  fetchAdminStatistics:           function({ type })                    { return AdminResource[GET] ({ type })                                                            },

  // stream
  fetchStream:                    function({ eid })                     { return StreamResource[GET] ({ eid })                                                            },

  // test
  fetchTests:                     function()                            { return TestResource[GET] ({ p1: 'configure' })                                                  },
  runTest:                        function({ id })                      { return TestResource[GET] ({ p1: 'run', id })                                                    }
}
