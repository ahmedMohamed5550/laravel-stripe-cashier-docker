<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Courses') }}
        </h2>
    </x-slot>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 row">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <strong>Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (request('message'))
                <div class="alert alert-{{ request('message') == 'Payment Successful.' ? 'success' : 'danger' }} alert-dismissible fade show mb-4" role="alert">
                    <strong>Message: </strong> {{ request('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (count($courses) > 0)
                @foreach ($courses as $course)
                    <div class="card col-4 text-white border-secondary mb-3 p-3">
                        <div class="p-6 m">
                            <a href="{{ route('courses.show', $course) }}">
                                <h5 class="text-white mt-3">{{ $course->name }}</h5>
                            </a>
                            <p class="">{{ Str::limit($course->desc, 100) }}</p>
                            <p class="text-info fw-bold">({{ $course->formatPrice() }})</p>
                            @if ($cart && $cart->courses->contains($course))
                            <a href="{{ route('cart.remove',$course) }}" class="btn btn-sm btn-danger w-100">Remove From Cart</a>
                            @else
                            <a href="{{ route('cart.add',$course) }}" class="btn btn-sm btn-primary w-100">Add To Cart</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
