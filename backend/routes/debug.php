<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Resources\UserResource;

Route::post("/debug-login", function() {
    try {
        $credentials = request(["email", "password"]);
        $result = Auth::attempt($credentials);
        $user = Auth::user();
        $token = $user->createToken("auth-token")->plainTextToken;
        $resource = new UserResource($user->load("role"));
        return response()->json(["user" => $resource->toArray(request()), "token" => $token]);
    } catch (\Throwable $e) {
        return response()->json(["error" => $e->getMessage(), "file" => $e->getFile(), "line" => $e->getLine(), "trace" => $e->getTraceAsString()], 500);
    }
});

