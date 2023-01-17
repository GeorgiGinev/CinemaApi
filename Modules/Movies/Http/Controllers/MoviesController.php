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
    
    public function allMovies(Request $request) {
        $keywords = $request->input('keywords');
        
        $movies = Movie::whereHas('slots')->where(function ($q) use ($keywords) {
            if ($keywords) {
                $q->where('name', 'like', "%{$keywords}%");
            }
        })->orderBy('id', 'DESC')->paginate(15);

        $movies->transform(function ($movie) {
            $movie->image = $this->retriveImages($movie->image);

            $movie->cinema = $movie->getCinema($movie->slots[0])->cinema;

            return $movie->transform(['cinema']);
        });

        return $movies;
    }
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
        $this->createValidator($request->input('attributes'))->validate();

        $attributes['image'] = $this->verifyAndUpload($attributes['image']);

        $movie = new Movie($attributes);
        $movie->owner_id = $user->id;

        $movie = Movie::create($movie->toArray());

        if($relationships) {
            foreach($relationships['slots']['data'] as $slot) {
                $movieSlot = new MovieSlot($slot['attributes']);
                $movieSlot->cinema_id = $slot['relationships']['cinema']['id'];
                $movieSlot->movie_id = $movie->id;
    
                MovieSlot::create($movieSlot->toArray());
            }
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $movie = Movie::withTrashed()->with('slots')->where('owner_id', $user->id)->findOrFail($id);

        if(!$movie) {
            return null;
        }
        
        /**  
         * Generate movie resources with slots->cinema
         */
        $slots = $movie->slots;
        $object = [];
        foreach($slots as $slot) {
            array_push($object, $slot->transform(['cinema']));
        }

        $movie = $movie->transform();
        $movie->relationships->slots = null;
        $movie->relationships->slots['data'] = $object;

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
        $relationships = $request->input('relationships');

        $this->createValidator($attributes)->validate();

        $user = $request->user();
        $movie = Movie::withTrashed()->where('owner_id', $user->id)->with('slots')->findOrFail($id);

        if(!$movie) {
            return null;
        }

        $this->deleteImages($movie->image);
        $attributes['image'] = $this->verifyAndUpload($attributes['image']);

        if($relationships) {
            $relSlots = collect($relationships['slots']['data']);

            $idsNotFound = [];

            foreach($movie->slots as $slot) {
                $desired_object = $relSlots->first(function($item) use ($slot) {
                    return (int)$item['id'] == $slot->id;
                });

                if(!$desired_object) {
                    array_push($idsNotFound, $slot->id);
                }
            }

            if (count($idsNotFound) > 0) {
                MovieSlot::whereIn('id', $idsNotFound)->delete();
            }
        } else {
            $idsNotFound = [];
            foreach ($movie->slots as $slot) {
                array_push($idsNotFound, $slot->id);
            }

            MovieSlot::whereIn('id', $idsNotFound)->delete();
        }

        if($relationships) {
            foreach($relationships['slots']['data'] as $slot) {
                $movieSlot = new MovieSlot($slot['attributes']);
                $movieSlot->cinema_id = $slot['relationships']['cinema']['id'];
                $movieSlot->movie_id = $movie->id;

                if($slot['id']) {
                    MovieSlot::find((int) $slot['id'])->update($movieSlot->toArray());
                } else {
                    MovieSlot::create($movieSlot->toArray());
                }
            }
        }
        
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
