<?php

declare(strict_types=1);

namespace Podium\Tests\Stubs;

trait ActiveRecordStubTrait
{
    /**
     * @var mixed
     */
    public static $findResult;
    public static bool $findCalled = false;
    /**
     * @var int|bool
     */
    public static $deleteResult = 1;
    public static bool $deletedCalled = false;
    public static bool $validationResult = true;
    public static bool $validateCalled = false;
    public static bool $saveResult = true;
    public static bool $saveCalled = false;

    public static function resetStub(): void
    {
        static::$findResult = null;
        static::$deleteResult = 0;
        static::$validationResult = true;
        static::$saveResult = true;

        static::$findCalled = false;
        static::$deletedCalled = false;
        static::$validateCalled = false;
        static::$saveCalled = false;
    }

    public static function find(): self
    {
        return new self();
    }

    public function where(): self
    {
        return $this;
    }

    /**
     * @return mixed
     */
    public function one()
    {
        static::$findCalled = true;
        return static::$findResult;
    }

    /**
     * @return int|bool
     */
    public function delete()
    {
        static::$deletedCalled = true;
        return static::$deleteResult;
    }

    public function validate($attributeNames = null, $clearErrors = true): bool
    {
        static::$validateCalled = true;
        $result = static::$validationResult;

        if ($result === false) {
            $this->addError('attribute', 'error');
        }

        return $result;
    }

    public function save($runValidation = true, $attributeNames = null): bool
    {
        static::$saveCalled = true;
        if ($runValidation && !$this->validate()) {
            return false;
        }

        return static::$saveResult;
    }
}
