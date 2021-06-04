<?php

use Feather\Session\Session;
use PHPUnit\Framework\TestCase;

/**
 * Description of SessionTest
 *
 * @author fcarbah
 */
class SessionTest extends TestCase
{

    public static function setUpBeforeClass(): void
    {
        session_start();
    }

    public static function tearDownAfterClass(): void
    {
        Session::flush(true);
    }

    /**
     * @test
     */
    public function canSaveToSession()
    {
        Session::set('name', 'John');
        Session::set('address', 'Broad Street');
        Session::set('age', 30);
        $this->assertArrayHasKey('name', $_SESSION);
    }

    /**
     * @test
     */
    public function canRetrieveDataFromSession()
    {
        $name = Session::get('name');
        $this->assertTrue($name === 'John');
    }

    /**
     * @test
     */
    public function willReturnNullForItemNotInSession()
    {
        $val = Session::get('uri');
        $this->assertNull($val);
    }

    /**
     * @test
     */
    public function willRemoveItemFromSessionAfterRetrival()
    {
        $name = Session::get('name', true);
        $val = Session::get('name');

        $this->assertTrue($name === 'John');
        $this->assertNull($val);
    }

    /**
     * @test
     */
    public function willRemoveKeyFromSession()
    {
        $age = Session::get('age');
        Session::remove('age');
        $ageAfter = Session::get('age');

        $this->assertEquals(30, $age);
        $this->assertNull($ageAfter);
    }

    /**
     * @test
     */
    public function willClearAllKeysInSession()
    {
        $addr = Session::get('address');
        Session::flush();
        $addrAfter = Session::get('address');

        $this->assertEquals('Broad Street', $addr);
        $this->assertNull($addrAfter);
    }

}
