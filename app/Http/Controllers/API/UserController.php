<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\User;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $this->authorize('isAdmin');
        if (\Gate::allows('isAdmin') || \Gate::allows('isAuthor')) {
            return User::latest()->paginate(10);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('isAdmin');

        $this->validate($request, [
            'name'      => 'required|string|max:191',
            'email'     => 'required|string|email|max:191|unique:users,email',
            'password'  => 'required|string|min:6'
        ]);

        return User::create([
            'name'      => $request['name'],
            'email'     => $request['email'],
            'type'      => $request['type'],
            'bio'       => $request['bio'],
            'photo'     => $request['photo'],
            'password'  => Hash::make($request['password']),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();

        $this->validate($request, [
            'name'      => 'required|string|max:191',
            'email'     => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'password'  => 'sometimes|min:6|nullable'
        ]);

        $previousPhoto = $user->photo;
        if($request->photo != $previousPhoto){
            $name = time().".".explode('/',explode(';',$request->photo)[0])[1];
            \Image::make($request->photo)->save(public_path('img/profile/').$name);

            $request->merge(['photo' => $name]);
            
            $latestPhoto = public_path('img/profile/').$previousPhoto;
            if(file_exists($latestPhoto)){
                @unlink($latestPhoto);
            }
        }

        if (!empty($request->password)) {
            $request->merge(['password' => Hash::make($request['password'])]);
            $user->update($request->all());
        }else{
            $user->update($request->except('password'));
        }

        // return ['message' => $request->photo];
    }
    
    public function profile()
    {
        return auth('api')->user();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('isAdmin');

        $user = User::findOrFail($id);
        $this->validate($request, [
            'name'      => 'required|string|max:191',
            'email'     => 'required|string|email|max:191|unique:users,email,'.$user->id,
            'password'  => 'sometimes|string|min:6'
        ]);
        $user->update($request->all());
        return ['message' => 'Success Update'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('isAdmin');

        $user = User::findOrFail($id);

        // delete user
        $user->delete();

        return ['message' => 'User Deleted'];
    }
}
