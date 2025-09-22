<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return News::with(['user:id,name','category:id,name'])
                ->latest()
                ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fields = $request->validate([
            'title'         => ['required', 'string'],
            'content'       => ['required', 'string'],
            'category_id'   => ['required', 'exists:categories,id'],
            'img'           => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ]);

        $path = $request->file('img')->store('news', 'public');
        $fields['img'] = $path;

        $data = $request->user()->news()->create($fields);

        return ['News' => $data];
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news)
    {
        $news->load(['user:id,name', 'category:id,name']);

        $cardFields = ['id', 'title', 'img', 'user_id', 'category_id', 'created_at'];

        $byUser = News::where('user_id', $news->user_id)
            ->where('id', '!=', $news->id)
            ->orderByDesc('created_at')
            ->limit(4)
            ->get($cardFields);

        $byUser->load(['user:id,name', 'category:id,name']);

        $byCategory = News::where('category_id', $news->category_id)
            ->where('id', '!=', $news->id)
            ->whereNotIn('id', $byUser->pluck('id'))
            ->orderByDesc('created_at')
            ->limit(4)
            ->get($cardFields);

        $byCategory->load(['user:id,name', 'category:id,name']);

        return response()->json([
            'data' => $news,
            'related' => [
                'by_user' => $byUser->values(),
                'by_category' => $byCategory->values()
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, News $news)
    {
        $request->validate([
            'title'       => ['required','string'],
            'content'     => ['required','string'],
            'category_id' => ['required','exists:categories,id'],
            'img'         => ['nullable','image','mimes:jpg,jpeg,png'],
        ]);

        $data = $request->only(['title','content','category_id']);

        if ($request->hasFile('img')) {
            $data['img'] = $request->file('img')->store('news','public');
        }

        $data['user_id'] = $news->user_id;

        $news->update($data);
        return $news->load('category','user');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        //
    }
}
