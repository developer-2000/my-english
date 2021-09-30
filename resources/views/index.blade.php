@extends('layouts.app')

@section('content')
    <div class="tab-content">
        <!-- на стартовой -->
        <div class="tab-pane active" id="default_pane">
            <!-- верхнее меню -->
            <div class="card card-primary card-outline top_menu">
                <div class="card-header"> </div>
            </div>
            <div class="block-tab">
                <ul>
                    <ol>
                        <a href="https://www.youtube.com/channel/UCcnjJu-ejZlLaz-OwpBd7dQ/featured" target="_blank">
                            БЕСПЛАТНЫЙ РЕПЕТИТОР - АНГЛИЙСКИЙ ЯЗЫК. УРОКИ 2
                        </a>
                    </ol>
                </ul>
            </div>
        </div>

        {{-- view для components --}}
        <router-view></router-view>

    </div>
@endsection

@section('js')
@stop
