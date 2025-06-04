<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Myth\Auth\Models\UserModel;
use App\Models\LinkModel;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ApiController extends BaseController
{
    use ResponseTrait;

    protected $userModel;
    protected $linkModel;
    protected $jwtSecretKey;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->linkModel = new LinkModel();
        $this->jwtSecretKey = getenv('JWT_SECRET_KEY') ?: 'JWTshlinkyangpanjangdanamanbangetZ911';
    }

    /**
     * API Login dengan Debug
     * POST /api/v1/login
     */
    public function login()
    {
        // Ambil raw input JSON
        $input = $this->request->getJSON(true);
        
        // Debug: Log raw input
        log_message('debug', 'Raw input: ' . json_encode($input));
        log_message('debug', 'Content-Type: ' . $this->request->getHeaderLine('Content-Type'));
        
        // Jika tidak ada JSON, coba POST biasa
        if (!$input) {
            $input = [
                'login' => $this->request->getPost('login'),
                'password' => $this->request->getPost('password')
            ];
            log_message('debug', 'Fallback to POST: ' . json_encode($input));
        }

        $login = $input['login'] ?? null;
        $password = $input['password'] ?? null;

        // Validasi input
        if (empty($login) || empty($password)) {
            return $this->fail([
                'message' => 'Login and password are required',
                'received_data' => $input
            ], 400);
        }

        // Debug: Log input yang diterima
        log_message('debug', 'API Login attempt - Login: ' . $login);

        // Cari user berdasarkan email atau username
        $user = $this->userModel->where('email', $login)
                                ->orWhere('username', $login)
                                ->first();

        // Debug: Log apakah user ditemukan
        if ($user) {
            log_message('debug', 'User found - ID: ' . $user->id . ', Email: ' . $user->email . ', Active: ' . $user->active);
            
            // Debug: Log hash password untuk perbandingan
            log_message('debug', 'Stored password hash: ' . substr($user->password_hash, 0, 20) . '...');
            
            // Cek password
            $passwordMatch = password_verify($password, $user->password_hash);
            log_message('debug', 'Password verification result: ' . ($passwordMatch ? 'MATCH' : 'NO MATCH'));
            
        } else {
            log_message('debug', 'User NOT found for login: ' . $login);
            
            // Debug: Coba cari dengan email saja
            $userByEmail = $this->userModel->where('email', $login)->first();
            if ($userByEmail) {
                log_message('debug', 'User found by email only');
            } else {
                log_message('debug', 'User NOT found by email');
            }
            
            // Debug: Coba cari dengan username saja
            $userByUsername = $this->userModel->where('username', $login)->first();
            if ($userByUsername) {
                log_message('debug', 'User found by username only');
            } else {
                log_message('debug', 'User NOT found by username');
            }
        }

        // Cek kredensial
        if (!$user || !password_verify($password, $user->password_hash)) {
            return $this->fail([
                'message' => 'Invalid credentials',
                'debug_info' => [
                    'user_found' => $user ? true : false,
                    'password_check' => $user ? password_verify($password, $user->password_hash) : false,
                    'login_attempted' => $login
                ]
            ], 401);
        }

        // Cek apakah akun aktif
        if (!$user->active) {
            return $this->fail([
                'message' => 'Account not activated',
                'debug_info' => [
                    'user_active' => $user->active,
                    'activate_hash' => !empty($user->activate_hash)
                ]
            ], 401);
        }

        // Generate JWT token
        $token = $this->generateJWT($user);

        return $this->respond([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'username' => $user->username,
                    'fullname' => $user->fullname
                ],
                'token' => $token,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+24 hours'))
            ]
        ]);
    }

    /**
     * API Create Short Link - Disesuaikan dengan struktur database existing
     */
    public function createShortLink()
    {
        // Validasi input (hapus validasi expiry karena tidak disimpan di DB)
        $rules = [
            'original_url' => 'required|valid_url'
        ];

        if (!$this->validate($rules)) {
            return $this->fail([
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ], 400);
        }

        // Ambil data dari request
        $originalUrl = $this->request->getPost('original_url');
        $aliasUrl = $this->request->getPost('alias_url');
        $encryption = $this->request->getPost('encryption') == true;
        $password = $this->request->getPost('password');
        $expiry = $this->request->getPost('expiry') ?: '1 Minggu'; // Default jika tidak ada

        // Dapatkan user dari token
        $user = $this->getCurrentUser();
        if (!$user) {
            return $this->fail(['message' => 'User not found'], 401);
        }

        // Validasi alias URL jika ada
        if (!empty($aliasUrl)) {
            if (strlen($aliasUrl) > 75) {
                return $this->fail([
                    'message' => 'Custom URL maximum 75 characters'
                ], 400);
            }

            // Cek apakah alias sudah digunakan
            $existingLink = $this->linkModel->where('alias_url', $aliasUrl)
                ->where('user_id', $user->id)
                ->where('expired_at >', date('Y-m-d H:i:s'))
                ->first();

            if ($existingLink) {
                return $this->fail([
                    'message' => 'Alias URL already exists and is active'
                ], 400);
            }
        }

        // Validasi password jika encryption aktif
        if ($encryption && empty($password)) {
            return $this->fail([
                'message' => 'Password required for encryption'
            ], 400);
        }

        // Generate shortened URL
        $userId = $user->id;
        $encodedUserId = rtrim(strtr(base64_encode($userId), '+/', '-_'), '=');

        if (!empty($aliasUrl)) {
            $shortenedUrl = 'https://s.pu.go.id/' . $encodedUserId . '/' . $aliasUrl;
        } else {
            $randomString = $this->generateRandomString(6);
            $shortenedUrl = 'https://s.pu.go.id/' . $encodedUserId . '/' . $randomString;
        }

        // Handle encryption jika diperlukan
        $encryptedPassword = null;
        $encryptedUrl = null;
        $isEncrypted = 0;

        if ($encryption) {
            $config = new \Config\Encryption();
            $config->key = 'Pupr#book.2024';
            $config->driver = 'OpenSSL';
            $encrypter = Services::encrypter($config);

            $encryptedPassword = base64_encode($encrypter->encrypt($password));
            $encryptedUrl = base64_encode($encrypter->encrypt($shortenedUrl));
            $isEncrypted = 1;
        }

        // Hitung tanggal kadaluarsa
        $expiredAt = $this->calculateExpiryDate($expiry);

        // Simpan ke database - HANYA field yang ada di database
        $data = [
            'original_url' => $originalUrl,
            'alias_url' => $aliasUrl,
            'shortened_url' => $shortenedUrl,
            'encryption' => $encryptedUrl,
            'password' => $encryptedPassword,
            'user_id' => $userId,
            'created_at' => date('Y-m-d H:i:s'),
            'is_encrypted' => $isEncrypted,
            'expired_at' => $expiredAt
            // TIDAK termasuk 'expiry' karena kolom tidak ada di database production
        ];

        if ($this->linkModel->insert($data)) {
            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Short link created successfully',
                'data' => [
                    'id' => $this->linkModel->getInsertID(),
                    'original_url' => $originalUrl,
                    'shortened_url' => $shortenedUrl,
                    'alias_url' => $aliasUrl,
                    'is_encrypted' => $isEncrypted,
                    'expiry' => $expiry, // Tetap return di response meski tidak disimpan
                    'expired_at' => $expiredAt,
                    'created_at' => $data['created_at']
                ]
            ]);
        }

        return $this->fail([
            'message' => 'Failed to create short link'
        ], 500);
    }

    /**
     * API Get My Links
     */
    public function getMyLinks()
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return $this->fail(['message' => 'User not found'], 401);
        }

        $links = $this->linkModel->where('user_id', $user->id)
                                ->orderBy('created_at', 'DESC')
                                ->findAll();

        $formattedLinks = array_map(function($link) {
            return [
                'id' => $link['id'],
                'original_url' => $link['original_url'],
                'shortened_url' => $link['shortened_url'],
                'alias_url' => $link['alias_url'],
                'is_encrypted' => $link['is_encrypted'],
                'expiry' => $link['expiry'],
                'expired_at' => $link['expired_at'],
                'created_at' => $link['created_at'],
                'is_expired' => (new \DateTime() > new \DateTime($link['expired_at']))
            ];
        }, $links);

        return $this->respond([
            'status' => 'success',
            'data' => $formattedLinks
        ]);
    }

    // ===== Helper Methods =====

    /**
     * Generate JWT Token
     */
    private function generateJWT($user)
    {
        $payload = [
            'iss' => base_url(),
            'aud' => base_url(),
            'iat' => time(),
            'exp' => time() + (24 * 60 * 60), // 24 jam
            'user_id' => $user->id,
            'email' => $user->email,
            'username' => $user->username
        ];

        return JWT::encode($payload, $this->jwtSecretKey, 'HS256');
    }

    /**
     * Get current user from JWT token
     */
    private function getCurrentUser()
    {
        $token = $this->getTokenFromRequest();
        
        if (!$token) {
            return null;
        }

        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecretKey, 'HS256'));
            return $this->userModel->find($decoded->user_id);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Extract token from request
     */
    private function getTokenFromRequest()
    {
        $header = $this->request->getHeaderLine('Authorization');
        
        if (strpos($header, 'Bearer ') === 0) {
            return substr($header, 7);
        }

        return null;
    }

    /**
     * Generate random string untuk URL
     */
    private function generateRandomString($length)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Calculate expiry date
     */
    private function calculateExpiryDate($expiry)
    {
        $maxDate = '9999-12-31 23:59:59';
        
        switch ($expiry) {
            case '2 Minggu':
                return date('Y-m-d H:i:s', strtotime('+2 Weeks'));
            case '1 Bulan':
                return date('Y-m-d H:i:s', strtotime('+1 Month'));
            case '1 Minggu':
                return date('Y-m-d H:i:s', strtotime('+1 Week'));
            case 'Tanpa Batasan Periode Waktu':
                return $maxDate;
            default:
                return date('Y-m-d H:i:s', strtotime('+1 Week'));
        }
    }
}