<?php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;
use App\Http\Requests\StoreProductRequest;
use Illuminate\Support\Facades\Validator;

class StoreProductRequestTest extends TestCase
{
    private StoreProductRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new StoreProductRequest();
    }

    /** @test */
    public function validation_rules_are_correct()
    {
        $rules = $this->request->rules();

        $this->assertEquals([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ], $rules);
    }

    /** @test */
    public function valid_data_passes_validation()
    {
        $validator = Validator::make([
            'name' => 'Test Product',
            'price' => 100,
            'stock' => 10,
            'is_active' => true
        ], $this->request->rules());

        $this->assertFalse($validator->fails());
    }
}
