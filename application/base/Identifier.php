<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-06-17
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class Identifier
{
    public static function isValid($identifier, &$message = '') : bool
    {
        // identifiers (e.g. usernames, or other 'named handles') must
        // follow the following rules:
        //     1. be a lowercase string
        //     2. must not be an eid
        //     3. have a length between 3 and 80 chars
        //     4. start with a letter
        //     5. not be one of a list of various reserved words

        // initialize the message parameter
        $message = '';

        // make sure identifier is a lowercase string
        if (!is_string($identifier))
            return false;
        if ($identifier !== strtolower($identifier))
        {
            $message = _('An identifier must be lowercase');
            return false;
        }

        if (\Flexio\Base\Eid::isValid($identifier))
        {
            $message = _('An identifier must not be an eid');
            return false;
        }

        // make sure identifier is correct length
        $identifier_minlength = 3;
        $identifier_maxlength = 80;
        $identifier_length = strlen($identifier);
        if ($identifier_length < $identifier_minlength || $identifier_length > $identifier_maxlength)
        {
            $message = _('An identifier must be between 3 and 80 characters');
            return false;
        }

        // make sure identifier starts with a letter; the rest of the
        // identifier must be made up of a letter, number, underscore
        // or hyphen
        if (!preg_match('/^[a-z][a-z0-9_-]{2,79}$/', $identifier))
        {
            $message = _('An identifier may only contain letters, numbers, hyphens and underscores');
            return false;
        }

        if (self::isKeyWord($identifier))
        {
            $message = _('An identifier must not be a reserved or illegal word');
            return false;
        }

        // valid identifier
        return true;
    }

    public static function isKeyWord($word) : bool
    {
        if (self::isReservedWord($word))
            return true;

        if (self::isIllegalWord($word))
            return true;

        return false;
    }

    private static function isReservedWord($word) : bool
    {
        switch ($word)
        {
            default:
                return false;

            // company related
            case 'flex':
            // case 'flexio': // allow flexio, since we want to use it as our public account
            case 'flexible':
            case 'flexiodata':
            //case 'goldprairie':
            //case 'kirix':
            //case 'kirixresearch':
                return true;

            // public file and folder names
            case 'api':
            case 'css':
            case 'dist':
            case 'doc':
            case 'fonts':
            case 'img':
            case 'js':
            case 'less':
                return true;
            /*
            TODO: do we need to include files in public folder?
                  '.htaccess', 'apple-touch-icon', etc.
            */

            // web-page related names
            case 'about':
            case 'account':
            case 'accounts':
            case 'blog':
            case 'index':
            case 'help':
            case 'home':
            case 'pricing':
            case 'support':
            case 'image':
            case 'images':
            case 'info':
            case 'features':
            case 'product':
            case 'products':
            case 'connections':
            case 'integrations':
            case 'input':
            case 'inputs':
            case 'output':
            case 'outputs':
            case 'contact':
            case 'contactus':
            case 'aboutus':
            case 'ourstory':
            case 'shop':
            case 'store':
            case 'solution':
            case 'solutions':
            case 'whatisflexio':
            case 'article':
            case 'articles':
            case 'extension':
            case 'extensions':
            case 'widgets':
            case 'downloads':
            case 'download':
            case 'software':
                return true;

            // controller-related names
            case 'account':
            case 'api':
            case 'common':
            case 'debug':
            case 'default':
            case 'install':
            case 'phantom':
            case 'test':
            case 'ui':
            case 'util':
                return true;

            // more controller-related names
            case 'signin':
            case 'signup':
            case 'embed':
            case 'connectionauth':
            case 'forgotpassword':
            case 'resetpassword':
            case 'shareauth':
                return true;

            // more controller-related names
            case 'join':
            case 'admin':
            case 'administrator':
            case 'root':
                return true;

            // api-related names
            case 'alias':
            case 'aliases':
            case 'id':
            case 'ids':
            case 'eid':
            case 'eids':
            case 'cname':
            case 'cnames':
            case 'name':
            case 'names':
            case 'description':
            case 'descriptions':
            case 'type':
            case 'types':
            case 'comment':
            case 'comments':
            case 'connection':
            case 'connections':
            case 'container':
            case 'containers':
            case 'install':
            case 'installation':
            case 'batch':
            case 'batches':
            case 'queue':
            case 'queues':
            case 'job':
            case 'jobs':
            case 'process':
            case 'processes':
            case 'subprocess':
            case 'subprocesses':
            case 'notification':
            case 'project':
            case 'projects':
            case 'repository':
            case 'repositories':
            case 'resource':
            case 'resources':
            case 'slug':
            case 'slugs':
            case 'system':
            case 'systems':
            case 'trash':
            case 'restore':
            case 'user':
            case 'users':
            case 'workspace':
            case 'workspace':
            case 'pipe':
            case 'pipes':
            case 'task':
            case 'tasks':
            case 'stream':
            case 'streams':
            case 'follower':
            case 'followers':
            case 'reply':
            case 'replies':
            case 'status':
            case 'statuses':
            case 'login':
            case 'logout':
            case 'upload':
            case 'uploads':
            case 'activate':
            case 'activation':
            case 'share':
            case 'unshare':
            case 'follow':
            case 'unfollow':
            case 'notice':
            case 'notices':
            case 'url':
            case 'urls':
                return true;

            // api-function related names
            case 'create':
            case 'delete':
            case 'set':
            case 'get':
            case 'list':
            case 'listall':
                return true;

            // model-related names
            case 'acl':
            case 'acls':
            case 'action':
            case 'actions':
            case 'model':
            case 'sql':
            case 'registry':
            case 'rights':
            case 'temp':
            case 'tempdata':
            case 'search':
                return true;

            // system-related
            case 'config':
            case 'configuration':
            case 'id':
            case 'eid':
            case 'email':
            case 'error':
            case 'database':
            case 'framework':
            case 'host':
            case 'hosts':
            case 'http':
            case 'https':
            case 'identifier':
            case 'include':
            case 'job':
            case 'json':
            case 'license':
            case 'plugin':
            case 'port':
            case 'ports':
            case 'item':
            case 'items':
            case 'param':
            case 'params':
            case 'parameter':
            case 'parameters':
            case 'serial':
            case 'server':
            case 'servers':
            case 'stdin':
            case 'stdins':
            case 'stdout':
            case 'stdouts':
            case 'system':
            case 'systems':
            case 'translate':
            case 'translates':
            case 'username':
            case 'user_name':
            case 'util':
            case 'utils':
            case 'validator':
            case 'validators':
            case 'password':
            case 'passwords':
            case 'token':
            case 'tokens':
            case 'access_token':
            case 'access_tokens':
            case 'refresh_token':
            case 'refresh_tokens':
            case 'refresh':
            case 'auth':
            case 'authorize':
            case 'authorization':
            case 'oauth':
                return true;

            // common extensions
            case 'cfg':
            case 'css':
            case 'ini':
            case 'html':
            case 'htm':
            case 'php':
            case 'com':
            case 'app':
            case 'exe':
            case 'tmp':
            case 'zip':
                return true;

            // data/content related
            case 'database':
            case 'gpx':
            case 'json':
            case 'jsonb':
            case 'dat':
            case 'xls':
            case 'xlsx':
            case 'mdb':
            case 'csv':
            case 'log':
            case 'rss':
            case 'sql':
            case 'txt':
            case 'xml':
            case 'content':
            case 'data':
            case 'private':
            case 'public':
            case 'structure':
            case 'table':
            case 'query':
            case 'index':
                return true;

            // command related
            case 'about':
            case 'abouts':
            case 'add':
            case 'added':
            case 'adds':
            case 'after':
            case 'alter':
            case 'alters':
            case 'analyze':
            case 'analyzes':
            case 'append':
            case 'appends':
            case 'array':
            case 'arrays':
            case 'as':
            case 'ascend':
            case 'ascending':
            case 'avg':
            case 'average':
            case 'averages':
            case 'back':
            case 'before':
            case 'begin':
            case 'begins':
            case 'block':
            case 'blocks':
            case 'boolean':
            case 'booleans':
            case 'bottom':
            case 'branch':
            case 'branches':
            case 'break':
            case 'breaks':
            case 'build':
            case 'builds':
            case 'buy':
            case 'buys':
            case 'calc':
            case 'calculate':
            case 'calculates':
            case 'call':
            case 'calls':
            case 'case':
            case 'cases':
            case 'catch':
            case 'catches':
            case 'character':
            case 'characters':
            case 'check':
            case 'checks':
            case 'class':
            case 'classes':
            case 'clean':
            case 'cleans':
            case 'close':
            case 'closes':
            case 'combine':
            case 'combines':
            case 'commit':
            case 'commits':
            case 'col':
            case 'cols':
            case 'column':
            case 'columns':
            case 'comment':
            case 'comments':
            case 'compress':
            case 'compresses':
            case 'connect':
            case 'connects':
            case 'continue':
            case 'continues':
            case 'convert':
            case 'converts':
            case 'copy':
            case 'copies':
            case 'count':
            case 'counts':
            case 'create':
            case 'creates':
            case 'cut':
            case 'cuts':
            case 'date':
            case 'dates':
            case 'datetime':
            case 'datetimes':
            case 'debug':
            case 'debugs':
            case 'deduplicate':
            case 'deduplicates':
            case 'define':
            case 'defines':
            case 'delegate':
            case 'delegates':
            case 'delete':
            case 'deletes':
            case 'delimit':
            case 'delimites':
            case 'delimiter':
            case 'descend':
            case 'descending':
            case 'describe':
            case 'describes':
            case 'dev':
            case 'develop':
            case 'develops':
            case 'developes':
            case 'developers':
            case 'diff':
            case 'diffs':
            case 'different':
            case 'distinct':
            case 'distincts':
            case 'divide':
            case 'divides':
            case 'do':
            case 'does':
            case 'download':
            case 'downloads':
            case 'double':
            case 'doubles':
            case 'drop':
            case 'drops':
            case 'duplicate':
            case 'duplicates':
            case 'echo':
            case 'echoes':
            case 'edit':
            case 'edits':
            case 'else':
            case 'elseif':
            case 'elif':
            case 'end':
            case 'ends':
            case 'error':
            case 'errors':
            case 'exclude':
            case 'excludes':
            case 'execute':
            case 'executes':
            case 'expire':
            case 'expires':
            case 'extract':
            case 'extracts':
            case 'field':
            case 'fields':
            case 'file':
            case 'files':
            case 'filesize':
            case 'filesizes':
            case 'filetype':
            case 'filetypes':
            case 'filecreatedate':
            case 'filemimetype':
            case 'filelastupdate':
            case 'fileselect':
            case 'fill':
            case 'fills':
            case 'filter':
            case 'filters':
            case 'find':
            case 'finds':
            case 'first':
            case 'float':
            case 'floats':
            case 'for':
            case 'format':
            case 'formats':
            case 'from':
            case 'front':
            case 'ftp':
            case 'function':
            case 'functions':
            case 'get':
            case 'gets':
            case 'go':
            case 'goto':
            case 'gotos':
            case 'grant':
            case 'grants':
            case 'group':
            case 'groups':
            case 'index':
            case 'indexes':
            case 'identify':
            case 'identifies':
            case 'include':
            case 'includes':
            case 'if':
            case 'ignore':
            case 'ignores':
            case 'in':
            case 'input':
            case 'inputs':
            case 'insert':
            case 'inserts':
            case 'install':
            case 'installs':
            case 'intersect':
            case 'intersects':
            case 'invoke':
            case 'invokes':
            case 'into':
            case 'iterate':
            case 'iterates':
            case 'has':
            case 'have':
            case 'having':
            case 'head':
            case 'heads':
            case 'header':
            case 'help':
            case 'helps':
            case 'hide':
            case 'hides':
            case 'host':
            case 'hosts':
            case 'log':
            case 'logs':
            case 'loop':
            case 'loops':
            case 'join':
            case 'joins':
            case 'jump':
            case 'jumps':
            case 'key':
            case 'keys':
            case 'keyword':
            case 'keywords':
            case 'language':
            case 'languages':
            case 'launch':
            case 'launches':
            case 'last':
            case 'limit':
            case 'limits':
            case 'list':
            case 'lists':
            case 'line':
            case 'lines':
            case 'look':
            case 'looks':
            case 'lookup':
            case 'lookups':
            case 'loop':
            case 'loops':
            case 'lowercase':
            case 'lowercases':
            case 'map':
            case 'maps':
            case 'mapfile':
            case 'max':
            case 'maximum':
            case 'merge':
            case 'merges':
            case 'meta':
            case 'mimetype':
            case 'mimetypes':
            case 'min':
            case 'minimum':
            case 'mode':
            case 'modes':
            case 'move':
            case 'moves':
            case 'multiply':
            case 'multiplies':
            case 'name':
            case 'names':
            case 'new':
            case 'news':
            case 'next':
            case 'nop':
            case 'numeric':
            case 'numerics':
            case 'number':
            case 'numbers':
            case 'obj':
            case 'object':
            case 'objects':
            case 'open':
            case 'opens':
            case 'order':
            case 'orders':
            case 'output':
            case 'outputs':
            case 'patch':
            case 'patches':
            case 'pad':
            case 'pads':
            case 'param':
            case 'params':
            case 'parameter':
            case 'parameters':
            case 'partition':
            case 'partitions':
            case 'paste':
            case 'pastes':
            case 'path':
            case 'paths':
            case 'pivot':
            case 'pivots':
            case 'pop':
            case 'pops':
            case 'port':
            case 'ports':
            case 'position':
            case 'positions':
            case 'post':
            case 'posts':
            case 'prepend':
            case 'prepends':
            case 'previous':
            case 'pull':
            case 'pulls':
            case 'push':
            case 'pushes':
            case 'purchase':
            case 'purchases':
            case 'put':
            case 'puts':
            case 'qualify':
            case 'qualifies':
            case 'qualifier':
            case 'query':
            case 'queries':
            case 'random':
            case 'range':
            case 'ranges':
            case 'read':
            case 'reads':
            case 'record':
            case 'records':
            case 'redo':
            case 'redos':
            case 'refine':
            case 'refines':
            case 'register':
            case 'registers':
            case 'remove':
            case 'removes':
            case 'rename':
            case 'renames':
            case 'replace':
            case 'replaces':
            case 'request':
            case 'requests':
            case 'report':
            case 'reports':
            case 'reset':
            case 'resets':
            case 'return':
            case 'returns':
            case 'revert':
            case 'reverts':
            case 'role':
            case 'roles':
            case 'roll':
            case 'rolls':
            case 'row':
            case 'rows':
            case 'run':
            case 'runs':
            case 'same':
            case 'sample':
            case 'save':
            case 'saves':
            case 'search':
            case 'searches':
            case 'select':
            case 'selects':
            case 'set':
            case 'sets':
            case 'settype':
            case 'settypes':
            case 'spawn':
            case 'spawns':
            case 'sftp':
            case 'show':
            case 'shows':
            case 'sleep':
            case 'sleeps':
            case 'split':
            case 'splits':
            case 'sort':
            case 'sorts':
            case 'start':
            case 'starts':
            case 'stat':
            case 'stats':
            case 'statistic':
            case 'statistics':
            case 'stop':
            case 'stops':
            case 'strict':
            case 'structure':
            case 'structures':
            case 'subtract':
            case 'subtracts':
            case 'sum':
            case 'summarize':
            case 'summarizes':
            case 'switch':
            case 'switches':
            case 'tail':
            case 'tails':
            case 'to':
            case 'top':
            case 'template':
            case 'templates':
            case 'templatefile':
            case 'templatefiles':
            case 'text':
            case 'texts':
            case 'then':
            case 'throw':
            case 'throws':
            case 'time':
            case 'times':
            case 'token':
            case 'tokens':
            case 'trace':
            case 'traces':
            case 'track':
            case 'tracks':
            case 'transfer':
            case 'transfers':
            case 'transform':
            case 'transforms':
            case 'trim':
            case 'trims':
            case 'try':
            case 'tries':
            case 'type':
            case 'types':
            case 'uncompress':
            case 'uncompresses':
            case 'undo':
            case 'undos':
            case 'union':
            case 'unions':
            case 'update':
            case 'updates':
            case 'upload':
            case 'uploads':
            case 'uppercase':
            case 'uppercases':
            case 'upsert':
            case 'upserts':
            case 'uninstall':
            case 'uninstalls':
            case 'unroll':
            case 'unrolls':
            case 'unzip':
            case 'use':
            case 'uses':
            case 'user':
            case 'users':
            case 'validate':
            case 'validates':
            case 'value':
            case 'values':
            case 'var':
            case 'variable':
            case 'variables':
            case 'view':
            case 'views':
            case 'watch':
            case 'watches':
            case 'with':
            case 'where':
            case 'while':
            case 'write':
            case 'writes':
            case 'zip':
                return true;

            // expression functions and variations
            case 'abs':
            case 'acos':
            case 'ascii':
            case 'asin':
            case 'atan':
            case 'cast':
            case 'ceil':
            case 'ceiling':
            case 'concat':
            case 'concatenate':
            case 'contains':
            case 'cos':
            case 'current_date':
            case 'day':
            case 'exp':
            case 'floor':
            case 'hash':
            case 'hour':
            case 'if':
            case 'iif':
            case 'initcap':
            case 'iskindof':
            case 'isnull':
            case 'left':
            case 'len':
            case 'length':
            case 'ln':
            case 'log':
            case 'lower':
            case 'lowercase':
            case 'lpad':
            case 'ltrim':
            case 'md5':
            case 'minute':
            case 'mod':
            case 'month':
            case 'now':
            case 'pad':
            case 'padl':
            case 'padr':
            case 'pi':
            case 'pow':
            case 'proper':
            case 'regex':
            case 'regex_match':
            case 'regex_replace':
            case 'regexp':
            case 'regexp_match':
            case 'regexp_replace':
            case 'replace':
            case 'reverse':
            case 'right':
            case 'round':
            case 'rpad':
            case 'rtrim':
            case 'second':
            case 'sign':
            case 'sin':
            case 'strpart':
            case 'strpos':
            case 'substr':
            case 'substring':
            case 'tan':
            case 'to_char':
            case 'to_date':
            case 'to_datetime':
            case 'to_number':
            case 'to_timestamp':
            case 'trim':
            case 'triml':
            case 'trimr':
            case 'trunc':
            case 'truncate':
            case 'upper':
            case 'uppercase':
            case 'year':
                return true;

            // expression keywords
            case 'true':
            case 'false':
            case 'null':
            case 'and':
            case 'or':
            case 'not':
                return true;

            // other
            case 'issue':
            case 'issues':
            case 'report':
            case 'reports':
            case 'dashboard':
            case 'dashboards':
                return true;
        }
    }

    private static function isIllegalWord($word) : bool
    {
        switch ($word)
        {
            default:
                return false;

            case 'sex':
            case 'shit':
            case 'damn':
            case 'fuck':
                return true;
        }
    }
}
