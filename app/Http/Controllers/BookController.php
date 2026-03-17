<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('category');

        // SEARCH JUDUL
        if ($request->judul) {
            $query->where('judul', 'like', '%' . $request->judul . '%');
        }

        // FILTER CATEGORY
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $books = $query->get();

        // TOTAL SEMUA BOOK
        $totalBooks = $books->count();

        $categories = Category::all();

        $totalPerCategory = Book::selectRaw('category_id, count(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        return view('books.index', compact(
            'books',
            'categories',
            'totalBooks',
            'totalPerCategory'
        ));
    }

    public function create()
    {
        $categories = Category::all();
        return view('books.create', compact('categories'));
    }

    // ✅ STORE (tambah cover)
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|numeric',
            'judul' => 'required',
            'penulis' => 'required',
            'tahun_terbit' => 'required|numeric',
            'stok' => 'required|numeric',
            'cover' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data = $request->all();

        // upload cover
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $namaFile = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('cover'), $namaFile);
            $data['cover'] = $namaFile;
        }

        Book::create($data);

        return redirect()->route('books.index')
            ->with('success','Data berhasil ditambahkan');
    }

    public function edit(Book $book)
    {
        $categories = Category::all();
        return view('books.edit', compact('book', 'categories'));
    }

    // ✅ UPDATE (update cover)
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'category_id' => 'required|numeric',
            'judul' => 'required',
            'penulis' => 'required',
            'tahun_terbit' => 'required|numeric',
            'stok' => 'required|numeric',
            'cover' => 'image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $data = $request->all();

        // kalau upload cover baru
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $namaFile = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('cover'), $namaFile);
            $data['cover'] = $namaFile;
        }

        $book->update($data);

        return redirect()->route('books.index')
            ->with('success','Data berhasil diupdate');
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()->route('books.index')
            ->with('success','Data berhasil dihapus');
    }
}