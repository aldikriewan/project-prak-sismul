<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stori extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'image',
        'background_image',
        'description',
        'detail',
    ];
}
