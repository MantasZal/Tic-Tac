<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function update(Request $req)
    {
        $id = $req->id;
        $change = $req->change;

        $user = User::findOrFail($id);
        $user->rank = max(0, $user->rank + $change);
        $user->save();

        return response()->json([
            'message' => 'Rank updated successfully.',
            'new_rank' => $user->rank,
        ]);
    }
}
