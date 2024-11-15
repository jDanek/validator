<?php

namespace Danek\Validator\Tests\Rule;

use Danek\Validator\Rule\UrlRule;
use Danek\Validator\Validator;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    /** @var Validator */
    protected $validator;

    public static function getValidUrls(): array
    {
        return [
            ['http://github.com'],
            ['git://berry:berry@github.com/berry-langerak/Validator?view=source&value=yes'],
        ];
    }

    public static function getInvalidUrls(): array
    {
        return [
            ['malformed:/github.com', UrlRule::INVALID_URL],
            ['http:///github.com', UrlRule::INVALID_URL],
        ];
    }

    /**
     * @dataProvider getValidUrls
     * @param mixed $value
     */
    public function testReturnsTrueOnValidUrls($value)
    {
        $this->validator->required('url')->url();
        $result = $this->validator->validate(['url' => $value]);
        $this->assertTrue($result->isValid());
        $this->assertEquals([], $result->getMessages());
    }

    /**
     * @dataProvider getInvalidUrls
     * @param mixed $value
     * @param string $error
     */
    public function testReturnsFalseOnInvalidUrls($value, string $error)
    {
        $this->validator->required('url')->url();
        $result = $this->validator->validate(['url' => $value]);
        $this->assertFalse($result->isValid());
        $expected = [
            'url' => [
                $error => 'url must be a valid URL',
            ],
        ];
        $this->assertEquals($expected, $result->getMessages());
    }

    public function testFailsOnNotWhiteListedScheme()
    {
        $this->validator->required('url')->url(['http', 'https']);

        $result = $this->validator->validate([
            'url' => 'git://github.com',
        ]);

        $this->assertFalse($result->isValid());

        $expected = [
            'url' => [
                UrlRule::INVALID_SCHEME => 'url must have one of the following schemes: http, https',
            ],
        ];

        $this->assertEquals($expected, $result->getMessages());
    }

    public function testSucceedsOnWhiteListedScheme()
    {
        $this->validator->required('url')->url(['http', 'https']);

        $result = $this->validator->validate([
            'url' => 'http://github.com',
        ]);

        $this->assertTrue($result->isValid());
    }

    public function testSucceedsOnAlternativeWhiteListedScheme()
    {
        $this->validator->required('url')->url(['mailto']);

        $result = $this->validator->validate([
            'url' => 'mailto:robbie@example.org',
        ]);

        $this->assertTrue($result->isValid());
    }

    protected function setUp(): void
    {
        $this->validator = new Validator();
    }
}
