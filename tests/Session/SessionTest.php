<?php

namespace Pickles\Tests\Session;

use Constants;
use PHPUnit\Framework\TestCase;
use Pickles\Session\Session;
use Pickles\Session\SessionStorage;

class SessionTest extends TestCase
{
    private function createMockSessionStorage()
    {
        $mock = $this->getMockBuilder(SessionStorage::class)->getMock();
        $mock->method("id")->willReturn("id");
        $mock->storage = [];
        $mock->method("has")->willReturnCallback(fn ($key) => isset($mock->storage[$key]));
        $mock->method("get")->willReturnCallback(fn ($key) => $mock->storage[$key] ?? null);
        $mock->method("set")->willReturnCallback(fn ($key, $value) => $mock->storage[$key] = $value);
        $mock->method("remove")->willReturnCallback(function ($key) use ($mock) {
            unset($mock->storage[$key]);
        });

        return $mock;
    }

    public function testAgeFlashData()
    {
        $mock = $this->createMockSessionStorage();

        $s1 = new Session($mock);

        $s1->set("test", "hello");

        $this->assertTrue(isset($mock->storage["test"]));

        // Check flash data
        $this->assertEquals([Constants::FLASH_OLD_KEY => [], Constants::FLASH_NEW_KEY => []], $mock->storage[Constants::FLASH_KEY]);
        $s1->flash("alert", "some alert");
        $this->assertEquals([Constants::FLASH_OLD_KEY => [], Constants::FLASH_NEW_KEY => ["alert"]], $mock->storage[Constants::FLASH_KEY]);

        // Check flash data is still set and keys are aged
        $s1->__destruct();
        $this->assertTrue(isset($mock->storage["alert"]));
        $this->assertEquals([Constants::FLASH_OLD_KEY => ["alert"], Constants::FLASH_NEW_KEY => []], $mock->storage[Constants::FLASH_KEY]);

        // Create new session and check previous session flash data
        $s2 = new Session($mock);
        $this->assertEquals([Constants::FLASH_OLD_KEY => ["alert"], Constants::FLASH_NEW_KEY => []], $mock->storage[Constants::FLASH_KEY]);
        $this->assertTrue(isset($mock->storage["alert"]));

        // Destroy session and check that flash keys are removed
        $s2->__destruct();
        $this->assertEquals([Constants::FLASH_OLD_KEY => [], Constants::FLASH_NEW_KEY => []], $mock->storage[Constants::FLASH_KEY]);
        $this->assertFalse(isset($mock->storage["alert"]));
    }
}
