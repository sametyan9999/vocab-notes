<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWordRequest extends FormRequest
{
    // このリクエストを使う権限があるか（今回は全員OK）
    public function authorize(): bool
    {
        return true;
    }

    // バリデーションルール
    public function rules(): array
    {
        return [
            'term' => ['required', 'string', 'max:255'],
            'meaning' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string'],

            // チェックボックス（複数OK、無くてもOK）
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],

            // 新規タグ（1つ追加、空でもOK）
            'new_tag_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    // エラーメッセージで表示する項目名を日本語化
    public function attributes(): array
    {
        return [
            'term' => '単語',
            'meaning' => '意味',
            'note' => 'メモ',
            'tags' => 'タグ',
            'tags.*' => 'タグ',
            'new_tag_name' => '新しいタグ',
        ];
    }

    // エラーメッセージ自体を日本語化
    public function messages(): array
    {
        return [
            'term.required' => ':attributeは必須です。',
            'meaning.required' => ':attributeは必須です。',

            'term.max' => ':attributeは:max文字以内で入力してください。',
            'meaning.max' => ':attributeは:max文字以内で入力してください。',
            'note.max' => ':attributeは:max文字以内で入力してください。',

            'tags.array' => ':attributeの形式が正しくありません。',
            'tags.*.integer' => ':attributeの形式が正しくありません。',
            'tags.*.exists' => '選択した:attributeが存在しません。',

            'new_tag_name.max' => ':attributeは:max文字以内で入力してください。',
        ];
    }
}