<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\WaliSantri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthWaliSantriController extends Controller
{
    /**
     * Login wali santri
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find wali santri by email
            $waliSantri = WaliSantri::where('email', $request->email)->first();

            if (!$waliSantri || !Hash::check($request->password, $waliSantri->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password tidak valid'
                ], 401);
            }

            // Check if account is active
            if ($waliSantri->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda sedang tidak aktif'
                ], 403);
            }

            // Create token
            $token = $waliSantri->createToken('WaliSantriApp')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => [
                        'id' => $waliSantri->id,
                        'name' => $waliSantri->name,
                        'email' => $waliSantri->email,
                        'phone' => $waliSantri->phone,
                        'address' => $waliSantri->address
                    ],
                    'token' => $token
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register wali santri (optional, bisa dimatikan di production)
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:wali_santri',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'nik' => 'nullable|string|max:16',
                'jenis_kelamin' => 'nullable|in:L,P',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'pekerjaan' => 'nullable|string|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $waliSantri = WaliSantri::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'nik' => $request->nik,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'pekerjaan' => $request->pekerjaan,
                'status' => 'active'
            ]);

            $token = $waliSantri->createToken('WaliSantriApp')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'data' => [
                    'user' => [
                        'id' => $waliSantri->id,
                        'name' => $waliSantri->name,
                        'email' => $waliSantri->email,
                        'phone' => $waliSantri->phone,
                        'address' => $waliSantri->address
                    ],
                    'token' => $token
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get authenticated user info
     */
    public function user(Request $request)
    {
        try {
            $waliSantri = Auth::user();

            return response()->json([
                'success' => true,
                'message' => 'Data user berhasil diambil',
                'data' => [
                    'id' => $waliSantri->id,
                    'name' => $waliSantri->name,
                    'email' => $waliSantri->email,
                    'phone' => $waliSantri->phone,
                    'address' => $waliSantri->address,
                    'nik' => $waliSantri->nik,
                    'jenis_kelamin' => $waliSantri->jenis_kelamin,
                    'tempat_lahir' => $waliSantri->tempat_lahir,
                    'tanggal_lahir' => $waliSantri->tanggal_lahir,
                    'pekerjaan' => $waliSantri->pekerjaan,
                    'status' => $waliSantri->status
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $waliSantri = Auth::user();

            $validator = Validator::make($request->all(), [
                'name' => 'string|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'nik' => 'nullable|string|max:16',
                'jenis_kelamin' => 'nullable|in:L,P',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'pekerjaan' => 'nullable|string|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $waliSantri->update($request->only([
                'name', 'phone', 'address', 'nik', 'jenis_kelamin', 
                'tempat_lahir', 'tanggal_lahir', 'pekerjaan'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Profile berhasil diperbarui',
                'data' => [
                    'id' => $waliSantri->id,
                    'name' => $waliSantri->name,
                    'email' => $waliSantri->email,
                    'phone' => $waliSantri->phone,
                    'address' => $waliSantri->address,
                    'nik' => $waliSantri->nik,
                    'jenis_kelamin' => $waliSantri->jenis_kelamin,
                    'tempat_lahir' => $waliSantri->tempat_lahir,
                    'tanggal_lahir' => $waliSantri->tanggal_lahir,
                    'pekerjaan' => $waliSantri->pekerjaan
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $waliSantri = Auth::user();

            if (!Hash::check($request->current_password, $waliSantri->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password saat ini tidak valid'
                ], 401);
            }

            $waliSantri->update([
                'password' => Hash::make($request->new_password)
            ]);

            // Logout from all devices for security
            $waliSantri->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah. Silakan login kembali.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500);
        }
    }
}
