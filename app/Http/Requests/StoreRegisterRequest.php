<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Customer registration is public
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|min:10|max:20|unique:users,phone',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->mixedCase()->numbers()->uncompromised()],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'email.unique'        => 'Email ini sudah terdaftar. Silakan login.',
            'password.confirmed'  => 'Konfirmasi password tidak cocok.',
            'password.min'        => 'Password minimal 8 karakter.',
            'phone.min'           => 'Nomor WhatsApp minimal 10 digit.',
            'phone.max'           => 'Nomor WhatsApp maksimal 20 digit.',
            'phone.unique'        => 'Nomor WhatsApp ini sudah terdaftar.',
            'password.uncompromised' => 'Password ini terlalu umum atau pernah bocor di internet. Gunakan password lain.',
            'password.mixed'      => 'Password harus mengandung huruf besar dan kecil.',
            'password.numbers'    => 'Password harus mengandung angka.',
        ];
    }
}
