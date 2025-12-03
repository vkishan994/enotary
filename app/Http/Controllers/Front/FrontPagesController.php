<?php

namespace App\Http\Controllers\Front;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FrontPagesController extends Controller
{
    public function index()
    {
        $data['testimonials'] = Testimonial::where('status', 1)->get();
        return view('front.home')->with($data);
    }
}
