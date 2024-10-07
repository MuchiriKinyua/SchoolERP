<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    public $table = 'employees';

    public $fillable = [
        'first_name',
        'middle_name',
        'surname',
        'email',
        'phone_number',
        'marital_status',
        'gender',
        'date_of_birth'
    ];

    protected $casts = [
        'first_name' => 'string',
        'middle_name' => 'string',
        'surname' => 'string',
        'email' => 'string',
        'phone_number' => 'string',
        'marital_status' => 'string',
        'gender' => 'string',
        'date_of_birth' => 'date'
    ];

    public static array $rules = [
        'first_name' => 'required',
        'middle_name' => 'required',
        'surname' => 'required',
        'email' => 'required|max:50',
        'phone_number' => 'required'
    ];

    
}
