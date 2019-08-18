<?php

use App\Http\Models\lunch;
use \Laravel\Lumen\Testing\DatabaseMigrations;

class lunchTest extends TestCase
{
    private $lunch;
    private $lastIdInserted;

    public function setUp()
    {
        parent::setUp();
        $this->lunch = factory(lunch::class)->make();
        try {
            $this->lastIdInserted = lunch::withTrashed()->orderBy('id')->get()->last()->id;
        } catch (Exception $e) {
            $this->lastIdInserted = 0;
        }
    }

    /**
     * should test required fields lunch.
     *
     * @return void
     */
    public function testShouldTestRequiredFiledlunch()
    {
        $this->json('POST', '/api/lunch', [])
            ->seeJson([
                'name' => ["The name field is required."]
            ]);
    }

    /**
     * should test create lunch.
     *
     * @return void
     */
    public function testShouldTestCreatelunch()
    {
        $expect = $this->lastIdInserted + 1;
        $this->json('POST', '/api/lunch', $this->lunch->toArray())
            ->seeJson(['id' => $expect]);
    }

    /**
     * should test update lunch.
     *
     * @return void
     */
    public function testShouldUpdatelunch()
    {
        $updatelunch = factory(lunch::class)->make();
        $this->json('PUT', '/api/lunch/' . $this->lastIdInserted, $updatelunch->toArray())
            ->dontSeeJson(['name' => $this->lunch->name])
            ->seeJson(['name' => $updatelunch->name]);
    }

    /**
     * should test delete lunch.
     *
     * @return void
     */
    public function testShouldTestDeletelunch()
    {
        $expect = ["DESTROY " => $this->lastIdInserted . ""];
        $this->json('DELETE', '/api/lunch/' . $this->lastIdInserted)
            ->seeJson($expect);
    }
}
