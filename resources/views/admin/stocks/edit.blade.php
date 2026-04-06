@extends('layouts.admin')

@section('content')
<h2>Edit Stock Item</h2>

@if($errors->any())
    <div style="color: red;">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('stocks.update', $stock->id) }}" method="POST">
    @csrf
    @method('PUT')
    <label>ID No:</label><br>
    <input type="text" name="id_no" value="{{ old('id_no', $stock->id_no) }}" required><br><br>

    <label>Description:</label><br>
    <input type="text" name="description" value="{{ old('description', $stock->description) }}" required><br><br>

    <label>Unit:</label><br>
    <input type="text" name="unit" value="{{ old('unit', $stock->unit) }}" required><br><br>

    <label>Total:</label><br>
    <input type="number" name="total" value="{{ old('total', $stock->total) }}" min="0" required><br><br>

    <label>Stock:</label><br>
    <input type="number" name="stock" value="{{ old('stock', $stock->stock) }}" min="0" required><br><br>

    <label>Category:</label><br>
    <select name="category_id" required>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ $stock->category_id == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select><br><br>

    <label>Hidden (Admin Only):</label>
    <input type="checkbox" name="hidden" value="1" {{ $stock->hidden ? 'checked' : '' }}><br><br>

    <button type="submit" style="padding:10px 16px; border-radius:8px; border:1px solid #2563eb; background:#2563eb; color:#fff; cursor:pointer; font-weight:700; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(37,99,235,.2)';" onmouseout="this.style.transform=''; this.style.boxShadow='';" onclick="this.style.transform='translateY(0)';">Update Stock</button>
</form>
@endsection
