<?php

namespace Modules\Cinema\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Cinema\Entities\Cinema;
use Modules\Cinema\Entities\CinemaLocation;
use App\Models\Token as Token;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ImageTrait;

class CinemaController extends Controller
{
    use SoftDeletes;
    use ImageTrait;

    /**
     * Create
     * @param Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        //get request attributes and relationships
        $attributes = $request->input('attributes');
        
        $relationships = $request->input('relationships');

        //validate data
        $this->createValidator($request->all())->validate();

        //get user id
        //$userId = $this->getUserId($request->bearerToken());
        $userId = $request->user()->id;

        $attributes['capacity'] = json_encode($attributes['capacity']);
        $attributes['logo'] = $this->verifyAndUpload($attributes['logo']);
        $attributes['images'] = json_encode($this->verifyAndUpload($attributes['images']), JSON_UNESCAPED_SLASHES);

        //create new cinema and cinema location and link them
        $cinema =  new Cinema($attributes);

        $cinemaLocation = CinemaLocation::create($relationships['cinemaLocation']['attributes']);
        $cinema->cinema_location_id = $cinemaLocation->id;
        $cinema->owner_id = $userId;
        $cinema->push();

        return $cinema;
    }

    /**
     * Get one
     * @param int $id
     * @return
     */
    public function getOne($id)
    {
        $cinema = Cinema::withTrashed()->findOrFail($id);

        if (!$cinema) {
            return null;
        }
        $cinema->images = json_decode(($cinema->images), true);
        $cinema->images = $this->retriveImages($cinema->images);
        $cinema->logo = $this->retriveImages($cinema->logo);
        $cinema->capacity = json_decode($cinema->capacity, true);

        return response()->json($cinema->transform(['cinemaLocation']));
    }

    public function getAll(Request $request)
    {
        $keywords = $request->input('keywords');
        $cinemas = Cinema::where(function ($q) use ($keywords) {
            if ($keywords) {
                $q->where('name', 'like', "%{$keywords}%");
            }
        })->orderBy('id', 'DESC')->paginate(15);

        $cinemas->transform(function ($cinema) {
            $cinema->images = json_decode(($cinema->images), true);
            $cinema->images = $this->retriveImages($cinema->images);
            $cinema->logo = $this->retriveImages($cinema->logo);
            $cinema->capacity = json_decode($cinema->capacity, true);

            return $cinema->transform(['cinemaLocation', 'owner']);
        });

        return $cinemas;
    }

    /**
     * Get one
     * @return null
     */
    public function getMany(Request $request)
    {
        $user = $request->user();
        $cinemas = null;
        $keywords = $request->input('keywords');
        
        if($request->input('with_trashed')) {
            $cinemas = Cinema::where('owner_id', $user->id)->onlyTrashed()->where(function ($q) use ($keywords) {
                if($keywords) {
                    $q->where('name', 'like', "%{$keywords}%");
                }
              })->orderBy('id', 'DESC')->paginate(15);
        } else {
            $cinemas = Cinema::where('owner_id', $user->id)->where(function ($q) use ($keywords) {
                if($keywords) {
                    $q->where('name', 'like', "%{$keywords}%");
                }
              })->orderBy('id', 'DESC')->paginate(15);
        }

        $cinemas->transform(function ($cinema) {
            $cinema->images = json_decode(($cinema->images), true);
            $cinema->images = $this->retriveImages($cinema->images);
            $cinema->logo = $this->retriveImages($cinema->logo);
            $cinema->capacity = json_decode($cinema->capacity, true);

            return $cinema->transform(['cinemaLocation', 'owner']);
        });
        return $cinemas;
    }

    /**
     * Update
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, int $id)
    {
        $cinema = Cinema::where('id', $id)->first()->transform(['cinemaLocation']);
        
        if(!$cinema) {
            return null;
        }

        //validate data
        $this->createValidator($request->all())->validate();
        
        $attributes = $request->input('attributes');
        $relationships = $request->input('relationships');

        $attributes['capacity'] = json_encode($attributes['capacity'], JSON_UNESCAPED_SLASHES);

        $this->deleteImages($cinema->attributes->logo);
        $this->deleteImages(json_decode($cinema->attributes->images, true));
        $attributes['logo'] = $this->verifyAndUpload($attributes['logo']);
        $attributes['images'] = json_encode($this->verifyAndUpload($attributes['images']), JSON_UNESCAPED_SLASHES);

        CinemaLocation::where('id', (int)$cinema->relationships->cinemaLocation->id)->update($relationships['cinemaLocation']['attributes']);

        return Cinema::where('id', $id)->update($attributes);
    }

    /**
     * Delete
     * @param int $id
     * @return Renderable
     */
    public function delete($id)
    {
        $cinema =  Cinema::findOrFail($id);

        if (!$cinema) {
            return null;
        }

        return $cinema->delete();
    }

    /**
     * Restore the resource
     * @param mixed $id of the resource
     * @return mixed
     */
    public function restore($id) {
        $cinema =  Cinema::withTrashed()->findOrFail((int)$id);

        if (!$cinema) {
            return null;
        }

        return $cinema->restore();
    }

    /**
     * Save logo in storage
     * @param $logo
     * @return string
     */
    public function getUserId($token): string
    {
        return $userId = Token::where('token', $token)->first()->tokenable_id;
    }

    private function createValidator($data) {
        return Validator::make($data, [
            'attributes.name' => 'required',
            'attributes.images' => 'required',
            'attributes.logo' => 'required',
            'attributes.capacity' => 'required',
        ]);
    }
}
