
@extends('layouts.app')

@section('title', 'Audit Log per User')

@section('header')
    📜 Audit Log untuk {{ $user->name }}
@endsection

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2">Waktu</th>
                <th class="px-4 py-2">Aksi</th>
                <th class="px-4 py-2">Detail</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $log->created_at }}</td>
                    <td class="px-4 py-2">{{ $log->action ?? '-' }}</td>
                    <td class="px-4 py-2 text-xs text-gray-600">{{ $log->payload ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center py-4 text-gray-500">Belum ada log untuk user ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">{{ $logs->links() }}</div>
</div>
@endsection
