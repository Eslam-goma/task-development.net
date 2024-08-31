<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'verification_code' => 'required|integer',
        ]);

        $user = User::where('phone', $request->phone)
                    ->where('verification_code', $request->verification_code)
                    ->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid verification code.'], 400);
        }

        $user->is_verified = true;
        $user->verification_code = null; // Clear the verification code after successful verification
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Account verified successfully.',
            'token' => $token,
            'user' => $user,
        ]);
    }
}
