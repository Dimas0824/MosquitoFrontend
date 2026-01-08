@extends('admin.layouts.base')

@section('content')
    @include('admin.partials.stats')
    @include('admin.partials.devices-table')
    @include('admin.partials.inference-table')
    @include('admin.partials.gallery-grid')
@endsection
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IoT Admin - Data Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            scroll-behavior: smooth;
        }

        .sidebar-item-active {
            background: linear-gradient(to right, rgba(79, 70, 229, 0.1), transparent);
            border-left: 4px solid #4f46e5;
            color: #4f46e5;
        }

        .glass-card {
            <button onclick="openModal('modalDevice')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2 text-sm font-bold shadow-lg shadow-indigo-100 hover:shadow-indigo-200 transition-all active:scale-95"><i data-lucide="plus" class="w-4 h-4"></i>Tambah Device </button></div><div class="overflow-x-auto"><table class="w-full text-left"><thead><tr class="bg-slate-50/50 text-slate-400 text-[10px] uppercase tracking-widest font-bold"><th class="px-8 py-4">Device Code</th><th class="px-8 py-4">Lokasi Instalasi</th><th class="px-8 py-4">Status</th><th class="px-8 py-4 text-right">Aksi</th></tr></thead><tbody class="divide-y divide-slate-100"><tr class="hover:bg-slate-50/50 transition-colors group"><td class="px-8 py-5"><div class="flex items-center gap-3"><div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center group-hover:bg-indigo-100 transition-colors"><i data-lucide="hard-drive" class="w-4 h-4 text-slate-400 group-hover:text-indigo-600"></i></div><span class="font-bold text-slate-900">ESP32-JENTIK-01</span></div></td><td class="px-8 py-5 text-sm font-medium text-slate-600">Gedung A,
            Area Taman Dalam </td><td class="px-8 py-5"><span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold px-3 py-1 rounded-full border border-emerald-200"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>AKTIF </span></td><td class="px-8 py-5"><div class="flex justify-end gap-2"><button class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-indigo-50 hover:text-indigo-600 transition-all"><i data-lucide="edit-3" class="w-4 h-4"></i></button><button class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all"><i data-lucide="trash-2" class="w-4 h-4"></i></button></div></td></tr>< !-- More devices could be listed here --></tbody></table></div></section>< !-- SECTION: INFERENCE RESULTS --> <section id="inference" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden animate-fade-in" style="animation-delay: 0.3s;"> <div class="p-8 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4"> <div> <h2 class="text-xl font-extrabold text-slate-900">Hasil Inferensi Terbaru</h2> <p class="text-sm text-slate-500 italic">Memproses 1.2k data per detik melalui model AI-Edge.</p> </div> <button class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2"> <i data-lucide="download" class="w-3.5 h-3.5"></i> Export Log </button> </div> <div class="overflow-x-auto"> <table class="w-full text-left"> <thead> <tr class="bg-slate-50/50 text-slate-400 text-[10px] uppercase tracking-widest font-bold"> <th class="px-8 py-4">Timestamp</th> <th class="px-8 py-4">Source Device</th> <th class="px-8 py-4">Detection Class</th> <th class="px-8 py-4 text-center">Score</th> <th class="px-8 py-4 text-right">Aksi</th> </tr> </thead> <tbody class="divide-y divide-slate-100"> <tr class="hover:bg-slate-50/50 transition-colors"> <td class="px-8 py-5 text-xs font-medium text-slate-500">Jan 08, 10:45:12</td> <td class="px-8 py-5 font-bold text-slate-700">ESP32-JENTIK-01</td> <td class="px-8 py-5"> <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-lg text-xs font-bold border border-indigo-100">Larva</span> </td> <td class="px-8 py-5 text-center"> <div class="w-24 bg-slate-100 h-2 rounded-full mx-auto overflow-hidden"> <div class="bg-indigo-500 h-full w-[98%]"></div> </div> <span class="text-[10px] font-bold text-indigo-600 mt-1 block tracking-wider">98.2%</span> </td> <td class="px-8 py-5 text-right"> <button class="text-slate-400 hover:text-indigo-600 font-bold text-xs">Detail View</button> </td> </tr> </tbody> </table> </div> </section> < !-- SECTION: GALLERY --> <section id="gallery" class="animate-fade-in" style="animation-delay: 0.4s;"> <div class="mb-8 flex items-center justify-between"> <div> <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Galeri Visual Deteksi</h2> <p class="text-sm text-slate-500">Dataset visual yang dikirim oleh modul kamera.</p> </div> <div class="flex items-center gap-2"> <span class="text-xs font-medium text-slate-400">Filter by:</span> <select class="bg-white border border-slate-200 text-xs font-bold rounded-lg px-3 py-1.5 outline-none focus:ring-2 focus:ring-indigo-500"> <option>Semua Device</option> <option>ESP32-01</option> </select> </div> </div> <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6"> < !-- Gallery Item --> <div class="group relative bg-white rounded-[2rem] overflow-hidden shadow-sm border border-slate-200 aspect-square cursor-pointer transition-all hover:shadow-2xl hover:-translate-y-2"> <img src="https://images.unsplash.com/photo-1542362567-b052d373196c?q=80&w=400&h=400&auto=format&fit=crop" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="Larva Capture"> <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col justify-end p-6"> <div class="translate-y-4 group-hover:translate-y-0 transition-transform duration-300"> <p class="text-[10px] text-indigo-400 font-black uppercase tracking-[0.2em] mb-1"> ESP32-01</p> <p class="text-sm text-white font-bold leading-tight mb-3">Deteksi: Larva (98.2%) </p> <div class="flex gap-2"> <button class="flex-1 bg-white/20 backdrop-blur-md hover:bg-white/40 text-white text-[10px] font-bold py-2 rounded-xl transition-colors">Download</button> <button class="w-10 bg-indigo-600 text-white flex items-center justify-center rounded-xl"><i data-lucide="maximize-2" class="w-4 h-4"></i></button> </div> </div> </div> </div> < !-- Skeleton Placeholders --> <div class="bg-slate-100 rounded-[2rem] aspect-square flex items-center justify-center border border-dashed border-slate-300"> <i data-lucide="image" class="w-8 h-8 text-slate-300"></i> </div> <div class="bg-slate-100 rounded-[2rem] aspect-square flex items-center justify-center border border-dashed border-slate-300"> <i data-lucide="image" class="w-8 h-8 text-slate-300"></i> </div> <div class="bg-slate-100 rounded-[2rem] aspect-square flex items-center justify-center border border-dashed border-slate-300"> <i data-lucide="image" class="w-8 h-8 text-slate-300"></i> </div> <div class="bg-slate-100 rounded-[2rem] aspect-square flex items-center justify-center border border-dashed border-slate-300"> <i data-lucide="image" class="w-8 h-8 text-slate-300"></i> </div> </div> </section> </div> </main> </div> < !-- Modal Create Device --> <div id="modalDevice" class="fixed inset-0 bg-slate-900/40 backdrop-blur-md z-50 flex items-center justify-center hidden p-4 opacity-0 transition-opacity duration-300"> <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl overflow-hidden border border-white transform scale-95 transition-transform duration-300"> <div class="p-8 bg-slate-50 border-b border-slate-100 flex justify-between items-center"> <div> <h3 class="font-black text-xl text-slate-900 tracking-tight">Tambah Perangkat</h3> <p class="text-xs text-slate-500 mt-1">Daftarkan endpoint IoT baru ke sistem.</p> </div> <button onclick="closeModal('modalDevice')" class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm text-slate-400 hover:text-red-500 transition-all"><i data-lucide="x" class="w-5 h-5"></i></button> </div> <form class="p-8 space-y-6"> <div> <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Device Serial Code</label> <div class="relative"> <i data-lucide="hash" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i> <input type="text" placeholder="ESP32-LAB-XXXX" class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium"> </div> </div> <div> <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Lokasi Instalasi</label> <div class="relative"> <i data-lucide="map-pin" class="w-4 h-4 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i> <input type="text" placeholder="Gedung, Lantai, Area..." class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium"> </div> </div> <div class="pt-4 flex gap-4"> <button type="button" onclick="closeModal('modalDevice')" class="flex-1 px-4 py-4 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">Batal</button> <button type="submit" class="flex-1 px-4 py-4 bg-indigo-600 text-white rounded-2xl text-sm font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 hover:shadow-indigo-200 transition-all">Simpan Perangkat</button> </div> </form> </div> </div> <script>
                // Initialize Lucide Icons
                lucide.createIcons();

                function openModal(id) {
                    const modal = document.getElementById(id);
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    setTimeout(() => {
                        modal.classList.remove('opacity-0');
                        modal.querySelector('div').classList.remove('scale-95');
                    }, 10);
                }

                function closeModal(id) {
                    const modal = document.getElementById(id);
                    modal.classList.add('opacity-0');
                    modal.querySelector('div').classList.add('scale-95');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }, 300);
                }

                // Sidebar Navigation Active State Toggle (Demo)
                document.querySelectorAll('nav a').forEach(link => {
                    link.addEventListener('click', function() {
                        document.querySelectorAll('nav a').forEach(l => l.classList.remove('sidebar-item-active',
                            'text-indigo-600'));
                        document.querySelectorAll('nav a').forEach(l => l.classList.add('text-slate-500'));
                        this.classList.add('sidebar-item-active');
                        this.classList.remove('text-slate-500');
                    });
                });

                // Close modal on click outside
                window.onclick = function(event) {
                    if (event.target.id === 'modalDevice') {
                        closeModal('modalDevice');
                    }
                }
            </script> </body> </html>
