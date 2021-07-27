<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts'; //relação com o migration posts, já é criado automaticamente criando o migration

    protected $fillable = ['title', 'content', 'image'];
    
}
