<?php

namespace App\Http\Controllers;

use App\Models\RoomBlock;
use App\Models\RoomDetail;
use Illuminate\Http\Request;

class RoomBlockController extends Controller
{
    public function index()
    {
        $blocks = RoomBlock::with('details.hotel')
            ->orderBy('from', 'desc')
            ->paginate(15);

        // return $blocks;
        return view('admin.blocks.list', compact('blocks'));
    }

    public function createBlock($id)
    {
        $room = RoomDetail::with('hotel')->findOrFail($id);

        session()->put('redirect_after_add_block', url()->previous());

        return view('admin.blocks.add', compact('room'));
    }

    public function storeBlock(Request $request)
    {
        $request->validate([
            'room_detail_id' => 'required|exists:room_details,id',
            'from' => 'required|date|after_or_equal:today',
            'to' => 'required|date|after:from',
            'reason' => 'nullable|string|max:255',
        ]);

        // return $request;

        RoomBlock::create($request->all());

        return redirect(session()->get('redirect_after_add_block'))->with('success', 'Room Block created.');
    }

    public function destroy(RoomBlock $roomblock)
    {
        $roomblock->delete();

        return back()->with('success', 'Room Blocked Removed');
    }
}
