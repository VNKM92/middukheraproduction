@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-12 px-4">
  <h1 class="text-2xl font-bold">Vendors Management</h1>
  <div class="mt-6 space-y-4">
    @foreach($vendors as $v)
      <div class="bg-white p-4 rounded shadow flex justify-between items-center">
        <div>
          <div class="font-semibold">{{ $v->name }}</div>
          <div class="text-sm text-gray-500">{{ $v->slug }} • {{ $v->user->email ?? '—' }}</div>
          <div class="text-sm mt-1">Status: <strong>{{ ucfirst($v->status) }}</strong></div>
        </div>
        <div>
          <form method="POST" action="{{ route('admin.vendor.updateStatus', $v) }}">
            @csrf
            <select name="status" class="p-2 border rounded">
              @foreach(['pending','approved','suspended'] as $s)
                <option value="{{ $s }}" {{ $v->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
              @endforeach
            </select>
            <button class="ml-2 px-3 py-2 bg-indigo-600 text-white rounded">Update</button>
          </form>
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
