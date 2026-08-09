@extends('layouts.admin')

@section('content')
<h3>Preview Exam: {{ $package->name }}</h3>

@foreach ($questions as $q)
    <div class="card mb-3">
        <div class="card-body">
            <strong>
                {{ $loop->iteration }}.
                {{ $q->question }}
            </strong>

            <ul class="mt-2">
                <li>A. {{ $q->option_a }}</li>
                <li>B. {{ $q->option_b }}</li>
                <li>C. {{ $q->option_c }}</li>
                <li>D. {{ $q->option_d }}</li>
            </ul>

            <div class="text-muted">
                <small>
                    Section: {{ $q->section }} |
                    Answer key: <strong>{{ $q->answer_key }}</strong>
                </small>
            </div>
        </div>
    </div>
@endforeach
@endsection
