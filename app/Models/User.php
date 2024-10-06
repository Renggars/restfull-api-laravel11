<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model implements Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'username',
        'password',
        'name',
        'token' // Token ditambahkan dalam fillable jika akan diupdate manual
    ];

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'user_id');
    }

    // Metode ini untuk mendapatkan identifier (username)
    public function getAuthIdentifierName()
    {
        return 'username'; // Mengembalikan nama kolom password
    }

    // Metode ini untuk mendapatkan nilai identifier (misalnya username)
    public function getAuthIdentifier()
    {
        return $this->username;
    }

    // Metode ini untuk mendapatkan identifier (password)
    public function getAuthPasswordName()
    {
        return 'password';
    }

    // Metode ini untuk mendapatkan kata sandi pengguna
    public function getAuthPassword()
    {
        return $this->password;
    }


    // Mengambil token yang digunakan untuk mengingat pengguna dalam sesi.
    public function getRememberToken()
    {
        return $this->remember_token; // Mengembalikan token "remember me"
    }

    // Menetapkan token "remember me" baru saat Laravel membutuhkan token tersebut, misalnya saat pengguna memilih opsi "remember me" saat login. Token ini kemudian disimpan di kolom remember_token untuk autentikasi sesi berikutnya.
    public function setRememberToken($value)
    {
        return $this->token = $value;
    }


    // Mengambil nama kolom database yang digunakan untuk menyimpan token "remember me". Secara default, Laravel menggunakan kolom remember_token, tetapi jika kamu menggunakan nama kolom lain, kamu dapat override fungsi ini.
    public function getRememberTokenName()
    {
        return 'remember_token'; // Mengembalikan nama kolom "remember me" token
    }
}
