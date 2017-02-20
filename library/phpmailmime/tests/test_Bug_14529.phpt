--TEST--
Bug #14529  basename() workaround
--SKIPIF--
--FILE--
<?php
include "Mail/mime.php";
$m = new Mail_mime();
// some text with polish Unicode letter at the beginning
$filename = base64_decode("xZtjaWVtYQ==");
$m->addAttachment('testfile', "text/plain", $filename, FALSE, 'base64', 'attachment', 'ISO-8859-1');
$root = $m->_addMixedPart();
$enc = $m->_addAttachmentPart($root, $m->_parts[0]);
print_r($enc->_headers['Content-Disposition']);
?>
--EXPECT--
attachment;
 filename*=ISO-8859-1''%C5%9Bciema;
 size=8
