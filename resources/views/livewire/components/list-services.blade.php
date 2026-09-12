<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
    {{-- Flash --}}
    <x-flash-message class="col-span-full mb-4" :timeout="1000"/>

    @foreach(\App\Enums\ServicesEnum::cases() as $service)
        <?php $currentService = $userServices->where('name', $service)->first(); ?>
        <x-service-card :service="$service" :current-service="$currentService"/>
    @endforeach
</div>
