<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'style_id' => 'required|string|unique:products,style_id|max:20',
            'name' => 'required|string|max:255',
            'fabric' => 'required|string|max:255',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'style_id.unique' => 'This Style ID already exists.',
            'selling_price.required' => 'Selling price is required.',
            'image.image' => 'Please upload a valid image.',
        ];
    }
}
