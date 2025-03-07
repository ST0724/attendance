<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminDetailRequest extends FormRequest
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
            'clock_in' => ['required', 'date_format:H:i'],
            'clock_out' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $clock_in = $this->input('clock_in');
                    if ($clock_in && $value && strtotime($value) <= strtotime($clock_in)) {
                        $fail('出勤時間もしくは退勤時間が不適切な値です');
                    }
                },
            ],
            'breaks' => ['array'],
            'breaks.*.break_start' => [
                'required_with:breaks.*.break_end',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $clock_in = $this->input('clock_in');
                    $clock_out = $this->input('clock_out');
                    if ($value && $clock_in && $clock_out) {
                        if (strtotime($value) <= strtotime($clock_in) || strtotime($value) >= strtotime($clock_out)) {
                            $fail('休憩時間が勤務時間外です');
                        }
                    }
                },
            ],
            'breaks.*.break_end' => [
                'required_with:breaks.*.break_start',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $break_start = $this->input("breaks.{$index}.break_start");
                    $clock_in = $this->input('clock_in');
                    $clock_out = $this->input('clock_out');
                    if ($value && $break_start && $clock_in && $clock_out) {
                        if (strtotime($value) <= strtotime($break_start) || strtotime($value) <= strtotime($clock_in) || strtotime($value) > strtotime($clock_out)) {
                            $fail('休憩時間が勤務時間外です');
                        }
                    }
                },
            ],
        ];
    }

    public function messages(){
        return[
            'clock_in.required' => '出勤時間を入力してください',
            'clock_out.required' => '退勤時間を入力してください',
            'clock_in.date_format' => '出勤時間の入力が不適切です',
            'clock_out.date_format' => '退勤時間の入力が不適切です',
            'breaks.*.break_start.date_format' => '休憩開始時間の入力が不適切です',
            'breaks.*.break_end.date_format' => '休憩終了時間の入力が不適切です',
        ];
    }
}
