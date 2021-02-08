<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('File Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if(session()->has('error'))
                    <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-red-500">
                        <span class="inline-block align-middle mr-8">
                            <b class="capitalize">Error!</b> {{ session()->get('error') }}
                        </span>
                    </div>
                @endif
                @if(session()->has('success'))
                    <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-green-500">
                        <span class="inline-block align-middle mr-8">
                            <b class="capitalize">Success!</b> {{ session()->get('success') }}
                        </span>
                    </div>
                @endif

                <a class="bg-blue-300 hover:bg-blue-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center mb-4" href="{{ route('files.create') }}">Upload File</a>

                <table class="shadow-lg bg-white table-auto" style="width: 100%">
                    <tr>
                        <th class="bg-blue-100 border text-left px-8 py-4">File Name</th>
                        <th class="bg-blue-100 border text-left px-8 py-4">Uploaded</th>
                        <th class="bg-blue-100 border text-left px-8 py-4">Actions</th>
                    </tr>
                    @forelse($files as $file)
                        <tr>
                            <td class="border px-8 py-4">{{ $file->original_filename }}</td>
                            <td class="border px-8 py-4">{{ norm_date($file->created_at) }}</td>
                            <td class="border px-8 py-4">
                                <a href="{{ route('files.show', $file) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center">
                                    <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/></svg>
                                    <span>Download</span>
                                </a>
                                <form method="POST" action="{{ route('files.destroy', $file) }}" class="inline">
                                    @method('DELETE')
                                    @csrf
                                    <button class="bg-red-300 hover:bg-red-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center" type="submit">
                                        <svg class="fill-current w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="border px-8 py-4">No Uploaded Files</td>
                        </tr>
                    @endforelse
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
