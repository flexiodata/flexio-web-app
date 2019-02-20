<?php
/**
 *
 * Copyright (c) 2013, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Benjamin I. Williams
 * Created:  2013-08-29
 *
 * @package flexio
 * @subpackage Model
 */


declare(strict_types=1);


class Tempdata extends ModelBase
{
    public function entryExists(string $name) : bool
    {
        $registry_model = $this->getModel()->registry;
        return $registry_model->entryExists('', "tempdata.$name");
    }

    public function cleanupExpiredEntries() : bool
    {
        $registry_model = $this->getModel()->registry;
        return $registry_model->cleanupExpiredEntries();
    }

    public function setValue(string $name, /* variable */ $value, int $expires = 86400) : bool
    {
        $registry_model = $this->getModel()->registry;
        return $registry_model->setString('', "tempdata.$name", $value, $expires);
    }

    public function getValue(string $name) // TODO: add return type
    {
        $registry_model = $this->getModel()->registry;
        return $registry_model->getString('', "tempdata.$name");
    }

    public function getArray(string $name) // TODO: add return type
    {
        $registry_model = $this->getModel()->registry;

        $db = $this->getDatabase();
        $db->beginTransaction();
        $values = $registry_model->getVariable('', "tempdata.$name", $db);
        $db->commit();

        if (is_null($values))
            return null;

        return @json_decode($values, true);
    }

    public function updateArray(string $name, array $changes, int $expires = 86400) : bool
    {
        $registry_model = $this->getModel()->registry;

        $db = $this->getDatabase();
        $db->beginTransaction();

        $values = $registry_model->getVariable('', "tempdata.$name", $db, true /* for update */);
        if (!is_null($values))
            $values = @json_decode($values, true);
        if (is_null($values))
            $values = array();

        $values = array_merge($values, $changes);

        // remove all null values from array (this is how you can delete items)
        $values = array_filter($values, function($var){return !is_null($var);} );
        if (!$registry_model->setString('', "tempdata.$name", json_encode($values), $expires, $db))
        {
            // return error; \Flexio\Model\Registry sets error code and message
            $db->rollback();
            return false;
        }

        $db->commit();
        return true;
    }
}
