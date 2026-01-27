<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $course->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 row">
                    <div class="card">
                        <div class="p-6 m">
                            <h5 class="text-white mt-3">{{ $course->name }}</h5>
                            <p class="underline">{{ Str::limit($course->desc, 100) }}</p>
                            <p class="text-info fw-bold">${{ $course->price }}</p>
                        </div>
                    </div>
        </div>
    </div>
</x-app-layout>
