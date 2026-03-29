<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CoachRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('coach') ? $this->route('coach')->id : null;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'display_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId)
            ],
            'password' => $isUpdate ?
                ['nullable', 'string', 'min:8', 'confirmed'] :
                ['required', 'string', 'min:8', 'confirmed'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:500'],
            'profile_photo_url' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'display_name.required' => 'Display name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already taken.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'whatsapp.max' => 'WhatsApp number cannot exceed 255 characters.',
            'bio.max' => 'Bio cannot exceed 500 characters.',
            'profile_photo_url.image' => 'Profile photo must be an image.',
            'profile_photo_url.mimes' => 'Profile photo must be a JPG, JPEG, or PNG file.',
            'profile_photo_url.max' => 'Profile photo size cannot exceed 2MB.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'first name',
            'last_name' => 'last name',
            'display_name' => 'display name',
            'email' => 'email address',
            'password' => 'password',
            'whatsapp' => 'WhatsApp number',
            'bio' => 'biography',
            'profile_photo_url' => 'profile photo',
        ];
    }
}
