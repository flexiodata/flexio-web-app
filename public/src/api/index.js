import { AxiosResource } from './resources'

const V2_GET = 'get'
const V2_POS = 'post'
const V2_PUT = 'put'
const V2_DEL = 'delete'

export default {
  // connections
  v2_fetchConnections:     function(team_name)                           { return AxiosResource(team_name)[V2_GET] (`/connections`)                                        },
  v2_fetchConnection:      function(team_name, object_identifier)        { return AxiosResource(team_name)[V2_GET] (`/connections/${object_identifier}`)                   },
  v2_createConnection:     function(team_name, attrs)                    { return AxiosResource(team_name)[V2_POS] (`/connections`, attrs)                                 },
  v2_updateConnection:     function(team_name, object_identifier, attrs) { return AxiosResource(team_name)[V2_POS] (`/connections/${object_identifier}`, attrs)            },
  v2_deleteConnection:     function(team_name, object_identifier)        { return AxiosResource(team_name)[V2_DEL] (`/connections/${object_identifier}`)                   },
  v2_testConnection:       function(team_name, object_identifier, attrs) { return AxiosResource(team_name)[V2_POS] (`/connections/${object_identifier}/connect`, attrs)    },
  v2_disconnectConnection: function(team_name, object_identifier, attrs) { return AxiosResource(team_name)[V2_POS] (`/connections/${object_identifier}/disconnect`, attrs) },

  // pipes
  v2_fetchPipes:           function(team_name)                           { return AxiosResource(team_name)[V2_GET] (`/pipes`)                                              },
  v2_fetchPipe:            function(team_name, object_identifier)        { return AxiosResource(team_name)[V2_GET] (`/pipes/${object_identifier}`)                         },
  v2_createPipe:           function(team_name, attrs)                    { return AxiosResource(team_name)[V2_POS] (`/pipes`, attrs)                                       },
  v2_updatePipe:           function(team_name, object_identifier, attrs) { return AxiosResource(team_name)[V2_POS] (`/pipes/${object_identifier}`, attrs)                  },
  v2_deletePipe:           function(team_name, object_identifier)        { return AxiosResource(team_name)[V2_DEL] (`/pipes/${object_identifier}`)                         },

  // processes
  v2_fetchProcesses:       function(team_name, attrs)                    { return AxiosResource(team_name)[V2_GET] (`/processes`, attrs)                                   },
  v2_fetchProcess:         function(team_name, object_identifier)        { return AxiosResource(team_name)[V2_GET] (`/processes/${object_identifier}`)                     },
  v2_createProcess:        function(team_name, attrs)                    { return AxiosResource(team_name)[V2_POS] (`/processes`, attrs)                                   },
  v2_cancelProcess:        function(team_name, object_identifier)        { return AxiosResource(team_name)[V2_POS] (`/processes/${object_identifier}/cancel`)              },
  v2_runProcess:           function(team_name, object_identifier, cfg)   { return AxiosResource(team_name)[V2_POS] (`/processes/${object_identifier}/run`, {}, cfg)        },
  v2_fetchProcessLog:      function(team_name, object_identifier)        { return AxiosResource(team_name)[V2_GET] (`/processes/${object_identifier}/log`)                 },
  v2_fetchProcessSummary:  function(team_name)                           { return AxiosResource(team_name)[V2_GET] (`/processes/summary`)                                  },

  // streams
  v2_fetchStream:          function(team_name, object_identifier)        { return AxiosResource(team_name)[V2_GET] (`/streams/${object_identifier}`)                       },

  // vfs
  v2_vfsListFiles:         function(team_name, path)                     { return AxiosResource(team_name)[V2_GET] (`/vfs/list`, { q: path })                              },
//v2_vfsGetFile:           function(team_name, path)                     { return AxiosResource(team_name)[V2_GET] (TODO)                                                  },
//v2_vfsPutFile:           function(team_name, path)                     { return AxiosResource(team_name)[V2_PUT] (TODO)                                                  },
//v2_vfsCreateDirectory:   function(team_name, path)                     { return AxiosResource(team_name)[V2_PUT] (TODO)                                                  },

  // users
  v2_fetchUser:            function(team_name)                           { return AxiosResource(team_name)[V2_GET] (`/account`)                                            },
  v2_updateUser:           function(team_name, attrs)                    { return AxiosResource(team_name)[V2_POS] (`/account`, attrs)                                     },
  v2_deleteUser:           function(team_name, attrs)                    { return AxiosResource(team_name)[V2_DEL] (`/account`, attrs)                                     },
  v2_changePassword:       function(team_name, attrs)                    { return AxiosResource(team_name)[V2_POS] (`/account/credentials`, attrs)                         },

  // cards
  v2_fetchCards:           function(team_name)                           { return AxiosResource(team_name)[V2_GET] (`/account/cards`)                                      },
  v2_createCard:           function(team_name, attrs)                    { return AxiosResource(team_name)[V2_POS] (`/account/cards`, attrs)                               },
  v2_deleteCard:           function(team_name, object_identifier)        { return AxiosResource(team_name)[V2_DEL] (`/account/cards/${object_identifier}`)                 },

  // tokens
  v2_fetchTokens:          function(team_name)                           { return AxiosResource(team_name)[V2_GET] (`/auth/keys`)                                          },
  v2_createToken:          function(team_name)                           { return AxiosResource(team_name)[V2_POS] (`/auth/keys`)                                          },
  v2_deleteToken:          function(team_name, object_identifier)        { return AxiosResource(team_name)[V2_DEL] (`/auth/keys/${object_identifier}`)                     },

  // validation
  v2_validate:             function(team_name, attrs)                    { return AxiosResource(team_name)[V2_POS] (`/validate`, attrs)                                               },

  // auth
//v2_signUp:               function(attrs)                                     { return AxiosResource(null)[V2_POS] (`/signup`)                                                        },
//v2_login:                function(attrs)                                     { return AxiosResource(null)[V2_POS] (`/login`)                                                         },
  v2_logout:               function()                                          { return AxiosResource(null)[V2_POS] (`/logout`)                                                        },
  v2_resetPassword:        function(attrs)                                     { return AxiosResource(null)[V2_POS] (`/resetpassword`, attrs)                                          },

  // admin
  v2_fetchAdminProcesses:  function(attrs)                                     { return AxiosResource('admin')[V2_GET] (`/info/processes`, attrs)                                      },
  v2_fetchAdminTests:      function()                                          { return AxiosResource('admin')[V2_GET] (`/tests/configure`)                                            },
  v2_runAdminTest:         function(attrs)                                     { return AxiosResource('admin')[V2_GET] (`/tests/run`, attrs)                                           },
}

/*
  VFS Notes...

  1) List Directory      GET https://localhost/api/me/v2/vfs/list?q=/
  2) Get File            GET https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder/aphist.csv
  3) Put File            PUT https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder/aphist.csv
  4) Create Directory    PUT https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder ?????
  5) Delete Files        DEL https://localhost/api/me/v2/vfs/my-name/my-folder/my-subfolder/aphist.csv
*/
