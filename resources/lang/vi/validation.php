<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'C�c:attribute thu?c t�nh ph?i ???c ch?p nh?n.',
    'active_url' => 'C�c:attribute thu?c t�nh kh�ng ph?i l� URL h?p l?.',
    'after' => 'C�c :attribute ph?i l� m?t ng�y sau ng�y :date.',
    'after_or_equal' => 'C�c :attribute thu?c t�nh ph?i l� m?t ng�y sau ho?c b?ng v?i:date.',
    'alpha' => 'C�c:attribute thu?c t�nh ch? ???c ch?a c�c k� t? ch? c�i.',
    'alpha_dash' => 'C�c:attribute thu?c t�nh ch? c� th? ch?a c�c k� t? ch? c�i, s?, d?u g?ch ngang v� d?u g?ch d??i.',
    'alpha_num' => 'C�c:attribute thu?c t�nh ch? ???c ch?a c�c k� t? ch? v� s?.',
    'array' => 'C�c:attribute thu?c t�nh ph?i l� m?t m?ng.',
    'before' => 'C�c:attribute thu?c t�nh ph?i l� m?t ng�y tr??c:date.',
    'before_or_equal' => 'C�c :attribute ph?i l� ng�y sau ho?c b?ng ng�y ???c ch? ??nh :date.',
    'between' => [
        'numeric' => 'C�c :attribute ph?i ? gi? :min v� :max.',
        'file' => 'C�c :attribute ph?i ? gi?a :min v� :max kilobytes.',
        'string' => 'C�c :attribute ph?i ? gi?a :min v� :max k� t?.',
        'array' => 'C�c :attribute ph?i ? gi?a :min v� :max m?c.',
    ],
    'boolean' => 'C�c :attribute tr??ng ph?i l� ?�ng ho?c sai.',
    'confirmed' => 'C�c :attribute x�c nh?n kh�ng kh?p.',
    'date' => 'C�c :attribute kh�ng ph?i l� ng�y h?p l?.',
    'date_equals' => 'C�c :attribute Ph?i l� m?t ng�y b?ng v?i :date.',
    'date_format' => 'C�c :attribute kh�ng kh?p v?i ??nh d?ng :format.',
    'different' => 'C�c :attribute v� :other kh�c nhau.',
    'digits' => 'C�c :attribute ph?i l� :digits ch? s?.',
    'digits_between' => 'C�c :attribute ph?i ? gi?a :min v� :max ch? s?.',
    'dimensions' => 'C�c :attribute c� k�ch th??c h�nh ?nh kh�ng h?p l?.',
    'distinct' => 'C�c tr??ng :attribute gi� tr? n�y ?� b? tr�ng l?p.',
    'email' => 'C�c :attribute ph?i l� ??a ch? email h?p l?.',
    'ends_with' => 'C�c :attribute ph?i k?t th�c b?ng m?t trong c�c k� t? sau ?�y: :values.',
    'exists' => 'C�c:attribute ???c l?a ch?n kh�ng h?p l?.',
    'file' => 'C�c :attribute ph?i l� m?t t?p.',
    'filled' => 'C�c tr??ng :attribute ph?i c� gi� tr?.',
    'gt' => [
        'numeric' => 'C�c :attribute ph?i l?n h?n :value.',
        'file' => 'C�c :attribute ph?i l?n shonw :value kilobytes.',
        'string' => 'C�c :attribute ph?i l?n h?n :value k� t?.',
        'array' => 'C�c :attribute ph?i l?n h?n :value m?c.',
    ],
    'gte' => [
        'numeric' => 'C�c :attribute ph?i l?n h?n ho?c b?ng v?i :value.',
        'file' => 'C�c :attribute ph?i l?n h?n ho?c b?ng v?i :value kilobytes.',
        'string' => 'C�c :attribute ph?i l?n h?n ho?c b?ng v?i :value characters.',
        'array' => 'c�c :attribute ph?i c� :value m?c ho?c nhi?u h?n.',
    ],
    'image' => 'C�c :attribute ph?i l� m?t h�nh ?nh.',
    'in' => 'C�c :attribute ???c l?a ch?n kh�ng h?p l?.',
    'in_array' => 'C�c tr??ng :attribute kh�ng t?n t?i trong :other.',
    'integer' => 'C�c :attribute ph?i l� m?t s? nguy�n.',
    'ip' => 'C�c :attribute ph?i l� ??a ch? IP h?p l?.',
    'ipv4' => 'C�c :attribute ph?i l� ??a ch? IPv4 h?p l?.',
    'ipv6' => 'C�c :attribute ph?i l� ??a ch? IPv6 h?p l?.',
    'json' => 'C�c :attribute Ph?i l� m?t chu?i JSON h?p l?.',
    'lt' => [
        'numeric' => 'C�c :attribute ph?i nh? h?n :value.',
        'file' => 'C�c :attribute ph?i nh? h?n :value kilobytes.',
        'string' => 'C�c :attribute ph?i nh? h?n :value k� t?.',
        'array' => 'C�c :attribute ph?i nh? h?n :value items.',
    ],
    'lte' => [
        'numeric' => 'C�c :attribute ph?i nh? h?n ho?c b?ng v?i :value.',
        'file' => 'C�c :attribute ph?i nh? h?n ho?c b?ng v?i :value kilobytes.',
        'string' => 'C�c :attribute ph?i nh? h?n ho?c b?ng v?i :value k� t?.',
        'array' => 'C�c :attribute kh�ng ???c c� nhi?u h?n :value m?c.',
    ],
    'max' => [
        'numeric' => 'C�c :attribute kh�ng ???c l?n h?n :max.',
        'file' => 'C�c :attribute kh�ng ???c l?n h?n :max kilobytes.',
        'string' => 'C�c :attribute Kh�ng ???c l?n h?n :max k� t?.',
        'array' => 'c�c :attribute Kh�ng ???c l?n h?n :max m?c.',
    ],
    'mimes' => 'C�c :attribute ph?i l� t?p c� ??nh d?ng: :values.',
    'mimetypes' => 'C�c :attribute ph?i l� t?p c� ??nh d?ng: :values.',
    'min' => [
        'numeric' => 'C�c :attribute ph?i c� �t nh?t :min.',
        'file' => 'C�c :attribute Ph?i c� �t nh?t :min kilobytes.',
        'string' => 'C�c :attribute Ph?i c� �t nh?t :min k� t?.',
        'array' => 'C�c :attribute Ph?i c� �t nh?t :min m?c.',
    ],
    'multiple_of' => 'C�c :attribute ph?i l� b?i s? c?a :value.',
    'not_in' => 'C�c :attribute ???c l?a ch?n kh�ng h?p l?.',
    'not_regex' => 'C�c ??nh d?ng :attribute kh�ng h?p l?.',
    'numeric' => 'C�c :attribute ph?i l� m?t s?.',
    'password' => 'M?t kh?u kh�ng ch�nh x�c.',
    'present' => 'C�c tr??ng :attribute c?n ???c ?i?n.',
    'regex' => 'C�c ??nh d?ng :attribute kh�ng h?p l?.',
    'required' => 'C�c tr??ng :attribute c?n ???c ?i?n.',
    'required_if' => 'C�c tr??ng :attribute c?n ???c ?i?n khi :other l� :value.',
    'required_unless' => 'C�c tr??ng :attribute c?n ???c ?i?n tr? khi :other ? :values.',
    'required_with' => 'C�c tr??ng :attribute c?n ???c ?i?n khi :values ???c ?i?n.',
    'required_with_all' => 'C�c tr??ng :attribute c?n ???c ?i?n khi :values ???c ?i?n.',
    'required_without' => 'C�c tr??ng :attribute c�n ???c ?i?n khi :values kh�ng ???c ?i?n.',
    'required_without_all' => 'C�c tr??ng :attribute c?n ???c ?i?n khi kh�ng c� gi� tr? :values n�o ???c ?i?n.',
    'same' => 'C�c :attribute v� :other c?n ph?i kh?p v?i nhau.',
    'size' => [
        'numeric' => 'C�c :attribute ph?i l� :size.',
        'file' => 'C�c :attribute ph?i l� :size kilobytes.',
        'string' => 'C�c :attribute ph?i l� :size k� t?.',
        'array' => 'C�c :attribute ph?i ch?a :size m?c.',
    ],
    'starts_with' => 'C�c :attribute ph?i b?t ??u v?i m?t trong: :values.',
    'string' => 'C�c :attribute ph?i l� m?t chu?i.',
    'timezone' => 'C�c :attribute ph?i l� khu v?c h?p l?.',
    'unique' => 'C�c :attribute ?� t?n t?i trong h? th?ng.',
    'uploaded' => 'C�c :attribute ?� c?p nh?t th?t b?i.',
    'url' => 'C�c ??nh d?ng :attribute kh�ng h?p l?.',
    'uuid' => 'C�c :attribute ph?i l� UUID h?p l?.',
    // 'required' => 'C�c tr??ng :attribute kh�ng th? b? tr?ng.',

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
            'rule-name' => 'Th�ng ?i?p-Kh�ch h�ng',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
