<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormMail;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FrontPagesController extends Controller
{
    public function index()
    {
        $data['testimonials'] = Testimonial::where('status', 1)->get();
        return view('front.home')->with($data);
    }

    public function contactUsForm()
    {
        return view('front.contact-us');
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => [
                'nullable',
                'regex:/^(\+?\d{1,3}\s?)?(\(?\d{3}\)?[\s\-]?)?\d{3}[\s\-]?\d{4}$/'
            ],
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string'
        ]);

        try {

            $data = $request->all();

            $admin_email = getValuesByKey('admin_email') ?? 'admin@gmail.com';

            Mail::to($admin_email)->send(new ContactFormMail($data));

            return back()->with('success', 'Your message has been sent successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }
}
