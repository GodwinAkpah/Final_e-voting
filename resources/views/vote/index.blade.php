@extends('layout.app', ['pageSlug' => 'votes', 'page' => 'Vote', 'section' => ''])

@section('content')
{{-- If voter status is 0 show a card else  --}}
@if ($voter->status === 0)
    <div class="row mb-3">
        <div class="col-12">
            <div class="card-header ">
                <div class="row">
                    <div class="col-sm-6 text-left">
                        <h2 class="card-title">Sorry {{ ($role === 'admin') ? 'Admin' : 'Voter'; }}, You are not activated, Contact Voting Officals</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
  <div class="page-title-container">
    <div class="row">
      <div class="col-12 col-sm-6">
        <h1 class="mb-0 pb-0 display-4">Vote</h1>
      </div>
      <div class="col-12 col-sm-6 d-flex gap-1 align-items-start justify-content-end">
        <!-- Tour Button Start -->


        {{-- <a href="{{ route('voters.active') }}" type="button" class="btn btn-outline-primary" >Activate Voting</a> --}}
        <!-- Tour Button End -->

        @if (count($not_voted_positions) === 0)
        <form action="{{ route('voter.deactivate', $voter->id) }}" method="post">
            @csrf
            <button type="submit" class="btn btn-outline-primary" >Deactivate Voting</button>
          </form>
        @endif
      </div>
    </div>
  </div>
  <div class="">
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <span>{{ $error }}</span>
            @endforeach
        </div>
    @endif
</div>
  @include('alerts.success')
  @include('alerts.error')
  <div class="card mb-5">
    <div class="card-body">
      <h5 class="card-title">Positions Available</h5>
      <table class="table">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Name</th>
            <th scope="col">Contestants</th>
            <th scope="col"></th>

          </tr>
        </thead>
        <tbody>
            @forelse ($not_voted_positions as $position)
            <tr>
              <th scope="row">{{ $position->id }}</th>
              <td>{{ $position->name }}</td>
              <td>
                {{ $position->contestants->count() }}
              </td>
              <td>
                <div class="d-flex align-items-center justify-content-end">
                  <button data-bs-toggle="modal" data-bs-target="#makeVote{{ $position->id }}"
                    class="btn btn-sm btn-outline-info ms-1" type="button">
                    <span class="text-success">Vote</span>
                  </button>
                  @include('vote.modals.create')
                </div>
              </td>
            </tr>
            @empty
            <th>
                <th scope="row" colspan="6" class="text-center">
                    <div class="text-warning text-center" >No Position available.</div>
                </th>
            </th>

            @endforelse
        </tbody>
      </table>
    </div>
  </div>
  <div class="card mb-5">
    <div class="card-body">
      <h5 class="card-title">Casted Votes</h5>
      <table class="table">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Position</th>
            <th scope="col">Contestant</th>
            <th scope="col">Party</th>
            <th scope="col"></th>

          </tr>
        </thead>
        <tbody>
            @forelse ($voted_positions as $vote)
            <tr>
              <th scope="row">{{ $vote->id }}</th>
              <td>{{ $vote->position->name }}</td>
                <td>
                    {{ $vote->contestant->name }}
                </td>
                <td>
                    {{ $vote->contestant->party }}
                </td>
              <td>
                <div class="d-flex align-items-center justify-content-end">
                  <button data-bs-toggle="modal" data-bs-target="#editVote{{ $vote->id }}"
                    class="btn btn-sm btn-icon btn-icon-start btn-outline-info ms-1" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 20 20"
                      fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                      stroke-linejoin="round" class="acorn-icons acorn-icons-edit-square undefined">
                      <path
                        d="M11 2L5.5 2C4.09554 2 3.39331 2 2.88886 2.33706C2.67048 2.48298 2.48298 2.67048 2.33706 2.88886C2 3.39331 2 4.09554 2 5.5L2 14.5C2 15.9045 2 16.6067 2.33706 17.1111C2.48298 17.3295 2.67048 17.517 2.88886 17.6629C3.39331 18 4.09554 18 5.5 18L14.5 18C15.9045 18 16.6067 18 17.1111 17.6629C17.3295 17.517 17.517 17.3295 17.6629 17.1111C18 16.6067 18 15.9045 18 14.5L18 11">
                      </path>
                      <path
                        d="M15.4978 3.06224C15.7795 2.78052 16.1616 2.62225 16.56 2.62225C16.9585 2.62225 17.3405 2.78052 17.6223 3.06224C17.904 3.34396 18.0623 3.72605 18.0623 4.12446C18.0623 4.52288 17.904 4.90497 17.6223 5.18669L10.8949 11.9141L8.06226 12.6223L8.7704 9.78966L15.4978 3.06224Z">
                      </path>
                    </svg>
                    <span class="d-none d-xxl-inline-block">Edit</span>
                  </button>
                  @include('vote.modals.edit')
                </div>
              </td>
            </tr>
            @empty
            <th>
                <th scope="row" colspan="6" class="text-center">
                    <div class="text-warning text-center" >No Vote casted.</div>
                </th>
            </th>

            @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer py-4">
        <nav class="d-flex justify-content-end" aria-label="...">
            {{ $voted_positions->links() }}
        </nav>
    </div>
  </div>
@endif
@endsection
