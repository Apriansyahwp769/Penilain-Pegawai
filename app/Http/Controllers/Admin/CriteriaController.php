<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Criterion;
use Illuminate\Http\Request;

class CriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $criteria = Criterion::orderBy('created_at', 'desc')->get();
        return view('admin.criteria.index', compact('criteria'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.criteria.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0|max:100',
            'category' => 'required|string|max:255',
            'status' => 'required|boolean'
        ]);

        try {
            Criterion::create($validated);
            return redirect()->route('admin.criteria.index')
                ->with('success', 'Kriteria berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat kriteria: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Criterion $criterion)
    {
        return view('admin.criteria.show', compact('criterion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Criterion $criterion)
    {
        return response()->json($criterion);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Criterion $criterion)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0|max:100',
            'category' => 'required|string|max:255',
            'status' => 'required|boolean'
        ]);

        try {
            $criterion->update($validated);
            return redirect()->route('admin.criteria.index')
                ->with('success', 'Kriteria berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui kriteria: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Criterion $criterion)
    {
        try {
            $criterion->delete();
            return redirect()->route('admin.criteria.index')
                ->with('success', 'Kriteria berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus kriteria: ' . $e->getMessage());
        }
    }
}
