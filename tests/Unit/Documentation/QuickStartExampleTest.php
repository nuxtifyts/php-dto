<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Documentation;

use Nuxtifyts\PhpDto\Tests\Dummies\DocsDummies\TodoData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\Todo\Status;
use DateTimeImmutable;
use DateTimeInterface;
use Throwable;

#[UsesClass(TodoData::class)]
final class QuickStartExampleTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function will_perform_serialize_and_deserialize_on_data_transfer_objects_from_docs(): void
    {
        $now = new DateTimeImmutable();

        $arrayData = [
            'title' => 'Learn PHP DTO',
            'content' => 'Learn how to use PHP DTO',
            'status' => 'ready',
            'dueDate' => $now->format(DateTimeInterface::ATOM)
        ];

        $todo = new TodoData(
            title: 'Learn PHP DTO',
            content: 'Learn how to use PHP DTO',
            status: Status::READY,
            dueDate: $now
        );

        $todoFrom = TodoData::from([
            'title' => 'Learn PHP DTO',
            'content' => 'Learn how to use PHP DTO',
            'status' => 'ready',
            'dueDate' => $now->format(DateTimeInterface::ATOM)
        ]);

        self::assertEquals($arrayData, $todoFrom->toArray());
        self::assertEquals($arrayData, $todo->toArray());
        self::assertEquals(json_encode($arrayData), json_encode($todo));
        self::assertEquals(json_encode($todoFrom), $todoFrom->toJson());
    }
}
