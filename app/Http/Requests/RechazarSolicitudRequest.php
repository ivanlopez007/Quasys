<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RechazarSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comentario_aprobador' => 'required|string|min:10|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'comentario_aprobador.required' => 'Debe ingresar el motivo del rechazo.',
            'comentario_aprobador.min'      => 'El motivo del rechazo debe contener al menos 10 caracteres.',
        ];
    }
}
