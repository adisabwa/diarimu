<?php

namespace App\Controllers;

use App\Libraries\GoogleAuth;

class Auth extends BaseController
{
    public $model;

    public function __construct()
    {
        
        $this->model = model('AnggotaModel');
        // helper('auth');
    }

    public function login($no_hp = NULL, $password = null)
    {
        $data = $no_hp ?? $this->request->getPost('no_hp');
        $password = $password ?? $this->request->getPost('password');

        $message = '';
       
        $email = $data;
        $hp = toPhoneNumber($data);
        
        if ($message) {
            return $this->respond([
                'message' => $message,
            ], 401);
        }
        $user = $this->model->login($email, $hp, md5($password));
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
    
    public function g_login()
    {
        $credential = $this->request->getGetPost('credential') ?? '';
        // $id = $this->request->getGetPost('id');

        $email = $this->request->getGetPost('email');

        if (!empty($credential)) {
            $google = new GoogleAuth();
            $userData = $google->verifyToken($credential);
            $email = $userData['email'] ?? $email ?? '';
        }

        // return $this->respond([
        //    'email' => $email,
        // ], 401);

        $user = $this->model->login($email, '', md5('admin12345diarimu') );
        // var_dump($user);
        if ($user) {
            // Getting user positions
            set_userdata($user);
            return $this->respondCreated($user);
        } else {
            return $this->respond([
                'email' => $email,
            ], 401);
        }

    }

    public function user()
    {
        if (empty(userdata())) {
            return $this->check_cookie();
        }
        return $this->respondCreated(userdata());
    }

    public function check_cookie()
    {
        $cookie = $this->request->getCookie('userData');
        // var_dump($cookie);
        if (empty($cookie)) {
            return $this->unauthorized();
        }
        $cookie = json_decode($cookie);
        return $this->login($cookie->no_hp, $cookie->password);
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

    
    public function change_role()
    {
        $role = $this->request->getGetPost('role');
        
        $userdata = userdata();
        $userdata->role = $role;

        set_userdata($userdata);

        return $this->respondCreated($userdata);
    }
    
    public function reset()
    {
        $user = userdata();
        $user = $this->model->login($user->email, $user->no_hp, $user->password);
        
        if ($user) {
            // Getting user positions
            clear_userdata();
            set_userdata($user);
            return $this->respondCreated($user);
        } else {
            return $this->respond([
                'message' => 'Maaf akun Anda belum terdaftar.',
            ], 401);
        }
    }

    public function send_request_reset() 
    {
        $phone = $this->request->getGetPost('phone');
        $email = $this->request->getGetPost('email');

        $user = $this->model->login($email, $phone, md5('admin12345diarimu') );

        if ($user) {
            $body = view('email-reset', compact('user'));
			//var_dump($user);
            //echo $body;
            //exit;
            $mailer = service('mailer');
            // var_dump($email);
            $ok = $mailer->send(
                $email,                                 // to
                'Reset Password',                    // subject
                $body, // HTML
                [
                    // 'attachments' => [WRITEPATH.'uploads/invoice.pdf'],
                    // 'cc' => ['team@your-domain.tld' => 'Team'],
                    // 'bcc' => ['audit@your-domain.tld'],
                ]
            );

            if ($ok) {
                return $this->respondCreated($user);
            } else {
                return $this->respond([
                    'message' => 'Server mengalami masalah',
                ], 401);
            }
        } else {
            return $this->respond([
                'message' => 'Maaf tidak akun dengan email ini. Silahkan masukkan no telepon dan email baru',
            ], 401);
        }
    }

    public function reset_password()
    {
        $md5_id = $this->request->getGetPost('id') ?? '-1';
        // $md5_id = md5(2);

        $data = $this->model->getDataWhere(whereAnd:[
            'md5({f}.id)' => $md5_id,
        ]);
        
        if ($data) {
            $new_password = substr($data->password, 0, 5);
            $update = $this->model->update($data->id, [
                'password' => md5($new_password)
            ]);;
            if ($update) {
                return redirect()->to(site_url());
            } else {
                exit('Tidak dapat mengubah password');
            }
        } else {
            exit('Data tidak ada');
        }
    }
}
