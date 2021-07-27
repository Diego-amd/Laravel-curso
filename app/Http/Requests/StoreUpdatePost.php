<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdatePost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->segment(2); //segmento da URL, ou seja, o segundo caminho (id)

        $rules = [
            //validações
            'title' => [
                'required',
                'min:3',
                'max:160',
                Rule::unique('posts')->ignore($id), //falando que a tabela posts é única, mas ignore o Id
            ],
            'content' => ['nullable','required', 'min:5', 'max:1000'], //valor pode ser nulo
            'image' => ['required', 'image']
        ];

        if($this->method() == 'PUT')
        {
            $rules['image'] = ['nullable', 'image']; //não é obrigatório, mas se for preenchido, é do tipo imagem
        }

        return $rules;
    }
}
