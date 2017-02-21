<?php
/**
 *
 * Copyright (c) 2015, Gold Prairie, Inc.  All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2015-09-17
 *
 * @package flexio
 * @subpackage Tests
 */


class Test
{
    public function run(&$results)
    {
        // TODO: might consider adding more format tests; currently the
        // schema validator relies on utility functions that are tested
        // in the system tests, so the implementation is fairly secure;
        // however, we may want to add a few more tests to guarantee the
        // functionality without assuming we're using these


        // TEST: single format parameter validation

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 1;
        $v5 = -0.9;
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $v8 = '';
        $v9 = 'a';
        $v10 = 'localhost';
        $v11 = 'www.domain.com';
        $v12 = '10.1.1.1';
        $v13 = '::1';
        $v14 = 'FFFF::0A';
        $v15 = 'firstname.lastname@email.com';
        $v16 = '1999-12-31T23:59:59+0000';
        $v17 = \Eid::generate();
        $v18 = 'identifier1';
        $template = <<<EOD
{
    "format": null
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $r17 = ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('A.1', 'ValidatorSchema::check(); return false if the format parameter isn\'t valid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 1;
        $v5 = -0.9;
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $v8 = '';
        $v9 = 'a';
        $v10 = 'localhost';
        $v11 = 'www.domain.com';
        $v12 = '10.1.1.1';
        $v13 = '::1';
        $v14 = 'FFFF::0A';
        $v15 = 'firstname.lastname@email.com';
        $v16 = '1999-12-31T23:59:59+0000';
        $v17 = \Eid::generate();
        $v18 = 'identifier1';
        $template = <<<EOD
{
    "format": false
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $r17 = ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18);
        $expected = array(false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false);
        TestCheck::assertArray('A.2', 'ValidatorSchema::check(); return false if the format parameter isn\'t valid',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 1;
        $v5 = -0.9;
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $v8 = '';
        $v9 = 'a';
        $v10 = 'localhost';
        $v11 = 'www.domain.com';
        $v12 = '10.1.1.1';
        $v13 = '::1';
        $v14 = 'FFFF::0A';
        $v15 = 'firstname.lastname@email.com';
        $v16 = '1999-12-31T23:59:59+0000';
        $v17 = \Eid::generate();
        $v18 = 'identifier1';
        $template = <<<EOD
{
    "format": ""
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $r17 = ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18);
        $expected = array(true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true);
        TestCheck::assertArray('A.3', 'ValidatorSchema::check(); return true if the format parameter is a string that isn\'t recognized',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 1;
        $v5 = -0.9;
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $v8 = '';
        $v9 = 'a';
        $v10 = 'localhost';
        $v11 = 'www.domain.com';
        $v12 = '10.1.1.1';
        $v13 = '::1';
        $v14 = 'FFFF::0A';
        $v15 = 'firstname.lastname@email.com';
        $v16 = '1999-12-31T23:59:59+0000';
        $v17 = \Eid::generate();
        $v18 = 'identifier1';
        $template = <<<EOD
{
    "format": "bad"
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $r17 = ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18);
        $expected = array(true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true);
        TestCheck::assertArray('A.4', 'ValidatorSchema::check(); return true if the format parameter is a string that isn\'t recognized',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 1;
        $v5 = -0.9;
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $v8 = '';
        $v9 = 'a';
        $v10 = 'localhost';
        $v11 = 'www.domain.com';
        $v12 = '10.1.1.1';
        $v13 = '::1';
        $v14 = 'FFFF::0A';
        $v15 = 'firstname.lastname@email.com';
        $v16 = '1999-12-31T23:59:59+0000';
        $v17 = \Eid::generate();
        $v18 = 'identifier1';
        $template = <<<EOD
{
    "format": "hostname"
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $r17 = ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18);
        $expected = array(true,true,true,true,true,true,true,false,true,true,true,true,false,false,false,false,true,true);
        TestCheck::assertArray('A.5', 'ValidatorSchema::check(); return false if the value doesn\'t match a known, specified format',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 1;
        $v5 = -0.9;
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $v8 = '';
        $v9 = 'a';
        $v10 = 'localhost';
        $v11 = 'www.domain.com';
        $v12 = '10.1.1.1';
        $v13 = '::1';
        $v14 = 'FFFF::0A';
        $v15 = 'firstname.lastname@email.com';
        $v16 = '1999-12-31T23:59:59+0000';
        $v17 = \Eid::generate();
        $v18 = 'identifier1';
        $template = <<<EOD
{
    "format": "ipv4"
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $r17 = ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18);
        $expected = array(true,true,true,true,true,true,true,false,false,false,false,true,false,false,false,false,false,false);
        TestCheck::assertArray('A.6', 'ValidatorSchema::check(); return false if the value doesn\'t match a known, specified format',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 1;
        $v5 = -0.9;
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $v8 = '';
        $v9 = 'a';
        $v10 = 'localhost';
        $v11 = 'www.domain.com';
        $v12 = '10.1.1.1';
        $v13 = '::1';
        $v14 = 'FFFF::0A';
        $v15 = 'firstname.lastname@email.com';
        $v16 = '1999-12-31T23:59:59+0000';
        $v17 = \Eid::generate();
        $v18 = 'identifier1';
        $template = <<<EOD
{
    "format": "ipv6"
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $r17 = ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18);
        $expected = array(true,true,true,true,true,true,true,false,false,false,false,false,true,true,false,false,false,false);
        TestCheck::assertArray('A.7', 'ValidatorSchema::check(); return false if the value doesn\'t match a known, specified format',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 1;
        $v5 = -0.9;
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $v8 = '';
        $v9 = 'a';
        $v10 = 'localhost';
        $v11 = 'www.domain.com';
        $v12 = '10.1.1.1';
        $v13 = '::1';
        $v14 = 'FFFF::0A';
        $v15 = 'firstname.lastname@email.com';
        $v16 = '1999-12-31T23:59:59+0000';
        $v17 = \Eid::generate();
        $v18 = 'identifier1';
        $template = <<<EOD
{
    "format": "email"
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $r17 = ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18);
        $expected = array(true,true,true,true,true,true,true,false,false,false,false,false,false,false,true,false,false,false);
        TestCheck::assertArray('A.8', 'ValidatorSchema::check(); return false if the value doesn\'t match a known, specified format',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 1;
        $v5 = -0.9;
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $v8 = '';
        $v9 = 'a';
        $v10 = 'localhost';
        $v11 = 'www.domain.com';
        $v12 = '10.1.1.1';
        $v13 = '::1';
        $v14 = 'FFFF::0A';
        $v15 = 'firstname.lastname@email.com';
        $v16 = '1999-12-31T23:59:59+0000';
        $v17 = \Eid::generate();
        $v18 = 'identifier1';
        $template = <<<EOD
{
    "format": "date-time"
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $r17 = ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18);
        $expected = array(true,true,true,true,true,true,true,false,false,false,false,false,false,false,false,true,false,false);
        TestCheck::assertArray('A.9', 'ValidatorSchema::check(); return false if the value doesn\'t match a known, specified format',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 1;
        $v5 = -0.9;
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $v8 = '';
        $v9 = 'a';
        $v10 = 'localhost';
        $v11 = 'www.domain.com';
        $v12 = '10.1.1.1';
        $v13 = '::1';
        $v14 = 'FFFF::0A';
        $v15 = 'firstname.lastname@email.com';
        $v16 = '1999-12-31T23:59:59+0000';
        $v17 = \Eid::generate();
        $v18 = 'identifier1';
        $template = <<<EOD
{
    "format": "fx.eid"
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $r17 = ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18);
        $expected = array(true,true,true,true,true,true,true,false,false,false,false,false,false,false,false,false,true,false);
        TestCheck::assertArray('A.10', 'ValidatorSchema::check(); return false if the value doesn\'t match a known, specified format',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 1;
        $v5 = -0.9;
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $v8 = '';
        $v9 = 'a';
        $v10 = 'localhost';
        $v11 = 'www.domain.com';
        $v12 = '10.1.1.1';
        $v13 = '::1';
        $v14 = 'FFFF::0A';
        $v15 = 'firstname.lastname@email.com';
        $v16 = '1999-12-31T23:59:59+0000';
        $v17 = \Eid::generate();
        $v18 = 'identifier1';
        $template = <<<EOD
{
    "format": "fx.identifier"
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $r17 = ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18);
        $expected = array(true,true,true,true,true,true,true,false,false,true,false,false,false,false,false,false,true,true);
        TestCheck::assertArray('A.11', 'ValidatorSchema::check(); return false if the value doesn\'t match a known, specified format',  $actual, $expected, $results);

        // BEGIN TEST
        $v1 = null;
        $v2 = true;
        $v3 = 0;
        $v4 = 1;
        $v5 = -0.9;
        $v6 = json_decode('{}');
        $v7 = json_decode('[]');
        $v8 = '';
        $v9 = 'a';
        $v10 = 'localhost';
        $v11 = 'www.domain.com';
        $v12 = '10.1.1.1';
        $v13 = '::1';
        $v14 = 'FFFF::0A';
        $v15 = 'firstname.lastname@email.com';
        $v16 = '1999-12-31T23:59:59+0000';
        $v17 = \Eid::generate();
        $v18 = 'identifier1';
        $template = <<<EOD
{
    "format": "fx.fieldname"
}
EOD;
        $r1 = ValidatorSchema::check($v1, $template)->hasErrors() === false;
        $r2 = ValidatorSchema::check($v2, $template)->hasErrors() === false;
        $r3 = ValidatorSchema::check($v3, $template)->hasErrors() === false;
        $r4 = ValidatorSchema::check($v4, $template)->hasErrors() === false;
        $r5 = ValidatorSchema::check($v5, $template)->hasErrors() === false;
        $r6 = ValidatorSchema::check($v6, $template)->hasErrors() === false;
        $r7 = ValidatorSchema::check($v7, $template)->hasErrors() === false;
        $r8 = ValidatorSchema::check($v8, $template)->hasErrors() === false;
        $r9 = ValidatorSchema::check($v9, $template)->hasErrors() === false;
        $r10 = ValidatorSchema::check($v10, $template)->hasErrors() === false;
        $r11 = ValidatorSchema::check($v11, $template)->hasErrors() === false;
        $r12 = ValidatorSchema::check($v12, $template)->hasErrors() === false;
        $r13 = ValidatorSchema::check($v13, $template)->hasErrors() === false;
        $r14 = ValidatorSchema::check($v14, $template)->hasErrors() === false;
        $r15 = ValidatorSchema::check($v15, $template)->hasErrors() === false;
        $r16 = ValidatorSchema::check($v16, $template)->hasErrors() === false;
        $r17 = ValidatorSchema::check($v17, $template)->hasErrors() === false;
        $r18 = ValidatorSchema::check($v18, $template)->hasErrors() === false;
        $actual = array($r1,$r2,$r3,$r4,$r5,$r6,$r7,$r8,$r9,$r10,$r11,$r12,$r13,$r14,$r15,$r16,$r17,$r18);
        $expected = array(true,true,true,true,true,true,true,false,true,true,false,false,false,false,false,false,true,true);
        TestCheck::assertArray('A.12', 'ValidatorSchema::check(); return false if the value doesn\'t match a known, specified format',  $actual, $expected, $results);
    }
}
