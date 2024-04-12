<?php

namespace Barzahlen\Tests;

use Barzahlen\Translate;

class TranslateTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
    }

    public function testSimpleTranslation()
    {
        Translate::$bShowTranslationWarnings = true;
        $this->assertStringContainsString('OK', Translate::__T('Test'));
    }

    public function testTranslationWithVariable()
    {
        Translate::$bShowTranslationWarnings = true;
        $this->assertStringContainsString('20', Translate::__T('Test %s of %s', array(20,100)),"",true);
        $this->assertStringContainsString('100', Translate::__T('Test %s of %s', array(20,100)),'',true);
        $this->assertStringContainsStringIgnoringCase('Ok', Translate::__T('Test %s of %s', array(20,100)),'',true);
    }
}
