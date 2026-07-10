<style>
 :root {
 --arca-brown: #BD9B75;
 --arca-white: #fff;
 --maroon-900: var(--arca-brown);
 --maroon-800: var(--arca-brown);
 --maroon-700: var(--arca-brown);
 --maroon-600: var(--arca-brown);
 --maroon-500: var(--arca-brown);
 --ink: var(--arca-brown);
 }

 html,
 body,
 main,
 section,
 [class*="bg-slate"],
 [class*="bg-gray"],
 [class*="bg-zinc"],
 [class*="bg-neutral"],
 [class*="bg-stone"],
 [class*="bg-red"],
 [class*="bg-orange"],
 [class*="bg-amber"],
 [class*="bg-yellow"],
 [class*="bg-lime"],
 [class*="bg-green"],
 [class*="bg-emerald"],
 [class*="bg-teal"],
 [class*="bg-cyan"],
 [class*="bg-sky"],
 [class*="bg-blue"],
 [class*="bg-indigo"],
 [class*="bg-violet"],
 [class*="bg-purple"],
 [class*="bg-fuchsia"],
 [class*="bg-pink"],
 [class*="bg-rose"],
 [class*="bg-white/"],
 .bg-white {
 background-color: var(--arca-white) !important;
 }

 [class*="bg-maroon"],
 [class*="bg-black"],
 [class*="from-"],
 [class*="via-"],
 [class*="to-"],
 [class*="file:bg-"]::file-selector-button,
 [class*="hover:file:bg-"]::file-selector-button:hover {
 background-color: var(--arca-brown) !important;
 }

 [class*="bg-gradient"],
 [class*="bg-[radial-gradient"],
 [class*="bg-[linear-gradient"] {
 background-image: none !important;
 background-color: var(--arca-brown) !important;
 }

 [class*="text-slate"],
 [class*="text-gray"],
 [class*="text-zinc"],
 [class*="text-neutral"],
 [class*="text-stone"],
 [class*="text-red"],
 [class*="text-orange"],
 [class*="text-amber"],
 [class*="text-yellow"],
 [class*="text-lime"],
 [class*="text-green"],
 [class*="text-emerald"],
 [class*="text-teal"],
 [class*="text-cyan"],
 [class*="text-sky"],
 [class*="text-blue"],
 [class*="text-indigo"],
 [class*="text-violet"],
 [class*="text-purple"],
 [class*="text-fuchsia"],
 [class*="text-pink"],
 [class*="text-rose"],
 [class*="text-maroon"],
 [class*="hover:text-"]:hover,
 [class*="focus:text-"]:focus,
 [class*="active:text-"]:active,
 .text-black {
 color: var(--arca-brown) !important;
 }

 .text-white,
 [class*="text-white"],
 [class*="file:text-white"]::file-selector-button {
 color: var(--arca-white) !important;
 }

 [class*="border-slate"],
 [class*="border-gray"],
 [class*="border-zinc"],
 [class*="border-neutral"],
 [class*="border-stone"],
 [class*="border-red"],
 [class*="border-orange"],
 [class*="border-amber"],
 [class*="border-yellow"],
 [class*="border-lime"],
 [class*="border-green"],
 [class*="border-emerald"],
 [class*="border-teal"],
 [class*="border-cyan"],
 [class*="border-sky"],
 [class*="border-blue"],
 [class*="border-indigo"],
 [class*="border-violet"],
 [class*="border-purple"],
 [class*="border-fuchsia"],
 [class*="border-pink"],
 [class*="border-rose"],
 [class*="border-maroon"],
 [class*="border-white"],
 [class*="divide-slate"] > :not([hidden]) ~ :not([hidden]),
 [class*="divide-gray"] > :not([hidden]) ~ :not([hidden]),
 [class*="divide-rose"] > :not([hidden]) ~ :not([hidden]) {
 border-color: var(--arca-brown) !important;
 }

 [class*="ring-slate"],
 [class*="ring-gray"],
 [class*="ring-red"],
 [class*="ring-amber"],
 [class*="ring-green"],
 [class*="ring-emerald"],
 [class*="ring-sky"],
 [class*="ring-blue"],
 [class*="ring-indigo"],
 [class*="ring-rose"],
 [class*="ring-maroon"],
 [class*="focus:ring-"]:focus,
 [class*="hover:ring-"]:hover {
 --tw-ring-color: var(--arca-brown) !important;
 }

 input,
 select,
 textarea {
 background-color: var(--arca-white) !important;
 border-color: var(--arca-brown) !important;
 color: var(--arca-brown) !important;
 }

 input::placeholder,
 textarea::placeholder {
 color: var(--arca-brown) !important;
 opacity: .75;
 }

 input[type="checkbox"],
 input[type="radio"] {
 color: var(--arca-brown) !important;
 }

 svg {
 color: currentColor;
 }

 .rounded-3xl {
 border-radius: 1rem !important;
 }

 .rounded-2xl {
 border-radius: .875rem !important;
 }

 .rounded-xl {
 border-radius: .75rem !important;
 }

 [class*="shadow"] {
 --tw-shadow: 0 1px 0 rgba(189, 155, 117, .28), 0 12px 28px rgba(189, 155, 117, .10) !important;
 --tw-shadow-colored: var(--tw-shadow) !important;
 box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow) !important;
 }

 .shadow-none {
 box-shadow: none !important;
 }

 table {
 border-color: var(--arca-brown) !important;
 }

 thead,
 thead tr,
 th {
 background-color: var(--arca-white) !important;
 color: var(--arca-brown) !important;
 border-color: var(--arca-brown) !important;
 }

 tbody tr {
 background-color: var(--arca-white) !important;
 }

 tbody tr:hover {
 outline: 1px solid var(--arca-brown);
 outline-offset: -1px;
 }

 a,
 button {
 letter-spacing: 0 !important;
 }

 button,
 a[class*="rounded"],
 input,
 select,
 textarea {
 transition: border-color .15s ease, box-shadow .15s ease, opacity .15s ease;
 }

 button:focus-visible,
 a:focus-visible,
 input:focus,
 select:focus,
 textarea:focus {
 outline: 2px solid var(--arca-brown) !important;
 outline-offset: 2px;
 }

 [class*="hover:bg-"]:hover {
 background-color: var(--arca-white) !important;
 box-shadow: inset 0 0 0 1px var(--arca-brown) !important;
 }

 [class*="hover:bg-maroon"]:hover,
 [class*="hover:bg-red"]:hover,
 [class*="hover:bg-rose"]:hover {
 background-color: var(--arca-brown) !important;
 color: var(--arca-white) !important;
 }

 [class*="opacity-25"],
 [class*="opacity-40"],
 [class*="opacity-50"],
 [class*="opacity-60"],
 [class*="opacity-70"],
 [class*="opacity-75"],
 [class*="opacity-80"],
 [class*="opacity-85"],
 [class*="opacity-90"],
 [class*="opacity-95"] {
 opacity: 1 !important;
 }
</style>
