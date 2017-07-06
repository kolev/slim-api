<?php
namespace App\Controllers;

class AuthController
{
    protected $db;

    public function __construct($c)
    {
        $this->db = $c->get('db');
    }

    public function login($request, $response)
    {
        $params = $request->getQueryParams();
        $sth = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $sth->execute(['username' => $params['username']]);
        $result = $sth->fetchObject();

        if (!$result || !password_verify($params['password'], $result->password))
            return $response->withStatus(401);

        // Generate token
        $token = bin2hex(openssl_random_pseudo_bytes(16));

        $sth = $this->db->prepare("UPDATE users SET token = :token, token_expire = DATE_ADD(NOW(), INTERVAL 2 HOUR) WHERE id = :id");
        $sth->execute(['id' => $result->id, 'token' => $token]);

        return $response->withJson([
            'token' => $token
        ]);
    }

    public function validateToken($token)
    {
        $sth = $this->db->prepare("SELECT * FROM users WHERE token = :token AND token_expire > NOW()");
        $sth->execute(['token' => $token]);
        $result = $sth->fetchObject();

        if ($result) {
            //Update expiration
            $sth = $this->db->prepare("UPDATE users SET token_expire = DATE_ADD(NOW(), INTERVAL 2 HOUR) WHERE token = :token");
            $sth->execute(['token' => $token]);

            return true;
        } else {
            return false;
        }
    }
}
