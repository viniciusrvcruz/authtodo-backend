<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

/**
 * INDEX
 */
describe('GET /tasks (index)', function () {
    it('should list only the tasks of the authenticated user', function () {
        Task::factory()->count(2)->create(['user_id' => $this->user->id]);
        Task::factory()->count(3)->create(); // tasks from another user

        $response = $this->actingAs($this->user)->getJson('/api/tasks');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    });

    it('should require authentication', function () {
        $this->getJson('/api/tasks')->assertUnauthorized();
    });
});

/**
 * STORE
 */
describe('POST /tasks (store)', function () {
    it('should create a valid task', function () {
        $payload = [
            'name' => 'New Task',
            'description' => 'Task description',
            'is_completed' => false,
        ];

        $response = $this->actingAs($this->user)->postJson('/api/tasks', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'New Task')
            ->assertJsonPath('data.is_completed', false);

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

/**
 * SHOW
 */
describe('GET /tasks/{task} (show)', function () {
    it('should show a task of the authenticated user', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson("/api/tasks/{$task->id}");

        $response->assertOk()
            ->assertJsonPath('data.name', $task->name);
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

/**
 * UPDATE
 */
describe('PUT /tasks/{task} (update)', function () {
    it('should update a task of the authenticated user', function () {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $payload = ['name' => 'Updated Task'];

        $response = $this->actingAs($this->user)->putJson("/api/tasks/{$task->id}", $payload);

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Task');

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
            ->assertJsonPath('data.is_completed', true)
            ->assertJsonPath('data.name', 'Original Task'); // name should remain unchanged

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

/**
 * DESTROY
 */
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
