<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class EPOSAuthController extends Controller
{
    /**
     * Autentikasi sistem ePOS
     */
    public function authenticate(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'system_key' => 'required|string',
                'system_secret' => 'required|string',
                'system_name' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validasi sistem key dan secret
            $validSystemKey = config('app.epos_system_key', 'EPOS_SIMPELS_2025');
            $validSystemSecret = config('app.epos_system_secret', 'EPOS_SECRET_KEY_SIMPELS');

            if ($request->system_key !== $validSystemKey || 
                $request->system_secret !== $validSystemSecret) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kredensial sistem tidak valid',
                    'data' => null
                ], 401);
            }

            // Buat token untuk sistem ePOS
            $user = \App\Models\User::where('email', 'system@epos.local')->first();
            
            if (!$user) {
                // Buat user sistem jika belum ada
                $user = \App\Models\User::create([
                    'name' => 'ePOS System',
                    'email' => 'system@epos.local',
                    'password' => Hash::make('epos_system_password'),
                ]);
            }

            // Revoke token lama jika ada
            $user->tokens()->where('name', 'epos-api-token')->delete();

            // Buat token baru
            $token = $user->createToken('epos-api-token', ['epos:access'])->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Autentikasi berhasil',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'system_name' => $request->system_name,
                    'expires_in' => 86400, // 24 jam
                    'permissions' => ['epos:access']
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error autentikasi sistem: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Refresh token untuk sistem ePOS
     */
    public function refreshToken(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid',
                    'data' => null
                ], 401);
            }

            // Revoke token lama
            $user->tokens()->where('name', 'epos-api-token')->delete();

            // Buat token baru
            $token = $user->createToken('epos-api-token', ['epos:access'])->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Token berhasil di-refresh',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => 86400, // 24 jam
                    'permissions' => ['epos:access']
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error refresh token: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Logout sistem ePOS
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if ($user) {
                // Revoke token yang sedang digunakan
                $user->currentAccessToken()->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Logout berhasil',
                    'data' => null
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid',
                'data' => null
            ], 401);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error logout: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Cek status token
     */
    public function checkToken(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $token = $request->bearerToken();

            if (!$user || !$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid',
                    'data' => null
                ], 401);
            }

            // Ambil info token
            $personalAccessToken = PersonalAccessToken::findToken($token);
            
            if (!$personalAccessToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan',
                    'data' => null
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Token valid',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ],
                    'token_info' => [
                        'name' => $personalAccessToken->name,
                        'abilities' => $personalAccessToken->abilities,
                        'created_at' => $personalAccessToken->created_at,
                        'last_used_at' => $personalAccessToken->last_used_at
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cek token: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Ambil konfigurasi untuk sistem ePOS
     */
    public function getConfig(Request $request): JsonResponse
    {
        try {
            $config = [
                'app_name' => config('app.name', 'SIMPelS'),
                'app_version' => '1.0.0',
                'api_version' => 'v1',
                'timezone' => config('app.timezone', 'Asia/Jakarta'),
                'currency' => 'IDR',
                'currency_symbol' => 'Rp',
                'features' => [
                    'rfid_payment' => true,
                    'cash_payment' => true,
                    'card_payment' => false,
                    'balance_check' => true,
                    'transaction_history' => true,
                    'refund_support' => true
                ],
                'limits' => [
                    'max_transaction_amount' => 1000000, // 1 juta
                    'min_balance_required' => 5000, // 5 ribu
                    'daily_transaction_limit' => 5000000 // 5 juta per hari
                ]
            ];

            return response()->json([
                'success' => true,
                'message' => 'Konfigurasi berhasil diambil',
                'data' => $config
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil konfigurasi: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
