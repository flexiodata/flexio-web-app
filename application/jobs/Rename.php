<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2016-01-21
 *
 * @package flexio
 * @subpackage Jobs
 */


declare(strict_types=1);
namespace Flexio\Jobs;

/*
// DESCRIPTION:
{
    "op": "rename"
    // TODO: fill out
}

// VALIDATOR:
$validator = \Flexio\Base\Validator::create();
if (($validator->check($params, array(
        'op'         => array('required' => true,  'enum' => ['rename'])
    ))->hasErrors()) === true)
    throw new \Flexio\Base\Exception(\Flexio\Base\Error::INVALID_PARAMETER);
*/

class Rename extends \Flexio\Jobs\Base
{
    public function run(\Flexio\IFace\IProcess $process) : void
    {
        parent::run($process);

        // stdin/stdout
        $instream = $process->getStdin();
        $outstream = $process->getStdout();
        $this->processStream($instream, $outstream, $process->getParams());
    }

    private function processStream(\Flexio\IFace\IStream &$instream, \Flexio\IFace\IStream &$outstream, array $env) : void
    {
        // any renames will be handled by the file/column rename handler; if there
        // aren't any operations, the stream will simply be copied to the output
        $outstream->set($instream->get());
        $outstream->setPath(\Flexio\Base\Util::generateHandle());

        $job_params = $this->getJobParameters();
        $mime_type = $outstream->getMimeType();

        // rename the output stream if appropriate
        if (isset($job_params['files']))
            $this->renameStream($outstream, $env);

        // if we have a table, rename any columns if specified; note: this may
        // works in conjunction with renaming the file, so a file that's renamed
        // may also have columns renamed
        if (isset($job_params['columns']))
        {
            if ($mime_type === \Flexio\Base\ContentType::FLEXIO_TABLE)
                $this->renameColumns($outstream);
        }
    }

    private function renameStream(\Flexio\IFace\IStream $outstream, array $env) : void
    {
        // get the files to rename
        $job_params = $this->getJobParameters();
        $job_file_renames = $job_params['files'];
        $stream_name = $outstream->getName();

        foreach ($job_file_renames as $f)
        {
            // if the name of the outstream doesn't match the file pattern, move on
            $file_match_pattern = $f['name'] ?? false;
            $file_new_name_expr = $f['new_name'] ?? false;

            if (!is_string($file_match_pattern) || !is_string($file_new_name_expr))
                continue;

            if (\Flexio\Base\File::matchPath($stream_name, $file_match_pattern, true) === false) // true: case-sensitive match
                continue;

            // the pattern matches; get the rename expression, evaluate it and rename the file

            // evaluate the expression with the environment variables
            $variables = array();
            $variables['stream.name'] = $outstream->getName();
            $variables['stream.name.base'] = \Flexio\Base\File::getFilename($outstream->getName());
            $variables['stream.name.ext'] = \Flexio\Base\File::getFileExtension($outstream->getName());
            $variables['stream.path'] = $outstream->getPath();
            $variables['stream.content.type'] = $outstream->getMimeType();
            $variables = array_merge($env, $variables);

            $retval = '';
            if (self::evaluateExpr($file_new_name_expr, $variables, $retval) === false)
                $retval = $file_new_name_expr; // unable to evaluate the expression

            $new_name = $retval;
            $outstream->setName($new_name);
            break; // rename based on the first valid match
        }
    }

    private function renameColumns(\Flexio\IFace\IStream $outstream) : void
    {
        // get the columns to rename
        $job_params = $this->getJobParameters();
        $job_columns = $job_params['columns'];

        // get the original names
        $original_structure = $outstream->getStructure();
        $original_columns = $original_structure->enum();

        $indexed = [];
        foreach ($job_columns as $column)
        {
            if (!isset($column['name']) || !isset($column['new_name']))
                continue;

            $indexed[strtolower($column['name'])] = $column['new_name'];
        }

        // create a new structure with the renamed columns
        $renamed_columns = [];
        foreach ($original_columns as $c)
        {
            $name = strtolower($c['name']);
            if (isset($indexed[$name]))
                $c['name'] = $indexed[$name];

            $renamed_columns[] = $c;
        }

        // update the structure
        $outstream->setStructure($renamed_columns);
    }

    private function evaluateExpr(string $expr, array $variables, &$retval) : bool
    {
        $structure = array();
        foreach ($variables as $name => $value)
        {
            $structure[] = array("name" => $name, "type" => "text");
        }

        return \Flexio\Base\ExprEvaluate::evaluate($expr, $variables, $structure, $retval);
    }
}
