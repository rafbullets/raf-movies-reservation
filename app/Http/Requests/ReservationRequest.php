<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
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
            'user_id' => ['required', 'integer'],
            'seats.*.row_number' => ['required', 'integer'],
            'seats.*.seat_number' => ['required', 'integer'],
            'projection_id' => ['required', 'integer'],
            'return_url' => ['required', 'url'],
            'cancel_url' => ['required', 'url']
        ];
    }
}
