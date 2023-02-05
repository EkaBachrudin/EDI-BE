<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\blogCategory;
use App\Models\category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    public function getAll(Request $request)
    {
        $query = Blog::query();

        if ($search = $request->input('search')) {
            $query->where('title', 'LIKE', '%' . $search . '%');
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy('created_at', $sort);
        }

        $perPage = $request->input('perPage', 9999);
        $currentPage = $request->input('currentPage', 1);
        $total = $query->count();

        $result = $query->offset(($currentPage - 1) * $perPage)->limit($perPage)->with('categories')->latest()->get();

        return response()->json([
            'message' => 'Success Get All Blog !',
            'totalData' => $total,
            'perPage' => $perPage,
            'currentPage' => $currentPage,
            'last_page' => ceil($total / $perPage),
            'data' => $result,
        ]);
    }

    public function getOne($id)
    {
        $data = Blog::with('categories')->find($id);
        return response()->json($data);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'body' => 'required',
            'category' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'message' => "Input Error"]);
        }

        $url = $this->curl_get_contents("https://source.unsplash.com/random/1920x1080");

        $input = ['title' => $request->input('title'), 'body' => $request->input('body'), 'image' => $url->urls->regular];
        $data = Blog::create($input);

        $reqCategories = $request->input('category');

        foreach ($reqCategories as $item) {
            blogCategory::create([
                'blog_id'      => $data->id,
                'category_id'  => (int) $item,
            ]);
        }

        return response()->json([
            'message' => 'Success Created !',
            'data' => $data,
        ]);
    }

    public function update(Request $request, Blog $blog)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'body' => 'required',
            'category' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'message' => "Input Error"]);
        }

        $input = ['title' => $request->input('title'), 'body' => $request->input('body')];

        $blog->update($input);

        $data = Blog::find($blog->id);

        $reqCategories = $request->input('category');

        blogCategory::where('blog_id', $data->id)->delete();

        foreach ($reqCategories as $item) {

            blogCategory::create([
                'blog_id'      => $data->id,
                'category_id'  => (int) $item,
            ]);
        }

        return response()->json([
            'message' => 'Success updated !',
            'data' => $data,
        ]);
    }

    public function delete($id)
    {
        $data  = Blog::find($id);
        blogCategory::where('blog_id', $data->id)->delete();

        $data->delete();

        return response()->json([
            'message' => 'Delete Successfuly !',
        ]);
    }

    public function getAllCategories()
    {
        $data = category::get();
        return response()->json([
            'message' => 'Success Get All Categories !',
            'data' => $data,
        ]);
    }


    public function curl_get_contents($url)
    {
        $twoRandomPhotosOfSomePeoples = \Unsplash::randomPhoto()
            ->orientation('landscape')
            ->toJson();

        return $twoRandomPhotosOfSomePeoples;
    }
}
