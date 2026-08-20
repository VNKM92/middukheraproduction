@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-12 px-4">
  <h1 class="text-2xl font-bold">Vendor Dashboard</h1>

  <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-4 rounded shadow">
      <div class="text-sm text-gray-500">Total Earnings</div>
      <div class="text-xl font-bold">₹{{ number_format($totalEarnings) }}</div>
    </div>
    <div class="bg-white p-4 rounded shadow">
      <div class="text-sm text-gray-500">My Packages</div>
      <div class="text-xl font-bold">{{ $packages->count() }}</div>
    </div>
    <div class="bg-white p-4 rounded shadow">
      <div class="text-sm text-gray-500">Bookings</div>
      <div class="text-xl font-bold">{{ $bookings->count() }}</div>
    </div>
  </div>

  <div class="mt-8">
    <h2 class="text-lg font-semibold">My Packages</h2>
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach($packages as $p)
        <div class="bg-white p-4 rounded shadow">
          <img src="{{ $p->image_path }}" class="w-full h-32 object-cover rounded">
          <div class="mt-2 font-semibold">{{ $p->name }}</div>
          <div class="text-sm text-gray-500">₹{{ number_format($p->price_min) }} - ₹{{ number_format($p->price_max) }}</div>
        </div>
      @endforeach
    </div>
  </div>

  <div class="mt-8">
    <h2 class="text-lg font-semibold">Recent Bookings</h2>
    <div class="mt-4 space-y-3">
      @foreach($bookings as $b)
        <div class="bg-white p-3 rounded shadow flex justify-between items-center">
          <div>
            <div class="font-semibold">{{ $b->user->name ?? 'Guest' }} — {{ $b->package->name ?? 'Package' }}</div>
            <div class="text-sm text-gray-500">{{ $b->booking_date }} • ₹{{ number_format($b->amount) }} • {{ ucfirst($b->payment_status) }}</div>
          </div>
          <div>
            <div class="text-sm">Status: <strong>{{ ucfirst($b->status) }}</strong></div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>
@endsection
