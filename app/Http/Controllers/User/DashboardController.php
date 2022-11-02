<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\Video;


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
    public function saveVideo(Request $request): JsonResponse
    {
        $name = uniqid() . '.mp4';
        $path = '/storage/upload/' . $name;
        $request->file('video')->move(public_path('/storage/upload/'), $name);
        $video = Video::create([
            'name' => $path,
            'user_id' => auth()->id(),
        ]);
        return response()->json([
            'success' => 'Video Uploaded Successfully',
            'file' => $video
        ]);
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
        $templates = Template::get(['name', 'id']);
        return response()->json(['templates' => $templates]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getSingleTemplate(Request $request): JsonResponse
    {
        $id = $request->input('id');
        $template = Template::find($id);
        $template->text = str_replace(':name', auth()->user()->name, $template->text);
        return response()->json(['text' => $template->text]);
    }
}
