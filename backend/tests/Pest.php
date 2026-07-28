<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class)
    ->beforeEach(function () {
        $this->seed();
    })
    ->in('Feature', 'Unit');

uses(RefreshDatabase::class)->in('Feature');
