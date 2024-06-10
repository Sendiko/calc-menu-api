<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuStoreRequest extends FormRequest
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
            "name" => "required|string|max:60",
            "description" => "required|string|max:255",
            "category" => "required|string|in:food,beverage",
            "image" => "required|image|mimes:jpg,jpeg,png",
            "price" => "required|integer",
            "restaurant_id" => "required|integer"
        ];
    }
}
