<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function __construct()
    {
        
        $this->penggunaModel = model('PenggunaModel');
        // helper('auth');
    }

    public function login($no_hp = NULL, $password = null)
    {
        $data = $no_hp ?? $this->request->getPost('no_hp');
        $password = $password ?? $this->request->getPost('password');

        $hp = $email = $message = '';
       
        if (filter_var($data, FILTER_VALIDATE_EMAIL)) {
            $email = $data;
        } else {
            $message = "Format email keliru";
            $hp = toNumber($data);
            if (substr($hp,0,2) == '62') {
                $hp = $data;
                $message = '';
            }
            else if (substr($hp,0,1) == '8') {
                $hp = '0'.$data;
                $message = '';
            } else {
                $message .= ' / Nomor HP tidak valid';
            }
        }
        
        if ($message) {
            return $this->respond([
                'message' => $message,
            ], 401);
        }
        $user = $this->penggunaModel->login($email, $hp, md5($password));
        if ($user) {
            // Getting user positions
            set_userdata($user);
            return $this->respondCreated($user);
        } else {
            return $this->respond([
                'message' => 'Maaf akun Anda belum terdaftar.',
            ], 401);
        }

    }

    public function user()
    {
        if (empty(userdata())) {
            return $this->unauthorized();
        }
        return $this->respondCreated(userdata());
    }

    public function forbidden()
    {
        return $this->failForbidden('Error 403 Forbidden');
    }

    public function unauthorized()
    {
        return $this->failUnauthorized('Error 401 Unauthorized');
    }

    public function logout()
    {
        clear_userdata();

        return $this->respondCreated();
    }

    
    public function reset()
    {
        $user = userdata();
        clear_userdata();
        $user = $this->penggunaModel->login($user->no_hp, $user->password);
        
        if ($user) {
            // Getting user positions
            set_userdata($user);
            return $this->respondCreated($user);
        } else {
            return $this->respond([
                'message' => 'Maaf akun Anda belum terdaftar.',
            ], 401);
        }
    }
}
