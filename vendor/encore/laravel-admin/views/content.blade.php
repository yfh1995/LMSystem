@extends('admin::index')

@section('content')
    <section class="content-header">

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>错误！</strong>
                <br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h1>
            {{ $header or trans('admin::lang.title') }}
            <small>{{ $description or trans('admin::lang.description') }}</small>
        </h1>

    </section>

    <section class="content">

        {!! $content !!}

    </section>
@endsection