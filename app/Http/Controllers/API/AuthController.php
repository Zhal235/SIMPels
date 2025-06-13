<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Santri;

class AuthController extends Controller
{
    /**
     * Login user and create token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable|string'
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Check password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        // Check if user has role 'wali_santri'
        if (!$user->hasRole('wali_santri')) {
            return response()->json([
                'success' => false,
                'message' => 'Akun ini tidak memiliki akses sebagai wali santri'
            ], 403);
        }

        // Create token
        $device = $request->device_name ?? $request->ip();
        $token = $user->createToken($device)->plainTextToken;

        // Get connected santri
        $santris = Santri::where('user_id', $user->id)
            ->orWhere('email_orangtua', $user->email)
            ->with(['kelasRelasi', 'asrama_anggota_terakhir.asrama'])
            ->get()
            ->map(function($santri) {
                return [
                    'id' => $santri->id,
                    'nama' => $santri->nama_santri,
                    'nis' => $santri->nis,
                    'kelas' => $santri->kelasRelasi->pluck('nama')->join(', ') ?: '-',
                    'asrama' => $santri->asrama_anggota_terakhir ? $santri->asrama_anggota_terakhir->asrama->nama : '-',
                    'foto' => $santri->foto ? asset('storage/' . $santri->foto) : null
                ];
            });

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ],
            'santri' => $santris
        ]);
    }

    /**
     * Register a new user as wali santri
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string',
            'santri_nis' => 'required|exists:santris,nis',
        ]);

        // Check if santri exists and verify relationship
        $santri = Santri::where('nis', $request->santri_nis)->first();
        
        if (!$santri) {
            return response()->json([
                'success' => false,
                'message' => 'Santri dengan NIS tersebut tidak ditemukan'
            ], 404);
        }

        // Verify if the phone matches parent's phone in santri record
        if ($santri->hp_ayah != $request->phone && $santri->hp_ibu != $request->phone) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor telepon tidak cocok dengan data wali santri yang terdaftar'
            ], 400);
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone
        ]);

        // Assign role wali_santri
        $user->assignRole('wali_santri');

        // Link user to santri if not already linked
        if (!$santri->user_id) {
            $santri->user_id = $user->id;
            $santri->save();
        }

        // Create token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ], 201);
    }

    /**
     * Get authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        $user = $request->user();
        
        // Get connected santri
        $santris = Santri::where('user_id', $user->id)
            ->orWhere('email_orangtua', $user->email)
            ->with(['kelasRelasi', 'asrama_anggota_terakhir.asrama'])
            ->get()
            ->map(function($santri) {
                return [
                    'id' => $santri->id,
                    'nama' => $santri->nama_santri,
                    'nis' => $santri->nis,
                    'kelas' => $santri->kelasRelasi->pluck('nama')->join(', ') ?: '-',
                    'asrama' => $santri->asrama_anggota_terakhir ? $santri->asrama_anggota_terakhir->asrama->nama : '-',
                    'foto' => $santri->foto ? asset('storage/' . $santri->foto) : null
                ];
            });
            
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'santri' => $santris
        ]);
    }

    /**
     * Logout user (revoke token)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Revoke all tokens
        $request->user()->tokens()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}
