<?php

declare(strict_types=1);

namespace Podium\Tests\Stubs;

use Exception;

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
    public static bool $loadResult = true;
    public static bool $loadCalled = false;
    public static bool $validationResult = true;
    public static bool $validateCalled = false;
    public static bool $saveResult = true;
    public static bool $saveCalled = false;
    public static array $eachResult = [];
    public static $hasOneResult;
    public static bool $linkResult = true;
    public static bool $unlinkResult = true;

    public static function resetStub(): void
    {
        static::$findResult = null;
        static::$deleteResult = 0;
        static::$validationResult = true;
        static::$loadResult = true;
        static::$saveResult = true;
        static::$eachResult = [];
        static::$hasOneResult = null;
        static::$linkResult = true;
        static::$unlinkResult = true;

        static::$findCalled = false;
        static::$deletedCalled = false;
        static::$loadCalled = false;
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

    public function orderBy(): self
    {
        return $this;
    }

    public function limit(): self
    {
        return $this;
    }

    public function each(): array
    {
        return static::$eachResult;
    }

    public function hasOne($class, $link)
    {
        return static::$hasOneResult;
    }

    public function andWhere(): self
    {
        return $this;
    }

    public function link($name, $model, $extraColumns = []): void
    {
        if (static::$linkResult === false) {
            throw new Exception('Link failed');
        }
    }

    public function unlink($name, $model, $delete = false): void
    {
        if (static::$unlinkResult === false) {
            throw new Exception('Unlink failed');
        }
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

    public function load($data, $formName = null): bool
    {
        static::$loadCalled = true;

        foreach ($data as $name => $value) {
            $this->$name = $value;
        }

        return static::$loadResult;
    }
}
