<html>
<body>

@guest
    @include('learners.public.partials.header')
@else
    @role('superadmin')
        @include('creator.superadmin.partials.header')
        @include('creator.superadmin.partials.sidebar')
    @endrole

    @role('admin')
        @include('creator.admin.partials.header')
        @include('creator.admin.partials.sidebar')
    @endrole

    @role('dealer')
        @include('learners.dealer.partials.header')
        @include('learners.dealer.partials.sidebar')
    @endrole

    @role('staff')
        @include('learners.staff.partials.header')
    @endrole
@endguest

</body>
</html>
