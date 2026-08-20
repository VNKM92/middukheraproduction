@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-12 px-4">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold">My Packages</h1>
    <a href="{{ route('vendor.packages.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Create Package</a>
  </div>

  <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($packages as $p)
      <div class="bg-white p-4 rounded shadow">
        <img src="{{ $p->image_path }}" class="w-full h-40 object-cover rounded">
        <div class="mt-2 font-semibold">{{ $p->name }}</div>
        <div class="text-sm text-gray-500">₹{{ number_format($p->price_min) }} - ₹{{ number_format($p->price_max) }}</div>
        <div class="mt-3 flex space-x-2">
          <a href="{{ route('vendor.packages.edit', $p) }}" class="px-3 py-2 bg-yellow-500 text-white rounded">Edit</a>
          <form action="{{ route('vendor.packages.destroy', $p) }}" method="POST">@csrf @method('DELETE')<button class="px-3 py-2 bg-red-600 text-white rounded">Delete</button></form>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
