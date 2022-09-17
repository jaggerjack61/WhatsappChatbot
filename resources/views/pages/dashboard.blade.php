@extends('layouts.master')

@section('style')
    @livewireScripts

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <x-livewire-alert::scripts />
    <title> Dashboard </title>
    @livewireStyles
@endsection


@section('content')

    @livewire('dashboard')

@endsection

@section('scripts')
    @livewireScripts
@endsection
