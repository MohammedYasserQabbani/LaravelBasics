<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['title','short_desc'];

    public function content(){
        return $this->hasOne(Content::class,'post_id');
    }
}
