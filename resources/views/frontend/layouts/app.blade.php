@include('frontend.partials.header')
@include('frontend.partials.sidebar')

<div class="main-panel">
    <div class="content">
        @yield('content')
    </div>
    @include('frontend.partials.footer')
</div>

@stack('scripts')