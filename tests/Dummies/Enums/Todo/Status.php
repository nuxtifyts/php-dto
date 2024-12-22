<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\Enums\Todo;

enum Status: string
{
    case BACKLOG = 'backlog';
    case READY = 'ready';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
}
