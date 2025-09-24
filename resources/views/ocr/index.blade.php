{{-- Quick inline Tailwind (Play CDN) - good for prototypes --}}
<script src="https://cdn.tailwindcss.com"></script>

<div class="max-w-3xl mx-auto py-8 px-4">
  <div class="bg-white shadow sm:rounded-lg p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">OCR — Upload product label</h2>

    <form action="{{ route('ocr.scan') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
      @csrf

      <label class="block">
        <span class="text-sm font-medium text-gray-700">Upload product label image</span>

        <!-- Styled file input: wrapper to make it look nicer -->
        <div class="mt-2 flex items-center gap-3">
          <input
            type="file"
            name="image"
            accept="image/*"
            required
            class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4
                   file:rounded-md file:border-0 file:text-sm file:font-semibold
                   file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
          />
        </div>
        <p class="mt-1 text-xs text-gray-500">PNG, JPG or GIF — max 10MB</p>
      </label>

      <div>
        <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white text-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-300">
          Scan
        </button>
      </div>
    </form>

    {{-- Show validation errors --}}
    @if ($errors->any())
      <div class="mt-4 bg-red-50 border border-red-200 p-3 rounded">
        <ul class="text-red-700 text-sm space-y-1">
          @foreach ($errors->all() as $err)
            <li>• {{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if (session('error'))
      <div class="mt-4 bg-red-50 border border-red-200 p-3 rounded text-red-700">
        {{ session('error') }}
      </div>
    @endif
  </div>
</div>
