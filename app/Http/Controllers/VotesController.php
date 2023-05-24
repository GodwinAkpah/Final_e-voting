<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    State,
    Position,
    Votes,
    User,
    Contestant,
    LocalGov,
    Voters,
};
use Illuminate\Support\Facades\Auth;

class VotesController extends Controller
{
    // generate resource controller functions
    public function index()
    {
        $votes = Votes::all();
        $user = Auth::user();
        $role = $user->getRoleNames()->first();
        $states = State::all();
        $local_govs = LocalGov::all();
        $contestants = Contestant::all();
        $voter = Voters::where('user_id', $user->id)->first();
        $state_id = $voter->state_id;
        $local_gov_id = $voter->local_gov_id;
        // dd($voter);
        // get positions where the state_id is equal to the state_id of the voter or the local_gov_id is equal to the local_gov_id of the voter or the position has no state_id and no local_gov_id
        // $positions = Position::where('is_active', 1)->where('states_id', $voter->state_id)
        //     ->orWhere('local_govs_id', $voter->local_gov_id)
        //     ->orWhereNull('states_id', $voter->state_id)
        //     ->orWhereNull('local_govs_id', $voter->local_gov_id)
        //     ->get();
        $positions = Position::where(function ($query) use ($state_id, $local_gov_id) {
            $query->where('states_id', $state_id)
                  ->orWhere('local_govs_id', $local_gov_id)
                  ->orWhere(function ($query) {
                        $query->whereNull('states_id')
                              ->whereNull('local_govs_id');
                  });
        })
        ->where('is_active', true)
        ->get();
        // dd($positions);
        // get the positons that the voter has voted for
        $voted_positions = Votes::where('user_id', $user->id)->paginate(10);
        // get the positions that the voter has not voted for
        $not_voted_positions = Position::where(function ($query) use ($state_id, $local_gov_id) {
            $query->where('states_id', $state_id)
                  ->orWhere('local_govs_id', $local_gov_id)
                  ->orWhere(function ($query) {
                        $query->whereNull('states_id')
                              ->whereNull('local_govs_id');
                  });
        })
        ->where('is_active', true) // add condition for active positions
        ->whereDoesntHave('votes', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->get();

        // dd($voter->state_id);
        // dd($not_voted_positions);
        // dd($not_voted_positions);
        return view('vote.index', compact('votes',
                                          'role',
                                          'user',
                                          'positions',
                                          'states',
                                          'local_govs',
                                          'contestants',
                                          'voter',
                                          'voted_positions',
                                          'not_voted_positions'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request, Position $position)
    {
        // get the user
        $user = Auth::user();
        // dd($position);
        // add the user_id to the request and the position_id to the request
        // $position_id = Position::where('id', $position)->first();
        if(Votes::where('user_id', $user->id)->where('position_id', $position->id)->first())
        {
            return back()->with('warning', 'Voted position already!');
        } else {
            $request->request->add(['user_id' => $user->id]);
        $request->request->add(['position_id' => $position->id]);
        // add the v123456789012345678oter state_id and local_gov_id to the request
        $voter = Voters::where('user_id', $user->id)->first();
        $request->request->add(['state_id' => $voter->state_id]);
        $request->request->add(['local_gov_id' => $voter->local_gov_id]);
        // Make a new vote with the request data
        // dd($request->all());
        $vote = Votes::create($request->all());
        // if the position_id in the request is123456789012345678 equal to
        return redirect()->route('vote')->with('success', 'Vote casted successfully');
        }

    }

    public function show(Votes $votes)
    {
        //
    }

    public function edit(Votes $votes)
    {
        //
    }

    public function update(Request $request, Votes $vote)
    {

        if($request->contestant_id == NULL)
        {
            return redirect()->route('vote')->withError('Please select a contestant.');
        } else {
        //update the vote
        // add the user_id to the request and the position_id to the request
        $user = Auth::user();
        $request->request->add(['user_id' => $user->id]);
        $request->request->add(['position_id' => $vote->position_id]);
        // add the voter state_id and local_gov_id to the request
        $voter = Voters::where('user_id', $user->id)->first();
        $request->request->add(['state_id' => $voter->state_id]);
        $request->request->add(['local_gov_id' => $voter->local_gov_id]);

        $vote->update($request->all());
        return redirect()->route('vote')->with('success', 'Vote updated successfully');
        }
    }



}

