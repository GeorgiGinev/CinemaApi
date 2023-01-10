<?php

namespace Modules\Cinema\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
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
        $request->validate([
            'attributes.description' => 'required',
            'attributes.name' => 'required',
            'attributes.images' => 'required',
            'attributes.logo' => 'required',
            'attributes.capacity' => 'required',
        ]);

        //get user id
        $userId = $this->getUserId($request->bearerToken());

        $attributes['capacity'] = json_encode($attributes['capacity']);
        $attributes['logo'] = $this->verifyAndUpload($attributes['logo']);
        $attributes['images'] = json_encode($this->verifyAndUpload($attributes['images']), JSON_UNESCAPED_SLASHES);

        //create new cinema and cinema location and link them
        $cinema =  Cinema::create($attributes);

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
        $cinema = Cinema::findOrFail($id);

        if (!$cinema) {
            return null;
        }
        $cinema['images'] = json_decode($cinema['images'], true);
        $cinema['images'] = $this->retriveImages($cinema['images']);
        $cinema['logo'] = $this->retriveImages($cinema['logo']);

        return $cinema;
    }

    /**
     * Get one
     * @return null
     */
    public function getMany()
    {
        $cinemas =  Cinema::paginate(15);
        foreach ($cinemas as $cinema) {
            $cinema['images'] = json_decode($cinema['images'], true);
            $cinema['images'] = $this->retriveImages($cinema['images']);
            $cinema['logo'] = $this->retriveImages($cinema['logo']);
        }
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
        $attributes = $request->input('attributes');
        $attributes['capacity'] = json_encode($attributes['capacity'], JSON_UNESCAPED_SLASHES);
        $this->deleteImages($attributes['logo']);
        $this->deleteImages($attributes['images']);
        $attributes['logo'] = $this->verifyAndUpload($attributes['logo']);
        $attributes['images'] = json_encode($this->verifyAndUpload($attributes['images']), JSON_UNESCAPED_SLASHES);

        return Cinema::where('id', $id)->update($attributes);
    }

    /**
     * Delete
     * @param int $id
     * @return Renderable
     */
    public function delete($id)
    {
        $record =  Cinema::findOrFail($id);

        $record->images = json_decode($record->images, true);
        $this->deleteImages($record->logo);
        $this->deleteImages($record->images);
        $record->cinemaLocation()->delete();

        return $record->delete();
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
}
