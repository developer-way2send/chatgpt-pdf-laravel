<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class PdfChatContent extends Authenticatable
{
    protected $table = 'pdf_chat_content';
}
