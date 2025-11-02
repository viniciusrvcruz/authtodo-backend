<?php

use App\Models\Task;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('PUT /tasks/{task} (update)', function () {
    it('should update a task of the authenticated user', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $payload = ['name' => 'Updated Task'];

        $response = $this->actingAs($this->user)->putJson("/api/tasks/{$task->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('name', 'Updated Task');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Updated Task',
        ]);
    });

    it('should allow updating without sending name (because of sometimes rule)', function () {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Original Task',
            'is_completed' => false,
        ]);

        $payload = ['is_completed' => true]; // no "name" field

        $response = $this->actingAs($this->user)->putJson("/api/tasks/{$task->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('is_completed', true)
            ->assertJsonPath('name', 'Original Task'); // name should remain unchanged

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Original Task',
            'is_completed' => true,
        ]);
    });

    it('should fail if name is sent but invalid', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $payload = ['name' => '']; // invalid

        $response = $this->actingAs($this->user)->putJson("/api/tasks/{$task->id}", $payload);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });

    it('should not allow updating a task from another user', function () {
        $task = Task::factory()->create();

        $this->actingAs($this->user)->putJson("/api/tasks/{$task->id}", [
            'name' => 'Hacking attempt',
        ])->assertNotFound();
    });

    it('should require authentication', function () {
        $task = Task::factory()->create();

        $this->putJson("/api/tasks/{$task->id}", [
            'name' => 'Test',
        ])->assertUnauthorized();
    });
});