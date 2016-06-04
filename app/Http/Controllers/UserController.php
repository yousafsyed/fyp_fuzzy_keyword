<?php
/**
 * User Controller
 */
namespace App\Http\Controllers;

use App\User;

class UserController extends Controller
{

    /**
     * Confirm email and verify account
     * @param  String $token
     * @return
     */
    public function confirmEmail($token)
    {
        try {
            User::whereToken($token)->firstOrFail()->confirmEmail();
            $status  = "status";
            $message = 'You are now confirmed. Please login.';
        } catch (\Exception $e) {
            $status  = "error";
            $message = "Invalid Verification Token";
        }

        return redirect('login')->with($status, $message);
    }
}
