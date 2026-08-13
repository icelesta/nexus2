<div
    x-data="{ open: false }"
    class="relative inline-block text-left"
>

    {{-- Action Button --}}
    <button
        type="button"
        @click="open = ! open"
        @click.away="open = false"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none"
    >
        <svg xmlns="http://www.w3.org/2000/svg"
             class="h-4 w-4"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M6 12h12M6 6h12M6 18h12"/>
        </svg>

        <span>Action</span>

        <svg xmlns="http://www.w3.org/2000/svg"
             class="h-4 w-4"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">

            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"/>
        </svg>

    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        x-transition
        class="absolute right-0 z-50 mt-2 w-48 rounded-lg border border-gray-200 bg-white shadow-xl"
        style="display:none;"
    >

        @if($viewUrl)

            <a
                href="{{ $viewUrl }}"
                class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-100"
            >
                👁
                <span>View</span>
            </a>

        @endif

        @if($editUrl)

            <a
                href="{{ $editUrl }}"
                class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-100"
            >
                ✏
                <span>Edit</span>
            </a>

        @endif

        @if($deleteUrl)

            <form
                action="{{ $deleteUrl }}"
                method="POST"
                onsubmit="return confirm('Delete this record?')"
            >

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="flex w-full items-center gap-3 px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50"
                >
                    🗑
                    <span>Delete</span>
                </button>

            </form>

        @endif

    </div>

</div>