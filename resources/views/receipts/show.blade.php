@extends('layouts.app')


@section('content')
<div class="container">
<h3>Receipt</h3>
<div class="card">
<div class="card-body">
<p><strong>Date:</strong> {{ $receipt->date->format('Y-m-d') }}</p>
<p><strong>Vendor:</strong> {{ $receipt->vendor }}</p>
<p><strong>Category:</strong> {{ $receipt->category }}</p>
<p><strong>Amount:</strong> {{ number_format($receipt->amount, 2) }} {{ $receipt->currency }}</p>
<p><strong>Notes:</strong> {{ $receipt->notes }}</p>
</div>
</div>
<a href="{{ route('receipts.index') }}" class="btn btn-link mt-3">Back</a>
</div>
@endsection