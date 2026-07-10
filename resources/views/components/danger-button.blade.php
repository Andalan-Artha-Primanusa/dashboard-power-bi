<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-xl border border-[#BD9B75] bg-[#BD9B75] px-4 py-2.5 text-xs font-bold uppercase tracking-wide text-white transition hover:opacity-95 focus:outline-none focus:ring-2 focus:ring-[#BD9B75] focus:ring-offset-2']) }}>
 {{ $slot }}
</button>
