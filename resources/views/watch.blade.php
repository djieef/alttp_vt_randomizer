@extends('layouts.default', ['title' => __('navigation.start_watching') . ' - '])

@section('content')
<h1>{{ __('watch.header') }}</h1>
<div class="card card-body bg-light">
    <div class="card border-info mt-4">
        <div class="card-header bg-info">
            <h3 class="card-title text-white">{{ __('watch.cards.tournament.header') }}</h3>
        </div>
        <div class="card-body">
            @foreach (__('watch.cards.tournament.sections') as $section)
            <h4>{{ $section['header'] }}</h4>
                @foreach ($section['content'] as $block)
                    <p>{!! $block !!}</p>
                @endforeach
            @endforeach
        </div>
    </div>

    <div class="card border-info mt-4">
        <div class="card-header bg-info">
            <h3 class="card-title text-white">{{ __('watch.cards.youtube.header') }}</h3>
        </div>
        <div class="card-body">
            @foreach (__('watch.cards.youtube.content') as $block)
                <p>{!! $block !!}</p>
            @endforeach
        </div>
    </div>
</div>
@overwrite
