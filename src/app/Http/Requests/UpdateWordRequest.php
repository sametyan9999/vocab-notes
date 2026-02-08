<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'term' => ['required', 'string', 'max:255'],
            'reading' => ['nullable', 'string', 'max:255'], // ← ここに追加
            'meaning' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string'],

            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],

            'new_tag_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'term' => '単語',
            'reading' => '読み方', // ← ここに追加
            'meaning' => '意味',
            'note' => 'メモ',
            'tags' => 'タグ',
            'tags.*' => 'タグ',
            'new_tag_name' => '新しいタグ',
        ];
    }

    public function messages(): array
    {
        return [
            'term.required' => ':attributeは必須です。',
            'meaning.required' => ':attributeは必須です。',

            'term.max' => ':attributeは:max文字以内で入力してください。',
            'reading.max' => ':attributeは:max文字以内で入力してください。', // ← 追加
            'meaning.max' => ':attributeは:max文字以内で入力してください。',
            'note.max' => ':attributeは:max文字以内で入力してください。',

            'tags.array' => ':attributeの形式が正しくありません。',
            'tags.*.integer' => ':attributeの形式が正しくありません。',
            'tags.*.exists' => '選択した:attributeが存在しません。',

            'new_tag_name.max' => ':attributeは:max文字以内で入力してください。',
        ];
    }
}