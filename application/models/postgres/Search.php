<?php
/**
 *
 * Copyright (c) 2014, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2014-09-23
 *
 * @package flexio
 * @subpackage Model
 */


declare(strict_types=1);


class SearchTerm
{
    public $node_eids = false;
    public $node_types = false;
    public $edge_types = false;
}

class Search extends ModelBase
{
    public function exec(string $search_path) : ?array
    {
        // parse the search path; if the path doesn't parse correctly,
        // return false
        $search_terms = array();
        if (self::parse($search_path, $search_terms) === false)
            return null;

        // path parses correctly, but we don't have any search terms;
        // return empty array
        if (empty($search_terms))
            return array();

        // evaluate the search terms
        $search_term_count = count($search_terms);
        $search_term_idx = 0;

        $search_results = null;
        foreach ($search_terms as $term)
        {
            $search_term_idx++;
            $is_last_term = ($search_term_idx === $search_term_count) ? true : false;

            $search_results = $this->evaluate($search_results, $term, $is_last_term);
        }

        // just return the eids
        $results = array();
        foreach ($search_results as $r)
        {
            $results[] = $r['eid'];
        }

        return $results;
    }

    public function recursive_search(string $search_path) : array
    {
        // use the output of one search as the input for the next search;
        // return a list of all objects that are found in the order in
        // which they are found
        $results = array();
        while (true)
        {
            // search for a list of objects based on the search path
            // if we can't find any objects, we're done
            $objects = $this->exec($search_path);
            if ($objects === false || count($objects) === 0)
                break;

            // add the objects onto the list and modify the search
            // expression for the next round
            $results = array_merge($results, $objects);
            $search_parts = explode('->', $search_path, 2); // only split on first occurrence of '->'

            // check for a bad search expression
            if (count($search_parts) < 1)
                return false;

            $search_predicate = $search_parts[1];
            $delimited_object_list = implode(', ', $objects);
            $search_path = '('.$delimited_object_list.')->' . $search_predicate;
        }

        return $results;
    }

    private function evaluate(array $input_eids = null, \SearchTerm $search_term, bool $is_last_term) : array
    {
        // takes the list of input eids and finds the corresponding
        // output eids based on the search terms

        // STEP 1: if we don't have a search term, there's nothing to
        // find; return the empty array
        if (!is_a($search_term, 'SearchTerm'))
            return array();

        // STEP 2: if the set of $input_eids is false, we're just
        // starting out and the initial set of eids should be contained
        // in the first search term; if it isn't, there's nothing to
        // find, so we're done; otherwise, set the list of input eids
        if (!isset($input_eids))
        {
            if ($search_term->node_eids === false)
                return array();

            // when we're first starting out, get the type of each of
            // the initial eids; this both excludes non-existent eids
            // as well as allows us to filter on type
            $input_eids = array();
            $initial_input_eids = $search_term->node_eids;

            foreach ($initial_input_eids as $eid)
            {
                $eid_type = $this->getModel()->getType($eid);
                if ($eid_type === \Model::TYPE_UNDEFINED)
                    continue;

                $input_item = array(
                    'eid' => $eid,
                    'eid_type' => $eid_type
                );

                $input_eids[] = $input_item;
            }
        }

        // STEP 3: if a list of edge nodes is specified, filter the set
        // of input eids against the list of allowed edge nodes
        self::filterInputEidsByListedEids($search_term, $input_eids);

        // STEP 4: if a list of edge types is specified, filter the set
        // of input eids against the list of allowed edge types
        self::filterInputEidsByListedEidType($search_term, $input_eids);

        // STEP 5: if we're on the last term and we don't have any edges,
        // return the filtered set of eids as the result set; otherwise
        // for each of the input eids and edges, find the corresponding
        // associated eids
        if ($is_last_term === true && $search_term->edge_types === false)
            return $input_eids;

        $search_edges = array();
        if ($search_term->edge_types !== false)
            $search_edges = $search_term->edge_types;

        $result_eids = array();
        foreach ($input_eids as $input_eid)
        {
            foreach ($search_edges as $search_edge)
            {
                $found_eids = $this->getModel()->assoc_range($input_eid['eid'], $search_edge);
                $result_eids = array_unique(array_merge($result_eids, $found_eids), SORT_REGULAR);
            }
        }

        return $result_eids;
    }

    private static function parse(string $search_path, array &$search_terms) : bool
    {
        // parsed search terms
        $search_terms_local = array();

        // get the components of the string
        $search_path_parts = explode('->', $search_path);

        // iterate over the string search parts and consolidate them into a
        // list of search terms
        $even_term = false;
        $search_term = null;
        foreach ($search_path_parts as $part)
        {
            // require something between path separaters besides blank space;
            // so "->", "eid->", "->eid", "eid->->eid" are disallowed; however
            // "eid->()->" is allowed
            if (strlen(self::trim_spaces($part)) === 0)
                return false;

            // every time we encounter an odd node, create a new search term;
            // the purpose here is to merge every odd/even pair of nodes and
            // edges (respectively) into a single search term that can be used
            // to search for all the resulting nodes from a set of input nodes
            // and edges
            if ($even_term === false)
            {
                $search_terms_local[] = new SearchTerm;
                $search_term = end($search_terms_local);
            }

            // odd parts should be eids or node types
            if ($even_term === false && !self::parse_nodepart($part, $search_term))
                return false;

            // even parts should be edges
            if ($even_term === true && !self::parse_edgepart($part, $search_term))
                return false;

            $even_term = !$even_term;
        }

        $search_terms = $search_terms_local;
        return true;
    }

    private static function parse_nodepart(string $part, \SearchTerm &$search_term) : bool
    {
        // trim away leading/trailing whitespace and parenthesis
        $part = self::trim_spaces($part);
        $part = self::trim_groupchars($part);
        $items = explode(',', $part);

        foreach ($items as $i)
        {
            // trim away leading/trailing whitespace
            $i = self::trim_spaces($i);

            // if the item is an eid, add it to the list of eids
            if (\Flexio\Base\Eid::isValid($i))
            {
                if ($search_term->node_eids === false)
                    $search_term->node_eids = array();

                $search_term->node_eids[] = $i;
                continue;
            }

            // if the item is an eid type, add it to the list of types
            if (\Model::isValidType($i))
            {
                if ($search_term->node_types === false)
                    $search_term->node_types = array();

                $search_term->node_types[] = $i;
                continue;
            }

            // something else that we don't know; ignore it rather
            // than returning false since it's like searching for
            // an eid or edge that doesn't exist
        }

        // make sure the eids and node types are unique
        if ($search_term->node_eids !== false)
            $search_term->node_eids = array_unique($search_term->node_eids);
        if ($search_term->node_types !== false)
            $search_term->node_types = array_unique($search_term->node_types);

        return true;
    }

    private static function parse_edgepart(string $part, \SearchTerm &$search_term) : bool
    {
        // trim away leading/trailing whitespace and parenthesis
        $part = self::trim_spaces($part);
        $part = self::trim_groupchars($part);
        $items = explode(',', $part);

        foreach ($items as $i)
        {
            // trim away leading/trailing whitespace
            $i = self::trim_spaces($i);
            if (\Model::isValidEdge($i))
            {
                if ($search_term->edge_types === false)
                    $search_term->edge_types = array();

                $search_term->edge_types[] = $i;
            }

            // something else that we don't know; ignore it rather
            // than returning false since it's like searching for
            // an eid or edge that doesn't exist
        }

        // make sure the edge types are unique
        if ($search_term->edge_types !== false)
            $search_term->edge_types = array_unique($search_term->edge_types);

        return true;
    }

    private static function filterInputEidsByListedEids(\SearchTerm $search_term, array &$input_eids) : void
    {
        // if no eids have been specified, let everything through
        if ($search_term->node_eids === false)
            return;

        $allowed_eids = array_flip($search_term->node_eids);
        $filtered_input_eids = array();

        foreach ($input_eids as $input_eid)
        {
            if (isset($allowed_eids[$input_eid['eid']]))
                $filtered_input_eids[] = $input_eid;
        }

        $input_eids = $filtered_input_eids;
    }

    private static function filterInputEidsByListedEidType(\SearchTerm $search_term, array &$input_eids) : void
    {
        // if no eid types have been specified, let everything through
        if ($search_term->node_types === false)
            return;

        $allowed_types = array_flip($search_term->node_types);
        $filtered_input_eids = array();

        foreach ($input_eids as $input_eid)
        {
            $input_eid_type = $input_eid['eid_type'];
            if (isset($allowed_types[$input_eid_type]))
                $filtered_input_eids[] = $input_eid;
        }

        $input_eids = $filtered_input_eids;
    }

    private static function trim_spaces(string $str) : string
    {
        return trim($str, " \t\n\r\0\x0B");
    }

    private static function trim_groupchars(string $str) : string
    {
        return trim($str, "()");
    }
}
