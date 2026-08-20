@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-12 px-4">
  <h1 class="text-2xl font-bold">Create Package</h1>
  <form action="{{ route('vendor.packages.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 bg-white p-6 rounded shadow">
    @csrf
    <div class="grid gap-4">
      <div>
        <label class="block text-sm">Name</label>
        <input name="name" required class="w-full p-2 border rounded" />
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm">Price Min</label>
          <input name="price_min" type="number" required class="w-full p-2 border rounded" />
        </div>
        <div>
          <label class="block text-sm">Price Max</label>
          <input name="price_max" type="number" required class="w-full p-2 border rounded" />
        </div>
      </div>
      <div>
        <label class="block text-sm">Description</label>
        <textarea name="description" required class="w-full p-2 border rounded"></textarea>
      </div>
      <div>
        <label class="block text-sm">Features (comma separated)</label>
        <input name="features" class="w-full p-2 border rounded" />
      </div>
      <div>
        <label class="block text-sm">Image File (or URL)</label>
        <input name="image" type="file" class="w-full p-2 border rounded" />
        <input name="image_url" class="w-full p-2 border rounded mt-2" placeholder="Or provide external image URL" />
      </div>
      <div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded">Create</button>
      </div>
    </div>
  </form>
</div>
@endsection
