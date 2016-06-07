@extends('layout.app')

@section('content')

<div class="container">
@if (count($items) > 0)
    <!-- content goes here -->
@else
    <div class="well">Nothing to show here.</div>
@endif
</div>