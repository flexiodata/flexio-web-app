<?php
/**
 *
 * Copyright (c) 2016, Gold Prairie LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-07-27
 *
 * @package flexio
 * @subpackage Tests
 */


declare(strict_types=1);
namespace Flexio\Tests;


class Test
{
    public function run(&$results)
    {
        // TEST: PipelineDeals API examples

        // BEGIN TEST; See here for example documentation: https://app.pipelinedeals.com/api/docs/resources/people
        $data = '
        [
            {
                "id": 3,
                "lead_source": {
                    "id": 2,
                    "name": "Cold Call"
                },
                "lead_status": {
                    "id": 5,
                    "name": "Follow-up"
                },
                "last_name": "Ajtai",
                "user": {
                    "id": 1,
                    "full_name": "admin nick"
                },
                "first_name": "Veronika",
                "created_at": "2012/01/23 13:10:27 -0500",
                "email": "ffie2@w22md22b.org",
                "custom_fields": {
                    "custom_label_90": 49,
                    "custom_label_83": [
                        28,
                        29,
                        30
                    ],
                    "custom_label_89": "2012/05/21"
                },
                "predefined_contacts_tags": [

                ],
                "work_address_1": null,
                "work_address_2": null,
                "type": "Contact",
                "company_id": 8,
                "user_id": 1,
                "predefined_contacts_tag_ids": [

                ],
                "phone": null
            },
            {
                "id": 4,
                "lead_source": {
                    "id": 2,
                    "name": "Cold Call"
                },
                "lead_status": {
                    "id": 3,
                    "name": "Warm"
                },
                "last_name": "Yarmosh",
                "user": {
                    "id": 6,
                    "full_name": "basic2 basic2"
                },
                "first_name": "Ken",
                "created_at": "2012/01/23 13:10:27 -0500",
                "email": "ken@technosight.com",
                "custom_fields": {
                    "custom_label_90": 48,
                    "custom_label_82": 66,
                    "custom_label_83": [
                        28,
                        29
                    ],
                    "custom_label_89": "2012/05/18"
                },
                "predefined_contacts_tags": [

                ],
                "work_address_1": null,
                "work_address_2": null,
                "type": "Lead",
                "company_id": 15,
                "user_id": 6,
                "predefined_contacts_tag_ids": [

                ],
                "phone": "1215-801-7554"
            }
        ]
        ';
        $schema = null;
        $delimiter = '_';
        $actual = \Flexio\Base\Mapper::flatten($data, $schema, $delimiter);
        $expected = '
        [
            {
                "id": 3,
                "lead_source_id": 2,
                "lead_source_name": "Cold Call",
                "lead_status_id": 5,
                "lead_status_name": "Follow-up",
                "last_name": "Ajtai",
                "user_id": 1,
                "user_full_name": "admin nick",
                "first_name": "Veronika",
                "created_at": "2012/01/23 13:10:27 -0500",
                "email": "ffie2@w22md22b.org",
                "custom_fields_custom_label_90": 49,
                "custom_fields_custom_label_83": 28,
                "custom_fields_custom_label_89": "2012/05/21",
                "predefined_contacts_tags": null,
                "work_address_1": null,
                "work_address_2": null,
                "type": "Contact",
                "company_id": 8,
                "predefined_contacts_tag_ids": null,
                "phone": null
            },
            {
                "id": 3,
                "lead_source_id": 2,
                "lead_source_name": "Cold Call",
                "lead_status_id": 5,
                "lead_status_name": "Follow-up",
                "last_name": "Ajtai",
                "user_id": 1,
                "user_full_name": "admin nick",
                "first_name": "Veronika",
                "created_at": "2012/01/23 13:10:27 -0500",
                "email": "ffie2@w22md22b.org",
                "custom_fields_custom_label_90": 49,
                "custom_fields_custom_label_83": 29,
                "custom_fields_custom_label_89": "2012/05/21",
                "predefined_contacts_tags": null,
                "work_address_1": null,
                "work_address_2": null,
                "type": "Contact",
                "company_id": 8,
                "predefined_contacts_tag_ids": null,
                "phone": null
            },
            {
                "id": 3,
                "lead_source_id": 2,
                "lead_source_name": "Cold Call",
                "lead_status_id": 5,
                "lead_status_name": "Follow-up",
                "last_name": "Ajtai",
                "user_id": 1,
                "user_full_name": "admin nick",
                "first_name": "Veronika",
                "created_at": "2012/01/23 13:10:27 -0500",
                "email": "ffie2@w22md22b.org",
                "custom_fields_custom_label_90": 49,
                "custom_fields_custom_label_83": 30,
                "custom_fields_custom_label_89": "2012/05/21",
                "predefined_contacts_tags": null,
                "work_address_1": null,
                "work_address_2": null,
                "type": "Contact",
                "company_id": 8,
                "predefined_contacts_tag_ids": null,
                "phone": null
            },
            {
                "id": 4,
                "lead_source_id": 2,
                "lead_source_name": "Cold Call",
                "lead_status_id": 3,
                "lead_status_name": "Warm",
                "last_name": "Yarmosh",
                "user_id": 6,
                "user_full_name": "basic2 basic2",
                "first_name": "Ken",
                "created_at": "2012/01/23 13:10:27 -0500",
                "email": "ken@technosight.com",
                "custom_fields_custom_label_90": 48,
                "custom_fields_custom_label_82": 66,
                "custom_fields_custom_label_83": 28,
                "custom_fields_custom_label_89": "2012/05/18",
                "predefined_contacts_tags": null,
                "work_address_1": null,
                "work_address_2": null,
                "type": "Lead",
                "company_id": 15,
                "predefined_contacts_tag_ids": null,
                "phone": "1215-801-7554"
            },
            {
                "id": 4,
                "lead_source_id": 2,
                "lead_source_name": "Cold Call",
                "lead_status_id": 3,
                "lead_status_name": "Warm",
                "last_name": "Yarmosh",
                "user_id": 6,
                "user_full_name": "basic2 basic2",
                "first_name": "Ken",
                "created_at": "2012/01/23 13:10:27 -0500",
                "email": "ken@technosight.com",
                "custom_fields_custom_label_90": 48,
                "custom_fields_custom_label_82": 66,
                "custom_fields_custom_label_83": 29,
                "custom_fields_custom_label_89": "2012/05/18",
                "predefined_contacts_tags": null,
                "work_address_1": null,
                "work_address_2": null,
                "type": "Lead",
                "company_id": 15,
                "predefined_contacts_tag_ids": null,
                "phone": "1215-801-7554"
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.1', '\Flexio\Base\Mapper::flatten(); subset of PipelineDeals "People" API call result',  $actual, $expected, $results);


        // BEGIN TEST; See here for example documentation: https://app.pipelinedeals.com/api/docs/resources/documents
        $data = '
        [
            {
                "created_at": "2014/01/09 16:11:54 -0500",
                "deal_id": 154,
                "id": 1,
                "owner_id": 1,
                "person_id": null,
                "title": "aocbar58.jpg",
                "updated_at": "2014/01/09 16:11:54 -0500",
                "upload_status": 2,
                "size_in_k": 238,
                "upload_state": "complete",
                "etag": "ab7d82d93e533a96191455a1838080b9",
                "owner": {
                    "id": 1,
                    "first_name": "hobo",
                    "last_name": "hoboson"
                },
                "deal": {
                    "id": 154,
                    "name": "maximize mission-critical interfaces"
                },
                "person": null,
                "company": null
            }
        ]
        ';
        $schema = null;
        $actual = \Flexio\Base\Mapper::flatten($data, $schema);
        $expected = '
        [
            {
                "created_at": "2014/01/09 16:11:54 -0500",
                "deal_id": 154,
                "id": 1,
                "owner_id": 1,
                "person_id": null,
                "title": "aocbar58.jpg",
                "updated_at": "2014/01/09 16:11:54 -0500",
                "upload_status": 2,
                "size_in_k": 238,
                "upload_state": "complete",
                "etag": "ab7d82d93e533a96191455a1838080b9",
                "owner.id": 1,
                "owner.first_name": "hobo",
                "owner.last_name": "hoboson",
                "deal.id": 154,
                "deal.name": "maximize mission-critical interfaces",
                "person": null,
                "company": null
            }
        ]
        ';
        \Flexio\Tests\Check::assertArray('A.2', '\Flexio\Base\Mapper::flatten(); subset of PipelineDeals "Documents" API call result',  $actual, $expected, $results);
    }
}
