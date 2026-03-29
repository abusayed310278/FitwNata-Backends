@extends('layouts.adminApp')

@section('title', 'Create Recipe')

@section('content')
<div class="content-wrapper">
    <div class="page-header d-flex justify-content-between align-items-center">
        <h3 class="page-title">Create New Recipe</h3>
        <a href="{{ route('nutritionist.recipes.index') }}" class="btn btn-secondary"><i class="ti-arrow-left"></i> Back</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('nutritionist.recipes.store') }}" enctype="multipart/form-data">
        @csrf
        @include('nutritionist.recipes.form')
        <button type="submit" class="btn btn-primary"><i class="ti-check"></i> Save Recipe</button>
    </form>
</div>
@endsection