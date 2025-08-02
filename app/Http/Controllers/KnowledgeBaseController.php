<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBase;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $knowledgeBases = KnowledgeBase::orderBy('created_at', 'desc')->get();
        return view('knowledge-base.index', compact('knowledgeBases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('knowledge-base.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'category' => 'nullable|string|max:100',
        ]);

        KnowledgeBase::create($request->all());

        return redirect()->route('knowledge-base.index')
            ->with('success', 'Data pengetahuan berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(KnowledgeBase $knowledgeBase)
    {
        return view('knowledge-base.show', compact('knowledgeBase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KnowledgeBase $knowledgeBase)
    {
        return view('knowledge-base.edit', compact('knowledgeBase'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KnowledgeBase $knowledgeBase)
    {
        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'category' => 'nullable|string|max:100',
        ]);

        $knowledgeBase->update($request->all());

        return redirect()->route('knowledge-base.index')
            ->with('success', 'Data pengetahuan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KnowledgeBase $knowledgeBase)
    {
        $knowledgeBase->delete();

        return redirect()->route('knowledge-base.index')
            ->with('success', 'Data pengetahuan berhasil dihapus!');
    }
}
