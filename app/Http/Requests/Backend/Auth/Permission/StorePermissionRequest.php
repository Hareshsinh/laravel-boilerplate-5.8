<?php

namespace App\Http\Requests\Backend\Auth\Permission;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StorePermissionRequest.
 */
class StorePermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'unique:permissions', 'max:191'],
        ];
    }
}
