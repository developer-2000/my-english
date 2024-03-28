@extends('layouts.app')

@section('content')
    <div class="tab-content">
        {{-- view для components --}}
        <router-view></router-view>
    </div>
@endsection

@section('js')
@stop
