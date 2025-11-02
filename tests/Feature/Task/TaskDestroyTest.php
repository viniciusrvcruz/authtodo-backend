<?php

use App\Models\Task;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('DELETE /tasks/{task} (destroy)', function () {
    it('should delete a task of the authenticated user', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/tasks/{$task->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    });

    it('should not allow deleting a task from another user', function () {
        $task = Task::factory()->create();

        $this->actingAs($this->user)->deleteJson("/api/tasks/{$task->id}")
            ->assertNotFound();
    });

    it('should require authentication', function () {
        $task = Task::factory()->create();

        $this->deleteJson("/api/tasks/{$task->id}")->assertUnauthorized();
    });
});