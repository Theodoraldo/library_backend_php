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
use App\Http\Resources\V1\UserResource;
use Exception;
use App\Exceptions\ExceptionHandler;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = UserResource::collection(User::paginate());
            return response()->json($users, Response::HTTP_OK);
        } catch (Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }


    public function show(String $id)
    {
        try {
            $user = new UserResource(User::findOrFail($id));
            return response()->json($user, Response::HTTP_OK);
        } catch (Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function signup(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
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

            return response()->json(['status' => Response::HTTP_CREATED, 'message' => 'New mobile user created successfully'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function signin(Request $request)
    {
        try {
            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return response()->json([
                    'status' => Response::HTTP_UNAUTHORIZED,
                    'message' => 'Invalid credentials',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $token = $request->user()->createToken('passport', ['*'], now()->addWeek(1))->plainTextToken;
            $cookie = cookie('jwt', $token, 60 * 24 * 7); // 1 week
            return response([
                'message' => $token,
            ])->withCookie($cookie);
        } catch (\Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function update(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);

            $validator = Validator::make($request->all(), [
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
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
            return response()->json(['status' => Response::HTTP_OK, 'message' => 'User record updated successfully !!!'], Response::HTTP_OK);
        } catch (Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }

    public function signout(Request $request)
    {
        try {
            $cookie = cookie('jwt', '', -1);

            $request->user()->tokens()->delete();

            return response()->json(['status' => Response::HTTP_OK, 'message' => 'User signed out successfully !!!'], Response::HTTP_OK)->withCookie($cookie);
        } catch (Exception $e) {
            return ExceptionHandler::handleException($e);
        }
    }
}
