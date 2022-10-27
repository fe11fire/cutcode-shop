@extends('layouts.auth')

@section('title', 'Восстановление пароля')
    

@section('content')
<x-forms.auth-forms title="Восстановление пароля" action="{{ route('signIn') }}" method="POST">
    @csrf
    <x-forms.text-input name="email" type="email" value="{{ old('email') }}" placeholder="E-mail" :isError="$errors->has('email')" required="true"/>
    @error('email')
        <x-forms.error>{{ $message }}</x-forms.error>
    @enderror
    <x-forms.text-input name="password" type="password"  placeholder="Пароль" :isError="$errors->has('password')" required="true"/>
    @error('password')
        <x-forms.error>{{ $message }}</x-forms.error>
    @enderror    
    <x-forms.text-input name="password_confirmation" type="password"  placeholder="Подтверждение пароля" :isError="$errors->has('password_confirmation')" required="true"/>
    @error('password_confirmation')
        <x-forms.error>{{ $message }}</x-forms.error>
    @enderror
    <x-forms.primary-button>Обновить пароль</x-forms.primary-button>
</x-forms.auth-forms>
@endsection[]