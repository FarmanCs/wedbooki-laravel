<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @foreach($stats as $label => $value)
        <div class="bg-white rounded-xl p-4 shadow">
            <p class="text-sm text-gray-500">{{ Str::headline($label) }}</p>
            <p class="text-2xl font-bold">{{ $value }}</p>
        </div>
    @endforeach
</div>
