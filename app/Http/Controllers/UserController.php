<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function getUsers(Request $request){

        $user = new User();
        $user = $user->select([
            'id',
            'name',
            'email',
            'profile_photo_path',
        ])->get();
        $responsData = [
            'status' => true,
            'data' => $user
        ];
        return response()->json($responsData);
    }

    public function getUser($id): JsonResponse
    {

        $user = User::where('id', $id)
            ->first();

        if (empty($user))
            return response()->json([
                'status' => false,
                'message' => 'No data'
            ]);

        return response()->json([
            'status' => true,
            'message' => 'success',
            'detail' => $user
        ]);
    }

    /**
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteUser($id, Request $request): JsonResponse
    {
        $user = auth()->user();
        $user->deleteProfilePhoto();
        $user->tokens->each->delete();
        $user->delete();        

        return response()->json([
            'status' => true,
            'message' => 'User is  deleted successfully'
        ]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $user = auth()->user();

        $data = $request->only(
            'current_password',
            'password',
        );

        $validator = Validator::make($data, [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->messages()]);
        }

        if(!Hash::check($data['current_password'], $user->password)){
            return response()->json(['status' => false, 'errors' => 'The provided password does not match your current password.']);
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
        ])->save();

        return response()->json([
            'status' => true,
            'message' => 'Password is updated'
        ]);
    }

    public function updateProfilePhoto(Request $request): JsonResponse
    {
        $user = auth()->user();

        $input = $request->only(
            'name',
            'email',
            'photo',
        );

        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->messages()]);
        }

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
            ])->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'User Profile is updated'
        ]);
    }

        /**
     * Update the given verified user's profile information.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    protected function updateVerifiedUser($user, array $input)
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
