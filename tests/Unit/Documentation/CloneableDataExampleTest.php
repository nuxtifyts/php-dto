<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Documentation;

use Nuxtifyts\PhpDto\Tests\Dummies\DocsDummies\TodoData;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\Test;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\Todo\Status;
use DateTimeImmutable;
use Throwable;

#[UsesClass(TodoData::class)]
#[UsesClass(PersonData::class)]
final class CloneableDataExampleTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function test_it_can_clone_data_as_example_in_documentation(): void
    {
        $emptyTodo = TodoData::empty();

        self::assertEquals([
            'title' => '',
            'content' => '',
            'status' => Status::BACKLOG->value,
            'dueDate' => null
        ], $emptyTodo->toArray());

        $todo = $emptyTodo->with(
            title: 'Learn PHP DTO',
            content: 'Learn how to use PHP DTO',
            status: Status::IN_PROGRESS
        );

        self::assertEquals([
            'title' => 'Learn PHP DTO',
            'content' => 'Learn how to use PHP DTO',
            'status' => Status::IN_PROGRESS->value,
            'dueDate' => null
        ], $todo->toArray());

        $dueDate = new DateTimeImmutable('2021-10-10');
        $todoWithDueDate = $todo->with(dueDate: $dueDate);

        self::assertEquals(
            $dueDate->format('Y-m-d H:i'),
            $todoWithDueDate->dueDate?->format('Y-m-d H:i')
        );
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function test_is_can_clone_dts_with_computed_properties(): void
    {
        $person = new PersonData(firstName: 'John', lastName: 'Doe');

        self::assertEquals('John Doe', $person->fullName);

        $personWithFirstName = $person->with(firstName: 'Jane');

        self::assertEquals('Jane Doe', $personWithFirstName->fullName);
    }
}
