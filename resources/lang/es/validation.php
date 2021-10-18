<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contener the default error messages used by
    | the validator class. Some of these rules tener multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'El campo :attribute debe ser aceptado.',
    'active_url' => 'El campo :attribute no es una URL válida.',
    'after' => 'El campo :attribute debe posterior a :date.',
    'after_or_equal' => 'El campo :attribute debe ser igual o posterior a :date.',
    'alpha' => 'El campo :attribute debe contener sólo letras.',
    'alpha_dash' => 'El campo :attribute debe contener sólo letras, números y guiones.',
    'alpha_num' => 'El campo :attribute debe contener sólo letras y números.',
    'alpha_spaces' => 'El campo :attribute debe contener sólo letras y espacios.',
    'array' => 'El campo :attribute debe ser un arreglo.',
    'before' => 'El campo :attribute debe anterior a :date.',
    'before_or_equal' => 'El campo :attribute debe ser igual o anterior a :date.',
    'between' => [
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'file' => 'El campo :attribute debe estar entre :min y :max kilobytes.',
        'string' => 'El campo :attribute debe estar entre :min y :max caracteres.',
        'array' => 'El campo :attribute debe tener entre :min y :max ítems.',
    ],
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'El campo :attribute debe estar confirmado.',
    'current_password' => 'Contraseña inválida.',
    'date' => 'El campo :attribute no es una fecha válida.',
    'date_equals' => 'El campo :attribute debe ser una fecha igual a :date.',
    'date_format' => 'El campo :attribute no está en el formato :format.',
    'different' => 'El campo :attribute y :other deben ser diferentes.',
    'digits' => 'El campo :attribute debe ser de :digits dígitos.',
    'digits_between' => 'El campo :attribute debe estar entre :min y :max dígitos.',
    'dimensions' => 'El campo :attribute tiene dimensiones inválidas.',
    'distinct' => 'El campo :attribute tiene un valor duplicado.',
    'email' => 'El campo :attribute debe ser una dirección de email.',
    'ends_with' => 'El campo :attribute debe terminar con uno de los siguientes valores: :values.',
    'exists' => 'El :attribute no existe.',
    'file' => 'El campo :attribute debe ser un archivo.',
    'filled' => 'El campo :attribute debe tener un valor.',
    'gt' => [
        'numeric' => 'El campo :attribute debe ser mayor que :value.',
        'file' => 'El campo :attribute debe ser mayor que :value kilobytes.',
        'string' => 'El campo :attribute debe tener más de :value caracteres.',
        'array' => 'El campo :attribute debe tener más de :value ítems.',
    ],
    'gte' => [
        'numeric' => 'El campo :attribute debe ser mayor o igual que :value.',
        'file' => 'El campo :attribute debe ser mayor o igual que :value kilobytes.',
        'string' => 'El campo :attribute debe ser mayor o igual que :value caracteres.',
        'array' => 'El campo :attribute debe tener :value ítems o más.',
    ],
    'image' => 'El campo :attribute debe ser una imagen.',
    'in' => 'El campo :attribute es inválido.',
    'in_array' => 'El campo :attribute no existe en :other.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'ip' => 'El campo :attribute debe ser una dirección IP.',
    'ipv4' => 'El campo :attribute debe ser una dirección IPv4.',
    'ipv6' => 'El campo :attribute debe ser una dirección IPv6.',
    'json' => 'El campo :attribute debe ser una cadena JSON.',
    'lt' => [
        'numeric' => 'El campo :attribute debe ser menor que :value.',
        'file' => 'El campo :attribute debe ser menor que :value kilobytes.',
        'string' => 'El campo :attribute debe ser menor que :value caracteres.',
        'array' => 'El campo :attribute debe tener menos de :value ítems.',
    ],
    'lte' => [
        'numeric' => 'El campo :attribute debe ser menor o igual que :value.',
        'file' => 'El campo :attribute debe ser menor o igual que :value kilobytes.',
        'string' => 'El campo :attribute debe ser menor o igual que :value caracteres.',
        'array' => 'El campo :attribute no debe tener más de :value ítems.',
    ],
    'max' => [
        'numeric' => 'El campo :attribute no debe ser mayor que :max.',
        'file' => 'El campo :attribute no debe ser mayor que :max kilobytes.',
        'string' => 'El campo :attribute no debe ser mayor que :max caracteres.',
        'array' => 'El campo :attribute no debe tener más de :max ítems.',
    ],
    'mimes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'mimetypes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'min' => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'file' => 'El campo :attribute debe ser de al menos :min kilobytes.',
        'string' => 'El campo :attribute debe ser de al menos :min caracteres.',
        'array' => 'El campo :attribute debe tener al menos :min ítems.',
    ],
    'multiple_of' => 'El campo :attribute debe ser múltiplo de :value.',
    'not_in' => 'El campo :attribute es inválido.',
    'not_regex' => 'El format :attribute es inválido.',
    'numeric' => 'El campo :attribute debe ser númeral.',
    'password' => 'Contraseña inválida.',
    'present' => 'El campo :attribute debe estar presentee.',
    'regex' => 'El formato :attribute es inválido.',
    'required' => 'El campo :attribute es requerido.',
    'required_if' => 'El campo :attribute es requerido cuando :other es :value.',
    'required_unless' => 'El campo :attribute es requerido si :other es :values.',
    'required_with' => 'El campo :attribute es requerido cuando :values está presente.',
    'required_with_all' => 'El campo :attribute es requerido cuando :values está presente.',
    'required_without' => 'El campo :attribute es requerido cuando :values no está presente.',
    'required_without_all' => 'El campo :attribute es requerido cuando ninguno de :values está presente.',
    'prohibited' => 'El campo :attribute está prohibido.',
    'prohibited_if' => 'El campo :attribute está prohibido cuando :other es :value.',
    'prohibited_unless' => 'El campo :attribute está prohibido si :other está en :values.',
    'same' => 'El campo :attribute y :other no concuerdan.',
    'size' => [
        'numeric' => 'El campo :attribute debe ser :size.',
        'file' => 'El campo :attribute debe ser de :size kilobytes como máximo.',
        'string' => 'El campo :attribute debe ser de :size caracteres.',
        'array' => 'El campo :attribute debe contener :size ítems.',
    ],
    'starts_with' => 'El campo :attribute debe comenzar con uno de los siguientes valores: :values.',
    'string' => 'El campo :attribute debe ser una cadena de texto.',
    'timezone' => 'El campo :attribute debe ser una zona horaria válida.',
    'unique' => 'El :attribute ya existe.',
    'uploaded' => 'El archivo :attribute fallo al cargarse.',
    'url' => 'El campo :attribute debe ser una URL válida.',
    'uuid' => 'El campo :attribute debe ser un UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "una dirección Mail" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'nombre',
        'last_name' => 'apellido',
        'identity_card' => 'documento de identidad',
        'code' => 'código',
        'origin' => 'procedencia',
        'detail' => 'detalle',
        'archived' => 'archivado',
        'area_id' => 'sección',
        'procedure_type_id' => 'tipo de trámite',
        'procedure_id' => 'trámite',
        'counter' => 'contador',
        'username' => 'usuario',
        'password' => 'contraseña',
        'address' => 'dirección',
        'phone' => 'teléfono',
        'old_password' => 'contraseña actual',
        'requirements' => 'requisitos',
        'attachments' => 'archivos',
        'attachments.*' => 'archivos',
        'requirements.*' => 'requisito',
        'search' => 'texto de búsqueda',
        'search_by' => 'parámetro de búsqueda',
        'start_date' => 'fecha inicial',
        'end_date' => 'fecha final',
    ],
];
