<?php

namespace Barzahlen\Tests;

use Barzahlen\Translate;

class TranslateTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
    }

    public function testSimpleTranslation()
    {
        Translate::$bShowTranslationWarnings = true;
        $this->assertContains('OK', Translate::__T('Test'));
    }

    public function testTranslationWithVariable()
    {
        Translate::$bShowTranslationWarnings = true;
        $this->assertContains('20', Translate::__T('Test %s of %s', array(20,100)),"",true);
        $this->assertContains('100', Translate::__T('Test %s of %s', array(20,100)),'',true);
        $this->assertContains('Ok', Translate::__T('Test %s of %s', array(20,100)),'',true);
    }
}
