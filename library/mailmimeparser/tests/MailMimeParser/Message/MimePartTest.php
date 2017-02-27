<?php
namespace ZBateson\MailMimeParser\Message;

use PHPUnit_Framework_TestCase;

/**
 * Description of MimePartTest
 *
 * @group MimePart
 * @group Message
 * @covers ZBateson\MailMimeParser\Message\MimePart
 * @author Zaahid Bateson
 */
class MimePartTest extends PHPUnit_Framework_TestCase
{
    private $mockHeaderFactory;
    private $mockMessageWriter;
    
    protected function setUp()
    {
        $this->mockHeaderFactory = $this->getMockBuilder('ZBateson\MailMimeParser\Header\HeaderFactory')
            ->disableOriginalConstructor()
            ->setMethods(['newInstance'])
            ->getMock();
        $this->mockMessageWriter = $this->getMockBuilder('ZBateson\MailMimeParser\Message\Writer\MimePartWriter')
            ->disableOriginalConstructor()
            ->getMock();
    }
    
    protected function getMockedParameterHeader($name, $value, $parameterValue = null)
    {
        $header = $this->getMockBuilder('ZBateson\MailMimeParser\Header\ParameterHeader')
            ->disableOriginalConstructor()
            ->setMethods(['getValue', 'getName', 'getValueFor', 'hasParameter'])
            ->getMock();
        $header->method('getName')->willReturn($name);
        $header->method('getValue')->willReturn($value);
        $header->method('getValueFor')->willReturn($parameterValue);
        $header->method('hasParameter')->willReturn(true);
        return $header;
    }
    
    protected function createNewMimePart()
    {
        return new MimePart($this->mockHeaderFactory, $this->mockMessageWriter);
    }
    
    public function testAttachContentResourceHandle()
    {
        $part = $this->createNewMimePart();
        $res = fopen('php://memory', 'rw');
        $part->attachContentResourceHandle($res);
        $this->assertSame($res, $part->getContentResourceHandle());
    }
    
    public function testHasContent()
    {
        $part = $this->createNewMimePart();

        $this->assertFalse($part->hasContent());
        $res = fopen('php://memory', 'rw');
        $part->attachContentResourceHandle($res);
        $this->assertTrue($part->hasContent());
    }

    public function testSetRawHeader()
    {
        $hf = $this->mockHeaderFactory;
        $firstHeader = $this->getMockedParameterHeader('First-Header', 'Value');
        $secondHeader = $this->getMockedParameterHeader('Second-Header', 'Second Value');
        
        $hf->expects($this->exactly(2))
            ->method('newInstance')
            ->withConsecutive(
                [$firstHeader->getName(), $firstHeader->getValue()],
                [$secondHeader->getName(), $secondHeader->getValue()]
            )
            ->willReturnOnConsecutiveCalls($firstHeader, $secondHeader);
        
        $part = $this->createNewMimePart();
        $part->setRawHeader($firstHeader->getName(), $firstHeader->getValue());
        $part->setRawHeader($secondHeader->getName(), $secondHeader->getValue());
        $this->assertSame($firstHeader, $part->getHeader($firstHeader->getName()));
        $this->assertSame($secondHeader, $part->getHeader($secondHeader->getName()));
        $this->assertEquals($firstHeader->getValue(), $part->getHeaderValue($firstHeader->getName()));
        $this->assertEquals($secondHeader->getValue(), $part->getHeaderValue($secondHeader->getName()));
        $this->assertCount(2, $part->getHeaders());
        $this->assertEquals(['first-header' => $firstHeader, 'second-header' => $secondHeader], $part->getHeaders());
    }
    
    public function testHeaderCaseInsensitive()
    {
        $hf = $this->mockHeaderFactory;
        $firstHeader = $this->getMockedParameterHeader('First-Header', 'Value');
        $secondHeader = $this->getMockedParameterHeader('Second-Header', 'Second Value');
        $thirdHeader = $this->getMockedParameterHeader('FIRST-header', 'Third Value');
        
        $hf->expects($this->exactly(3))
            ->method('newInstance')
            ->withConsecutive(
                [$firstHeader->getName(), $firstHeader->getValue()],
                [$secondHeader->getName(), $secondHeader->getValue()],
                [$thirdHeader->getName(), $thirdHeader->getValue()]
            )
            ->willReturnOnConsecutiveCalls($firstHeader, $secondHeader, $thirdHeader);
        
        $part = $this->createNewMimePart();
        $part->setRawHeader($firstHeader->getName(), $firstHeader->getValue());
        $part->setRawHeader($secondHeader->getName(), $secondHeader->getValue());
        $part->setRawHeader($thirdHeader->getName(), $thirdHeader->getValue());
        
        $this->assertSame($thirdHeader, $part->getHeader('first-header'));
        $this->assertSame($secondHeader, $part->getHeader('second-header'));
    }
    
    public function testParent()
    {
        $part = $this->createNewMimePart();
        $parent = $this->createNewMimePart();
        $part->setParent($parent);
        $this->assertSame($parent, $part->getParent());
    }
    
    public function testGetHeaderParameter()
    {
        $hf = $this->mockHeaderFactory;
        $header = $this->getMockedParameterHeader('First-Header', 'Value', 'param-value');
        $hf->expects($this->exactly(1))
            ->method('newInstance')
            ->withConsecutive(
                [$header->getName(), $header->getValue()]
            )
            ->willReturnOnConsecutiveCalls($header);
        $part = $this->createNewMimePart();
        $part->setRawHeader($header->getName(), $header->getValue());
        
        $this->assertEquals('param-value', $part->getHeaderParameter('first-header', 'param'));
    }
    
    public function testGetUnsetHeader()
    {
        $part = $this->createNewMimePart();
        $this->assertNull($part->getHeader('Nothing'));
        $this->assertNull($part->getHeaderValue('Nothing'));
        $this->assertEquals('Default', $part->getHeaderValue('Nothing', 'Default'));
    }
    
    public function testGetUnsetHeaderParameter()
    {
        $part = $this->createNewMimePart();
        $this->assertNull($part->getHeaderParameter('Nothing', 'Non-Existent'));
        $this->assertEquals('Default', $part->getHeaderParameter('Nothing', 'Non-Existent', 'Default'));
    }
}
