<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Support\Authentication;

abstract class TestCase extends BaseTestCase
{
    protected function setUpTraits(): void
    {
        $uses = parent::setUpTraits();

        if (isset($uses[Authentication::class])) {
            call_user_func([$this, 'setupAuthentication']);
        }
    }
}