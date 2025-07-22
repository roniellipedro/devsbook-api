<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Laravel\Facades\Image;

class UserController extends BaseController
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');

        $this->loggedUser = Auth::user();
    }

    public function update(Request $request)
    {
        $array = ['error' => ''];

        $name = $request->input('name');
        $email = $request->input('email');
        $birthdate = $request->input('birthdate');
        $city = $request->input('city');
        $work = $request->input('work');
        $password = $request->input('password');
        $password_confirm = $request->input('password_confirm');

        $user = User::find($this->loggedUser['id']);

        if ($name) {
            $user->name = $name;
        }

        if ($email) {
            if ($email != $user->email) {
                $emailExists = User::where('email', $email)->count();
                if ($emailExists === 0) {
                    $user->email = $email;
                } else {
                    $array['error'] = 'E-mail já existe!';
                    return $array;
                }
            }
        }

        if ($birthdate) {
            if (strtotime($birthdate) === false) {
                $array['error'] = 'Data de nascimento inválida!';
                return $array;
            }
            $user->birthdate = $birthdate;
        }

        $user->city = $city;
        $user->work = $work;

        if ($password && $password_confirm) {
            if ($password === $password_confirm) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $user->password = $hash;
            } else {
                $array['error'] = 'As senhas não se conferem.';
                return $array;
            }
        } else {
            $array['error'] = 'Preencha a senha duas vezes.';
            return $array;
        }

        $user->save();

        return $array;
    }

    public function updateAvatar(Request $request)
    {
        $array = ['error' => ''];
        $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];

        $image = $request->file('avatar');

        if ($image) {
            if (in_array($image->getClientMimeType(), $allowedTypes)) {
                $filename = md5(time() . rand(0, 9999)) . '.jpg';

                $destPath = public_path('/media/avatars');

                $img = Image::make($image->path())
                    ->fit(200, 200)
                    ->save($destPath . '/' . $filename);

                $user = User::find($this->loggedUser['id']);
                $user->avatar = $filename;
                $user->save();

                $array['url'] = url('/media/avatars/' . $filename);
            } else {
                $array['error'] = 'Arquivo não suportado!';
                return $array;
            }
        } else {
            $array['error'] = 'Arquivo não enviado!';
            return $array;
        }

        return $array;
    }
}
