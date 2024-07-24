<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice_attachment extends Model
{
    use HasFactory;
    protected $filalble=[
        'file_name',
        'invoice_number',
        'Created_by',
        'invoice_id',
    ];
}
