<!-- Stored in resources/views/login.blade.php -->

@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <button class="btn btn-secondary btn-sm" type="button" href="http://192.168.1.125/api/v1/user/localization">Отправить</button>
        {{ __('navbar.main') }}
@endsection
