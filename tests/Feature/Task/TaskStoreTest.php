<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('POST /tasks (store)', function () {
    it('should create a valid task', function () {
        $payload = [
            'name' => 'New Task',
            'description' => 'Task description',
            'is_completed' => false,
        ];

        $response = $this->actingAs($this->user)->postJson('/api/tasks', $payload);

        $response->assertCreated()
            ->assertJsonPath('name', 'New Task')
            ->assertJsonPath('is_completed', false);

        $this->assertDatabaseHas('tasks', [
            'name' => 'New Task',
            'user_id' => $this->user->id,
        ]);
    });

    it('should fail when creating with invalid data', function () {
        $payload = [
            'name' => '', // required
            'description' => str_repeat('a', 600), // exceeds max:500
            'is_completed' => 'not-boolean',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/tasks', $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'description', 'is_completed']);
    });

    it('should require authentication', function () {
        $this->postJson('/api/tasks', [
            'name' => 'Test',
        ])->assertUnauthorized();
    });
});