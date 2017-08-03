import {
  TASK_TYPE_INPUT,
  TASK_TYPE_OUTPUT,
  TASK_TYPE_CONVERT,
  TASK_TYPE_EMAIL_SEND,
  TASK_TYPE_EXECUTE,
  TASK_TYPE_COMMENT
} from '../constants/task-type'

import {
  CONNECTION_TYPE_HTTP,
  CONNECTION_TYPE_RSS
} from '../constants/connection-type'

export default [
  {
    label: 'Add Input',
    task_json: {
      'type': TASK_TYPE_INPUT,
      'params': {
      }
    }
  },{
    label: 'Add Output',
    task_json: {
      'type': TASK_TYPE_OUTPUT,
      'params': {
      }
    }
  },{
    label: 'Input from Web',
    task_json: {
      'type': TASK_TYPE_INPUT,
      'params': {
        'items': [
          {
            'path': 'https://www.pexels.com/photo/mountain-filled-with-snow-under-blue-sky-during-daytime-51387'
          }
        ]
      },
      'metadata': {
        'connection_type': CONNECTION_TYPE_HTTP
      }
    }
  },{
    label: 'Convert From CSV to JSON',
    task_json: {
      'type': TASK_TYPE_CONVERT,
      'params': {
        'input': {
          'format': 'delimited',
          'delimiter': '{comma}',
          'qualifier': '{double-quote}'
        },
        'output': {
          'format': 'json'
        }
      }
    }
  },{
    label: 'Execute Python Code',
    task_json: {
      'type': TASK_TYPE_EXECUTE,
      'params': {
        'lang': 'python',
        // "Hello, World!" example code
        'code': 'ZGVmIGZsZXhpb19oYW5kbGVyKGlucHV0LCBvdXRwdXQpOgogICAgd3JpdGVyID0gb3V0cHV0LmNyZWF0ZShuYW1lPSdIZWxsbycpCiAgICBpZiAnbWVzc2FnZScgaW4gaW5wdXQuZW52OgogICAgICAgIHdyaXRlci53cml0ZShpbnB1dC5lbnZbJ21lc3NhZ2UnXSkKICAgIGVsc2U6ICAKICAgICAgICB3cml0ZXIud3JpdGUoJ0hlbGxvLCBXb3JsZCEnKQ=='
      }
    }
  },{
    label: 'Send Email',
    task_json: {
      'type': TASK_TYPE_EMAIL_SEND,
      'params': {
        'to': ['john.smith@example.com'],
        'subject': 'My Subject',
        'body_text': 'This is a test of Flex.io'
      }
    }
  }
]
