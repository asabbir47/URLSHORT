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
                <button class="input-group-text btn btn-success" id="inputGroup-sizing-default">Generate Short
                    URL</button>

                @error('original_url')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

    </form>
    <div class="text-danger mt-4">
        @if($errors->any())
        <h5>{!! implode('', $errors->all(':message')) !!}</h5>
        @endif
    </div>
    <div class="text-success mt-4">
        @if(session('success'))
        <h5>{!!session('success')!!}</h5>
        @endif
    </div>

    <div class="mt-5">
        <h4 class="text-center">Generated Urls</h4>
        <table class="table" id="generated_url_table" style="">
            <thead>
                <tr>
                    <th>Short Url</th>
                    <th>Destination</th>
                </tr>
            </thead>
            <tbody>
                @foreach($urls as $url)
                <tr>
                    <td>
                        <a target="_blank" href="http://{{request()->getHttpHost()}}/{{$url->short_url}}">
                            http://{{request()->getHttpHost()}}/{{$url->short_url}}
                        </a>
                    </td>
                    <td>
                        {{$url->original_url}}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex">
            {{ $urls->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection