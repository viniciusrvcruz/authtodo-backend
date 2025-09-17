<?php

use App\Models\User;

describe('PUT /user/update', function () {
    it('should update user data successfully', function () {
        $user = User::factory()->create();

        $data = [
            'name' => 'New_Name',
        ];

        $this->actingAs($user);

        $response = $this->putJson(route('user.update'), $data);

        $response->assertNoContent();

        $user->refresh();
        expect($user->name)->toBe($data['name']);
    });

    it('should return 401 for unauthorized user', function () {
        $data = [
            'name' => 'New_Name',
        ];

        $response = $this->putJson(route('user.update'), $data);

        $response->assertUnauthorized();
    });
});
