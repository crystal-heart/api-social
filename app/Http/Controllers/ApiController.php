<?php

namespace App\Http\Controllers;

use App\Note;
use App\User;
use Auth;
use Hash;
use Illuminate\Http\Request;
use JWTAuth;
use JWTAuthException;

class ApiController extends Controller
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $token = null;
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['invalid_email_or_password'], 422);
            }
        } catch (JWTAuthException $e) {
            return response()->json(['failed_to_create_token'], 500);
        }
        $id = Auth::id();
        return response()->json(compact('token', 'id'));
    }

    public function register(Request $request)
    {
        try {

            $user = $this->user->create([
                'name' => $request->input('name', 'user'),
                'email' => $request->get('email'),
                'password' => Hash::make($request->get('password')),
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'User created successfully',
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function myProfile()
    {
        $user = Auth::user();

        if ($user) {
            return response($user, Response::HTTP_OK);
        }

        return response(null, Response::HTTP_BAD_REQUEST);
    }

    public function updateProfile()
    {

    }

    public function myStories()
    {
        $list = Note::where('user_id', Auth::id())->get();

        return response()->json(compact('list'));
    }
    public function showStory($id)
    {

        $note = Note::where('user_id', Auth::id())->where('id', $id)->first();

        return response()->json(compact('note'));

    }
    public function createStory(Request $request)
    {
        try {

            $note = new Note();
            $note->title = $request->title;
            $note->content = $request->content;
            $note->created_at = new \DateTime();
            $note->user_id = Auth::id();
            $note->save();
            return response()->json($note);
        } catch (\Exception $e) {
            throw $e;

            return response()->json([]);
        }
    }

    public function updateStory(Request $request)
    {
        try {

            $note = Note::findOrFail($request->id);
            $note->title = $request->title;
            $note->content = $request->content;
            $note->updated_at = new \DateTime();
            $note->user_id = Auth::id();
            $note->save();
            return response()->json($note);
        } catch (\Exception $e) {
            throw $e;

            return response()->json([]);
        }
    }

    public function deleteStory($id)
    {
        try {

            $note = Note::findOrFail($id);
            $note->delete();
            return response()->json(['satus' => 'ok']);
        } catch (\Exception $e) {
            //throw $th;

            return response()->json(['satus' => 'ok']);
        }

    }

    public function synData(Request $request)
    {

        $list = json_decode($request->data, true);
        $offLineListId = [];

        foreach ($list as $item) {
            array_push($offLineListId, $item['id']);
        }
        $myNoteId = Note::where('user_id', Auth::id())->pluck('id')->toArray();

        $deletedItem = array_diff($myNoteId, $offLineListId);

        foreach ($deletedItem as $item) {
            try {
                $item = Note::findOrFail($item);
                $item->delete();
            } catch (\Exception $e) {
                return response()->json(['satus' => 'false']);
            }
        }

        foreach ($list as $item) {
            try {
                $note = Note::findOrFail($item['id']);
                $note->title = $item['title'];
                $note->content = $item['content'];
                $note->save();
            } catch (\Exception $e) {

                $newNote = new Note();
                $newNote->user_id = Auth::id();
                $newNote->title = $item['title'];
                $newNote->content = $item['content'];
                $newNote->created_at = new \DateTime();
                $newNote->save();
            }
        }
        return response()->json(['satus' => 'ok']);
    }

}
