<?php
/**
 *
 * Copyright (c) 2018, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2018-05-11
 *
 * @package flexio
 * @subpackage Base
 */


declare(strict_types=1);
namespace Flexio\Base;


class Keyword
{
    public static function exists(string $word) : bool
    {
        if (array_key_exists($word, self::$reserved_words))
            return true;

        if (array_key_exists($word, self::$illegal_words))
            return true;

        return false;
    }

    private static $reserved_words = array(

        // company related
        'flex' => '',
        // 'flexio' => '', // allow flexio, since we want to use it as our public account
        'flexible' => '',
        'flexiodata' => '',
        // 'goldprairie' => '',
        // 'kirix' => '',
        // 'kirixresearch' => '',

        // public file and folder names
        'api' => '',
        'css' => '',
        'dist' => '',
        'doc' => '',
        'fonts' => '',
        'img' => '',
        'js' => '',
        'less' => '',

        /*
        TODO: do we need to include files in public folder?
                '.htaccess', 'apple-touch-icon', etc.
        */
        // web-page related names
        'about' => '',
        'account' => '',
        'accounts' => '',
        'blog' => '',
        'index' => '',
        'help' => '',
        'home' => '',
        'pricing' => '',
        'support' => '',
        'image' => '',
        'images' => '',
        'info' => '',
        'features' => '',
        'product' => '',
        'products' => '',
        'connections' => '',
        'integrations' => '',
        'input' => '',
        'inputs' => '',
        'output' => '',
        'outputs' => '',
        'contact' => '',
        'contactus' => '',
        'aboutus' => '',
        'ourstory' => '',
        'shop' => '',
        'store' => '',
        'solution' => '',
        'solutions' => '',
        'whatisflexio' => '',
        'article' => '',
        'articles' => '',
        'extension' => '',
        'extensions' => '',
        'widgets' => '',
        'downloads' => '',
        'download' => '',
        'software' => '',

        // controller-related names
        'account' => '',
        'api' => '',
        'common' => '',
        'debug' => '',
        'default' => '',
        'install' => '',
        'phantom' => '',
        'test' => '',
        'ui' => '',
        'util' => '',

        // more controller-related names
        'signin' => '',
        'signup' => '',
        'embed' => '',
        'connectionauth' => '',
        'forgotpassword' => '',
        'resetpassword' => '',
        'shareauth' => '',

        // more controller-related names
        'join' => '',
        'admin' => '',
        'administrator' => '',
        'root' => '',

        // api-related names
        'alias' => '',
        'aliases' => '',
        'id' => '',
        'ids' => '',
        'eid' => '',
        'eids' => '',
        'cname' => '',
        'cnames' => '',
        'name' => '',
        'names' => '',
        'description' => '',
        'descriptions' => '',
        'type' => '',
        'types' => '',
        'comment' => '',
        'comments' => '',
        'connection' => '',
        'connections' => '',
        'container' => '',
        'containers' => '',
        'install' => '',
        'installation' => '',
        'batch' => '',
        'batches' => '',
        'queue' => '',
        'queues' => '',
        'job' => '',
        'jobs' => '',
        'process' => '',
        'processes' => '',
        'subprocess' => '',
        'subprocesses' => '',
        'notification' => '',
        'project' => '',
        'projects' => '',
        'repository' => '',
        'repositories' => '',
        'resource' => '',
        'resources' => '',
        'slug' => '',
        'slugs' => '',
        'system' => '',
        'systems' => '',
        'trash' => '',
        'restore' => '',
        'user' => '',
        'users' => '',
        'workspace' => '',
        'workspace' => '',
        'pipe' => '',
        'pipes' => '',
        'task' => '',
        'tasks' => '',
        'stream' => '',
        'streams' => '',
        'follower' => '',
        'followers' => '',
        'reply' => '',
        'replies' => '',
        'status' => '',
        'statuses' => '',
        'login' => '',
        'logout' => '',
        'upload' => '',
        'uploads' => '',
        'activate' => '',
        'activation' => '',
        'share' => '',
        'unshare' => '',
        'follow' => '',
        'unfollow' => '',
        'notice' => '',
        'notices' => '',
        'url' => '',
        'urls' => '',

        // api-function related names
        'create' => '',
        'delete' => '',
        'set' => '',
        'get' => '',
        'list' => '',
        'listall' => '',

        // model-related names
        'acl' => '',
        'acls' => '',
        'action' => '',
        'actions' => '',
        'model' => '',
        'sql' => '',
        'registry' => '',
        'rights' => '',
        'temp' => '',
        'tempdata' => '',
        'search' => '',

        // system-related
        'config' => '',
        'configuration' => '',
        'id' => '',
        'eid' => '',
        'email' => '',
        'error' => '',
        'database' => '',
        'framework' => '',
        'host' => '',
        'hosts' => '',
        'http' => '',
        'https' => '',
        'identifier' => '',
        'include' => '',
        'job' => '',
        'json' => '',
        'license' => '',
        'plugin' => '',
        'port' => '',
        'ports' => '',
        'item' => '',
        'items' => '',
        'param' => '',
        'params' => '',
        'parameter' => '',
        'parameters' => '',
        'serial' => '',
        'server' => '',
        'servers' => '',
        'stdin' => '',
        'stdins' => '',
        'stdout' => '',
        'stdouts' => '',
        'system' => '',
        'systems' => '',
        'translate' => '',
        'translates' => '',
        'username' => '',
        'user_name' => '',
        'util' => '',
        'utils' => '',
        'validator' => '',
        'validators' => '',
        'password' => '',
        'passwords' => '',
        'token' => '',
        'tokens' => '',
        'access_token' => '',
        'access_tokens' => '',
        'refresh_token' => '',
        'refresh_tokens' => '',
        'refresh' => '',
        'auth' => '',
        'authorize' => '',
        'authorization' => '',
        'oauth' => '',

        // common extensions
        'cfg' => '',
        'css' => '',
        'ini' => '',
        'html' => '',
        'htm' => '',
        'php' => '',
        'com' => '',
        'app' => '',
        'exe' => '',
        'tmp' => '',
        'zip' => '',

        // data/content related
        'database' => '',
        'gpx' => '',
        'json' => '',
        'jsonb' => '',
        'dat' => '',
        'xls' => '',
        'xlsx' => '',
        'mdb' => '',
        'csv' => '',
        'log' => '',
        'rss' => '',
        'sql' => '',
        'txt' => '',
        'xml' => '',
        'content' => '',
        'data' => '',
        'private' => '',
        'public' => '',
        'structure' => '',
        'table' => '',
        'query' => '',
        'index' => '',

        // command related
        'about' => '',
        'abouts' => '',
        'add' => '',
        'added' => '',
        'adds' => '',
        'after' => '',
        'alter' => '',
        'alters' => '',
        'analyze' => '',
        'analyzes' => '',
        'append' => '',
        'appends' => '',
        'array' => '',
        'arrays' => '',
        'as' => '',
        'ascend' => '',
        'ascending' => '',
        'avg' => '',
        'average' => '',
        'averages' => '',
        'back' => '',
        'before' => '',
        'begin' => '',
        'begins' => '',
        'block' => '',
        'blocks' => '',
        'boolean' => '',
        'booleans' => '',
        'bottom' => '',
        'branch' => '',
        'branches' => '',
        'break' => '',
        'breaks' => '',
        'build' => '',
        'builds' => '',
        'buy' => '',
        'buys' => '',
        'calc' => '',
        'calculate' => '',
        'calculates' => '',
        'call' => '',
        'calls' => '',
        'case' => '',
        'cases' => '',
        'catch' => '',
        'catches' => '',
        'character' => '',
        'characters' => '',
        'check' => '',
        'checks' => '',
        'class' => '',
        'classes' => '',
        'clean' => '',
        'cleans' => '',
        'close' => '',
        'closes' => '',
        'combine' => '',
        'combines' => '',
        'commit' => '',
        'commits' => '',
        'col' => '',
        'cols' => '',
        'column' => '',
        'columns' => '',
        'comment' => '',
        'comments' => '',
        'compress' => '',
        'compresses' => '',
        'connect' => '',
        'connects' => '',
        'continue' => '',
        'continues' => '',
        'convert' => '',
        'converts' => '',
        'copy' => '',
        'copies' => '',
        'count' => '',
        'counts' => '',
        'create' => '',
        'creates' => '',
        'cut' => '',
        'cuts' => '',
        'date' => '',
        'dates' => '',
        'datetime' => '',
        'datetimes' => '',
        'debug' => '',
        'debugs' => '',
        'deduplicate' => '',
        'deduplicates' => '',
        'define' => '',
        'defines' => '',
        'delegate' => '',
        'delegates' => '',
        'delete' => '',
        'deletes' => '',
        'delimit' => '',
        'delimites' => '',
        'delimiter' => '',
        'descend' => '',
        'descending' => '',
        'describe' => '',
        'describes' => '',
        'dev' => '',
        'develop' => '',
        'develops' => '',
        'developes' => '',
        'developers' => '',
        'diff' => '',
        'diffs' => '',
        'different' => '',
        'distinct' => '',
        'distincts' => '',
        'divide' => '',
        'divides' => '',
        'do' => '',
        'does' => '',
        'download' => '',
        'downloads' => '',
        'double' => '',
        'doubles' => '',
        'drop' => '',
        'drops' => '',
        'duplicate' => '',
        'duplicates' => '',
        'echo' => '',
        'echoes' => '',
        'edit' => '',
        'edits' => '',
        'else' => '',
        'elseif' => '',
        'elif' => '',
        'end' => '',
        'ends' => '',
        'error' => '',
        'errors' => '',
        'exclude' => '',
        'excludes' => '',
        'execute' => '',
        'executes' => '',
        'expire' => '',
        'expires' => '',
        'extract' => '',
        'extracts' => '',
        'field' => '',
        'fields' => '',
        'file' => '',
        'files' => '',
        'filesize' => '',
        'filesizes' => '',
        'filetype' => '',
        'filetypes' => '',
        'filecreatedate' => '',
        'filemimetype' => '',
        'filelastupdate' => '',
        'fileselect' => '',
        'fill' => '',
        'fills' => '',
        'filter' => '',
        'filters' => '',
        'find' => '',
        'finds' => '',
        'first' => '',
        'float' => '',
        'floats' => '',
        'for' => '',
        'format' => '',
        'formats' => '',
        'from' => '',
        'front' => '',
        'ftp' => '',
        'function' => '',
        'functions' => '',
        'get' => '',
        'gets' => '',
        'go' => '',
        'goto' => '',
        'gotos' => '',
        'grant' => '',
        'grants' => '',
        'group' => '',
        'groups' => '',
        'index' => '',
        'indexes' => '',
        'identify' => '',
        'identifies' => '',
        'include' => '',
        'includes' => '',
        'if' => '',
        'ignore' => '',
        'ignores' => '',
        'in' => '',
        'input' => '',
        'inputs' => '',
        'insert' => '',
        'inserts' => '',
        'install' => '',
        'installs' => '',
        'intersect' => '',
        'intersects' => '',
        'invoke' => '',
        'invokes' => '',
        'into' => '',
        'iterate' => '',
        'iterates' => '',
        'has' => '',
        'have' => '',
        'having' => '',
        'head' => '',
        'heads' => '',
        'header' => '',
        'help' => '',
        'helps' => '',
        'hide' => '',
        'hides' => '',
        'host' => '',
        'hosts' => '',
        'log' => '',
        'logs' => '',
        'loop' => '',
        'loops' => '',
        'join' => '',
        'joins' => '',
        'jump' => '',
        'jumps' => '',
        'key' => '',
        'keys' => '',
        'keyword' => '',
        'keywords' => '',
        'language' => '',
        'languages' => '',
        'launch' => '',
        'launches' => '',
        'last' => '',
        'limit' => '',
        'limits' => '',
        'list' => '',
        'lists' => '',
        'line' => '',
        'lines' => '',
        'look' => '',
        'looks' => '',
        'lookup' => '',
        'lookups' => '',
        'loop' => '',
        'loops' => '',
        'lowercase' => '',
        'lowercases' => '',
        'map' => '',
        'maps' => '',
        'mapfile' => '',
        'max' => '',
        'maximum' => '',
        'merge' => '',
        'merges' => '',
        'meta' => '',
        'mimetype' => '',
        'mimetypes' => '',
        'min' => '',
        'minimum' => '',
        'mode' => '',
        'modes' => '',
        'move' => '',
        'moves' => '',
        'multiply' => '',
        'multiplies' => '',
        'name' => '',
        'names' => '',
        'new' => '',
        'news' => '',
        'next' => '',
        'nop' => '',
        'numeric' => '',
        'numerics' => '',
        'number' => '',
        'numbers' => '',
        'obj' => '',
        'object' => '',
        'objects' => '',
        'open' => '',
        'opens' => '',
        'order' => '',
        'orders' => '',
        'output' => '',
        'outputs' => '',
        'patch' => '',
        'patches' => '',
        'pad' => '',
        'pads' => '',
        'param' => '',
        'params' => '',
        'parameter' => '',
        'parameters' => '',
        'partition' => '',
        'partitions' => '',
        'paste' => '',
        'pastes' => '',
        'path' => '',
        'paths' => '',
        'pivot' => '',
        'pivots' => '',
        'pop' => '',
        'pops' => '',
        'port' => '',
        'ports' => '',
        'position' => '',
        'positions' => '',
        'post' => '',
        'posts' => '',
        'prepend' => '',
        'prepends' => '',
        'previous' => '',
        'pull' => '',
        'pulls' => '',
        'push' => '',
        'pushes' => '',
        'purchase' => '',
        'purchases' => '',
        'put' => '',
        'puts' => '',
        'qualify' => '',
        'qualifies' => '',
        'qualifier' => '',
        'query' => '',
        'queries' => '',
        'random' => '',
        'range' => '',
        'ranges' => '',
        'read' => '',
        'reads' => '',
        'record' => '',
        'records' => '',
        'redo' => '',
        'redos' => '',
        'refine' => '',
        'refines' => '',
        'register' => '',
        'registers' => '',
        'remove' => '',
        'removes' => '',
        'rename' => '',
        'renames' => '',
        'replace' => '',
        'replaces' => '',
        'request' => '',
        'requests' => '',
        'report' => '',
        'reports' => '',
        'reset' => '',
        'resets' => '',
        'return' => '',
        'returns' => '',
        'revert' => '',
        'reverts' => '',
        'role' => '',
        'roles' => '',
        'roll' => '',
        'rolls' => '',
        'row' => '',
        'rows' => '',
        'run' => '',
        'runs' => '',
        'same' => '',
        'sample' => '',
        'save' => '',
        'saves' => '',
        'search' => '',
        'searches' => '',
        'select' => '',
        'selects' => '',
        'set' => '',
        'sets' => '',
        'settype' => '',
        'settypes' => '',
        'spawn' => '',
        'spawns' => '',
        'sftp' => '',
        'show' => '',
        'shows' => '',
        'sleep' => '',
        'sleeps' => '',
        'split' => '',
        'splits' => '',
        'sort' => '',
        'sorts' => '',
        'start' => '',
        'starts' => '',
        'stat' => '',
        'stats' => '',
        'statistic' => '',
        'statistics' => '',
        'stop' => '',
        'stops' => '',
        'strict' => '',
        'structure' => '',
        'structures' => '',
        'subtract' => '',
        'subtracts' => '',
        'sum' => '',
        'summarize' => '',
        'summarizes' => '',
        'switch' => '',
        'switches' => '',
        'tail' => '',
        'tails' => '',
        'to' => '',
        'top' => '',
        'template' => '',
        'templates' => '',
        'templatefile' => '',
        'templatefiles' => '',
        'text' => '',
        'texts' => '',
        'then' => '',
        'throw' => '',
        'throws' => '',
        'time' => '',
        'times' => '',
        'token' => '',
        'tokens' => '',
        'trace' => '',
        'traces' => '',
        'track' => '',
        'tracks' => '',
        'transfer' => '',
        'transfers' => '',
        'transform' => '',
        'transforms' => '',
        'trim' => '',
        'trims' => '',
        'try' => '',
        'tries' => '',
        'type' => '',
        'types' => '',
        'uncompress' => '',
        'uncompresses' => '',
        'undo' => '',
        'undos' => '',
        'union' => '',
        'unions' => '',
        'update' => '',
        'updates' => '',
        'upload' => '',
        'uploads' => '',
        'uppercase' => '',
        'uppercases' => '',
        'upsert' => '',
        'upserts' => '',
        'uninstall' => '',
        'uninstalls' => '',
        'unroll' => '',
        'unrolls' => '',
        'unzip' => '',
        'use' => '',
        'uses' => '',
        'user' => '',
        'users' => '',
        'validate' => '',
        'validates' => '',
        'value' => '',
        'values' => '',
        'var' => '',
        'variable' => '',
        'variables' => '',
        'view' => '',
        'views' => '',
        'watch' => '',
        'watches' => '',
        'with' => '',
        'where' => '',
        'while' => '',
        'write' => '',
        'writes' => '',
        'zip' => '',

        // expression functions and variations
        'abs' => '',
        'acos' => '',
        'ascii' => '',
        'asin' => '',
        'atan' => '',
        'cast' => '',
        'ceil' => '',
        'ceiling' => '',
        'concat' => '',
        'concatenate' => '',
        'contains' => '',
        'cos' => '',
        'current_date' => '',
        'day' => '',
        'exp' => '',
        'floor' => '',
        'hash' => '',
        'hour' => '',
        'if' => '',
        'iif' => '',
        'initcap' => '',
        'iskindof' => '',
        'isnull' => '',
        'left' => '',
        'len' => '',
        'length' => '',
        'ln' => '',
        'log' => '',
        'lower' => '',
        'lowercase' => '',
        'lpad' => '',
        'ltrim' => '',
        'md5' => '',
        'minute' => '',
        'mod' => '',
        'month' => '',
        'now' => '',
        'pad' => '',
        'padl' => '',
        'padr' => '',
        'pi' => '',
        'pow' => '',
        'proper' => '',
        'regex' => '',
        'regex_match' => '',
        'regex_replace' => '',
        'regexp' => '',
        'regexp_match' => '',
        'regexp_replace' => '',
        'replace' => '',
        'reverse' => '',
        'right' => '',
        'round' => '',
        'rpad' => '',
        'rtrim' => '',
        'second' => '',
        'sign' => '',
        'sin' => '',
        'strpart' => '',
        'strpos' => '',
        'substr' => '',
        'substring' => '',
        'tan' => '',
        'to_char' => '',
        'to_date' => '',
        'to_datetime' => '',
        'to_number' => '',
        'to_timestamp' => '',
        'trim' => '',
        'triml' => '',
        'trimr' => '',
        'trunc' => '',
        'truncate' => '',
        'upper' => '',
        'uppercase' => '',
        'year' => '',

        // expression keywords
        'true' => '',
        'false' => '',
        'null' => '',
        'and' => '',
        'or' => '',
        'not' => '',

        // other
        'issue' => '',
        'issues' => '',
        'report' => '',
        'reports' => '',
        'dashboard' => '',
        'dashboards' => '',
    );

    private static $illegal_words = array(
        'sex'  => '',
        'shit' => '',
        'damn' => '',
        'fuck' => '',
    );
}
