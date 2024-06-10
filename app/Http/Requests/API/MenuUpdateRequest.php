<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class MenuUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => "string|max:60",
            "description" => "string|max:255",
            "category" => "string|in:food,beverage",
            "image" => "image|mimes:jpg,jpeg,png",
            "price" => "integer",
            "restaurant_id" => "integer"
        ];
    }
}
