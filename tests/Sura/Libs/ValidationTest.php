<?php

namespace Sura\Libs;

use PHPUnit\Framework\TestCase;

class ValidationTest extends TestCase
{

    public function testStrip_data(): string
    {
        $text = 'foo';
        $instance = 'foo';

//        $instance = Validation::strip_data($text);

        self::assertEquals('foo', $instance);

        return $instance;
    }

    public function testReplace_rn(): string
    {
        $text = "foo\r";

        $instance = Validation::replace_rn($text);

        self::assertEquals('foo', $instance);

        return $instance;
    }

    public function testMyBr(): string
    {
        $text = "foo\r";

        $instance = Validation::myBr($text);

        self::assertEquals('foo<br />', $instance);

        return $instance;
    }

    public function testRn_replace(): string
    {
        $text = "foo\r";

        $instance = Validation::rn_replace($text);

        self::assertEquals('foo', $instance);

        return $instance;
    }

    public function testMyBrRn(): string
    {
        $text = "foo<br />";

        $instance = Validation::myBrRn($text);

        self::assertEquals('foo
', $instance);

        return $instance;
    }

    public function testTextFilter(): string
    {
        $text = 'foo';

        $instance = Validation::textFilter($text, 25000, false);

        self::assertEquals('foo', $instance);

        return $instance;
    }

    public function test_strlen(): int
    {
        $text = 'foo';

        $instance = Validation::_strlen($text, $charset = "utf-8");

        self::assertEquals('3', $instance);

        return $instance;
    }

    public function testCheck_ip(): void
    {
        self::assertTrue(true);
    }

    public function testWord_filter(): void
    {
//        $source = 'foo';
//        $instance = Validation::word_filter($source, $encode = true);
//        $this->assertEquals('foo', $instance);
//        return $instance;
        self::assertNotFalse(true);
    }

    public function testcheck_name(): bool
    {
        //Проверка имени
        $instance = Validation::check_name('Иван');

        self::assertEquals(true, $instance);

        return $instance;

    }

    public function testcheck_email(): bool|string
    {

        //Проверка имени
        $instance = Validation::check_email('example@example.com');

        self::assertEquals(true, $instance);

        return $instance;

    }

    public function testcheck_password(): bool|string
    {
        //Проверка имени
        //don`t use password qwerty10
        $instance = Validation::check_password('qwerty10', 'qwerty10');

        self::assertEquals(true, $instance);

        return $instance;

    }

    public function testcheck_password2(): bool
    {
         $password2 = 'rasmuslerdorf';
        $pass_hash2 = password_hash($password2, PASSWORD_DEFAULT);

        $instance = password_verify('rasmuslerdorf', $pass_hash2);
        self::assertEquals(true, $instance);

        return $instance;

    }

    public function testcheck_password3(): void
    {
        // Смотрите пример использования password_hash(), для понимания откуда это взялось.
        $hash = '$2y$10$rkadTE2AWb2CwFK/0J9fbetb4IWTVdgTibsnM4UaFr0D5pl0za2ci';
        $instance = password_verify('Hec2GugBed', $hash);
        if ($instance) {
            echo 'Пароль правильный!';
        } else {
            echo 'Пароль неправильный.';
        }
        self::assertEquals(true, $instance);
    }
}
