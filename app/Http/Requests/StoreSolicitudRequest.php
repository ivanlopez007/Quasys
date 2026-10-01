<?php

namespace App\Http\Requests;

use App\Models\CambioDocumento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        // IDs reales del catálogo, resueltos por nombre (Nuevo / Revisión / Eliminar)
        $nuevo = CambioDocumento::tipoId(CambioDocumento::TIPO_NUEVO) ?? 0;
        $eliminar = CambioDocumento::tipoId(CambioDocumento::TIPO_ELIMINAR) ?? 0;

        return [
            'tipo_solicitud_id' => ['required', 'integer', Rule::in(array_filter(CambioDocumento::tiposIds()))],

            // Revisión / Eliminar parten de un documento vigente; Nuevo no.
            'documento_id' => ["required_unless:tipo_solicitud_id,{$nuevo}", "prohibited_if:tipo_solicitud_id,{$nuevo}", 'nullable', 'integer', 'exists:documentos,id'],
            'codigo_documento' => ["required_if:tipo_solicitud_id,{$nuevo}", "prohibited_unless:tipo_solicitud_id,{$nuevo}", 'nullable', 'string', 'max:100'],
            // En una Revisión, si no se envía, se conserva el nombre del documento origen.
            'nombre_documento' => ["required_if:tipo_solicitud_id,{$nuevo}", 'nullable', 'string', 'max:255'],
            'archivo' => ["required_unless:tipo_solicitud_id,{$eliminar}", 'nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg', 'max:10240'],

            // Clasificación (en una Revisión, lo que no se envíe se hereda del documento origen).
            'nivel_id' => ["required_if:tipo_solicitud_id,{$nuevo}", 'nullable', 'integer', 'exists:nivels,id'],
            'subnivel_id' => ['nullable', 'integer', Rule::exists('sub_nivels', 'id')->where('nivel_id', $this->input('nivel_id'))],
            'localidad_id' => ["required_if:tipo_solicitud_id,{$nuevo}", 'nullable', 'integer', 'exists:localidads,id'],
            'area_id' => ["required_if:tipo_solicitud_id,{$nuevo}", 'nullable', 'integer', 'exists:areas,id'],
            'lugar_retencion_id' => ['nullable', 'integer', 'exists:lugar_retencions,id'],
            'disposicion_final_id' => ['nullable', 'integer', 'exists:disposicion_finals,id'],

            // Retención: el periodo del catálogo ya trae cantidad y unidad (p. ej. "5 Años").
            'periodo_retencion_id' => ['nullable', 'integer', Rule::exists('periodo_retencions', 'id')->where('activo', true)],
            'fecha_proxima_revision' => ['nullable', 'date', 'after:today'],

            // Plantas que podrán verlo (en una Revisión, si no se envían, se heredan).
            'plantas' => ["required_if:tipo_solicitud_id,{$nuevo}", 'nullable', 'array', 'min:1'],
            'plantas.*' => ['integer', 'distinct', Rule::exists('plantas', 'id')->whereNull('deleted_at')],

            'motivo_cambio' => ["required_unless:tipo_solicitud_id,{$nuevo}", 'nullable', 'string', 'max:2000'],
            'descripcion_cambios' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'documento_id.required_unless' => 'Selecciona el documento vigente a revisar o dar de baja.',
            'codigo_documento.required_if' => 'Captura el código del documento nuevo.',
            'nombre_documento.required_if' => 'El nombre del documento es obligatorio.',
            'archivo.required_unless' => 'Debes adjuntar el archivo del documento.',
            'archivo.mimes' => 'El archivo debe ser PDF, Word, Excel, PNG o JPG.',
            'archivo.max' => 'El peso máximo del archivo es de 10 MB.',
            'subnivel_id.exists' => 'El subnivel no pertenece al nivel seleccionado.',
            'fecha_proxima_revision.after' => 'La próxima revisión debe ser una fecha futura.',
            'motivo_cambio.required_unless' => 'El motivo es obligatorio para revisiones y bajas.',
            'tipo_solicitud_id.in' => 'Tipo de solicitud no válido. Revisa que el catálogo tenga los tipos Nuevo, Revisión y Eliminar.',
            'plantas.required_if' => 'Selecciona al menos una planta que pueda ver el documento.',
            'plantas.min' => 'Selecciona al menos una planta que pueda ver el documento.',
            'plantas.*.exists' => 'Una de las plantas seleccionadas no existe.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nivel_id' => 'nivel',
            'localidad_id' => 'localidad',
            'area_id' => 'área',
        ];
    }
}
