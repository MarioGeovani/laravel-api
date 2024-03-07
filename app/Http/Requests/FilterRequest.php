<?php

namespace App\Http\Requests;

use App\Enums\FilterHardDisckType;
use App\Enums\FilterParams;
use App\Enums\FilterRam;
use App\Enums\FilterStorage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class FilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            FilterParams::MAX_STORAGE->value => ['required_with:'. FilterParams::MIN_STORAGE->value, new Enum(FilterStorage::class)],
            FilterParams::MIN_STORAGE->value => ['required_with:'. FilterParams::MAX_STORAGE->value, new Enum(FilterStorage::class)],
            FilterParams::RAM->value =>'sometimes|array',
            FilterParams::RAM->value . '.*' => 'in:'. implode(',',FilterRam::getAllValues()),
            FilterParams::HDD_TYPE->value => ['sometimes', new Enum(FilterHardDisckType::class)],
            FilterParams::LOCATION->value => 'sometimes|string:min1',
        ];
    }
}
