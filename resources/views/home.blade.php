@extends('layouts.app')

@section('content')
<div class="container">
    <form method="POST" action="{{ route('shorturl.store') }}">
        @csrf

        <div class="">
            <div class="input-group mb-3">
                <input id="original_url" type="text" class="form-control @error('original_url') is-invalid @enderror"
                    name="original_url" value="{{ old('original_url') }}" autocomplete="original_url" autofocus
                    placeholder="Type Original Url Here" required>
                <button class="input-group-text btn btn-success" id="inputGroup-sizing-default">Generate Short URL</button>

                @error('original_url')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

    </form>
    <div class="text-danger">
        @if($errors->any())
        {{ implode('', $errors->all(':message')) }}
    @endif
    </div>
    
</div>
@endsection