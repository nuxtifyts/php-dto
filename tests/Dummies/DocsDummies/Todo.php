<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\DocsDummies;

use Nuxtifyts\PhpDto\Data;
use DateTimeImmutable;
use Nuxtifyts\PhpDto\Normalizers\Normalizer;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\Todo\Status;
use Nuxtifyts\PhpDto\Tests\Dummies\Normalizers\GoalTodoNormalizer;

final readonly class Todo extends Data
{
    public function __construct(
        public string $title,
        public string $content,
        public Status $status,
        public ?DateTimeImmutable $dueDate
    ) {}

    /**
     * @return list<class-string<Normalizer>>
     */
    protected static function normalizers(): array
    {
        return [
            GoalTodoNormalizer::class,
        ];
    }
}
