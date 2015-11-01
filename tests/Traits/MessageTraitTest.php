<?php
namespace yriveiro\Psr7\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Psr\Http\Message\MessageInterface;
use yriveiro\Psr7\Request;
use yriveiro\Psr7\Headers;

class MessageTraitTest extends TestCase
{
    /**
     * @var MessageInterface
     */
    protected $message;

    public function setUp()
    {
        $this->message = new Request(new Headers());
    }

    public function testProtocolWithDefault()
    {
        $this->assertEquals('1.1', $this->message->getProtocolVersion());
    }

    public function testProtocolMutatorWithProtocolVersion()
    {
        $message = $this->message->withProtocolVersion('1.0');

        $this->assertNotSame($this->message, $message);
        $this->assertEquals('1.0', $message->getProtocolVersion());
    }

    public function testGetHeaderAsArray()
    {
        $message = $this->message->withHeader('X-Foo', ['Foo', 'Bar']);

        $this->assertNotSame($this->message, $message);
        $this->assertEquals(['Foo', 'Bar'], $message->getHeader('X-Foo'));
    }

    public function testGetHeaderLinedAsCommaConcatenatedString()
    {
        $message = $this->message->withHeader('X-Foo', ['Foo', 'Bar']);

        $this->assertNotSame($this->message, $message);
        $this->assertEquals('Foo, Bar', $message->getHeaderLine('X-Foo'));
    }

    public function testGetHeadersReturnCaseSensitivityHeader()
    {
        $message = $this->message->withHeader('X-Foo', ['Foo', 'Bar']);

        $this->assertNotSame($this->message, $message);
        $this->assertEquals([ 'X-Foo' => [ 'Foo', 'Bar' ] ], $message->getHeaders());
    }

    public function testGetHeadersReturnsCaseWithWhichHeaderFirstRegistered()
    {
        $message = $this->message
            ->withHeader('X-Foo', 'Foo')
            ->withAddedHeader('x-foo', 'Bar');

        $this->assertNotSame($this->message, $message);
        $this->assertEquals([ 'X-Foo' => [ 'Foo', 'Bar' ] ], $message->getHeaders());
    }

    public function testHasHeaderReturnsFalseIfHeaderIsNotPresent()
    {
        $this->assertFalse($this->message->hasHeader('X-Foo'));
    }

    public function testHasHeaderReturnsTrueIfHeaderIsPresent()
    {
        $message = $this->message->withHeader('X-Foo', 'Foo');

        $this->assertNotSame($this->message, $message);
        $this->assertTrue($message->hasHeader('X-Foo'));
    }

    public function testAddHeaderAppendsToExistingHeader()
    {
        $message  = $this->message->withHeader('X-Foo', 'Foo');

        $this->assertNotSame($this->message, $message);

        $message2 = $message->withAddedHeader('X-Foo', 'Bar');

        $this->assertNotSame($message, $message2);
        $this->assertEquals('Foo, Bar', $message2->getHeaderLine('X-Foo'));
    }

    public function testCanRemoveHeaders()
    {
        $message = $this->message->withHeader('X-Foo', 'Foo');

        $this->assertNotSame($this->message, $message);
        $this->assertTrue($message->hasHeader('x-foo'));

        $message2 = $message->withoutHeader('x-foo');

        $this->assertNotSame($this->message, $message2);
        $this->assertNotSame($message, $message2);

        $this->assertFalse($message2->hasHeader('X-Foo'));
    }

    public function testHeaderRemovalIsCaseInsensitive()
    {
        $message = $this->message
            ->withHeader('X-Foo', 'Foo')
            ->withAddedHeader('x-foo', 'Bar')
            ->withAddedHeader('X-FOO', 'Baz');
        $this->assertNotSame($this->message, $message);
        $this->assertTrue($message->hasHeader('x-foo'));

        $message2 = $message->withoutHeader('x-foo');
        $this->assertNotSame($this->message, $message2);
        $this->assertNotSame($message, $message2);
        $this->assertFalse($message2->hasHeader('X-Foo'));

        $headers = $message2->getHeaders();
        $this->assertEquals(0, count($headers));
    }

    public function invalidGeneralHeaderValues()
    {
        return [
            'null'   => [null],
            'true'   => [true],
            'false'  => [false],
            'int'    => [1],
            'float'  => [1.1],
            'array'  => [[ 'foo' => [ 'bar' ] ]],
            'object' => [(object) [ 'foo' => 'bar' ]],
        ];
    }

    /**
     * @dataProvider invalidGeneralHeaderValues
     */
    public function testWithHeaderRaisesExceptionForInvalidNestedHeaderValue($value)
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid header value');
        $message = $this->message->withHeader('X-Foo', [ $value ]);
    }

    public function invalidHeaderValues()
    {
        return [
            'null'   => [null],
            'true'   => [true],
            'false'  => [false],
            'int'    => [1],
            'float'  => [1.1],
            'object' => [(object) [ 'foo' => 'bar' ]],
        ];
    }

    /**
     * @dataProvider invalidHeaderValues
     */
    public function testWithHeaderRaisesExceptionForInvalidValueType($value)
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid header value');
        $message = $this->message->withHeader('X-Foo', $value);
    }

    /**
     * @dataProvider invalidGeneralHeaderValues
     */
    public function testWithAddedHeaderRaisesExceptionForNonStringNonArrayValue($value)
    {
        $this->setExpectedException('InvalidArgumentException', 'must be a string');
        $message = $this->message->withAddedHeader('X-Foo', $value);
    }

    public function testWithoutHeaderDoesNothingIfHeaderDoesNotExist()
    {
        $this->assertFalse($this->message->hasHeader('X-Foo'));

        $message = $this->message->withoutHeader('X-Foo');

        $this->assertNotSame($this->message, $message);
        $this->assertFalse($message->hasHeader('X-Foo'));
    }

    public function testHeadersInitialization()
    {
        $headers = ['X-Foo' => ['bar']];

        $message = new Request(new Headers($headers));

        $this->assertSame($headers, $message->getHeaders());
    }

    public function testGetHeaderReturnsAnEmptyArrayWhenHeaderDoesNotExist()
    {
        $this->assertSame([], $this->message->getHeader('X-Foo-Bar'));
    }

    public function testGetHeaderLineReturnsEmptyStringWhenHeaderDoesNotExist()
    {
        $this->assertEmpty($this->message->getHeaderLine('X-Foo-Bar'));
    }

    public function testWithHeaderAllowsHeaderContinuations()
    {
        $message = $this->message->withHeader('X-Foo-Bar', "value,\r\n second value");
        $this->assertEquals("value,\r\n second value", $message->getHeaderLine('X-Foo-Bar'));
    }

    public function testWithAddedHeaderAllowsHeaderContinuations()
    {
        $message = $this->message->withAddedHeader('X-Foo-Bar', "value,\r\n second value");
        $this->assertEquals("value,\r\n second value", $message->getHeaderLine('X-Foo-Bar'));
    }
}
