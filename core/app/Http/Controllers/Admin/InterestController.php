<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    public function index()
    {
        $pageTitle = __('All Interests');
        $emptyMessage = __('No interest found');
        $interests = Interest::latest()->paginate(getPaginate());

        return view('admin.interest.index', compact('pageTitle', 'emptyMessage', 'interests'));
    }

    public function saveInterest(Request $request, $id=0)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $interest = new Interest();
        $notification =  __('Interest created successfully');

        if($id){
            $interest = Interest::findOrFail($id);
            $interest->status = $request->status ? 1 : 0;
            $notification = __('Interest updated successfully');
        }

        $interest->name = $request->name;
        $interest->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);

    }
}
