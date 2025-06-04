<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Myth\Auth\Models\UserModel;

class ApiAuthFilter implements FilterInterface
{
    protected $userModel;
    protected $jwtSecretKey;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jwtSecretKey = getenv('JWT_SECRET_KEY') ?: 'JWTshlinkyangpanjangdanamanbangetZ911';
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        // Ambil token dari header
        $token = $this->getTokenFromRequest($request);

        if (!$token) {
            return $this->unauthorizedResponse('Token not provided');
        }

        try {
            // Decode JWT token
            $decoded = JWT::decode($token, new Key($this->jwtSecretKey, 'HS256'));
            
            // Cek apakah token sudah expired
            if ($decoded->exp < time()) {
                return $this->unauthorizedResponse('Token expired');
            }

            // Ambil user dari database
            $user = $this->userModel->find($decoded->user_id);
            
            if (!$user || !$user->active) {
                return $this->unauthorizedResponse('User not found or inactive');
            }

            // Simpan informasi user ke request untuk digunakan di controller
            $request->user = $user;
            $request->token_payload = $decoded;

        } catch (\Exception $e) {
            return $this->unauthorizedResponse('Invalid token');
        }

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi setelah request
    }

    /**
     * Extract token dari request headers
     */
    private function getTokenFromRequest(RequestInterface $request)
    {
        $header = $request->getHeaderLine('Authorization');
        
        if (strpos($header, 'Bearer ') === 0) {
            return substr($header, 7);
        }

        return null;
    }

    /**
     * Return unauthorized response
     */
    private function unauthorizedResponse($message = 'Unauthorized')
    {
        $response = service('response');
        $response->setStatusCode(401);
        $response->setJSON([
            'status' => 'error',
            'message' => $message,
            'code' => 401
        ]);
        
        return $response;
    }
}