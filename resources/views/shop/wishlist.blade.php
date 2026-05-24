@extends('layouts.app')
@section('title', 'المفضلة')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-white mb-8">المفضلة</h1>

    @if($items->count())
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($items as $item)
                @include('components.product-card', ['product' => $item->product])
            @endforeach
        </div>
    @else
        <div class="text-center py-16">
            <p class="text-white/40 text-lg mb-4">المفضلة فارغة</p>
            <a href="{{ route('shop') }}" class="text-brand-red hover:underline">تصفح المتجر</a>
        </div>
    @endif
</div>
@endsection
