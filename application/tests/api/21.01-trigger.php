<?php
/**
 *
 * Copyright (c) 2016, Flex Research LLC. All rights reserved.
 *
 * Project:  Flex.io App
 * Author:   Aaron L. Williams
 * Created:  2016-08-03
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
        // ENDPOINT: POST /:teamid/pipes


        // SETUP
        $apibase = \Flexio\Tests\Util::getTestHost() . '/v1';
        $username = \Flexio\Base\Util::generateHandle();
        $password = \Flexio\Base\Password::generate();
        $userid = \Flexio\Tests\Util::createUser($username, null, $password);
        $token = \Flexio\Tests\Util::createToken($userid);


        // TEST: create a new pipe

        // BEGIN TEST
        $name = \Flexio\Base\Util::generateHandle();
        $task = array(
            "op" => "echo",
            "msg" => 'From: ${email-from-display} <${email-from}>; Message: ${input}; Files: ${files}'
        );
        $params = array(
            'method' => 'POST',
            'url' => "$apibase/$userid/pipes",
            'token' => $token,
            'content_type' => 'application/json',
            'params' => json_encode(["name" => $name, "task" => $task, "deploy_mode" => "R"])
        );
        \Flexio\Tests\Util::callApi($params);

        $content = getSampleEmailWithAttachment($username, $name);
        $path = \Flexio\Base\File::getTempFilename('txt');
        file_put_contents($path, $content);
        $handle = fopen($path, 'r');

        ob_start();
        \Flexio\Api\Trigger::handleEmail($handle, true);
        $actual = ob_get_clean();

        fclose($handle);
        unlink($path);

        $expected = "From: Test User <test@flex.io>; Message: \nThis is an email with a CSV attachment.\n\n\n\n; Files: {\"Vend_mast.csv\":{\"name\":\"Vend_mast.csv\",\"extension\":\"csv\",\"size\":8695,\"content_type\":\"application/octet-stream\"}}";
        \Flexio\Tests\Check::assertString('A.1', 'EMAIL <username>/<name>@pipes.flex.io; check for handling email input to a pipe',  $actual, $expected, $results);
    }
}

function getSampleEmailWithAttachment(string $username, string $name)
{
    // following is the raw text of an email with the following values:
    // from: test@flex.io
    // to: $username/$name@pipes.flex.io
    // subject: Attachment Test
    // body: This is an email with a CSV attachment.
    // attachment: Vend_mast.csv

    $data = <<<EOD
From test@flex.io  Wed Aug  3 23:52:22 2016
Return-Path: <test@flex.io>
X-Original-To: $username/$name@pipes.flex.io
Delivered-To: $username/$name@pipes.flex.io
Received: from NAM02-SN1-obe.outbound.protection.outlook.com (mail-sn1nam02on0052.outbound.protection.outlook.com [104.47.36.52])
        by pipes.flex.io (Postfix) with ESMTPS id 6DD0B805B6
        for <$username/$name@pipes.flex.io>; Wed,  3 Aug 2016 23:52:22 +0000 (UTC)
DKIM-Signature: v=1; a=rsa-sha256; c=relaxed/relaxed;
 d=flexio.onmicrosoft.com; s=selector1-flex-io;
 h=From:Date:Subject:Message-ID:Content-Type:MIME-Version;
 bh=OD6P+69gssDuX8P6hOgBJSoZR5GS1wuiHS6WYAJEK2A=;
 b=ByLutEEEWeYNHWFvZGz114atu0pc6TjCR5c5vS217Lxnj+veVGwxSApkktcoyKsF8bEz45P6K1ff14ukujR6OnLYWLTLbrAFTnJy+W6r0tUlTR8ymTukdDZkrEwIUPN9m2p5pokhkjP3BsvQrpl5ucQFQgHWYNVqNEWMpW+uSBk=
Received: from BN6PR12MB1233.namprd12.prod.outlook.com (10.168.227.19) by
 BN6PR12MB1233.namprd12.prod.outlook.com (10.168.227.19) with Microsoft SMTP
 Server (version=TLS1_2, cipher=TLS_ECDHE_RSA_WITH_AES_256_CBC_SHA384_P384) id
 15.1.549.15; Wed, 3 Aug 2016 23:53:19 +0000
Received: from BN6PR12MB1233.namprd12.prod.outlook.com ([10.168.227.19]) by
 BN6PR12MB1233.namprd12.prod.outlook.com ([10.168.227.19]) with mapi id
 15.01.0549.022; Wed, 3 Aug 2016 23:53:19 +0000
From: Test User <test@flex.io>
To: "$username/$name@pipes.flex.io" <$username/$name@pipes.flex.io>
Subject: Attachment Test
Thread-Topic: Attachment Test
Thread-Index: AdHt4e012cGejNcTRqasix1Acbd9mA==
Date: Wed, 3 Aug 2016 23:53:18 +0000
Message-ID: <BN6PR12MB1233850235FFD6CEF9272D10B6060@BN6PR12MB1233.namprd12.prod.outlook.com>
Accept-Language: en-US
Content-Language: en-US
X-MS-Has-Attach: yes
X-MS-TNEF-Correlator:
authentication-results: spf=none (sender IP is ) smtp.mailfrom=test@flex.io;
x-originating-ip: [168.93.116.250]
x-ms-office365-filtering-correlation-id: 8ae1131a-6f87-498c-b862-08d3bbf95838
x-microsoft-exchange-diagnostics: 1;BN6PR12MB1233;6:xi+E9EQFCeoG4V8rRlWmsfsDhmdK/PxJAYRfNWt5KOiVp8PpZid2c/U7vDS2wrEXSY1Vf6SVyv4aP0MVHJHvG69i4an940i7JHrf+232nthIznK7F9gpMR8767P4ksdZjubxie6x0YzG90mRwGZ/QcI5k05oBxqDPdrItuIpQj/DCMfsK0UtQ0RT2r9QdaJVy3oRU5gNg4ejpiyEHXuLyLIeLqQfTaSkDNznq1OtRq8pSLXmoNNhQcMYfkyiBoAIpDih20lI0FYDS9P6CHBpDDW19P4OHv0XOeQ6KPtSUmko54blw2IPA6MDKsedU+o7;5:jkqqPbWAI53qk0XAFktwK4MX/fxjNJy8/OZL7wd7fRkWZQlOXSd/Op14agKyEZJKWUI99kB0DQLaGx8KMj/pLhPWZKU1xFkOsMZttqDwGSn5LWCSmPgnTT90mi7lQuukk76RkIGNVfoCQjsc9S4N7Q==;24:TL9klAIbAVn9SjfKlkHNjRL7i6j4No/+T4WW90cDQkNXUd66nKZUzlopA4Kd/AUOFpgPRLPfEbrjhSUpAl4Ljvv0LvvTdCiOkJQY4V6tZkg=;7:zq3arcxSlcUCuCWZ0dvLcoebOvAUv0xMP1lCUTqY8yFa4N9OFWHX/HenFkhAmtp1Pbi/aFCKurIfgcqpXeSvjjac//0Ww0e1PScGE/Bwj6wkHUnL+vMcE+ndr4fsbHbwYq3Uq32N+RVtzV+YxE4nWbdtg5YXDwo7GVMgv1p3uWldLb5iT0ps8pWq7+R2zmeBIPRrmgFlQb9HiI0EZnxJjFZ8WBe3OjVgrYxUJ0wi3pH3McfpRAXm10RzbDUOLlZd
x-microsoft-antispam: UriScan:;BCL:0;PCL:0;RULEID:;SRVR:BN6PR12MB1233;
x-microsoft-antispam-prvs: <BN6PR12MB1233997D941C418AC2E05B28B6060@BN6PR12MB1233.namprd12.prod.outlook.com>
x-exchange-antispam-report-test: UriScan:(21748063052155);
x-exchange-antispam-report-cfa-test: BCL:0;PCL:0;RULEID:(102415321)(6040130)(601004)(2401047)(8121501046)(5005006)(3002001)(10201501046)(6041072)(6043046);SRVR:BN6PR12MB1233;BCL:0;PCL:0;RULEID:;SRVR:BN6PR12MB1233;
x-forefront-prvs: 00235A1EEF
x-forefront-antispam-report: SFV:NSPM;SFS:(10009020)(6009001)(7916002)(199003)(189002)(7696003)(2351001)(229853001)(101416001)(97736004)(7846002)(7736002)(19625215002)(68736007)(77096005)(74316002)(76576001)(19580395003)(588024002)(87936001)(110136002)(107886002)(4270600005)(9686002)(221733001)(1240700005)(5890100001)(15975445007)(54356999)(33656002)(586003)(790700001)(558084003)(3480700004)(92566002)(5002640100001)(6116002)(66066001)(102836003)(81166006)(81156014)(8676002)(106356001)(3660700001)(1730700003)(3280700002)(3846002)(16236675004)(10400500002)(50986999)(8936002)(122556002)(11100500001)(74482002)(189998001)(99936001)(2906002)(99286002)(2900100001)(86362001)(2501003)(19300405004)(105586002)(450100001);DIR:OUT;SFP:1101;SCL:1;SRVR:BN6PR12MB1233;H:BN6PR12MB1233.namprd12.prod.outlook.com;FPR:;SPF:None;PTR:InfoNoRecords;A:1;MX:1;LANG:en;
received-spf: None (protection.outlook.com: flex.io does not designate
 permitted sender hosts)
spamdiagnosticoutput: 1:99
spamdiagnosticmetadata: NSPM
Content-Type: multipart/mixed;
        boundary="_004_BN6PR12MB1233850235FFD6CEF9272D10B6060BN6PR12MB1233namp_"
MIME-Version: 1.0
X-OriginatorOrg: flex.io
X-MS-Exchange-CrossTenant-originalarrivaltime: 03 Aug 2016 23:53:18.6351
 (UTC)
X-MS-Exchange-CrossTenant-fromentityheader: Hosted
X-MS-Exchange-CrossTenant-id: ea643978-1238-48ab-9676-b53394c6d27b
X-MS-Exchange-Transport-CrossTenantHeadersStamped: BN6PR12MB1233

--_004_BN6PR12MB1233850235FFD6CEF9272D10B6060BN6PR12MB1233namp_
Content-Type: multipart/alternative;
        boundary="_000_BN6PR12MB1233850235FFD6CEF9272D10B6060BN6PR12MB1233namp_"

--_000_BN6PR12MB1233850235FFD6CEF9272D10B6060BN6PR12MB1233namp_
Content-Type: text/plain; charset="us-ascii"
Content-Transfer-Encoding: quoted-printable


This is an email with a CSV attachment.



--_000_BN6PR12MB1233850235FFD6CEF9272D10B6060BN6PR12MB1233namp_
Content-Type: text/html; charset="us-ascii"
Content-Transfer-Encoding: quoted-printable

<html xmlns:v=3D"urn:schemas-microsoft-com:vml" xmlns:o=3D"urn:schemas-micr=
osoft-com:office:office" xmlns:w=3D"urn:schemas-microsoft-com:office:word" =
xmlns:m=3D"http://schemas.microsoft.com/office/2004/12/omml" xmlns=3D"http:=
//www.w3.org/TR/REC-html40">
<head>
<meta http-equiv=3D"Content-Type" content=3D"text/html; charset=3Dus-ascii"=
>
<meta name=3D"Generator" content=3D"Microsoft Word 15 (filtered medium)">
<style><!--
/* Font Definitions */
@font-face
        {font-family:"Cambria Math";
        panose-1:2 4 5 3 5 4 6 3 2 4;}
@font-face
        {font-family:Calibri;
        panose-1:2 15 5 2 2 2 4 3 2 4;}
/* Style Definitions */
p.MsoNormal, li.MsoNormal, div.MsoNormal
        {margin:0in;
        margin-bottom:.0001pt;
        font-size:11.0pt;
        font-family:"Calibri",sans-serif;}
a:link, span.MsoHyperlink
        {mso-style-priority:99;
        color:#0563C1;
        text-decoration:underline;}
a:visited, span.MsoHyperlinkFollowed
        {mso-style-priority:99;
        color:#954F72;
        text-decoration:underline;}
span.EmailStyle17
        {mso-style-type:personal-compose;
        font-family:"Calibri",sans-serif;
        color:windowtext;}
.MsoChpDefault
        {mso-style-type:export-only;
        font-family:"Calibri",sans-serif;}
@page WordSection1
        {size:8.5in 11.0in;
        margin:1.0in 1.0in 1.0in 1.0in;}
div.WordSection1
        {page:WordSection1;}
--></style><!--[if gte mso 9]><xml>
<o:shapedefaults v:ext=3D"edit" spidmax=3D"1026" />
</xml><![endif]--><!--[if gte mso 9]><xml>
<o:shapelayout v:ext=3D"edit">
<o:idmap v:ext=3D"edit" data=3D"1" />
</o:shapelayout></xml><![endif]-->
</head>
<body lang=3D"EN-US" link=3D"#0563C1" vlink=3D"#954F72">
<div class=3D"WordSection1">
<p class=3D"MsoNormal"><o:p>&nbsp;</o:p></p>
<p class=3D"MsoNormal">This is an email with a CSV attachment.<o:p></o:p></=
p>
<p class=3D"MsoNormal"><o:p>&nbsp;</o:p></p>
<p class=3D"MsoNormal"><o:p>&nbsp;</o:p></p>
</div>
</body>
</html>

--_000_BN6PR12MB1233850235FFD6CEF9272D10B6060BN6PR12MB1233namp_--

--_004_BN6PR12MB1233850235FFD6CEF9272D10B6060BN6PR12MB1233namp_
Content-Type: application/octet-stream; name="Vend_mast.csv"
Content-Description: Vend_mast.csv
Content-Disposition: attachment; filename="Vend_mast.csv"; size=8695;
        creation-date="Thu, 04 Feb 2016 16:24:49 GMT";
        modification-date="Thu, 04 Feb 2016 16:24:33 GMT"
Content-Transfer-Encoding: base64

VmVuZF9ubyxWZW5kX25hbWUsVmVuZF9hZGRyMSxWZW5kX2FkZHIyLENpdHksU3RhdGUsWmlwLFBh
eV9jZCxTdGF0X2NkLFRlcm1zLERpc2MsTHN0X3VwZGF0ZQ0KMDAwMDMxLEJPSVNFIEZJRUxEUyw2
OTkgSkFDS1NPTiwjNTAwLE5BTVBBLElELDgzNjg2LCwsMzAsMCwyMDEzLzAzLzA3DQowMDAwNTMs
Q1JVTkNISUVTLFBPIEJPWCA1ODAwLCxBVExBTlRBLEdBLDMwMzIwLEEsLDMwLDAsMjAxMy8wMS8x
Mw0KMDAwMDU2LEhBTUlMVE9OIEZPT0RTLDI0NiBTT1VUSCBXSUxTT04gQVZFTlVFLCxTQUxUIExB
S0UgQ0lUWSxVVCw4NDExNiwsLDMwLDIwLDIwMTMvMDEvMjENCjAwMDA4NixIRUFSVFkgTUVBTFMg
Q08sNzc1IE1BSU4gU1QsLExJTkNPTE4sTkUsNjg1MjAsLCwzMCwwLDIwMTMvMDMvMjUNCjAwMDA4
OSxBTUFOTkEgQkFOQU5BLFAgTyBCT1ggNzE0MzIsQVRUTiBDQVJSSUUgU01JVEgsTUlBTUksRkws
MzMxMTQsLCw0NSwwLDIwMTMvMDQvMDINCjAwMDE3MyxST01BTk8gQlJBTkRTLFAgTyBCT1ggNzUy
NSwsQk9TVE9OLE1BLDAyMTEyLEEsLDMwLDE1LDIwMTMvMDMvMDUNCjAwMDE4NixDT1JNSU4sUC5P
LiBCT1ggMTAxODcsLFNDT1RUU0RBTEUsQVosODUyNjksLCwzMCwwLDIwMTMvMDQvMDMNCjAwMDIy
NSxHTE9CQUwgRk9PRCBDT1JQLFAuTy4gQk9YIDExMTEsLEtBTlNBUyBDSVRZLEtTLDY2MTEwLCws
MzAsMjAsMjAxMy8wMy8xNQ0KMDAwMjcwLEdSRUVOIEZJU0gsQy9PIFdBTFRFUiBKT05FUywzODE4
IEdMRU5EQUxFLFNFQVRUTEUsV0EsOTgxMTksQSwsMzAsMCwyMDEzLzAzLzA4DQowMDA4MDIsTU9M
TFkgVEhPUlRPTiwyMzMgMTdUSCBORSwsQ0hJQ0FHTyxJTCw2MDYwNiwsLDMwLDAsMjAxMy8wMi8x
Nw0KMDAwODM0LFBBUlNFTidTLFAuTy4gQk9YIDEwOTg0LCxTQUlOVCBQQVVMLE1OLDU1MTY1LCxY
LDMwLDAsMjAwOS8wMi8wNw0KMDAxMjk1LEdVTkRZIEJSQU5EUyxQIE8gQk9YIDY1MDksLEdSRUVO
IEJBWSxXSSw1NDMwNywsWCwzMCwwLDIwMTEvMDMvMTUNCjAxMDAwNixBVklWQSBGT09EUyxQIE8g
Qk9YIDIwMDQsLExVQkJPQ0ssVFgsNzk0MDgsLCwzMCwwLDIwMTMvMDEvMjQNCjAxMDIyMSxBQ09S
TiBNSUxMUyxQTyBCT1ggNzAwMSwsTElOQ09MTixORSw2ODUyOSwsLDQ1LDAsMjAxMy8wMy8wNQ0K
MDEwMjM2LElUQUxJQU4gWkVTVCxQIE8gQk9YIDg3MzQwMSwsTkVXIFlPUkssTlksMTAxODUsLCwz
MCwwLDIwMTMvMDQvMDYNCjAxMDI2NyxUQVNURSBPRiBGTE9SRU5DRSxQLk8uIEJPWCAyMDkxMCws
U1QgTE9VSVMsTU8sNjMxNTYsLCw0NSwwLDIwMTMvMDMvMDQNCjAxMDI2OCxPUklFTlQgRVhQUkVT
UyxQIE8gQk9YIDgwMTcsLFBPUlRMQU5ELE9SLDk3MjI4LCwsNjAsMCwyMDEzLzAxLzEyDQowMTAy
OTcsQU5EQVRJLFBPIEJPWCAxMjksLE5FVyBZT1JLLE5ZLDEwMTA4LCwsMzAsMTUsMjAxMy8wMS8y
Ng0KMDEwMzUxLEJBS0VSJ1MgRlJJRU5ELFAuTy4gQk9YIDIwMTIsLE9BS0xBTkQsQ0EsOTQ2MDQs
LFgsMzAsMCwyMDExLzEwLzI3DQowMTA0MTIsUEFOT1NUUkEsUC5PLiBCT1ggMTIwMDYsLEtBTlNB
UyBDSVRZLE1PLDY0MTcxLCwsMzAsMTAsMjAxMy8wMy8wNQ0KMDEwNDE4LFBBUlNFTidTLDcxMiAx
MlRIIFNUIE5PUlRILCxNSU5ORUFQT0xJUyxNTiw1NTQwOSxBLCwzMCwwLDIwMTMvMDMvMTkNCjAx
MTIzMCxGQVNUIE1PUk5JTkcgQ08sODAwIE1VUkRMRSwsUkFMRUlHSCxOQywyNzYxNSwsWCwzMCww
LDIwMDkvMDUvMDINCjAxMTM5MSxCT1dMIEFORCBTUE9PTiBMVEQsR0VPUkdFIEZJU0hFUiwsU1Qg
TE9VSVMsTU8sNjMxNTYsQSwsMzAsMCwyMDEzLzAyLzIzDQowMTE1MTcsQUFBIEJMVUUgUklCQk9O
LDE5IE1FU0EgQkxWRCwsU0FMVCBMQUtFIENJVFksVVQsODQxMDMsQSwsMzAsMjAsMjAxMy8wMS8x
Mg0KMDEyMzk1LEFNQU5OQSBCQU5BTkEsUCBPIEJPWCA5MDgxLCxGT1JUIExBVURFUkRBTEUsRkws
MzMzMDcsLFgsNDUsMCwyMDExLzAxLzIzDQowMTI1MDIsU0FOIEpPQVFVSU4sUCBPIEJPWCAxODAy
MTMsLERBTExBUyxUWCw3NTIyMiwsLDQ1LDAsMjAxMy8wMS8xMg0KMDEyNTM5LEFNRVJJQ0EgR1JP
Q0VSWSBGT09EUyxQIE8gQk9YIDM2MTQxOCwsQ0xFVkVMQU5ELE9ILDQ0MTAxLCwsMzAsMjAsMjAx
My8wNC8wMQ0KMDEyNTc5LERJTk5FQyxERVBUICM3MDIsUCBPIEJPWCAzNzE1LEJMT09NSU5HVE9O
LElOLDQ3NDA3LCwsMzAsMTIsMjAxMy8wMy8xNQ0KMDEzMTI2LEJSRUFLRkFTVCBCT1VOVFksUCBP
IEJPWCA5MDg3LCxUVUxTQSxPSyw3NDEyMSwsLDMwLDAsMjAxMy8wMy8xMw0KMDEzOTE5LFVTRSBU
SEUgTk9PRExFIEZPT0RTLFAgTyBCT1ggNTAyMzE3LCxMT1MgQU5HRUxFUyxDQSw5MDA1MSwsLDQ1
LDAsMjAxMy8wMy8wMw0KMDE1NTQ4LEdSRUVOUyBHQUxPUkUsUCBPIEJPWCAxMjM5MDksLFRVTFNB
LE9LLDc0MTIxLCwsMzAsMCwyMDEzLzA0LzEwDQowMTczMzcsQ0FSSUJCRUFOIFRBU1RFLDcwMDYg
VEhFUkVTQSBBVkUuLCxUQU1QQSxGTCwzMzYyMCwsLDMwLDAsMjAxMy8wMi8wMQ0KMDE3NDc4LEJF
Vi1MSVRFLFBPIEJPWCAxODAwMCwsU0NPVFRTREFMRSxBWiw4NTI2MSwsLDMwLDIwLDIwMTMvMDEv
MjANCjAxODU1NCxESVJFQ1RXQVkgVFJBTlNQT1JUQVRJT04sUC5PLiBCT1ggMTA0NTI0LCxNSU5O
RUFQT0xJUyxNTiw1NTQwOSwsRiwyMCwwLDIwMTIvMDEvMzANCjAyMTQwNSxTQU4gSk9BUVVJTiw4
OTUgQkxVRSBTS0lFUyBBVkUuLCxFTCBQQVNPLFRYLDc5OTMxLCxYLDQ1LDAsMjAwOC8wNy8wMQ0K
MDIxNzY1LFJFRCBSQUJCSVQsUE8gQk9YIDkwMjMsLENISUNBR08sSUwsNjA2MDYsLCwzMCwwLDIw
MTMvMDEvMTkNCjAyMjMyNCxHUkVBVCBQTEFJTlMgU0hJUFBFUlMsUCBCIEJPWCA1ODUwLCxMSU5D
T0xOLE5FLDY4NTIwLCxGLDMwLDAsMjAxMi8wNC8xNQ0KMDIzNDk4LElUQUxJQU4gWkVTVCxQIE8g
Qk9YIDg3MzMwMSwsTkVXIFlPUkssTlksMTAxODUsLFgsMzAsMCwyMDEwLzA4LzE2DQowMjM5NDgs
U1RBTkxFWSBQT1dFUixQIE8gQk9YIDE0MzY5LCxDTEVWRUxBTkQsT0gsNDQxOTksLFUsMTUsMCwy
MDEyLzA0LzE5DQowMjc1NzYsUEVOVEVMTEEsODgwMCBIQVJSSVNPTixNQUlMU1RPUCAjMTAyMzUs
Q0hBUkxPVFRFLE5DLDI4MjEwLCwsMzAsMCwyMDEzLzAxLzExDQowMjgwMTUsU0FOIEpPQVFVSU4s
UCBPIEJPWCAyMzU4NywsRUwgUEFTTyxUWCw3OTkzMSwsWCw0NSwwLDIwMTEvMDIvMTUNCjAyODU5
OCxCQU5HS09LIEZPT0RTLDEwNTAgTEVMQU5EIEFWRSwsTE9TIEFOR0VMRVMsQ0EsOTAwNTIsLCwz
MCwwLDIwMTMvMDMvMjcNCjAzMjMyNCxNRVNBIFBST1BFUlRZIE1BTkFHRU1FTlQsMTgxOCBWSVNU
QSBBVkUuLCxQSE9FTklYLEFaLDg1MDE1LCxQLDAsMCwyMDEyLzA1LzI1DQowMzI5OTEsQkFLRVIn
UyBGUklFTkQsMjAxMiBCUk9BRCBTVCwsQkFLRVJTRklFTEQsQ0EsOTMzMDMsQSwsMzAsMCwyMDEz
LzAxLzAxDQowMzQ3NjcsRkFTVCBGTEVFVCBWQU5TICYgVFJVQ0tJTkcsUCBPIEJPWCAxODQ5NSws
UkFMRUlHSCxOQywyNzYwMiwsRiwzMCwwLDIwMTMvMDEvMDENCjAzNTQxMixXSU5EIFJJVkVSIE5B
VFVSQUwgR0FTLDc4NTAgODVUSCBTVCBORSwsUE9SVExBTkQsT1IsOTcyMDgsLFUsMTUsMCwyMDEz
LzAzLzIyDQowMzU2ODgsU0FOIEpPQVFVSU4sUCBPIEJPWCAxODA3NjMsLERBTExBUyxUWCw3NTIy
MiwsLDQ1LDAsMjAxMy8wMS8xNA0KMDM1OTU1LFRIQUkgRUxFUEhBTlQgSU5DLFAgTyBCT1ggMjMw
LCxQT1JUTEFORCxPUiw5NzIyOCwsLDMwLDAsMjAxMy8wMS8xNg0KMDM2Mzc5LEdPT0QgTU9STklO
RyBGT09EUyxQTyBCT1ggNTAwLCxDTEVWRUxBTkQsT0gsNDQxOTksLCwzMCwwLDIwMTMvMDMvMjIN
CjAzNjQ5MSxNT1JOIE9GIFBMRU5UWSw4OCBSSUNITU9ORCBDSVJDTEUsU1VJVEUgNTAwLFRVTFNB
LE9LLDc0MTIxLEEsLDMwLDAsMjAxMy8wMS8xMA0KMDM2NzUwLCJXT1JMRCBTUElDRVMsIElOQyIs
UCBPIEJPWCA3MDEyMiwsQ0hJQ0FHTyxJTCw2MDYwNixBLCwzMCwwLDIwMTMvMDIvMDYNCjAzNzY3
MixXQVNISU5HVE9OIFBMQVpBIFBST1BFUlRJRVMsUCBPIEJPWCAxNDU4LCxQT1JUTEFORCxPUiw5
NzIyOCwsUCwwLDAsMjAwOS8wOC8yMA0KMDM4MjQ4LENPTUlEQSBOQUNJT05BTCwxMCBHUkVBVCBD
SVJDTEUgRFJJVkUsLFNBTiBESUVHTyxDQSw5MjExNSwsLDMwLDAsMjAxMy8wMy8yMA0KMDM5ODQw
LCJCUkVBS0ZBU1QgQk9VTlRZLCBJTkMiLDIwODcgTUFQTEUgU1QuLFNURS4gMTAyNSxLQU5TQVMg
Q0lUWSxNTyw2NDExNCwsWCwzMCwwLDIwMTAvMDcvMjgNCjA0MTIwMSwiQlJFQUtGQVNUIEJPVU5U
WSwgSU5DIixQIE8gQk9YIDMwMTIyMywsS0FOU0FTIENJVFksTU8sNjQxNDIsLCwzMCwwLDIwMTMv
MDMvMTUNCjA0MjM3OCxTUFJJTkdGSUVMRCBNQUxMIExMQyxQLk8uIEJPWCAyMzg3NCwsU1BSSU5H
RklFTEQsTUEsMDExMDgsLFAsMCwwLDIwMTMvMDEvMTUNCjA0Mjg3OSxNQUdJQyBUT01BVE8sNTgw
MCBIQVJQRVIgTEFORSwsQklSTUlOR0hBTSxBTCwzNTIxMSwsLDMwLDAsMjAxMy8wMy8yMQ0KMDQy
OTkxLE9SSUVOVCBFWFBSRVNTIEZPT0RTLFAgTyBCT1ggNTc4MCwsUE9SVExBTkQsT1IsOTcyMjgs
LCw2MCwwLDIwMTMvMDEvMDkNCjA0Mzc4NCxUV0lOIE9BS1MgUFJPUEVSVFkgTU5HTVQgTExQLDE3
OCBKQU1FUyBXQVksLFNBQ1JBTUVOVE8sQ0EsOTU4MzAsLFAsMzAsMCwyMDEyLzEyLzAxDQowNDU4
MzAsVEFOR0VOVCxQLk8uIEJPWCA3ODEyLCxXQUNPLFRYLDc2NzAyLCxYLDMwLDAsMjAwNy8wOS8y
NQ0KMDQ2ODIwLEdVTkRZIEJSQU5EUyxQIE8gQk9YIDE2NzIxLCxDSElDQUdPLElMLDYwNjA2LCws
MzAsMCwyMDEzLzAzLzIzDQowNDc0NzksSU5DUkVCRUJJREEgSU5DLFAgTyBCT1ggNjQwMiwsQ0hJ
Q0FHTyxJTCw2MDYwNiwsLDQ1LDAsMjAxMy8wMS8yMQ0KMDQ3OTA5LE9QRU4gU0VTQU1FIFNFRUQs
MzQwMSBTLiBLRUxMWSBBVkUsLE1BRElTT04sV0ksNTM3MTUsLCwzMCwwLDIwMTMvMDQvMDUNCjA0
ODU2MyxTSUFNIFJJQ0UgQ09NUEFOWSwyODAwIElORFVTVFJJQUwgQkxWRCxERVBUIE5PIDM1LFNF
QVRUTEUsV0EsOTgxMTIsLCw0NSwwLDIwMTMvMDEvMjQNCjA0OTUwMSxKQVlWSVMgQlJPUyw5NTAg
T1JBTkdFIEJMVkQsLEpBQ0tTT05WSUxMRSxGTCwzMjIwNywsLDMwLDAsMjAxMy8wMS8yOQ0KMDUw
Njk5LFVTRSBUSEUgTk9PRExFIEZPT0QgQ09SUCxQIE8gQk9YIDMzMDQ0LCxTQU4gRlJBTkNJU0NP
LENBLDk0MTI1LCwsMzAsMCwyMDEzLzAzLzA1DQowNTI3MjcsQVZJVkEgRk9PRFMsUCBPIEJPWCAy
MDU4LCxMVUJCT0NLLFRYLDc5NDA4LCwsMzAsMCwyMDEzLzAxLzI2DQowNTI5NzAsRkFTVCBNT1JO
SU5HIENPLFAgTyBCT1ggMTA4MjcsLFJBTEVJR0gsTkMsMjc2MjAsLCwzMCwwLDIwMTMvMDMvMTQN
CjA1MzA3NSxTVVBFUiBDSEVGIFRIQUksUCBPIEJPWCA5ODU3LCxMT1MgQU5HRUxFUyxDQSw5MDA3
MiwsLDQ1LDAsMjAxMy8wMy8zMQ0KMDUzNDg3LE5PUlRIV09PRCBQUk9QRVJUSUVTIExURCBQQVJU
TkVSU0hJUCxQIE8gQk9YIDM4OTMsLE1JTk5FQVBPTElTLE1OLDU1NDA5LCxQLDAsMCwyMDExLzAx
LzE1DQowNTM1MTQsQlJFQUtGQVNUIE1BR0lDLDE3MDAgVklDVE9SSUEgU1RSRUVULCxJTkRJQU5B
UE9MSVMsSU4sNDYyMDksQSwsMzAsMCwyMDEzLzAzLzI0DQowNTc0MDIsR1JJRERMRSBIRUFWRU4g
TFRELFAgTyBCT1ggODUwMCwsSU5ESUFOQVBPTElTLElOLDQ2MjMwLEEsLDMwLDAsMjAxMy8wMy8x
NA0KMDU3NjY0LE1FWElDQUxJIEFaVUxFUyxQTyBCT1ggMTAyNDEsLEhPVVNUT04sVFgsNzcwNTIs
LCwzMCwwLDIwMTMvMDEvMDcNCjA1Nzg3NyxBTEkgU1BJQ0UgQ09NUEFOWSwxMjEyIE9BSyBST0FE
LCxTUFJJTkdGSUVMRCxNQSwwMTEwOCxBLCwzMCwwLDIwMTMvMDIvMTANCjA2MjIzNyxUQU5HRU5U
LDg1OCBDSVJDTEUgRFJJVkUsIzEyNSxEQUxMQVMsVFgsNzUyMjIsLCwzMCwwLDIwMTMvMDMvMzAN
CjA2MjMxNixTQUxTQSBET1JBREEsMTgwNyBHRU9SR0UgU1QsLFNBTiBBTlRPTklPLFRYLDc4MjEw
LCwsNDUsMCwyMDEzLzAyLzIwDQowNjMyNzUsRkxZSU5HIFBJWlpBLDE4MDAgU1VOTllTSURFLCxL
QU5TQVMgQ0lUWSxNTyw2NDEwOCwsLDMwLDAsMjAxMy8wNC8wOQ0KMDYzNDU5LFNVTk5ZIERBWSBG
T09EUywyMjAwIEZPUkVTVCBMQU5FLCxTQUNSQU1FTlRPLENBLDk1ODMwLCwsMzAsMCwyMDEzLzAz
LzE1DQowNjM0NjcsR1JFQVQgTEFLRVMgRU5FUkdZLFAgTyBCT1ggNTQzMTcsLENISUNBR08sSUws
NjA2MDYsLFUsMTUsMCwyMDEyLzEyLzAxDQowNjM4MDEsU09MVFJBTSBTV0VFVFMsUCBPIEJPWCA3
ODAsLEJBTFRJTU9SRSxNRCwyMTIwMywsLDMwLDAsMjAxMy8wMy8xMQ0KMDY2NDk5LEFMRVhBTkRF
UiAmIEFMRVhBTkRSQSwxMDAwIEFMRVhBTkRFUiBXQVksLEJMT09NSU5HVE9OLElOLDQ3NDA3LEEs
LDQ1LDAsMjAxMy8wMi8xNQ0KMDY5ODQyLCJKT0UnUyBUUlVDS0lORywgSU5DLiIsMjM3MCBXLiBO
T1JUSCBBVkUsQVRUTiBKT0UgTEVOVElOSSxDSElDQUdPLElMLDYwNjQ3LCxGLDYwLDAsMjAxMi8w
My8yMA0KMDcwMzMwLFRBTkdFTlQsUC5PLiBCT1ggODQ4MCwsRk9SVCBXT1JUSCxUWCw3NjExMyws
WCwzMCwwLDIwMTEvMDYvMTINCjA3MTIzOSxBVExBUyBFTEVDVFJJQ0lUWSBDTyxQIE8gQk9YIDIz
NDE1MiwsTE9TIEFOR0VMRVMsQ0EsOTAwNzIsLFUsMTUsMCwyMDEzLzAxLzE1DQowNzEzMjYsTUFJ
TiBTVFJFRVQgUFJPUEVSVFkgTUFOQUdFTUVOVCxQLk8uIEJPWCAxMjAwNCwsTkVXIFlPUkssTlks
MTAwMDgsLFAsMCwwLDIwMTMvMDIvMTgNCjA3MjMzMixXRVNUIFNVQlVSQkFOIERFVkVMT1BNRU5U
IENPLFAgTyBCT1ggMTE5NDIsLFBISUxBREVMUEhJQSxQQSwxOTEwOCwsUCwwLDAsMjAxMi8xMi8w
NA0KMDc0NTcyLEFQUExFVFJFRSBDT01NRVJDSUFMIFJFQUwgRVNUQVRFLDU3NzUgV0lMU09OIENU
LiwsTUFESVNPTixXSSw1MzcwNCwsUCwwLDAsMjAxMy8wMy8wMQ0KMDc1MTU3LFJPR0VOLDQwNTAg
UEFMTSBCTFZELCxQQVNBREVOQSxDQSw5MTEwNyxBLCwzMCwwLDIwMTMvMDEvMTMNCjA3NzM5OCxG
TEVNSVNIIENBTk5JTkcsMjcwMDEgUk9PU0VWRUxULCxQUk9WSURFTkNFLFJJLDAyOTA1LEEsLDMw
LDIwLDIwMTMvMDMvMDMNCjA3ODM0MSxBTkRBVEksUE8gQk9YIDUwNTAsLFBISUxBREVMUEhJQSxQ
QSwxOTEwOCwsLDMwLDE1LDIwMTMvMDEvMjgNCjA3OTIxMyxUJkcgUFJPRFVDVFMsMTEyIEZBSVJG
SUVMRCBBVkUuLCxNQUNPTixHQSwzMTIwNCwsWCwzMCwyMCwyMDEwLzAxLzMxDQowNzk0NjMsVEhB
SSBFTEVQSEFOVCxQIE8gQk9YIDk4MDczLCxTQU4gRlJBTkNJU0NPLENBLDk0MTIwLCwsNDUsMCwy
MDEzLzAxLzE4DQowNzk0NjgsRkxZSU5HIFBJWlpBLDQ1NyBXSEVFTElORyBXQVksIzEwOCxLQU5T
QVMgQ0lUWSxLUyw2NjEwNSwsWCwzMCwwLDIwMTAvMDEvMTUNCjA3OTY5NCxTVVBFUiBDSEVGIFRI
QUksUCBPIEJPWCA5OTAxLCxMT1MgQU5HRUxFUyxDQSw5MDA3MiwsLDQ1LDAsMjAxMy8wMy8yOQ0K
MDc5ODkyLElUQUxJQU4gWlNULFAgTyBCT1ggOTA4MzA1LCxORVcgWU9SSyxOWSwxMDE4NSwsLDMw
LDAsMjAxMy8wNC8wOA0KMDgwODQxLFQmRyBQUk9EVUNUUyxQIE8gQk9YIDY2ODcsLE1FTVBISVMs
VE4sMzgxMDEsLFgsMzAsMjAsMjAwOC8wNi8xMg0KMDgxOTM0LEJMVUUgU0tJRVMgQkFLRVJZLDE2
ODggQVBQTEVUUkVFIFdBWSwsTU9SUklTLElMLDYwNDUwLCwsMzAsMCwyMDEzLzAzLzA4DQowODIz
ODcsVFJJLVNUQVRFIEdBUyxQIE8gQk9YIDMyNzQ2LCxJTkRJQU5BUE9MSVMsSU4sNDYyMzAsLFUs
MTUsMCwyMDEzLzAxLzE1DQowODU1NDYsVEhBSSBNQVNURVIgRk9PRCwxOTA1IE1JVENIRUxMIFNU
LCxORVdBUkssTkosMDcxMDUsLCwzMCwwLDIwMTMvMDQvMDcNCjA4NjQ1NixUJkcgUFJPRFVDVFMs
UCBPIEJPWCAxMTAwMiwsTE9VSVNWSUxMRSxLWSw0MDI4NSxBLCwzMCwyMCwyMDEzLzAyLzE0DQow
ODY0NjMsR09VUk1FVC1aQSwxNjU4IDExIFNUIFNPVVRILCxQT1JUTEFORCxPUiw5NzIwOCxBLCwz
MCwxMCwyMDEzLzAzLzAyDQowODY1NDAsUk9ZLUctQklWLFAuTy4gQk9YIDEwMTAsLFJBTEVJR0gs
TkMsMjc2MDIsLCwzMCwwLDIwMTMvMDEvMjANCjA4NzQ4NSxST0xBTkQgQlJBTkRTLFAgTyBCT1gg
ODAwMTUsLERFUyBNT0lORVMsSUEsNTAzMDQsQSwsMzAsMjAsMjAxMy8wMi8wOA0KMDg4MjMxLFNI
T1BXT1JMRCBNQUxMLFAgTyBCT1ggNzgzMDEsLERBTExBUyxUWCw3NTIyMiwsUCwwLDAsMjAxMy8w
Mi8wMg0KMDg4NjMzLERVTENFUyBERSBNSUdVRUwsUCBPIEJPWCAxMDksLE5FVyBZT1JLLE5ZLDEw
MDA4LCwsMzAsMCwyMDEzLzAzLzExDQowODk3MzMsQUxMSUFOQ0UgVFJVQ0tJTkcgQ08sMTY3MiBX
LiBNT05ST0UsLENISUNBR08sSUwsNjA2MTIsLEYsMzAsMCwyMDEzLzAyLzI3DQowOTAxMTIsIk1B
U1RFUiBQUk9QRVJUSUVTLCBJTkMuIixQIE8gQk9YIDU3NywsREVTIE1PSU5FUyxJQSw1MDMwNCws
UCwwLDAsMjAxMy8wMy8wNw0KMDkwNjE0LFdJTExZIE5JTExZLDgwOCBLSU5HUyBXQVkgUk9BRCxD
L08gVE9NIE1BWFdFTEwsTUFESVNPTixXSSw1MzcwNCwsLDMwLDAsMjAxMy8wNC8wMQ0KMDk5MjM0
LFQmRyBQUk9EVUNUUyxQIE8gQk9YIDExMDAyLCxOQVNIVklMTEUsVE4sMzcyMjQsLFgsMzAsMjAs
MjAwOS8xMS8wNw0KMDk5OTQxLEZMRU1JU0ggQ0FOTklORyxQIE8gQk9YIDEyMDMyMSwsQk9TVE9O
LE1BLDAyMTEyLCxYLDMwLDIwLDIwMDkvMTAvMDENCg==

--_004_BN6PR12MB1233850235FFD6CEF9272D10B6060BN6PR12MB1233namp_--
EOD;

    return $data;
}
