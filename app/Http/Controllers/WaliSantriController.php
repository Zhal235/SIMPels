<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Santri;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class WaliSantriController extends Controller
{
    /**
     * Display a listing of the wali santri users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $waliSantris = User::role('wali_santri')
            ->withCount('santri')
            ->orderBy('name')
            ->paginate(15);
        
        return view('wali-santri.index', compact('waliSantris'));
    }

    /**
     * Show the form for creating a new wali santri user.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get all santri that don't have a wali yet
        $santris = Santri::whereNull('user_id')
                    ->orWhere('user_id', 0)
                    ->orderBy('nama_santri')
                    ->get();
        
        return view('wali-santri.create', compact('santris'));
    }

    /**
     * Store a newly created wali santri user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'santri_ids' => 'required|array'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create the user
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            
            // Assign role
            $user->assignRole('wali_santri');
            
            // Link santris to this wali
            Santri::whereIn('id', $request->santri_ids)
                ->update([
                    'user_id' => $user->id,
                    'email_orangtua' => $request->email
                ]);
            
            DB::commit();
            
            return redirect()->route('wali-santri.index')
                ->with('success', 'Wali santri berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified wali santri user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $waliSantri = User::findOrFail($id);
        $santris = Santri::where('user_id', $id)->get();
        
        return view('wali-santri.show', compact('waliSantri', 'santris'));
    }

    /**
     * Show the form for editing the specified wali santri user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $waliSantri = User::findOrFail($id);
        $assignedSantris = Santri::where('user_id', $id)->get();
        $availableSantris = Santri::whereNull('user_id')
                            ->orWhere('user_id', 0)
                            ->orderBy('nama_santri')
                            ->get();
        
        return view('wali-santri.edit', compact('waliSantri', 'assignedSantris', 'availableSantris'));
    }

    /**
     * Update the specified wali santri user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
            'santri_ids' => 'required|array'
        ]);
        
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed'
            ]);
        }
        
        DB::beginTransaction();
        
        try {
            $user = User::findOrFail($id);
            $user->name = $request->name;
            $user->email = $request->email;
            
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            
            $user->save();
            
            // Clear all previous santri associations
            Santri::where('user_id', $user->id)
                ->update([
                    'user_id' => null,
                    'email_orangtua' => null
                ]);
            
            // Link selected santris to this wali
            Santri::whereIn('id', $request->santri_ids)
                ->update([
                    'user_id' => $user->id,
                    'email_orangtua' => $request->email
                ]);
            
            DB::commit();
            
            return redirect()->route('wali-santri.index')
                ->with('success', 'Wali santri berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified wali santri user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $user = User::findOrFail($id);
            
            // Clear santri associations
            Santri::where('user_id', $user->id)
                ->update([
                    'user_id' => null,
                    'email_orangtua' => null
                ]);
            
            // Delete the user
            $user->delete();
            
            DB::commit();
            
            return redirect()->route('wali-santri.index')
                ->with('success', 'Wali santri berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reset the password for a wali santri user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function resetPassword($id)
    {
        $waliSantri = User::findOrFail($id);
        
        return view('wali-santri.reset-password', compact('waliSantri'));
    }

    /**
     * Update the password for a wali santri user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ]);
        
        try {
            $user = User::findOrFail($id);
            $user->password = Hash::make($request->password);
            $user->save();
            
            return redirect()->route('wali-santri.index')
                ->with('success', 'Password wali santri berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
