<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Documentation;

use Nuxtifyts\PhpDto\Tests\Dummies\Enums\Todo\Status;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Nuxtifyts\PhpDto\Tests\Dummies\DocsDummies\TodoData;
use Nuxtifyts\PhpDto\Tests\Dummies\DocsDummies\NonData\Goal;
use DateTimeImmutable;
use DateTimeInterface;
use Throwable;

#[UsesClass(TodoData::class)]
#[UsesClass(Goal::class)]
final class NormalizersExampleTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function will_be_able_to_normalize_instance_of_goal_class_to_todo_class(): void
    {
        $now = new DateTimeImmutable();

        $goal = new Goal(
            summary: $summary = 'Learn a new programming language',
            description: $description = 'Learn a new programming language suited to machine machine learning',
            dueDate: $now
        );

        $todo = TodoData::from($goal);

        self::assertEquals(
            [
                'title' => $summary,
                'content' => $description,
                'status' => Status::READY->value,
                'dueDate' => $now->format(DateTimeInterface::ATOM)
            ],
            $todo->toArray()
        );
    }
}
