@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @auth('admin')
                        @can('level','super')
                        <div class="alert alert-success" role="alert">
                            Show for level Super
                        </div>
                        @endcan

                        @can('level','admin')
                        <div class="alert alert-success" role="alert">
                            Show for level Admin
                        </div>
                        @endcan

                        @can('level',['super','admin'])
                        <div class="alert alert-warning" role="alert">
                            Show for level Super & Admin
                        </div>
                        @endcan

                    @else
                        <div class="alert alert-success" role="alert">
                            Show for User
                        </div>
                    @endauth

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
