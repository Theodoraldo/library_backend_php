<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Resources\V1\UserResource;
use Exception;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = UserResource::collection(User::paginate());
            return response()->json($users, Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function show(String $id)
    {
        try {
            $user = new UserResource(User::findOrFail($id));
            return response()->json($user, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User details not found'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response([
                'error' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $defaultImagePath = 'default.png';
        $newImagePath = date('Y-m-d_H-i-s') . '.png';

        Storage::disk('images')->copy($defaultImagePath, $newImagePath);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => $newImagePath,
        ]);

        return response([
            'message' => 'New mobile user created successfully',
        ], Response::HTTP_CREATED);
    }

    public function signin(Request $request)
    {
        try {
            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return response([
                    'error' => ['Invalid credentials']
                ], Response::HTTP_UNAUTHORIZED);
            }

            $token = $request->user()->createToken('passport', ['*'], now()->addWeek(1))->plainTextToken;
            $cookie = cookie('jwt', $token, 60 * 24 * 7); // 1 week
        } catch (\Exception $e) {
            return response([
                'error' => ['Request failed: ' . $e->getMessage(), 'code' => $e->getCode()]
            ], Response::HTTP_BAD_REQUEST);
        }

        return response([
            'message' => $token,
        ])->withCookie($cookie);
    }

    public function update(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);

            $validator = Validator::make($request->all(), [
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return response([
                    'error' => $validator->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $data = $request->except(['image', 'type', 'password', 'email', 'name']);
            $user->fill($data);

            if ($request->hasFile('image')) {
                if ($user->image && Storage::disk('images')->exists($user->image)) {
                    Storage::disk('images')->delete($user->image);
                }
                $image = $request->file('image');
                $imageName = $image->getClientOriginalName();
                $path = $image->storeAs('/', $imageName, 'images');
                $user->image = $path;
            }
            $user->save();
            return response()->json("User record updated successfully", Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User details not found'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['error' => 'Internal Server Error' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function signout(Request $request)
    {
        $cookie = cookie('jwt', '', -1); // delete cookie

        $request->user()->tokens()->delete();

        return response([
            'message' => 'User signed out successfully',
        ])->withCookie($cookie);
    }
}
