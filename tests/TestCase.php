<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    public function actAsUser(?User $user = null): void
    {
        $tester = $user ?? User::factory()->create();
        
        Sanctum::actingAs($tester);
    }
}
