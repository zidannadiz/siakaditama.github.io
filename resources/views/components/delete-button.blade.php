@props(['action', 'message' => 'Yakin ingin menghapus?', 'class' => 'text-red-600 hover:text-red-900'])

<form action="{{ $action }}" method="POST" class="inline delete-form" data-message="{{ $message }}">
    @csrf
    @method('DELETE')
    <button type="button" class="{{ $class }} delete-btn" style="cursor: pointer;">Hapus</button>
</form>

