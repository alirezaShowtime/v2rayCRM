@extends('terminal.layout')

@section('style')
    <style>
        .output {
            resize: none;
            color: dimgray;
            text-wrap: nowrap;
        }

        .output::-webkit-scrollbar {
            width: 7px;
            height: 7px;

        }

        /* Track */
        .output::-webkit-scrollbar-track {
            background-color: transparent;
        }

        /* Handle */
        .output::-webkit-scrollbar-thumb {
            background: dimgray;
            border-radius: 10px;
        }
    </style>
@endsection



@section('body')
    <div class="w-full h-full flex flex-row justify-center items-start gird-cols-3">


        <div class="h-full w-full flex flex-col bg-gray-950">


            <div class="bg-blue-700 h-16 flex flex-row justify-center items-center order-first">

                <h class="font-bold text-white black">Terminal</h>

            </div>

            <form method="POST" action="{{ route('terminal.run-command') }}" class="bg-gray-950 h-20 flex flex-row justify-center items-center flex-row m-2 relative sm:order-first order-last">
                @method("post")
                <input name="command" placeholder="Enter commaned" autocomplete="off" value="{{ $command }}"
                    class="text-white font-normal text-lg w-full h-full py-1 px-5 text-white bg-gray-950 outline-none  border-4 border-gray-900 focus:border-blue-700 rounded-xl">

                <div class="py-5 pr-3 h-20 absolute right-0">
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-auto px-5 py-2.5 text-center h-full">RUN</button>
                </div>

            </form>

            <p class="font-normal text-lg text-white mx-3">{{ $path }}</p>

            <div class="w-full h-full bg-gray-950 text-white overflow-hidden p-1 flex flex-row">

                <textarea class="w-full h-full bg-gray-950 p-3 outline-none border-none output" style="text-wrap: nowrap;" readonly>
                {{ $output }}
                </textarea>

            </div>


        </div>


    </div>
@endsection
