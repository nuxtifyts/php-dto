<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\Normalizers;

use Nuxtifyts\PhpDto\Normalizers\Normalizer;
use Nuxtifyts\PhpDto\Tests\Dummies\DocsDummies\NonData\Goal;
use DateTimeInterface;

final readonly class GoalTodoNormalizer extends Normalizer
{
    public function normalize(): array|false
    {
        if (!$this->value instanceof Goal) {
            return false;
        }

        return [
            'title' => $this->value->summary,
            'content' => $this->value->description,
            'status' => 'ready',
            'dueDate' => $this->value->dueDate->format(DateTimeInterface::ATOM)
        ];
    }
}
