<?php
// app/Http/Controllers/Admin/SiklusController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siklus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SiklusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $siklusList = Siklus::orderBy('created_at', 'desc')->get();
        return view('admin.siklus.index', compact('siklusList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.siklus.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'tanggal_finalisasi' => 'required|date|after:tanggal_selesai',
            'status' => 'required|in:draft,active,completed'
        ], [
            'nama.required' => 'Nama siklus wajib diisi',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai',
            'tanggal_finalisasi.required' => 'Tanggal finalisasi wajib diisi',
            'tanggal_finalisasi.after' => 'Tanggal finalisasi harus setelah tanggal selesai',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status tidak valid'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal membuat siklus. Periksa kembali input Anda.');
        }

        try {
            Siklus::create($request->all());

            return redirect()->route('admin.siklus.index')
                ->with('success', 'Siklus berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Siklus $siklus)
    {
        return view('admin.siklus.show', compact('siklus'));
    }

    /**
     * Show the form for editing the specified resource.
     * Return JSON untuk AJAX request dari modal edit
     */
    public function edit($id)
    {
        $siklus = Siklus::findOrFail($id);
        
        return response()->json([
            'id' => $siklus->id,
            'nama' => $siklus->nama,
            'tanggal_mulai' => $siklus->tanggal_mulai->format('Y-m-d'),
            'tanggal_selesai' => $siklus->tanggal_selesai->format('Y-m-d'),
            'tanggal_finalisasi' => $siklus->tanggal_finalisasi->format('Y-m-d'),
            'status' => $siklus->status
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Debug log
        Log::info('Update request received', [
            'id' => $id,
            'data' => $request->all()
        ]);

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'tanggal_finalisasi' => 'required|date|after:tanggal_selesai',
            'status' => 'required|in:draft,active,completed'
        ], [
            'nama.required' => 'Nama siklus wajib diisi',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai',
            'tanggal_finalisasi.required' => 'Tanggal finalisasi wajib diisi',
            'tanggal_finalisasi.after' => 'Tanggal finalisasi harus setelah tanggal selesai',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status tidak valid'
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed', ['errors' => $validator->errors()]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Gagal mengupdate siklus. Periksa kembali input Anda.');
        }

        try {
            $siklus = Siklus::findOrFail($id);
            
            // Log before update
            Log::info('Before update', ['siklus' => $siklus->toArray()]);
            
            // Update data
            $siklus->update([
                'nama' => $request->nama,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'tanggal_finalisasi' => $request->tanggal_finalisasi,
                'status' => $request->status
            ]);
            
            // Refresh untuk mendapatkan data terbaru dari database
            $siklus->refresh();
            
            // Log after update
            Log::info('After update', ['siklus' => $siklus->toArray()]);

            return redirect()->route('admin.siklus.index')
                ->with('success', 'Siklus berhasil diupdate!');
                
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Siklus not found: ' . $id);
            return redirect()->route('admin.siklus.index')
                ->with('error', 'Data siklus tidak ditemukan.');
                
        } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $siklus = Siklus::findOrFail($id);
            $siklusName = $siklus->nama;
            
            // Hapus data
            $siklus->delete();
            
            return redirect()->route('admin.siklus.index')
                ->with('success', "Siklus '{$siklusName}' berhasil dihapus!");
                
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.siklus.index')
                ->with('error', 'Data siklus tidak ditemukan.');
                
        } catch (\Exception $e) {
            Log::error('Error deleting siklus: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->route('admin.siklus.index')
                ->with('error', 'Terjadi kesalahan saat menghapus: ' . $e->getMessage());
        }
    }
}