<?php

use App\Http\Models\User;
use \Laravel\Lumen\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    private $user;
    private $lastIdInserted;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->make();
        try {
            $this->lastIdInserted = User::withTrashed()->orderBy('id')->get()->last()->id;
        } catch (Exception $e) {
            $this->lastIdInserted = 0;
        }
    }

    /**
     * should test required fields user.
     *
     * @return void
     */
    public function testShouldTestRequiredFiledUser()
    {
        $this->json('POST', '/api/user', [])
            ->seeJson([
                'name' => ["The name field is required."]
            ]);
    }

    /**
     * should test create user.
     *
     * @return void
     */
    public function testShouldTestCreateUser()
    {
        $expect = $this->lastIdInserted + 1;
        $this->json('POST', '/api/user', $this->user->toArray())
            ->seeJson(['id' => $expect]);
    }

    /**
     * should test update user.
     *
     * @return void
     */
    public function testShouldUpdateUser()
    {
        $updateUser = factory(User::class)->make();
        $this->json('PUT', '/api/user/' . $this->lastIdInserted, $updateUser->toArray())
            ->dontSeeJson(['name' => $this->user->name])
            ->seeJson(['name' => $updateUser->name]);
    }

    /**
     * should test delete user.
     *
     * @return void
     */
    public function testShouldTestDeleteUser()
    {
        $expect = ["DESTROY " => $this->lastIdInserted . ""];
        $this->json('DELETE', '/api/user/' . $this->lastIdInserted)
            ->seeJson($expect);
    }
}
