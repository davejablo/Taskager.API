<?php

namespace App\Http\Requests\Document;

use App\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
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
        return [
            'project_id' => 'required', Rule::in(Project::all()->pluck('id')),
            'description' => 'string|min:5|max:255|nullable',
            'document' => 'required|mimetypes:application/pdf|max:10000',
        ];
    }
}