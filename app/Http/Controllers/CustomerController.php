<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Mail\ConfirmEmail;
use Illuminate\Support\Facades\Mail;

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
        Mail::to($customer->email)->send(new ConfirmEmail($customer));
    }

    public function confirmationPage(Request $request) {
        $email = $request->query('email');
        $token = $request->query('token');
        $message = '';
        $messageType = '';

        if (empty($email) || empty($token)) {
            $message = 'Invalid confirmation link.';
            $messageType = 'error';
        }

        $customer = Customer::where('email', $email)->first();
        if (! $customer) {
            $message = 'Customer not found.';
            $messageType = 'error';
        }

        if (md5($customer->email) !== $token) {
            $message = 'Invalid confirmation token.';
            $messageType = 'error';
        }

        if ($customer->email_verified) {
            $message = 'Email already verified.';
            $messageType = 'info';
        }

        if (empty($message)) {
            // Mark email as verified.
            $customer->email_verified = true;
            $customer->save();

            $message = 'Email successfully verified.';
            $messageType = 'success';
        }

        // Display a view passing the message and message type.
        return view('confirmationPage', [
            'message' => $message,
            'messageType' => $messageType,
        ]);
    }
}
