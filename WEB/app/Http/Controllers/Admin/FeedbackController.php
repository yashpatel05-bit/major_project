<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::with(['user', 'order'])->latest()->get();
        $averageRating = Feedback::avg('rating') ?: 5.0;
        return view('admin.feedback.index', compact('feedbacks', 'averageRating'));
    }
}
