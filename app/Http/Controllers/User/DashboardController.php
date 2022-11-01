<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('user.dashboard');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveVideo(Request $request) {
        $video = $request->file('video');
        $name = uniqid() . '.mp4';
        $path = '/upload/' . $name;
        $request->file('video')->move(public_path('/upload/'), $name);
        $video = Video::create([
            'name' => $path,
            'user_id' => auth()->id(),
        ]);
        return response()->json(['success' => 'Video Uploaded Successfully',
            'file' => $video
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getUserName(): JsonResponse
    {
        $users = User::where('id', auth()->id())->get();
        return response()->json(['users' => $users]);
    }

    /**
     * @return JsonResponse
     */
    public function getVideo(): JsonResponse
    {
        $videos = Video::where('user_id', auth()->id())->get();
        return response()->json(['videos' => $videos]);
    }

    /**
     * @return JsonResponse
     */
    public function getTextList(): JsonResponse
    {
        $templates = Template::all();
        $userName = auth()->user()->name;
        foreach($templates as $template){

            $template->text = str_replace(':name',$userName, $template->text);
        }
        return response()->json(['templates' => $templates]);
    }
}
