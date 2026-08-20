@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-12 px-4">
  <h1 class="text-2xl font-bold">Vendor Registration</h1>
  <form action="{{ route('vendor.register') }}" method="POST" class="mt-6 bg-white p-6 rounded shadow">
    @csrf
    <div class="grid gap-4">
      <div>
        <label class="block text-sm">Your Name</label>
        <input name="name" required class="w-full p-2 border rounded" />
      </div>
      <div>
        <label class="block text-sm">Email</label>
        <input name="email" type="email" required class="w-full p-2 border rounded" />
      </div>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm">Password</label>
          <input name="password" type="password" required class="w-full p-2 border rounded" />
        </div>
        <div>
          <label class="block text-sm">Confirm Password</label>
          <input name="password_confirmation" type="password" required class="w-full p-2 border rounded" />
        </div>
      </div>
      <div>
        <label class="block text-sm">Studio / Vendor Name</label>
        <input name="studio_name" required class="w-full p-2 border rounded" />
      </div>
      <div>
        <label class="block text-sm">Description</label>
        <textarea name="description" class="w-full p-2 border rounded"></textarea>
      </div>
      <div>
        <button class="px-4 py-2 bg-indigo-600 text-white rounded">Register as Vendor</button>
      </div>
    </div>
  </form>
</div>
@endsection
