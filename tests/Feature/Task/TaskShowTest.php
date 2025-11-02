<?php

use App\Models\Task;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('GET /tasks/{task} (show)', function () {
    it('should show a task of the authenticated user', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson("/api/tasks/{$task->id}");

        $response->assertOk()
            ->assertJsonPath('name', $task->name);
    });

    it('should not allow viewing tasks from another user', function () {
        $task = Task::factory()->create();

        $this->actingAs($this->user)->getJson("/api/tasks/{$task->id}")
            ->assertNotFound();
    });

    it('should require authentication', function () {
        $task = Task::factory()->create();

        $this->getJson("/api/tasks/{$task->id}")->assertUnauthorized();
    });
});