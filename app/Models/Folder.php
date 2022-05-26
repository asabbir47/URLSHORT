<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'folder_id'
    ];

    public function short_urls()
    {
        $this->hasMany(ShortUrl::class);
    }
}
