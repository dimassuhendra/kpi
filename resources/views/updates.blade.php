@extends($layout)

@section('content')
    <div class="space-y-6">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 bg-gold-500/10 rounded-2xl flex items-center justify-center border border-gold-500/20">
                <i class="fas fa-bullhorn text-gold-600 text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Log Pembaruan</h2>
                <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Informasi perkembangan sistem
                    MyBolo</p>
            </div>
        </div>

        <div
            class="relative space-y-8 before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-emerald-500 before:via-gold-500 before:to-transparent">

            @foreach ($updates as $update)
                <div
                    class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                    <div
                        class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-emerald-600 text-gold-400 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                        <i class="fas fa-rocket text-xs"></i>
                    </div>

                    <div
                        class="w-[calc(100%-4rem)] md:w-[45%] bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:border-gold-400/50 transition-all duration-300">
                        <div class="flex items-center justify-between mb-2">
                            <span
                                class="text-[10px] font-black px-2 py-1 bg-emerald-50 text-emerald-700 rounded-lg uppercase tracking-wider">
                                v{{ $update['version'] }}
                            </span>
                            <time class="font-mono text-[10px] text-slate-400 font-bold">{{ $update['date'] }}</time>
                        </div>

                        <h3 class="text-lg font-bold text-slate-800 mb-1">{{ $update['title'] }}</h3>
                        <p class="text-xs text-slate-500 leading-relaxed mb-4 italic text-justify">
                            "{{ $update['description'] }}"</p>

                        <ul class="space-y-2">
                            @foreach ($update['changes'] as $change)
                                <li class="flex items-start gap-2 text-[11px] text-slate-600 font-medium text-justify">
                                    <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
                                    <span>{{ $change }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="mt-4 pt-4 border-t border-slate-50 flex justify-end">
                            <span class="text-[9px] font-bold text-slate-300 uppercase tracking-tighter italic">MyBolo Dev
                                Team</span>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
@endsection
