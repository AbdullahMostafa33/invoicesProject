<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
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
            "invoice_number" => 'required',
            "invoice_Date" => 'required',
            "Due_date" => 'required',
            "section_id" => 'required',
            "product" => 'required',
            "Amount_collection" => 'required',
            "Amount_Commission" => "required",
            "Discount" => "required",
            "Rate_VAT" => "required",
            "Value_VAT" => "required",
            "Total" => "required",
        ];
    }
    public function messages(): array
    {
        return [
            'invoice_number.required' => 'رقم الفاتورة مطلوب',
            'invoice_Date.required' => 'تاريخ الفاتورة مطلوب',
            'Due_date.required' => 'تاريخ الاستحقاق مطلوب',
            'section_id.required' => 'القسم مطلوب',
            'product.required' => 'المنتج مطلوب',
            'Amount_collection.required' => ' مبلغ التحصيل مطلوب',
            'Amount_Commission.required' => 'مبلغ العمولة مطلوب',
            'Discount.required' => 'الخصم مطلوب',
            'Rate_VAT.required' => 'نسبة ضريبة القيمة المضافة مطلوبة',
            'Value_VAT.required' => 'قيمة ضريبة القيمة المضافة مطلوبة',
            'Total.required' => 'الاجمالي شامل الضريبة مطلوب',
        ];
    }
}
