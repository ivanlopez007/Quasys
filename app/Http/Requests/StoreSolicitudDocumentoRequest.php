<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitudDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Controlado por Policies o Roles si es necesario
    }

    public function rules(): array
    {
        return [
            // Si viene documento_id, es una NUEVA VERSIÓN; si es NULL, es un DOCUMENTO NUEVO
            'documento_id' => 'nullable|exists:documentos,id',
            'nombre_documento' => 'required|string|max:255',
            'archivo' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:20480', // Máx 20MB

            // Metadatos y Clasificación
            'nivel_id' => 'required|exists:nivels,id',
            'subnivel_id' => 'nullable|exists:sub_nivels,id',
            'localidad_id' => 'required|exists:localidads,id',
            'area_id' => 'required|exists:areas,id',
            'lugar_retencion_id' => 'nullable|exists:lugar_retencions,id',
            'periodo_retencion_id' => 'nullable|exists:periodo_retencions,id',
            'disposicion_final_id' => 'nullable|exists:disposicion_finals,id',
            'tipo_solicitud_id' => 'required|exists:tipo_solicituds,id',

            // Justificaciones de auditoría
            'motivo_cambio' => 'required|string|min:10',
            'descripcion_cambios' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'archivo.required' => 'Debes adjuntar el archivo del documento.',
            'archivo.mimes' => 'El archivo debe ser en formato PDF, Word o Excel.',
            'archivo.max' => 'El peso máximo del archivo es de 20 MB.',
            'nombre_documento.required' => 'El nombre del documento es obligatorio.',
            'motivo_cambio.required' => 'Debes especificar el motivo de la creación o cambio.',
            'motivo_cambio.min' => 'El motivo del cambio debe ser descriptivo (mínimo 10 caracteres).',
        ];
    }
}
