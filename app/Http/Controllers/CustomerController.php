<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Customer;

/**
 * CustomerController
 */
class CustomerController extends Controller
{
    /**
     * Validates the email of a customer. Adds it to the database if it doesn't exist.
     * Determines if the email is verified or not.
     *
     * @param Request $request The api request.
     * @return void
     */
    public function validateEmail(Request $request) {
        $customer = Customer::where('email', $request->input('email'))->first();
        if ($customer) {
            if ( ! empty( $request->input('name') ) && $customer->name !== $request->input('name') ) {
                $customer->name = $request->input('name');
                $customer->save();
            }
            if ($customer->email_verified) {
                // We'll return the email hashed here as well.
                return response()->json(['message' => 'Email already verified.'], 200);
            }
            // Resend verification email.
            $this->sendEmailVerification($customer);
            return response()->json(['message' => 'Verification email resent.'], 401);
        }

        // Add customer.
        $customer = Customer::create([
            'email' => $request->input('email'),
            'name' => $request->input('name') ?? '',
        ]);
        // Send verification email.
        $this->sendEmailVerification($customer);

        return response()->json(['message' => 'Verification email sent.'], 401);
    }

    public function sendEmailVerification(Customer $customer) {
        // Logic to send email verification.
    }
}
