<?php

use App\Models\Task;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('GET /tasks (index)', function () {
    it('should list only the tasks of the authenticated user', function () {
        Task::factory()->count(2)->create(['user_id' => $this->user->id]);
        Task::factory()->count(3)->create(); // tasks from another user

        $response = $this->actingAs($this->user)->getJson('/api/tasks');

        $response->assertOk()
            ->assertJsonCount(2);
    });

    it('should require authentication', function () {
        $this->getJson('/api/tasks')->assertUnauthorized();
    });
});