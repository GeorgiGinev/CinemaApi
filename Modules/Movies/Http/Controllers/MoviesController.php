<?php

namespace Modules\Movies\Http\Controllers;

use App\Traits\ImageTrait;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use \App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Movies\Entities\Movie;
use Modules\Movies\Entities\MovieSlot;
use Illuminate\Database\Eloquent\SoftDeletes;

class MoviesController extends Controller
{
    use SoftDeletes;
    use ImageTrait;
    
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $keywords = $request->input('keywords');

        $movies = null;

        if($request->input('with_trashed')) {
            $movies = Movie::where('owner_id', $user->id)->onlyTrashed()->where(function ($q) use ($keywords) {
                if ($keywords) {
                    $q->where('name', 'like', "%{$keywords}%");
                }
            })->orderBy('id', 'DESC')->paginate(15);
        } else {
            $movies = Movie::where('owner_id', $user->id)->where(function ($q) use ($keywords) {
                if ($keywords) {
                    $q->where('name', 'like', "%{$keywords}%");
                }
            })->orderBy('id', 'DESC')->paginate(15);
        }

        $movies->transform(function ($movie) {
            $movie->image = $this->retriveImages($movie->image);

            return $movie->transform();
        });

        return $movies;
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $user = $request->user();
        //get request attributes and relationships
        $attributes = $request->input('attributes');
        
        $relationships = $request->input('relationships');

        //validate data
        $this->createValidator($request->all())->validate();

        $attributes['image'] = $this->verifyAndUpload($attributes['image']);

        $movie = new Movie($attributes);
        $movie->owner_id = $user->id;

        $movie->push();

        // foreach($relationships['slots']['data'] as $slot) {

        // }

        // $movieSlot = MovieSlot::create($relationships['movieSlot']['data']);
        // $cinema->cinema_location_id = $cinemaLocation->id;
        // $cinema->owner_id = $userId;
        // $cinema->push();

        return response()->json([
            'status : ' => $movie
        ]);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $movie = Movie::withTrashed()->where('owner_id', $user->id)->findOrFail($id)->transform();

        if(!$movie) {
            return null;
        }

        if($movie->attributes->image) {
            $movie->attributes->image = $this->retriveImages($movie->attributes->image);
        }

        return $movie;
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $attributes = $request->input('attributes');
        $this->createValidator($attributes)->validate();

        $user = $request->user();
        $movie = Movie::withTrashed()->where('owner_id', $user->id)->findOrFail($id);

        if(!$movie) {
            return null;
        }

        $this->deleteImages($movie->image);
        $attributes['image'] = $this->verifyAndUpload($attributes['image']);



        return Movie::withTrashed()->where('id', $id)->update($attributes);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $movie =  Movie::withTrashed()->findOrFail($id);

        if (!$movie) {
            return null;
        }

        return $movie->delete();
    }

    /**
     * Restore the resource
     * @param mixed $id of the resource
     * @return mixed
     */
    public function restore($id) {
        $movie =  Movie::withTrashed()->findOrFail((int)$id);

        if (!$movie) {
            return null;
        }

        return $movie->restore();
    }

    private function createValidator($data) {
        return Validator::make($data, [
            'name' => 'required',
            'image' => 'required'
        ]);
    }
}
