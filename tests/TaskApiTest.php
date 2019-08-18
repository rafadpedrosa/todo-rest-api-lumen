<?php

use App\Http\Models\Task;
use \Laravel\Lumen\Testing\DatabaseMigrations;

class TaskTest extends TestCase
{
    private $task;
    private $lastIdInserted;

    public function setUp()
    {
        parent::setUp();
        $this->task = factory(Task::class)->make();
        try {
            $this->lastIdInserted = Task::withTrashed()->orderBy('id')->get()->last()->id;
        } catch (Exception $e) {
            $this->lastIdInserted = 0;
        }
    }

    /**
     * should test required fields task.
     *
     * @return void
     */
    public function testShouldTestRequiredFiledTask()
    {
        $this->json('POST', '/api/task', [])
            ->seeJson([
                'name' => ["The name field is required."]
            ]);
    }

    /**
     * should test create task.
     *
     * @return void
     */
    public function testShouldTestCreateTask()
    {
        $expect = $this->lastIdInserted + 1;
        $this->json('POST', '/api/task', $this->task->toArray())
            ->seeJson(['id' => $expect]);
    }

    /**
     * should test update task.
     *
     * @return void
     */
    public function testShouldUpdateTask()
    {
        $updateTask = factory(Task::class)->make();
        $this->json('PUT', '/api/task/' . $this->lastIdInserted, $updateTask->toArray())
            ->dontSeeJson(['name' => $this->task->name])
            ->seeJson(['name' => $updateTask->name]);
    }

    /**
     * should test delete task.
     *
     * @return void
     */
    public function testShouldTestDeleteTask()
    {
        $expect = ["DESTROY " => $this->lastIdInserted . ""];
        $this->json('DELETE', '/api/task/' . $this->lastIdInserted)
            ->seeJson($expect);
    }
}
