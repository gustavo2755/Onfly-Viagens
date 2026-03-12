<?php

namespace App\Http\Requests;

use App\Enums\TravelOrderStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexTravelOrderStatusLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'travel_order_id' => ['nullable', 'integer', 'exists:travel_orders,id'],
            'admin_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'to_status' => ['nullable', Rule::enum(TravelOrderStatusEnum::class)],
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date', 'after_or_equal:created_from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }
}
