<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Customer;

class CustomerController extends Controller
{
    public function validateEmail(Request $request) {
        $customer = Customer::where('email', $request->input('email'))->first();
        if ($customer) {
            if ($customer->email_verified) {
                // We'll return the email hashed here as well.
                return response()->json(['message' => 'Email already verified.'], 200);
            }
            // Send verification email logic would go here.
            return response()->json(['message' => 'Verification email resent.'], 401);
        }

        // Add customer.
        $customer = Customer::create([
            'email' => $request->input('email'),
            'name' => '',
        ]);
        // Send verification email logic would go here.
        return response()->json(['message' => 'Verification email sent.'], 401);
    }
}
