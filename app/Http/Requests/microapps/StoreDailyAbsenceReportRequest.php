<?php

namespace App\Http\Requests\microapps;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\microapps\DailyAbsenceReport;

class StoreDailyAbsenceReportRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'report_date' => ['required', 'date', 'before_or_equal:today'],
            'absent_count' => ['required', 'integer', 'min:0'],
            'comments' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check if deadline has passed
            if (DailyAbsenceReport::deadlinePassed($this->report_date)) {
                $validator->errors()->add('report_date', 'Η προθεσμία για αυτή την ημερομηνία έχει παρέλθει (10:00 π.μ.).');
            }
        });
    }

    public function messages()
    {
        return [
            'absent_count.required' => 'Παρακαλώ εισάγετε τον αριθμό των απόντων μαθητών.',
            'absent_count.min' => 'Ο αριθμός απόντων μαθητών δε μπορεί να είναι αρνητικός.',
            'report_date.before_or_equal' => 'Δε μπορείτε να υποβάλετε αναφορές για μελλοντικές ημερομηνίες.',
            'comments.max' => 'Οι παρατηρήσεις δε μπορεί να ξεπερνούν τους 1000 χαρακτήρες.',
        ];
    }

}
